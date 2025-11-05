<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;

class PrometheusServiceProvider extends ServiceProvider
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
}
