<?php

namespace App\Observability\Enums;

enum Headers: string
{
    case CorrelationId = 'X-Correlation-Id';
}
