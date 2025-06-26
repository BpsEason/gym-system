<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| This file is intended for API routes accessible via your backend.
| For module-specific API routes, they are loaded via their respective service providers
| or explicitly grouped as shown below.
|
*/

# Authentication routes for API (Sanctum)
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

# Prometheus metrics endpoint
Route::get('/metrics', function (CollectorRegistry $registry) {
    $renderer = new RenderTextFormat();
    $result = $renderer->render($registry->getMetricFamilySamples());
    return response($result, 200)->header('Content-Type', RenderTextFormat::MIME_TYPE);
});

# Load module-specific API routes
Route::prefix('membership')->group(base_path('app/Modules/Membership/Routes/api.php'));
Route::prefix('trainer')->group(base_path('app/Modules/Trainer/Routes/api.php'));
Route::prefix('course')->group(base_path('app/Modules/Course/Routes/api.php'));
