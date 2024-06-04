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
                        'status' => 400,
                        'message' => 'Failed. ' . explode('\\', $e->getPrevious()->getModel())[2] . ' not found'
                    ], 400);
                }

                if ($e->getPrevious() instanceof ModelGetEmptyException) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Failed. No ' . $e->getPrevious()->getMessage() . ' found'
                    ], 400);
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

        $this->renderable(function (AgoraException $e, $request) {
            if ($request->is('api/*')) {
                $response = [
                    'status' => 400,
                    'message' => 'Failed Creating Meeting Room'                    
                ];
                if(config('app.debug')) {
                    $response['errors'] = collect(json_decode($e->getMessage()))->pluck('message');
                }
                return response()->json($response, 404);
            }
        });

        // NotEnrolledException
        $this->renderable(function (NotEnrolledException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 400,
                    'message' => $e->getMessage()
                ], 400);
            }
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                if (config('app.debug')) {
                    return response($e, 500);
                }

                $response = [
                    'status' => 500,
                    'message' => 'Internal server error'
                ];
                return response()->json($response, 500);
            }
        });
    }
}
