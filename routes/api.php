<?php

use App\Http\Controllers\CropController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserControllerLogin;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [UserControllerLogin::class, 'login']);

Route::post('register', [UserControllerLogin::class, 'register']);

Route::post('validateEmail', [UserControllerLogin::class, 'validateEmail']);

Route::post('sendCode', [UserControllerLogin::class, 'sendCode']);

Route::post('updatePassword', [UserControllerLogin::class, 'updatePassword']);

Route::post('addCrop', [CropController::class, 'addCrop']);

Route::post('getCrops', [CropController::class, 'getCrops']);

Route::post('getCrop', [CropController::class, 'getCrop']);

Route::post('getMeasuresWaterContainer', [CropController::class, 'getMeasuresWaterContainer']);

Route::post('setMeasuresWaterContainer', [CropController::class, 'setMeasuresWaterContainer']);