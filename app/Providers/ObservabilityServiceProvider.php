<?php

namespace App\Providers;

use App\Observability\Enums\Headers;
use App\Observability\Enums\Keys;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ObservabilityServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Log de queries lentas
        DB::listen(static function ($query) {
            if ($query->time > 1000) { // más de 1 segundo
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time_ms' => $query->time,
                    'type' => 'slow_query',
                ]);
            }
        });

        // Log todas las queries en desarrollo
        if (config('app.debug')) {
            DB::listen(static function ($query) {
                Log::debug('Query executed', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time_ms' => $query->time,
                ]);
            });
        }

        Request::macro('initCorrelation', function () {
            $id = $this->header(Headers::CorrelationId->value) ?? Str::uuid()->toString();

            Context::add(Keys::CorrelationId->value, $id);

            $this->headers->set(Headers::CorrelationId->value, $id);

            return $id;
        });

        Response::macro('withCorrelation', function ($id) {
            $this->headers->set(Headers::CorrelationId->value, $id);
        });
    }
}
