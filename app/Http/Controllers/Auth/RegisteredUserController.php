<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Customer; // مهم

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */


    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        /** 🔥 إنشاء Customer جديد مرتبط بالمستخدم */
        $customer = Customer::create([
            'first_name' => $request->name,
            'last_name'  => '',
            'email'      => $request->email,
        ]);
    
        /** 🔥 ربط اليوزر بالكوستمر */
        $customer->users()->attach($user->id);
    
        event(new Registered($user));
    
        Auth::login($user);
    
        return response()->noContent();
    }

}
