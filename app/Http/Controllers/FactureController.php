<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use Illuminate\Http\Request;
use App\Models\FactureProduits;
use App\Models\Produit;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;


class FactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validation des données d'entrée
            $validatedData = $request->validate([
                'client_id' => ['required', 'integer'],
                'statut' => ['required', 'string'],
                'type_facture_id' => ['required', 'integer'],
                'produits' => ['required', 'array'],
                'produits.*.produit_id' => ['required', 'integer'],
                'produits.*.quantite' => ['required', 'integer'],
            ]);
    
            // Initialisation du total de la facture
            $total = 0;
    
            // Création de la facture
            $facture = Facture::create([
                'user_id' => Auth::id(),
                'client_id' => $validatedData['client_id'],
                'type_facture_id' => $validatedData['type_facture_id'],
                'total' => 0, // Temporairement à 0, sera mis à jour après
                'date' => now(),
                'statut' => $validatedData['statut'],
            ]);
    
            // Parcours des produits pour les ajouter à la facture et calculer le total
            foreach ($validatedData['produits'] as $produitData) {
                $produit = Produit::find($produitData['produit_id']);
                $prixTotal = $produit->prix * $produitData['quantite'];
    
                // Ajout du produit à la facture via la table pivot
                FactureProduits::create([
                    'facture_id' => $facture->id,
                    'produit_id' => $produit->id,
                    'quantite' => $produitData['quantite'],
                    'prix_total' => $prixTotal,
                ]);
    
                // Mise à jour du stock du produit
                $produit->qteStock -= $produitData['quantite'];
                $produit->save();
    
                // Ajout du prix total de ce produit au total de la facture
                $total += $prixTotal;
            }
    
            // Mise à jour du total de la facture
            $facture->total = $total;
            $facture->save();
    
            return response()->json([
                'message' => 'Facture créée avec succès',
                'facture' => $facture,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de la facture',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }


    /**
     *2eme method pour genre une fature
     */
    public function stores(Request $request)
    {
        try {
            // Validation des données d'entrée
            $validatedData = $request->validate([
                'client.nom' => ['required', 'string'],
                'client.prenom' => ['required', 'string'],
                'client.email' => ['required', 'email'],
                'client.telephome_un' => ['required', 'string'],
                'statut' => ['required', 'string'],
                'type_facture_id' => ['required', 'integer'],
                'produits' => ['required', 'array'],
                'produits.*.produit_id' => ['required', 'integer'],
                'produits.*.quantite' => ['required', 'integer'],
            ]);

            // Création du client s'il n'existe pas déjà
            $client = Client::create([
                'nom' => $validatedData['client']['nom'],
                'prenom' => $validatedData['client']['prenom'],
                'email' => $validatedData['client']['email'],
                'telephome_un' => $validatedData['client']['telephome_un'],
                'adresse' => $request->input('client.adresse'), // Optionnel
            ]);

            // Initialisation du total de la facture
            $total = 0;

            // Création de la facture
            $facture = Facture::create([
                'user_id' => Auth::id(),
                'client_id' => $client->id, // Lien avec le client créé
                'type_facture_id' => $validatedData['type_facture_id'],
                'total' => 0, // Temporairement à 0, sera mis à jour après
                'date' => now(),
                'statut' => $validatedData['statut'],
            ]);

            // Parcours des produits pour les ajouter à la facture et calculer le total
            foreach ($validatedData['produits'] as $produitData) {
                $produit = Produit::find($produitData['produit_id']);
                $prixTotal = $produit->prix * $produitData['quantite'];

                // Ajout du produit à la facture via la table pivot
                FactureProduits::create([
                    'facture_id' => $facture->id,
                    'produit_id' => $produit->id,
                    'quantite' => $produitData['quantite'],
                    'prix_total' => $prixTotal,
                ]);

                // Mise à jour du stock du produit
                $produit->qteStock -= $produitData['quantite'];
                $produit->save();

                // Ajout du prix total de ce produit au total de la facture
                $total += $prixTotal;
            }

            // Mise à jour du total de la facture
            $facture->total = $total;
            $facture->save();

            return response()->json([
                'message' => 'Facture et client créés avec succès',
                'facture' => $facture,
                'client' => $client,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création de la facture ou du client',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Pour chaque client j'affiche la liste de
     *  toutes ses factures ainsi que le details pour chque factures 
     */
    public function getFacturesByClient($clientId)
    {
        try {
            // Récupérer le client avec ses factures et les détails des produits de chaque facture
            $factures = Facture::where('client_id', $clientId)
                ->with(['produits' => function ($query) {
                    $query->select('produits.id', 'produits.nom', 'facture_produits.quantite', 'facture_produits.prix_total');
                }])
                ->get();
    
            if ($factures->isEmpty()) {
                return response()->json([
                    'message' => 'Aucune facture trouvée pour ce client',
                    'status' => 404
                ], 404);
            }
    
            return response()->json([
                'message' => 'Récupération des factures pour chaque client reussi',
                'factures' => $factures,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des factures',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
    


    
    
    
    /**
     * Display the specified resource.
     */
    public function show(Facture $facture)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Facture $facture)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Facture $facture)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facture $facture)
    {
        //
    }
}
