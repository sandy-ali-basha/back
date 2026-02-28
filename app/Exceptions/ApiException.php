<?php

namespace App\Exceptions;

use App\Http\Response\ErrorResponse;
use Exception;
use Illuminate\Http\JsonResponse;

abstract class ApiException extends Exception
{
    /**
     * @param array $errors
     *
     * @return JsonResponse
     */
    public function render(array $errors = []): JsonResponse
    {
        $response = new ErrorResponse($this->getMessage(), $this->getCode(),$errors);

        return response()->error($response);
    }
}
