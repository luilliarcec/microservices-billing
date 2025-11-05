<?php

namespace App\Observability\Middleware;

use App\Observability\Enums\Headers;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;

class AddCorrelationId
{
    public function handle(Request $request, Closure $next)
    {
        $id = $request->initCorrelation();

        $response = $next($request);

        $response->withCorrelation($id);

        return $response;
    }
}
