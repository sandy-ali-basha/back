<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    protected $database;

    public function __construct()
    {
        $this->database = app('firebase.database');
    }

    /**
     * إضافة إشعار ليوزر معيّن
     */
    public function addNotificationToUser(int $userId, string $title, string $body, ?string $type = null)
    {
       
        $notificationData = [
            'title' => $title,
            'body' => $body,
            'type' => $type ?? null,
            'is_read' => false,
            'is_active' => true,
            'created_at' => now()->timestamp, // أفضل من string
        ];

        $this->database
            ->getReference("notifications/{$userId}")
            ->push($notificationData);

        return response()->json([
            'success' => true,
            'message' => 'Notification added successfully'
        ]);
    }

    /**
     * جلب إشعارات يوزر (الفعالة فقط)
     */
    public function getUserNotifications()
    {
        $userId =auth()->id();
        $notifications = $this->database
            ->getReference("notifications/{$userId}")
            ->orderByChild('is_active')
            ->equalTo(true)
            ->getValue();

        return response()->json($notifications ?? []);
    }

    /**
     * تحويل إشعار إلى مقروء
     */
   public function markAsRead($notificationId)
    {
    $userId = auth()->id();

    // 1️⃣ المرجع للإشعار
    $notificationRef = $this->database
        ->getReference("notifications/{$userId}/{$notificationId}");

    // 2️⃣ نجيب القيمة لنشوف إذا موجود
    $notification = $notificationRef->getValue();

    if (!$notification) {
        // إذا ما موجود → نرجع خطأ 404
        return response()->json([
            'success' => false,
            'message' => 'Notification not found'
        ], 404);
    }

    // 3️⃣ تحديث الإشعار
    $notificationRef->update([
        'is_read' => true
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Notification marked as read',
    ]);
}

public function markAllAsRead()
{
    $userId = auth()->id();

    // 1️⃣ المرجع لكل إشعارات المستخدم
    $notificationsRef = $this->database
        ->getReference("notifications/{$userId}");

    // 2️⃣ نجيب كل الإشعارات
    $notifications = $notificationsRef->getValue();

    if (!$notifications) {
        // إذا ما في إشعارات → نرجع رسالة مناسبة
        return response()->json([
            'success' => false,
            'message' => 'No notifications found'
        ], 404);
    }

    // 3️⃣ تحديث كل الإشعارات لتصبح مقروءة
    foreach ($notifications as $id => $notification) {
        $notificationsRef->getChild($id)->update([
            'is_read' => true
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'All notifications marked as read',
    ]);
}


    /**
     * عدد الإشعارات غير المقروءة
     */
    public function unreadCount()
    {
        $userId =auth()->id();

        $notifications = $this->database
            ->getReference("notifications/{$userId}")
            ->orderByChild('is_read')
            ->equalTo(false)
            ->getValue();

        return response()->json([
            'count' => $notifications ? count($notifications) : 0
        ]);
    }

    /**
     * تعطيل إشعار (حذف منطقي)
     */
    public function deactivateNotification($notificationId)
    {
         $userId =auth()->id();

        $this->database
            ->getReference("notifications/{$userId}/{$notificationId}")
            ->update([
                'is_active' => false,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification deactivated',
        ]);
    }
    
    public function checkReorderPointsAndNotify()
    {
        // جلب كل Variants
        $variants = \Lunar\Models\ProductVariant::all();
        
        foreach ($variants as $variant) {
            // إذا المخزون أقل أو مساوي للـ reorder point
            if ($variant->storage_qty <= $variant->reorder_point) {
                
                // إضافة إشعار للمسؤول (مثلاً user ID = 1)
                $this->addNotificationToUser(
                    5, // ID اليوزر المسؤول
                    'تنبيه مخزون منخفض ⚠️',
                    "المخزون للمنتج '{$variant->product->name}' (SKU: {$variant->sku}) وصل إلى {$variant->storage_qty} وحدة، أقل من reorder point ({$variant->reorder_point})",
                    'inventory'
                );
            }
        }
    
        return response()->json(['success' => true, 'message' => 'Reorder points checked and notifications sent.']);
    }

}
