<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try{
            return response()->json([
                'Produit' => Produit::with('categorie')->get(),
                'status' => 200
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération des produits',
                'error' => $e->getMessage(),
                'satus' => 500
            ], );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
           // Validation des données d'entrée
            $validatedData = $request->validate([
                'nom' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string', 'max:255'],
                'img' => ['required', 'image', 'mimes:jpg,jpeg,png'], // Validation de l'image
                'prix' => ['required', 'numeric'],
                'qteStock' => ['required', 'integer'],
                'categorie_id' => ['required', 'integer'],
            ]);

            // Traiter l'image si elle est présentes
            $imagePath = null;
            if ($request->hasFile('img')) {
                // Stocker l'image dans un dossier public
                $imagePath = $request->file('img')->store('images', 'public');
            }
            $Produit = new Produit();
            $Produit->nom = $validatedData['nom'];
            $Produit->description = $validatedData['description'];
            $Produit->img = $imagePath; // Attribuer le chemin de l'image
            $Produit->prix = $validatedData['prix'];
            $Produit->qteStock = $validatedData['qteStock'];
            $Produit->categorie_id = $validatedData['categorie_id'];
            $Produit->save();
            return response()->json([
                'message' => 'Produit créée avec succès',
                'Produit' => $Produit,
                'status' => 200
            ]);
        }catch (\Exception $e) {
            // En cas d'erreur, renvoyez une réponse JSON avec un message d'erreur
            return response()->json([
                'message' => 'Échec de la création du Produit',
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
                'Produit' => Produit::with('categorie')->findOrFail($id),
                'message' => 'Produit récupéré avec succès',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Produit pas été trouvé',
                'error' => $e->getMessage(),
                'status' => 404
            ], 404);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        try {
            // Validation des données d'entrée
            $validatedData = $request->validate([
                'nom' => 'string|max:255',
                'description' => 'string|max:255',
                'img' => 'image|mimes:jpg,jpeg,png', // Validation de l'image
                'prix' => 'numeric',
                'qteStock' => 'integer',
                'categorie_id' => 'integer',
            ]);
        // Trouver le produit
        $produit = Produit::findOrFail($id);
        // Traiter l'image si elle est présente
        if ($request->hasFile('img')) {
            $imagePath = $request->file('img')->store('images', 'public');
            $produit->img = $imagePath; // Mettre à jour le chemin de l'image
        }
        // Mettre à jour les autres champs
        $produit->update($validatedData);

        return response()->json([
            'message' => 'Produit mis à jour avec succès',
            'Produit' => $produit,
            'status' => 200
        ]);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Échec de la mise à jour du produit',
                'error' => $e->getMessage(),
                'status' => 500
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        try {
            $categorie = Produit::findOrFail($id);
            $categorie->delete();
            return response()->json([
                'message' => 'Produit supprimé avec succès',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression du Produit',
                'error' => $e->getMessage(),
                'status' => 500
            ],);
        }
    }
}
