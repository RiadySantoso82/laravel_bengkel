<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            $errors = $exception->validator->errors()->all();
            $msg = implode('<br>', array_slice($errors, 0, 3));
            if (count($errors) > 3) $msg .= '<br><em>...dan ' . (count($errors) - 3) . ' lainnya</em>';

            if ($request->expectsJson()) {
                return response()->json(['error' => true, 'messages' => $errors], 422);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors($exception->validator)
                ->with('validation_error', $msg);
        }

        if (strpos($request->path(), 'login') === false &&
            strpos($request->path(), 'queue') === false &&
            !$request->expectsJson()) {
            try {
                if (method_exists($exception, 'getMessage')) {
                    $msg = $exception->getMessage();
                    if (!empty($msg)) {
                        return redirect()->back()->withInput()->with('error', 'Error: ' . $msg);
                    }
                }
            } catch (\Throwable $e) {
                // silent fallback
            }
        }

        return parent::render($request, $exception);
    }
}
