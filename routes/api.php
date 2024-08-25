<?php

use App\Http\Controllers\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\PermissionsController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

Route::post('/register', [AuthController::class, 'create']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot', [ResetPasswordController::class, 'forgot']);
Route::post('/reset', [ResetPasswordController::class, 'reset']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/users', [UsersController::class, 'getAll']);
    Route::get('/users/{id}', [UsersController::class, 'getOne']);
    Route::post('/users', [UsersController::class, 'create']);
    Route::put('/users/{id}', [UsersController::class, 'update']);
    Route::delete('/users/{id}', [UsersController::class, 'delete']);

    Route::post('/images', [ImagesController::class, 'upload']);
    Route::get('/permissions', [PermissionsController::class, 'getAllPermissions']);
    Route::get('/roles', [PermissionsController::class, 'getAllRoles']);
    Route::get('/categories', [CategoriesController::class, 'getAll']);
    Route::post('/categories', [CategoriesController::class, 'create']);

    Route::get('/products', [ProductsController::class, 'getAll']);
    Route::post('/products', [ProductsController::class, 'create']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
