<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeValueController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PermissionsController;


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

    Route::get('/permissions', [PermissionsController::class, 'getAllPermissions']);
    Route::get('/roles', [PermissionsController::class, 'getAllRoles']);
    Route::post('/roles', [PermissionsController::class, 'createRoleWithPermissions']);
    Route::get('/roles/{role_id}', [PermissionsController::class, 'getRole']);
    Route::post('/roles/{role_id}', [PermissionsController::class, 'updateRoleWithPermissions']);
    Route::delete('/roles/{role_id}', [PermissionsController::class, 'deleteRole']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::controller(CategoriesController::class)->group(function () {
    Route::get('/categories', 'getAll');
    Route::post('/categories', 'create');
    Route::get('/categories/{id}', 'getCategory');
    Route::post('/categories/{id}', 'update');
    Route::delete('/categories/{id}', 'delete');
});

Route::controller(AttributeController::class)->group(function () {
    Route::get('/attributes', 'getAll');
    Route::post('/attributes', 'create');
    Route::get('/attributes/{id}', 'getOne');
    Route::post('/attributes/{id}', 'update');
    Route::delete('/attributes/{id}', 'delete');
});

Route::controller(AttributeValueController::class)->group(function () {
    Route::post('/attribute-values', 'create');
    Route::post('/attribute-values/{id}', 'update');
    Route::delete('/attribute-values/{id}', 'delete');
});

Route::controller(ColorController::class)->group(function () {
    Route::get('/colors', 'getColors');
    Route::post('/colors', 'create');
    Route::get('/colors/{id}', 'getColor');
    Route::post('/colors/{id}', 'update');
    Route::delete('/colors/{id}', 'delete');
});

Route::controller(BrandController::class)->group(function () {
    Route::get('/brands', 'getBrands');
    Route::post('/brands', 'create');
    Route::get('/brands/{id}', 'getBrand');
    Route::post('/brands/{id}', 'update');
    Route::delete('/brands/{id}', 'delete');
});

Route::controller(ProductsController::class)->group(function () {
    Route::get('/products', 'getProducts');
    Route::get('/products/{id}', 'getProduct');
    Route::post('/products', 'create');
    Route::post('/products/{id}', 'update');
    Route::delete('/products/{id}', 'delete');
});
