<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;

class ObservabilityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CollectorRegistry::class, function () {
            return new CollectorRegistry(new Redis([
                'host' => config('database.redis.default.host'),
                'port' => config('database.redis.default.port'),
                'timeout' => 0.1,
                'read_timeout' => 10,
                'persistent_connections' => false,
            ]));
        });
    }

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
    }
}
