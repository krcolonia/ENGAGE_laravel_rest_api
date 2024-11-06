<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('user', UserController::class);

Route::controller(UserController::class)->group(function() 
{
	Route::post('/register', 'register');
	Route::post('/login', 'login');

	Route::middleware('auth:sanctum')->group(function()
	{
		Route::post('/logout', 'logout');
		Route::get('/user', function (Request $request) {
			return $request->user();
		});
	});
});

