<?php 
namespace App\Http\Response;

use Lunar\Base\DataTransferObjects\PaymentAuthorize as LunarPaymentAuthorize;

class PaymentAuthorize extends LunarPaymentAuthorize
{
    public array $data = [];

    public function __construct(
        bool $success = false,
        ?string $message = null,
        ?array $data = []
    ) {
        parent::__construct($success, $message);    
        $this->data = $data;
    }

}