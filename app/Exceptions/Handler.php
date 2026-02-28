<?php

namespace App\Exceptions;

use App\Http\Response\ErrorResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function(Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request   $request
     * @param Throwable $e
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e): JsonResponse
    {
        
        
        if ($e instanceof ApiException) {
            Log::error($e->getMessage()); 
            $this->logDetails($e);
            return $e->render();
        }
        elseif ($e instanceof ValidationException){
            $exception= new ApiValidationException('validation exception', Response::HTTP_BAD_REQUEST);
            return $exception->render($e->errors());
        }
        elseif ($e instanceof AuthenticationException){
            $exception= new ApiAuthException($e->getMessage(), Response::HTTP_UNAUTHORIZED);
            return $exception->render();

        }
        elseif ($e instanceof AuthorizationException || $e instanceof UnauthorizedException){
            $exception= new ApiAuthException($e->getMessage(), Response::HTTP_UNAUTHORIZED);
            return $exception->render();

        }
        elseif ($e instanceof ModelNotFoundException && $request->wantsJson()) {
            $exception= new ApiAuthException($e->getMessage(), Response::HTTP_BAD_REQUEST);
            return $e->render();
        }
        $this->logDetails($e);
        return response()->error(new ErrorResponse('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR));
    }

    /**
     * @param Throwable $e
     *
     * @return void
     */
    public function report(Throwable $e)
    {
        if ($e instanceof ApiException || $e instanceof ValidationException) {
            return;

        }
        //dd($e);
        // Remove stack-trace when not debugging.
        if (config('app.debug')) {
            Log::error(
                sprintf(
                    "type: %s\nmessage: %s\nin file: %s line:%d\n",
                    get_class($e),
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                )
            );
        }
    }

    protected function logDetails(Throwable $e) {
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/details/'.now()->format("Y_m_d_H_i_s").".log"),
        ])->error( $e->getMessage(), [
            "requestVars" => request()->toArray(),
            "request" => request(),
            "error" => "An error occured in file {$e->getFile()}, Line ({$e->getLine()}), Code: {$e->getCode()}, Error: {$e->getMessage()}, ",
            "trace" => $e->getTraceAsString(),
        ]);
    }
}
