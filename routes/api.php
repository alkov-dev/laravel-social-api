<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UploadController;

Route::get('/test', function () {
    return response()->json(['message' => 'API работает!']);
});

// Публичные маршруты
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Защищённые маршруты
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Посты
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    // Лайки
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle']);
    Route::get('/posts/{post}/likes', [LikeController::class, 'users']);

    // Комментарии
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // Профиль
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::get('/users/{user}', [ProfileController::class, 'showUser']);

    // Загрузка файлов
    Route::post('/upload/image', [UploadController::class, 'image']);
    Route::delete('/upload', [UploadController::class, 'destroy']);
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
