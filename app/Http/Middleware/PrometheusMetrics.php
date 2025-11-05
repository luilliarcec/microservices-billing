<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;
use Spatie\Prometheus\Facades\Prometheus;

class PrometheusMetrics
{
    private $registry;

    public function __construct()
    {
        // Usar Redis para almacenar métricas (necesitas tener Redis)
        // O puedes usar InMemory para desarrollo: new \Prometheus\Storage\InMemory()
        $adapter = new Redis(['host' => config('database.redis.default.host')]);
        $this->registry = new CollectorRegistry($adapter);
    }

    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $start;

        Prometheus::addCounter('http_requests_total')
            ->helpText('Total HTTP requests')
            ->labels(['method', 'route', 'status'])
            ->inc([
                $request->method(),
                $request->route() ? $request->route()->getName() ?? $request->path() : 'unknown',
                $response->getStatusCode()
            ]);

        Prometheus::addGauge('memory_usage_bytes')
            ->helpText('Memory usage in bytes')
            ->value(fn () => memory_get_usage(true));

        // Histograma de duración
        $histogram = $this->registry->getOrRegisterHistogram(
            'laravel',
            'http_request_duration_seconds',
            'HTTP request duration in seconds',
            ['method', 'route'],
            [0.005, 0.01, 0.025, 0.05, 0.075, 0.1, 0.25, 0.5, 0.75, 1.0, 2.5, 5.0, 7.5, 10.0]
        );

        $histogram->observe($duration, [
            $request->method(),
            $request->route() ? $request->route()->getName() ?? $request->path() : 'unknown'
        ]);

        return $response;
    }
}
