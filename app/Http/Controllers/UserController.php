<?php

namespace App\Http\Controllers;

use App\Http\Data\AddCustomerData;
use App\Http\Data\AddCustomerDataSocial;
use App\Http\Data\AddUserData;
use App\Http\Data\ChangePasswordData;
use App\Http\Data\ForgetPasswordData;
use App\Http\Data\LoginUserData;
use App\Http\Data\AddPasswordData;
use App\Http\Data\AddUserDataSocial;
use App\Http\Data\VerifyAccountData;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\UserResource;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Mail\ResetPasswordEmail;
use App\Mail\VerifyRegisterEmail;
use App\Models\User;
use App\Services\CustomerService;
use App\Services\UserService;
use Exception;
use Ichtrojan\Otp\Otp;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public UserService     $userService;
    public CustomerService $customerService;

    public function __construct(UserService $userService, CustomerService $customerService)
    {
        $this->userService     = $userService;
        $this->customerService = $customerService;
    }

    public function register(Request $request)
    {
        $userData = AddUserData::from($request);

        try {
            DB::beginTransaction();

            $userData->remember_token = Str::random(10);
            $userData->password = Hash::make($userData->password);
            $user = $this->userService->createNewUser($userData);

            $request->request->add(['user_id' => $user->id]);

            $customerData    = AddCustomerData::from($request);
            $token           = $user->createToken('Laravel Password Grant Client')->accessToken;
            $customer        = $this->customerService->createNewCustomer($customerData);
            $customer->token = $token;
            $user->markEmailAsVerified();
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            $response = new ErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }

        // Mail::to($userData->email)->send(new VerifyRegisterEmail([
        //     'otp' => (new Otp)->generate($userData->email, 'numeric', 6, 15)->token,
        // ]));
        $customerResource = new CustomerResource($customer);
        $response         = new SuccessResponse($customerResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function login(Request $request)
    {
        $userData = LoginUserData::from($request);
        $user     = User::where('email', '=', $userData->email)->first();

        if ($user) {
            if (Hash::check($userData->password, $user->password)) {
                $token        = $user->createToken('Laravel Password Grant Client')->accessToken;

                $customer = $this->customerService->getByUserId($user->id);
                      if(!$customer){
                    $response = new ErrorResponse("Customer not found", Response::HTTP_NOT_FOUND);

                    return response()->error($response);
                }
                $customer->token  = $token;
                $UserResource = new CustomerResource($customer);
                $response     = new SuccessResponse($UserResource, Response::HTTP_OK);

                return response()->success($response);
            } else {
                $response = new ErrorResponse("Password mismatch", Response::HTTP_UNPROCESSABLE_ENTITY);

                return response()->error($response);
            }
        } else {
            $response = new ErrorResponse("User does not exist", Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
    }

    public function verifyAccount(Request $request)
    {
        $userData = VerifyAccountData::from($request);
        $verify = (new Otp)->validate($userData->email, $userData->token);
        if ($verify->status != true) {
            $response = new ErrorResponse("Validation Error, Please try after a while", Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }

        $user = User::where('email', '=', $userData->email)->first();
        DB::table('otps')->where('identifier', $userData->email)->delete();
        $user->markEmailAsVerified();
        $token        = $user->createToken('Laravel Password Grant Client')->accessToken;
        $user->token  = $token;
        $UserResource = new UserResource($user);
        $response     = new SuccessResponse($UserResource, Response::HTTP_OK);
        return response()->success($response);
    }

    public function forgotPassword(Request $request)
    {
        $data  = ForgetPasswordData::from($request);
        $token = Password::createToken(User::where('email', '=', $data->email)->first());
        $link  = "https://dawaaalhayat.com/reset-password?token=$token&email=$data->email";
        Mail::to($data->email)->send(new ResetPasswordEmail([
            'link' => $link,
        ]));
        $response = new SuccessResponse(['url' => $link], Response::HTTP_OK);

        return response()->success($response);
    }

    public function resetPassword(Request $request, string $token)
    {

        $data = AddPasswordData::from($request);

        $status = Password::reset(
            [
                'email' => $data->email,
                'password' => $data->password,
                'password_confirmation' => $data->password_confirmation,
                'token' => $token
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        $response = new SuccessResponse(['data' => $status === Password::PASSWORD_RESET
            ? 'Password Reset'
            : $status], Response::HTTP_OK);

        return response()->success($response);
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

    public function socialLogin(Request $request, $provider)
    {
        return response()->json(['url' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl()]);
    }

    public function socialCallback(Request $request, $provider)
    {
        $gtoken = $request->input('access_token');
        $user = Socialite::driver($provider)->userFromToken($gtoken);

        $existUser = User::where('email', $user->email)->first();

        if ($existUser) {
            $token = $existUser->createToken('Laravel Password Grant Client')->accessToken;
            $customer = $this->customerService->getByUserId($existUser->id);
            $existUser->update([
                'provider_name' => $provider,
                'provider_id' => $user->id,
                'provider_token' => $gtoken
            ]);

            $customer->token = $token;

            $UserResource = new CustomerResource($customer);
            $response = new SuccessResponse($UserResource, Response::HTTP_OK);
            return response()->success($response);
        } else {

            $request->merge([
                'name' => $user->name,
                'email' => $user->email,
            ]);

            $userData = AddUserDataSocial::from($request);
            try {
                DB::beginTransaction();

                $userData->remember_token = Str::random(10);
                $userData->provider_name = $provider;
                $userData->provider_id = $user->id;
                $userData->provider_token = $user->token;

                $user = $this->userService->createNewUser($userData);
                $name = explode(' ', $userData->name);

                if (count($name) > 2) {
                    $request->merge([
                        'first_name' => array_shift($name),
                        'last_name' => implode(' ', $name)
                    ]);
                } else {
                    $request->merge([
                        'first_name' => $name[0],
                        'last_name' => isset($name[1]) ? $name[1] : ''
                    ]);
                }
                $request->merge(['user_id' => $user->id]);

                $customerData = AddCustomerDataSocial::from($request);
                $customer = $this->customerService->createNewCustomer($customerData);
                
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $customer->token = $token;
                $user->markEmailAsVerified();
                DB::commit();
            } catch (Exception $exception) {
                DB::rollBack();
                Log::error($exception->getMessage());
                $response = new ErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

                return response()->error($response);
            }

            // Mail::to($userData->email)->send(new VerifyRegisterEmail([
            //     'otp' => (new Otp)->generate($userData->email, 'numeric', 6, 15)->token,
            // ]));
            $customerResource = new CustomerResource($customer);
            $response         = new SuccessResponse($customerResource, Response::HTTP_OK);

            return response()->success($response);
        }
    }

    public function myPoints() {
        $auth = Auth::user();
        if (!$auth) {
            $response = new ErrorResponse("User not logged in.", Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }
        
        $response = new SuccessResponse([
            'points' => $auth->rewardPoints->points
        ], Response::HTTP_OK);

        return response()->success($response);
    }
}
