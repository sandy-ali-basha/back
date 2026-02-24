<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Lunar\Models\ProductVariant;
use App\Http\Controllers\FirebaseController;
use Illuminate\Support\Facades\App;

class CheckReorderPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-reorder-points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check product variants for reorder point and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1️⃣ نجيب كل الفارينت اللي المخزون أقل أو يساوي reorder_point
        $variants = ProductVariant::whereColumn('storage_qty', '<=', 'reorder_point')->get();

        if ($variants->isEmpty()) {
            $this->info('No variants below reorder point.');
            return 0;
        }

        // 2️⃣ نستدعي FirebaseController لإضافة الإشعارات
        $firebase = App::make(FirebaseController::class);

        $userId = 5; // اليوزر المسؤول اللي رح يستقبل الإشعارات
        $successCount = 0;

        foreach ($variants as $variant) {
            $result = $firebase->addNotificationToUser(
                $userId,
                'Stock Alert: Reorder Point Reached',
                "Variant {$variant->sku} reached its reorder point. Current stock: {$variant->storage_qty}",
                'stock'
            );

            if ($result) {
                $successCount++;
            }
        }

        // 3️⃣ رسالة تبين إذا أرسلنا إشعارات فعليًا
        if ($successCount > 0) {
            $this->info("Reorder point notifications sent successfully to {$successCount} variant(s).");
        } else {
            $this->info("No notifications were added.");
        }
    }
}
