<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TypeFactureController;
use App\Http\Controllers\FactureController;




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


Route::middleware(['auth','auth:api'])->group(function () {
    //Categorie
    Route::get('/liste/categorie', [CategorieController::class ,'index']);
    Route::get('/show/categorie/{id}', [CategorieController::class ,'show']);
    //Produits
    Route::get('/liste/produit', [ProduitController::class ,'index']);
    Route::get('/show/produit/{id}', [ProduitController::class ,'show']);
    //Client
    Route::get('/liste/client', [ClientController::class ,'index']);
    Route::get('/show/client/{client}', [ClientController::class ,'show']);
    Route::post('/ajout/client', [ClientController::class ,'store'])->middleware('auth:api');
    Route::post('/update/client/{id}', [ClientController::class ,'update'])->middleware('auth:api');
    Route::post('/destroy/client/{id}', [ClientController::class ,'destroy'])->middleware('auth:api');
    //TypeFacture
    Route::get('/liste/typeFacture', [TypeFactureController::class ,'index']);
    Route::get('/show/typeFacture/{id}', [TypeFactureController::class ,'show']);
    //GEstion Facture
    Route::post('/ajout/facture', [FactureController::class ,'store']);
    //ajout fature 2
    Route::post('/ajouts/facture', [FactureController::class ,'stores']);
});

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
    //TypeFacture
    Route::post('/ajout/typeFacture', [TypeFactureController::class ,'store']);
    Route::post('/update/typeFacture/{id}', [TypeFactureController::class ,'update']);
    Route::post('/destroy/typeFacture/{id}', [TypeFactureController::class ,'destroy']);

});

Route::middleware(['auth', 'role:vendeur','auth:api'])->group(function () {
    //Role vendeur

});