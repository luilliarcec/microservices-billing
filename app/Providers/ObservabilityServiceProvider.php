<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ObservabilityServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Log de queries lentas
        DB::listen(function ($query) {
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
            DB::listen(function ($query) {
                Log::debug('Query executed', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time_ms' => $query->time,
                ]);
            });
        }
    }

    public function register()
    {
        //
    }
}
