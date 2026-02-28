<?php

namespace App\Drivers;

use App\Http\Helpers\FibApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Response\PaymentAuthorize;
use Lunar\Base\DataTransferObjects\PaymentCapture;
use Lunar\Base\DataTransferObjects\PaymentRefund;
// use Lunar\Models\Transaction;
use App\Models\Transaction;
use Lunar\PaymentTypes\AbstractPayment;

class FibPaymentDriver extends AbstractPayment
{
    
    /**
     * {@inheritDoc}
     */
    public function authorize($update=false): PaymentAuthorize
    {
        try {
            // Use the FIB SDK to authorize a payment
            if (!$this->order) {
                if (!$this->order = $this->cart->order) {
                    $this->order = $this->cart->createOrder();
                }
            }
            if ($update && $this->order->transactions()->count() > 0) {
                $this->order->transactions()->first()->delete();
            }
            $fibApi = new FibApi();

            $response = $fibApi->createPayment($this->order->total->decimal(true));

            if ($response['code'] === 200) {
                $this->order->transactions()->create([
                    'success' => false,
                    'type' => 'intent',
                    'driver' => 'fib',
                    'amount' => $this->order->total->decimal(true),
                    'reference' => $response['data']['paymentId'],
                    'status' => 'pending',
                    'card_type' => 'fib',
                    'meta' => array_merge([], $response['data']),
                ]);
                // $this->order->first()->update([
                //     'status' => 'awaiting-payment',
                //     'placed_at' => now()
                // ]);
                return new PaymentAuthorize(
                    success: true,
                    message: 'Invoice Created successfully',
                    data: $response['data'],
                );

            } else {
                return new PaymentAuthorize(
                    success: false,
                    message: 'Could not connect to FiB',
                );
            }


        } catch (\Exception $e) {
            $failure = new PaymentAuthorize(
                success: false,
                message: $e->getMessage(),
            );

            return $failure;
        }
    }

    public function capture($transaction, $amount = 0): PaymentCapture
    {
        //Not applicable for FiB

        return new PaymentCapture(success: true);
    }

    public function refund($transaction, int $amount = 0, $notes = null): PaymentRefund
    {
        try {
            $fibApi = new FibApi();

            $response = $fibApi->refundPayment($transaction->reference);
            if ($response['code'] === 202) {
                $transaction->order->transactions()->create([
                    'success' => true,
                    'type' => 'refund',
                    'driver' => 'fib',
                    'amount' => $amount,
                    'reference' => 'offline',
                    'status' => 'refund',
                    'notes' => $notes,
                    'card_type' => $transaction->card_type,
                    'last_four' => $transaction->last_four,
                ]);
                return new PaymentRefund(
                    success: true,
                    message: 'Invoice Created successfully',
                );

            } else {
                return new PaymentRefund(
                    success: false,
                    message: 'Could not connect to FiB',
                );
            }
           
            

            
        } catch (\Exception $e) {
            Log::error($e->getMessage());   
            return new PaymentRefund(
                success: false,
                message: 'Some Error Occured',
            );
        }
    }
}
