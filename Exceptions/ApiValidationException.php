<?php
/**
 * lamsa-school-service-2 - ValidationException.php
 *
 * Date: 1/15/2023
 * Time: 1:35 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2023 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Exceptions;


class ApiValidationException extends ApiException
{
    public function __construct(string $message = "validation exception",int $code)
    {
        parent::__construct($message, $code);
    }
}
