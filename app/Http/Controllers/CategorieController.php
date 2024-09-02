<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;


class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {            
            return response()->json([
                'categories' => Categorie::all(),
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération des catégories',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $validatedData = $request->validate([
                'nom' => ['required', 'string', 'max:255'],
            ]);
            $categorie = new Categorie();
            $categorie->nom = $validatedData['nom'];
            $categorie->save();
            return response()->json([
                'message' => 'Catégorie créée avec succès',
                'categorie' => $categorie,
                'status' => 200
            ]);
        }catch (\Exception $e) {
            // En cas d'erreur, renvoyez une réponse JSON avec un message d'erreur
            return response()->json([
                'message' => 'Échec de la création de la catégorie',
                'error' => $e->getMessage(),
                'status'=>500
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        try {
            return response()->json([
                'Categorie' => Categorie::find($id),
                'message' => 'Categorie recuperer',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'La catégorie non trouvée',
                'error' => $e->getMessage(),
                'status' => 404
            ],);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        try {
            $validatedData = $request->validate([
                'nom' => ['required', 'string', 'max:255'],
            ]);
    
            $categorie = Categorie::findOrFail($id);
            $categorie->update($validatedData);
    
            return response()->json([
                'message' => 'Catégorie mise à jour avec succès',
                'categorie' => $categorie,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour de la catégorie',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        try {
            $categorie = Categorie::findOrFail($id);
            $categorie->delete();
            return response()->json([
                'message' => 'Catégorie supprimée avec succès',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression de la catégorie',
                'error' => $e->getMessage(),
                'status' => 500
            ],);
        }
    }
}