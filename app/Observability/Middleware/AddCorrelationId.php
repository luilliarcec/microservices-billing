<?php

namespace App\Observability\Middleware;

use App\Observability\Enums\Headers;
use App\Observability\Enums\Keys;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AddCorrelationId
{
    public function handle(Request $request, Closure $next)
    {
        $id = $this->init($request);

        $response = $next($request);

        $response->headers->set(Headers::CorrelationId->value, $id);

        return $response;
    }

    protected function init(Request $request): string
    {
        $id = $request->header(Headers::CorrelationId->value) ?? Str::uuid()->toString();

        context()->add(Keys::CorrelationId->value, $id);

        $request->headers->set(Headers::CorrelationId->value, $id);

        return $id;
    }
}
