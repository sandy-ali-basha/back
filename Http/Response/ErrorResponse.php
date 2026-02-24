<?php

namespace App\Http\Response;

class ErrorResponse
{
    /**
     * @var int $code
     */
    private int $code;
    /**
     * @var array|null $error
     *
     */
    private ?array $error;

    public function __construct(string $message,int $code,array $errors = [])
    {
        $this->code=$code;
        $this->error =["message"=>$message,"errors"=>$errors];
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return get_object_vars($this);
    }



}
