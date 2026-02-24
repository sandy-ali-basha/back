<?php
/**
 * Dawaa - ss.php
 *
 * Date: 24/04/28
 * Time: 7:15 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Services;

use App\Models\User;
use App\Models\WhatsAppAPI;
use App\Models\WhatsAppOTP;

class WhatsAppOTPService
{
    private User        $user;
    private WhatsAppOTP $otp;
    private WhatsAppAPI $api;

    public function sendOTPToUser()
    {

    }

    public function validateUserOTP()
    {

    }

}
