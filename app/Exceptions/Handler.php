<?php

namespace App\Exceptions;

use App\Traits\ApiResponses;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponses;

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected $handlers = [
        ValidationException::class => 'handleValidationException',
        NotFoundHttpException::class => 'handleNotFoundException',
        ModelNotFoundException::class => 'handleModelNotFoundException',
        AuthenticationException::class => 'handleAuthenticationException',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $throwable) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // Custom JSON response for API requests
        if ($request->expectsJson()) {
            $className = get_class($e);

            $index = strrpos($className, '\\');

            if (array_key_exists($className, $this->handlers)) {
                $method = $this->handlers[$className];

                return $this->error($this->$method($e));
            }

            return $this->error([
                'type' => substr($className, $index + 1),
                'status' => 0,
                'message' => $e->getMessage(),
                'source' => 'Line: ' . $e->getLine() . ': ' . $e->getFile(),
            ]);
        }

        // Default behavior for web requests
        return parent::render($request, $e);
    }

    private function handleValidationException(ValidationException $e)
    {
        foreach ($e->errors() as $key => $value) {
            foreach ($value as $message) {
                $errors[] = [
                    'status' => 422,
                    'message' => $message,
                    'source' => $key
                ];
            }
        }

        return $errors;
    }

    private function handleNotFoundException(NotFoundHttpException $e)
    {
        return [
            [
                'status' => 404,
                'message' => 'The resource cannot be found',
                'source' => $e->getFile()
            ]
        ];
    }

    private function handleModelNotFoundException(ModelNotFoundException $e)
    {
        return [
            [
                'status' => 404,
                'message' => 'The resource cannot be found',
                'source' => $e->getModel()
            ]
        ];
    }

    private function handleAuthenticationException(AuthenticationException $e)
    {
        return [
            [
                'status' => 401,
                'message' => 'Unauthenticated.',
                'source' => ''
            ]
        ];
    }
}
