<?php

namespace App\Drivers;

use Lunar\Base\DataTransferObjects\PaymentAuthorize;
use Lunar\Base\DataTransferObjects\PaymentCapture;
use Lunar\Base\DataTransferObjects\PaymentRefund;
// use Lunar\Models\Transaction;
use Lunar\PaymentTypes\AbstractPayment;
use App\Models\Transaction;

class OfflinePaymentDriver extends AbstractPayment
{
    /**
     * {@inheritDoc}
     */
    public function authorize($update=false): PaymentAuthorize
    {
        if (! $this->order) {
            if (! $this->order = $this->cart->order) {
                $this->order = $this->cart->createOrder();
            }
        }
        if ($update && $this->order->transactions()->count() > 0) {
            $this->order->transactions()->first()->delete();
        }
        $this->order->transactions()->create([
            'success' => false,
            'type' => 'intent',
            'driver' => 'coffline',
            'amount' => $this->order->total->decimal(true),
            'reference' => 'offline',
            'status' => 'unpaid',
            'card_type' => 'offline',
            'meta' => [],
        ]);

        return new PaymentAuthorize(true);
    }

    
    public function refund($transaction, int $amount = 0, $notes = null): PaymentRefund
    {
        return new PaymentRefund(true);
    }

   
    public function capture($transaction, $amount = 0): PaymentCapture
    {
        return new PaymentCapture(true);
    }
}
