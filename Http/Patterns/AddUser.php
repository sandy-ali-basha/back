<?php

namespace App\Http\Patterns;

use App\Models\Logs;
use App\Models\User;

class AddUser implements IOperations
{

    public function doOperation(array $data)
    {
        return User::create($data);
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'User');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD NEW User us IN User CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
