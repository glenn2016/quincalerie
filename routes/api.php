<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


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


Route::post('login', [AuthController::class ,'login']);
Route::post('logout', [AuthController::class ,'logout']);
Route::post('refresh', [AuthController::class ,'refresh']);
Route::post('me', [AuthController::class ,'me']);



Route::middleware(['auth', 'role:admin','auth:api'])->group(function () {
    //vendeur
    Route::post('/creation/vendeur',[AuthController::class,'create']);
    Route::post('/vendeur/{id}/bloquer',[Authcontroller::class,'bloquer']);
    Route::post('/vendeur/{id}/debloquer',[Authcontroller::class,'debloquer']);
    Route::get('/liste/vendeur/bloquer',[Authcontroller::class,'listeVendeurBolquer']);

});

Route::middleware(['auth', 'role:admin'])->group(function () {

});