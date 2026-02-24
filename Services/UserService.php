<?php
/**
 * Dawaa - UserService.php
 *
 * Date: 24/04/28
 * Time: 7:41 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Services;

use App\Http\Data\AddCustomerData;
use App\Http\Data\AddUserData;
use App\Http\Data\UpdateUserData;
use App\Http\Patterns\AddCustomer;
use App\Http\Patterns\AddUser;
use App\Http\Patterns\UpdateUser;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function createNewUser($userData): User
    {
        $user     = new AddUser();

        return $user->doOperation($userData->toArray());
    }
    public function update(User $user,UpdateUserData $userData)
    {
        $userUpdate     = new UpdateUser();

        return $userUpdate->doOperation(['id'=>$user->id,'data'=>$userData->toArray()]);
    }

    public function getById($id, $with = null)
    {
        if ($with !== null) {
            return User::with($with)->find($id);
        } else {
            return User::find($id);
        }
    }

    public function getAdmins() {
        return User::with(['roles', 'roles.permissions'])->whereDoesntHave('customers')->get();
    }
}
