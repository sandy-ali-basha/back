<?php

namespace App\Http\Patterns;

use App\Models\Logs;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UpdateUser implements IOperations
{

    public function doOperation(array $data)
    {
        $user = User::find($data['id']);
        $user->update($data['data']);
        return $user;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'User');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD NEW User us IN User CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
