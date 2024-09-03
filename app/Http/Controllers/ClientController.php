<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {            
            return response()->json([
                'Client' => Client::all(),
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération d Client',
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
            $userId = Auth::id(); // Récupérer l'ID de l'utilisateur connecté
            $validatedData = $request->validate([
                'nom' => ['nullable', 'string', 'max:255'],
                'prenom' => ['nullable', 'string', 'max:255'],
                'nom_entreprise' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'max:255'],
                'telephome_un' => ['nullable', 'string', 'max:255'],
                'telephome_deux' => ['nullable', 'string', 'max:255'],
                'adresse' => ['nullable', 'string', 'max:255'],
            ]);
            $Client = new Client();
            $Client->nom = $validatedData['nom'];
            $Client->prenom = $validatedData['prenom'];
            $Client->email = $validatedData['email'];
            $Client->telephome_un = $validatedData['telephome_un'];
            $Client->telephome_deux = $validatedData['telephome_deux'];
            $Client->adresse = $validatedData['adresse'];
            $Client->usercreate =  $userId;
            $Client->save();

            return response()->json([
                'message' => 'Client créée avec succès',
                'Client' => $Client,
                'status' => 200
            ]);
        }catch (\Exception $e) {
            // En cas d'erreur, renvoyez une réponse JSON avec un message d'erreur
            return response()->json([
                'message' => 'Échec de la création de la Client',
                'error' => $e->getMessage(),
                'status'=>500
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
        try {
            return response()->json([
                'Client' => Client::find( $client),
                'message' => 'Client recuperer',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Le Client non trouvée',
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
            $userId = Auth::id(); // Récupérer l'ID de l'utilisateur connecté
            $validatedData = $request->validate([
                'nom' => ['nullable', 'string', 'max:255'],
                'prenom' => ['nullable', 'string', 'max:255'],
                'nom_entreprise' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'max:255'],
                'telephome_un' => ['nullable', 'string', 'max:255'],
                'telephome_deux' => ['nullable', 'string', 'max:255'],
                'adresse' => ['nullable', 'string', 'max:255'],
            ]);

            // Trouver le produit
            $Client = Client::findOrFail($id);

            // Mettre à jour les autres champs
            $Client->update($validatedData);

            return response()->json([
                'message' => 'Client mis à jour avec succès',
                'Client' => $Client,
                'status' => 200
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Échec de la mise à jour du Client',
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
            $Client = Client::findOrFail($id);
            $Client->delete();
            return response()->json([
                'message' => 'Client supprimée avec succès',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression de la Client',
                'error' => $e->getMessage(),
                'status' => 500
            ],);
        }
    }
}