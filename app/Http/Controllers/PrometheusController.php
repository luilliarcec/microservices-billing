<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;

class PrometheusController
{
    public function __invoke(CollectorRegistry $registry): Response
    {
        $renderer = new RenderTextFormat();

        return response($renderer->render($registry->getMetricFamilySamples()))
            ->header('Content-Type', RenderTextFormat::MIME_TYPE);
    }
}
