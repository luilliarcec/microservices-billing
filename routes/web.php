<?php

use App\Http\Controllers\PrometheusController;
use Illuminate\Support\Facades\Route;

Route::get('/metrics', PrometheusController::class);
