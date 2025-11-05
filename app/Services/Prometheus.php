<?php

namespace App\Services;

use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use Prometheus\Gauge;
use Prometheus\Histogram;
use Prometheus\Summary;

class Prometheus
{
    public const string NAMESPACE = 'api_billing_service';

    public function __construct(protected CollectorRegistry $registry)
    {
    }

    public function count(string $name, string $help, array $labels = []): Counter
    {
        return $this->registry
            ->getOrRegisterCounter(self::NAMESPACE, $name, $help, $labels);
    }

    public function gauge(string $name, string $help, array $labels = []): Gauge
    {
        return $this->registry
            ->getOrRegisterGauge(self::NAMESPACE, $name, $help, $labels);
    }

    public function summary(string $name, string $help, array $labels = [], int $maxAgeSeconds = 600, ?array $quantiles = null): Summary
    {
        return $this->registry
            ->getOrRegisterSummary(self::NAMESPACE, $name, $help, $labels, $maxAgeSeconds, $quantiles);
    }

    public function histogram(string $name, string $help, array $labels = [], ?array $buckets = null): Histogram
    {
        return $this->registry
            ->getOrRegisterHistogram(self::NAMESPACE, $name, $help, $labels, $buckets);
    }
}
