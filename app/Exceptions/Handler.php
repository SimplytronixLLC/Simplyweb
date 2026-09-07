<?php
namespace App\Exceptions;  // ← Exceptions, not Providers

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle 404 - URL not found
        $this->renderable(function (NotFoundHttpException $e, $request) {
            return response()->view('errors.404', [], 404);
        });

        // Handle model not found (findOrFail etc.)
        $this->renderable(function (ModelNotFoundException $e, $request) {
            return response()->view('errors.404', [], 404);
        });
    }
    // ← NO boot() method here
}