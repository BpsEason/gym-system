<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\LaravelValidation\Rules\EnvironmentVariable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EnvValidationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->validateEnvironment();
    }

    protected function validateEnvironment(): void
    {
        $rules = [
            'APP_NAME' => 'required|string',
            'APP_ENV' => 'required|in:local,testing,production',
            'APP_KEY' => 'required|string',
            'DB_CONNECTION' => 'required|string',
            'DB_HOST' => 'required|string',
            'DB_PORT' => 'required|numeric',
            'DB_DATABASE' => 'required|string',
            'DB_USERNAME' => 'required|string',
            'DB_PASSWORD' => 'required|string',
            'REDIS_HOST' => 'required|string',
            'REDIS_PORT' => 'required|numeric',
            'SENTRY_LARAVEL_DSN' => 'nullable|url',
            'GOOGLE_CALENDAR_CREDENTIALS_PATH' => 'nullable|string',
            'GOOGLE_CALENDAR_TOKEN_PATH' => 'nullable|string',
        ];

        try {
            foreach ($rules as $key => $rule) {
                Validator::make([$key => env($key)], [$key => $rule])->validate();
            }
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    \Log::error("Environment variable validation error for {$field}: {$message}");
                }
            }
            throw $e;
        }
    }
}
