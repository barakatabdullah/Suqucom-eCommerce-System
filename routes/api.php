<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');


Route::post('/register', [AuthController::class, 'create']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {
     Route::get('/user', function (Request $request) {
         return $request->user();
     });

     Route::get('/categories', [CategoriesController::class, 'getAll']);
     Route::post('/categories', [CategoriesController::class, 'create']);

});
