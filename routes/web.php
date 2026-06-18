<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;

Route::inertia('/', 'welcome')->name('home');


Route::resource('posts', PostController::class);







