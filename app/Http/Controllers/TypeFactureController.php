<?php

namespace App\Http\Controllers;

use App\Models\TypeFacture;
use Illuminate\Http\Request;

class TypeFactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {            
            return response()->json([
                'TypeFacture' => TypeFacture::all(),
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération d\'un TypeFacture',
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
            $TypeFacture = new TypeFacture();
            $TypeFacture->nom = $validatedData['nom'];
            $TypeFacture->save();
            return response()->json([
                'message' => 'TypeFacture créée avec succès',
                'TypeFacture' => $TypeFacture,
                'status' => 200
            ]);
        }catch (\Exception $e) {
            // En cas d'erreur, renvoyez une réponse JSON avec un message d'erreur
            return response()->json([
                'message' => 'Échec de la création d\'un TypeFacture',
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
                'TypeFacture' => TypeFacture::find($id),
                'message' => 'TypeFacture recuperer',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'La TypeFacture non trouvée',
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
    
            $TypeFacture = TypeFacture::findOrFail($id);
            $TypeFacture->update($validatedData);
    
            return response()->json([
                'message' => 'TypeFacture mise à jour avec succès',
                'TypeFacture' => $TypeFacture,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour d\'une TypeFacture',
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
            $TypeFacture = TypeFacture::findOrFail($id);
            $TypeFacture->delete();
            return response()->json([
                'message' => 'TypeFacture supprimée avec succès',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression d\'un TypeFacture',
                'error' => $e->getMessage(),
                'status' => 500
            ],);
        }
    }
}
