<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\k8sAPIController;


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
Route::get('/status',[k8sAPIController::class,'getK8sStatus']);
Route::get('/statusstream',[k8sAPIController::class,'getK8sStatusStream']);

Route::post('/delete',[k8sAPIController::class,'deleteGsPvc']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
