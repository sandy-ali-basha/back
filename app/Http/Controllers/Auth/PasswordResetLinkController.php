<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Exception;

class PasswordResetLinkController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => ['required', 'email'],
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status !== Password::RESET_LINK_SENT) {

                // Custom: User not found
                if ($status === Password::INVALID_USER) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'User not found',
                    ],200 );
                }

                // Other known errors
                throw ValidationException::withMessages([
                    'email' => [__($status)],
                ]);
            }

            return response()->json([
                'status' => __($status),
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'validation_error',
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage() ?: 'Unknown error occurred',
            ], 500);
        }
    }
}
