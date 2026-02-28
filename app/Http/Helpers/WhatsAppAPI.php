<?php
/**
 * Dawaa - WhatsAppAPI.php
 *
 * Date: 24/04/28
 * Time: 7:48 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Helpers;

class WhatsAppAPI
{
    protected string $phoneNumber;
    protected string $apiKey;
    protected string $apiUrl;

    public function __construct(string $phoneNumber)
    {
        $this->phoneNumber  = $phoneNumber;
    }
    public function connect(){

    }
    public function sendMessage(){

    }

}
