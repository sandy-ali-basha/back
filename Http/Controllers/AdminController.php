<?php

namespace App\Http\Controllers;

use App\Http\Data\AddAdminData;
use App\Http\Data\AddUserData;
use App\Http\Data\ChangePasswordData;
use App\Http\Data\LoginUserData;
use App\Http\Data\UpdateAdminData;
use App\Http\Resources\AdminResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public UserService     $userService;

    public function __construct(UserService $userService)
    {
        $this->userService     = $userService;
    }

    public function index()
    {
        $users        = $this->userService->getAdmins();
        $UserResource = new UserCollection($users);
        $response         = new SuccessResponse($UserResource, Response::HTTP_OK);

        return response()->success($response);
    }
    
    public function show($id)
    {
        $users        = $this->userService->getById($id, ['roles', 'roles.permissions']);
        if (!$users) {
            $response = new ErrorResponse('Admin not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $UserResource = new AdminResource($users);
        $response = new SuccessResponse($UserResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function store(Request $request)
    {
        $userData = AddAdminData::from($request);

        try {
            DB::beginTransaction();

            
            $userData->password = Hash::make($userData->password);
            $user = $this->userService->createNewUser($userData);
            $user->assignRole($userData->role);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            $response = new ErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }

        
        $customerResource = new AdminResource($user);
        $response         = new SuccessResponse($customerResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function update(Request $request, $id)
    {
        $userData = UpdateAdminData::from($request);
        
        $users        = $this->userService->getById($id);
        if (!$users) {
            $response = new ErrorResponse('Admin not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        try {
            DB::beginTransaction();
            if ($userData->password) {
                $userData->password = Hash::make($userData->password);
            }
            $users->update($userData->toArray());
            $users->syncRoles([$userData->role]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            $response = new ErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }

        
        $customerResource = new AdminResource($users);
        $response         = new SuccessResponse($customerResource, Response::HTTP_OK);

        return response()->success($response);
    }


    public function login(Request $request)
    {
        $userData = LoginUserData::from($request);
        $user     = User::where('email', '=', $userData->email)
                        ->whereDoesntHave('customers')->first();
        if ($user) {
            if (Hash::check($userData->password, $user->password)) {
                $token        = $user->createToken('Laravel Password Grant Client')->accessToken;

                $user->token  = $token;
                $UserResource = new UserResource($user);
                $response     = new SuccessResponse($UserResource, Response::HTTP_OK);

                return response()->success($response);
            } else {
                $response = new ErrorResponse("Password mismatch", Response::HTTP_UNPROCESSABLE_ENTITY);

                return response()->error($response);
            }
        } else {
            $response = new ErrorResponse("Admin does not exist", Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
    }


    public function changePassword(Request $request)
    {
        $data = ChangePasswordData::from($request);

        $auth = Auth::user();

        if (!Hash::check($data->current_password, $auth->password)) {
            $response = new ErrorResponse("Current Password is Invalid", Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }

        if (strcmp($data->current_password, $request->new_password) == 0) {
            $response = new ErrorResponse("New Password cannot be same as your current password.", Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }

        $user           = User::find($auth->id);
        $user->password = Hash::make($data->password);
        $user->save();
        $response = new SuccessResponse("Password Changed Successfully", Response::HTTP_OK);

        return response()->success($response);
    }

    public function destroy($id)
    {
        $users        = $this->userService->getById($id, ['roles', 'roles.permissions']);
        if (!$users) {
            $response = new ErrorResponse('Admin not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }

        try {
            $users->delete();
            $response = new SuccessResponse("Admin is deleted", Response::HTTP_OK);
    
            return response()->success($response);
        } catch (\Exception $e) {
            $response = new ErrorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            return response()->error($response);
        }

    }

}
