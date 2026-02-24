<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Logs extends Model
{
    use HasFactory;
    protected $fillable = ['message', 'user_id', 'privilege_id', 'page'];

    public function privilege()
    {
        return $this->belongsTo('App\Models\Privilege');
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\User');
    }

    public static function createNewRecord($privilege, $page)
    {
        //get current user
        $user = Auth::user();
        //get user id
        $data['user_id'] = ($user != null) ? $user->id : null;
        $username = ($user != null) ? $user->name : 'N/A';
        //get privilege
//        $data['privilege_id'] = Privilege::getPrivilegeId($privilege);
        //get page
        $data['page'] = $page;
        //log msg
        $data['message'] = 'The User with username (' . $username .
            ') perform (' . $privilege . ') Operation on (' . $page . ') Page';

        //insert in DB
        Log::create($data);
    }

    public static function logs()
    {
        return Log::all();
    }

    public static function activate($id)
    {
        $row = Log::find($id);
        if ($row != null)
        {
            $row->active = true;
            //here we can activate some relating things
            //
            return $row->save();
        }
        return false;
    }

    public static function deactivate($id)
    {
        $row = Log::find($id);
        if ($row != null)
        {
            $row->active = false;
            //here we can deactivate some relating things
            //
            return $row->save();
        }
        return false;
    }

    public static function recover($id)
    {
        $row = Log::find($id);
        if ($row != null)
        {
            $row->trashed = false;
            //here we can deactivate some relating things
            //
            return $row->save();
        }
        return false;
    }
}
