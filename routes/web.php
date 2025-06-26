<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
| For an API-only backend, this file will be minimal.
|
*/

# Default root route can show a simple message for the backend API
Route::get('/', function () {
    return 'Welcome to the Gym Management System Backend API!';
});

# Health check endpoint (for external monitoring if not using /metrics)
# Route::get('/up', function () {
#     return response('OK', 200);
# });
