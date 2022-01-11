<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
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
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                if ($e->getPrevious() instanceof ModelNotFoundException) {
                    return response()->json([
                        'status' => 204,
                        'message' => 'Failed. Data not found'
                    ], 200);
                }
                
                if($e->getPrevious() instanceof ModelGetEmptyException){
                    return response()->json([
                        'status' => 204,
                        'message' => 'Failed. No ' . $e->getPrevious()->getMessage() .' found'
                    ], 200);
                }
                return response()->json([
                    'status' => 404,
                    'message' => 'Target not found'
                ], 404);
            }
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Not authenticated'
                ], 403);
            }
        });

        $this->renderable(function (UnauthorizedException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Not authenticated'
                ], 403);
            }
        });

        $this->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 400,
                    'message' => "Validation failed",
                    'errors' => $e->validator->getMessageBag()
                ], 400);
            }
        });

        $this->renderable(function (JWTException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Cannot verify token'
                ], 403);
            }
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                $response = [
                    'status' => 500,
                    'message' => 'Internal server error'
                ];
                if (env('APP_DEBUG')) {
                    return response($e, 500);
                }

                return response()->json($response, 500);
            }
        });
    }
}
