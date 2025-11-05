<?php

namespace App\Observability\Middleware;

use App\Observability\Enums\Keys;
use App\Services\Prometheus;
use Closure;
use Illuminate\Http\Request;

class PrometheusMetrics
{
    public function __construct(protected Prometheus $prometheus)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $start;

        $correlation = context()->get(Keys::CorrelationId->value);

        $this
            ->prometheus
            ->count('http_requests_total', 'Total number of HTTP requests', [
                'method',
                'path',
                'status',
                'correlation_id'
            ])
            ->inc([
                $request->method(),
                $request->path(),
                $response->getStatusCode(),
                $correlation,
            ]);

        $this
            ->prometheus
            ->gauge('memory_usage_bytes', 'Memory usage in bytes', [
                'correlation_id',
            ])
            ->set(memory_get_usage(true), [
                $correlation,
            ]);

        $this
            ->prometheus
            ->histogram(
                'http_request_duration_seconds',
                'HTTP request duration in seconds',
                ['method', 'path', 'correlation_id'],
                [0.005, 0.01, 0.025, 0.05, 0.075, 0.1, 0.25, 0.5, 0.75, 1.0, 2.5, 5.0, 7.5, 10.0]
            )
            ->observe($duration, [
                $request->method(),
                $request->path(),
                $correlation,
            ]);

        return $response;
    }
}
