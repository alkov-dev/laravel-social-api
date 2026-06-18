<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\File;

Route::get('/test', function () {
    return response()->json(['message' => 'API работает!']);
});

// Публичные маршруты
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Защищённые маршруты
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});


Route::get('/docs-json', function () {
    $path = storage_path('api-docs/api-docs.json');

    if (!File::exists($path)) {
        return response()->json([
            'error' => 'Documentation not found. Run: php artisan l5-swagger:generate'
        ], 404);
    }

    return response()->stream(function () use ($path) {
        echo File::get($path);
    }, 200, [
        'Content-Type' => 'application/json',
    ]);
});
