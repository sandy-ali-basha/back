<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Order;
// use Lunar\Models\Transaction;
use App\Models\Transaction;
// use Lunar\Models\OrderAddress;
use App\Models\OrderAddress;

class OrderModel extends Order
{
    //use  Translatable;

    // protected $translatedAttributes = [];
    public static $orderStatuses = [
        'order_requested',
        'order_processing',
        'order_processed',
        'order_under_delivery',
        'order_delivered',
        'cancel_requested',
        'order_canceled',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('lunar.database.table_prefix').'orders');

        if ($connection = config('lunar.database.connection', false)) {
            $this->setConnection($connection);
        }
    }
    public static function create(array $attributes = [])
    {

        return static::query()->create($attributes);
    }

    public function getById($id)
    {
        $order =  $this->where('id', '=', $id)->first();

        return $order;
    }

    /**
     * Get Order's Total Points
     * @return int
     */
    public function totalPoints() {
        return $this->lines->sum('points');
    }

    public function lines()
    {
        return $this->hasMany(OrderLine::class, 'order_id', 'id');
    }

    public function canCancel() {
        $trans = $this->transactions()->first();
        if ($trans) {
            if ($trans->status === 'paid') {
                return false;
            }
        }

        if (array_search($this->status, self::$orderStatuses) > 1) {
            return false;
        }

        return true;
    }

        /**
     * Return the transactions relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'order_id', 'id');
    }
    
        /**
     * Return the addresses relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(OrderAddress::class, 'order_id');
    }

    /**
     * Return the shipping address relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shippingAddress()
    {
        return $this->hasOne(OrderAddress::class, 'order_id')->whereType('shipping');
    }

    /**
     * Return the billing address relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function billingAddress()
    {
        return $this->hasOne(OrderAddress::class, 'order_id')->whereType('billing');
    }
    public function customer()
{
    return $this->belongsTo(\App\Models\Customer::class);
}
}
