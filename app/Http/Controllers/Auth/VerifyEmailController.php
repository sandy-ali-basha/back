<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Exception;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        try {
            $user = $request->user();

            if ($user->hasVerifiedEmail()) {
                return $this->successRedirect();
            }

            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }

            return $this->successRedirect();

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage() ?: 'Unknown error occurred during email verification',
            ], 500);
        }
    }

    private function successRedirect(): RedirectResponse
    {
        return redirect()->intended(
            config('app.frontend_url') . '/dashboard?verified=1'
        );
    }
}
