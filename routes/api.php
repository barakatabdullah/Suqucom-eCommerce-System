<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeValueController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\OrderController;
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

Route::middleware('auth:admin')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });


});


Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'create');
    Route::post('/forgot', 'forgot');
    Route::post('/reset', 'reset');
    Route::post('/logout', 'logout');

});

Route::group(['prefix' => 'admin'], function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'adminLogin');
    });
});


Route::controller(UsersController::class)->group(function () {
    Route::get('/users', 'getAll');
    Route::get('/users/{id}', 'getOne');
    Route::post('/users', 'create');
    Route::put('/users/{id}', 'update');
    Route::delete('/users/{id}', 'delete');
});

Route::controller(PermissionsController::class)->group(function () {
    Route::get('/permissions', 'getAllPermissions');
    Route::get('/roles', 'getAllRoles');
    Route::post('/roles', 'createRoleWithPermissions');
    Route::get('/roles/{role_id}', 'getRole');
    Route::post('/roles/{role_id}', 'updateRoleWithPermissions');
    Route::delete('/roles/{role_id}', 'deleteRole');
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

Route::controller(OrderController::class)->group(function () {
    Route::get('/orders', 'getAll');
    Route::get('/orders/{id}', 'getOrder');
    Route::post('/orders', 'create');
    Route::put('/orders/{id}', 'update');
    Route::delete('/orders/{id}', 'delete');
});
