<?php
/**
 * Dawaa - WhatsAppOTP.php
 *
 * Date: 24/04/28
 * Time: 7:43 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Services;


use App\Models\User;

class WhatsAppOTP
{
    protected User $user;
    protected int $otp;
    protected string $expiryData;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function generateOTP(){

    }

    public function validateOTP(){

    }
}
