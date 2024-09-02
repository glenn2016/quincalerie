<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ProduitController;


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


//Categorie
Route::get('/liste/categorie', [CategorieController::class ,'index']);
Route::get('/show/categorie/{id}', [CategorieController::class ,'show']);

//Produits
Route::get('/liste/produit', [ProduitController::class ,'index']);
Route::get('/show/produit/{id}', [ProduitController::class ,'show']);



Route::middleware(['auth', 'role:admin','auth:api'])->group(function () {
    //vendeur
    Route::post('/creation/vendeur',[AuthController::class,'create']);
    Route::get('/liste/vendeur',[Authcontroller::class,'listeVendeurr']);
    Route::get('/show/utilisateur/{id}',[Authcontroller::class,'show']);
    Route::post('/vendeur/{id}/bloquer',[Authcontroller::class,'bloquer']);
    Route::post('/vendeur/{id}/debloquer',[Authcontroller::class,'debloquer']);
    Route::get('/liste/vendeur/bloquer',[Authcontroller::class,'listeVendeurBolquer']);
    //Categorie
    Route::post('/ajout/categorie', [CategorieController::class ,'store']);
    Route::post('/update/categorie/{id}', [CategorieController::class ,'update']);
    Route::post('/destroy/categorie/{id}', [CategorieController::class ,'destroy']);
    //Produits
    Route::post('/ajout/produit', [ProduitController::class ,'store']);
    Route::post('/update/produit/{id}', [ProduitController::class ,'update']);
    Route::post('/destroy/produit/{id}', [ProduitController::class ,'destroy']);


});

Route::middleware(['auth', 'role:admin','auth:api'])->group(function () {

});