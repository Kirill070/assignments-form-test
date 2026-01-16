<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::resource('users', UserController::class)->only(['create', 'store']);
Route::get('/', fn () => redirect()->route('users.create'));
