<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'transactions');
    }
}
