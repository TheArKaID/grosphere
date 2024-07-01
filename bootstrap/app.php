<?php

use App\Exceptions\AgoraException;
use App\Exceptions\EndClassSessionException;
use App\Exceptions\JoinClassSessionException;
use App\Exceptions\MailException;
use App\Exceptions\MessageException;
use App\Exceptions\ModelGetEmptyException;
use App\Exceptions\RegisterStudentClassException;
use App\Exceptions\TeacherFileException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'permission' => PermissionMiddleware::class,
            'role' => RoleMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
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

        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Not authenticated'
                ], 403);
            }
        });

        $exceptions->renderable(function (UnauthorizedException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Not authenticated'
                ], 403);
            }
        });

        $exceptions->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 400,
                    'message' => "Validation failed",
                    'errors' => $e->validator->getMessageBag()
                ], 400);
            }
        });

        $exceptions->renderable(function (JWTException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Cannot verify token'
                ], 403);
            }
        });

        $exceptions->renderable(function (AgoraException $e, $request) {
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

        $exceptions->renderable(function (JoinClassSessionException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 400,
                    'message' => $e->getMessage()
                ], 400);
            }
        });

        $exceptions->renderable(function (RegisterStudentClassException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 400,
                    'message' => $e->getMessage()
                ], 400);
            }
        });
        
        $exceptions->renderable(function (EndClassSessionException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 400,
                    'message' => $e->getMessage()
                ], 400);
            }
        });

        $exceptions->renderable(function (TeacherFileException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 400,
                    'message' => $e->getMessage()
                ], 400);
            }
        });

        $exceptions->renderable(function (MailException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 400,
                    'message' => $e->getMessage()
                ], 400);
            }
        });

        $exceptions->renderable(function (MessageException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 400,
                    'message' => $e->getMessage()
                ], 400);
            }
        });

        $exceptions->renderable(function (Throwable $e, $request) {
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
    })->create();
