<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthController extends Controller
{
    //

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     * 
     */

    public function login()
    {
        try {
            $credentials = request(['email', 'password']);
            // Tentative d'authentification avec les informations d'identification fournies
            if (! $token = auth()->attempt($credentials)) {
                return response()->json(['error' => ' L\'e-mail ou le mot de passe fourni est incorrect'], 401);
            }
            $user = Auth::user();
            // Vérifier si l'utilisateur est bloqué
            if ($user->etat === 0) {
                return response()->json([
                    'error' => 'Votre compte est bloqué',
                    'status' => 402,
                ]);
            }
            // Récupérer les rôles de l'utilisateur
            $user = User::find(Auth::user()->id);
            $user_roles = $user->roles()->pluck('nom');
            // Retourner une réponse avec le token et les rôles de l'utilisateur
            return response()->json([
                'success' => true,
                'status' => 200,
                'roles' => $user_roles,
                'token' => $token,
            ]);
        } catch (JWTException $e) {
            // En cas d'échec de la génération du token
            return response()->json([
                'error' => 'Impossible de créer un token',
                'status' => 500,
            ]);
        } catch (\Exception $e) {
            // En cas de toute autre exception
            return response()->json([
                'error' => 'Une erreur s\'est produite',
                'message' => $e->getMessage(),
                'status' => 500,
            ]);
        }
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try{
            auth()->logout();
            return response()->json(
                [
                    'message' => 'Déconnexion réussie',
                    'status'=>200
            ]);
        }catch(\Exception $e){
             // En cas d'erreur, renvoyez une réponse JSON avec un message d'erreur
            return response()->json(
                [
                    'error' => 'Une erreur s\'est produite l\'ors de la déconnexion',
                    'message'=> $e->getMessage()
                ]
            );
        }
    }
    /**
     * listes vendeur
     */
    public function listeVendeurr(){
        try{
            // Récupérer les utilisateurs avec le rôle "Vendeur" et qui ne sont pas bloqués (etat = 1)
            $vendeurs = User::whereHas('roles', fn($query) => $query->where('nom', 'Vendeur'))
                ->where(['etat' => 1, 'usercreate' => Auth::id()])
                ->get();
            return response()->json([
                'Admins' => $vendeurs,
                'status' => 200
            ]);

        }catch(Exception $e){{
            // En cas d'exception, retourner une réponse JSON avec un message d'erreur
            return response()->json([
                'message' => 'Erreur lors de la récupération des administrateurs',
                'error' => $e->getMessage(),
                'status' => 500
            ], ); // 500 Internal Server Error

        }}
    }
    /**
     * detail d'un utilisateur
     */
    public function show($id){
        try {
            return response()->json([
                'Utilisateur' => User::find($id),
                'message' => 'Utilisateur recuperer',
                'status' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'La Utilisateur non trouvée',
                'error' => $e->getMessage(),
                'status' => 404
            ],);
        }

    }
    /** 
     * Creation Vendeur 
     */
    public function create(Request $request)
    {
        try {
            $userId = Auth::id(); // Récupérer l'ID de l'utilisateur connecté
            // Validation des données d'entrée
            $validations = Validator::make($request->all(), [
                'nom' => ['required', 'string', 'max:255'],
                'prenom' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string',  'email', 'max:255', 'unique:'.User::class],
                'password' => 'required|string|min:8',
                'telephone' => ['required', 'string', 'max:255'],
                'addresse' => ['required', 'string', 'max:255'],
                'img' => ['required', 'image', 'mimes:jpg,jpeg,png'], // Validation de l'image
            ]);
            // Si la validation échoue, retourner les erreurs
            if ($validations->fails()) {
                return response()->json([
                    'errors' => $validations->errors(),
                    'status' => 422 // Code d'état HTTP pour erreur de validation
                ]);
            }
            // Traiter l'image si elle est présente
            $imagePath = null;
            if ($request->hasFile('img')) {
                // Stocker l'image dans un dossier public
                $imagePath = $request->file('img')->store('images', 'public');
            }
            // Si la validation réussit, procéder à la création de l'utilisateur
            if ($validations->passes()) {
                // Créer l'utilisateur avec les données validées
                $user = User::create([
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'telephone' => $request->telephone,
                    'addresse' => $request->addresse,
                    'email' => strtolower($request->email),
                    'entreprise_id' => $request->entreprise_id, // Assurez-vous que ce champ est présent dans la table 'users'
                    'password' => Hash::make($request->password),
                    'usercreate' => $userId,
                    'img' => $imagePath // Sauvegarder le chemin de l'image dans la base de données
                ]);
                // Assigner le rôle spécifique à l'utilisateur (ex. ID 3 pour un rôle particulier)
                $user->roles()->attach(2);
                // Retourner une réponse JSON avec le token d'authentification
                return response()->json([
                    'type' => 'Bearer',
                    'message' => 'Utilisateur créé avec succès',
                    'status' => 200
                ], ); // 201 Created
            }
        } catch (\Exception $e) {
            // En cas d'exception, retourner une réponse JSON avec un message d'erreur
            return response()->json([
                'message' => 'Erreur lors de la création de l\'utilisateur',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }
    /**
     * Bloquer un utilisateur
     */
    public function bloquer($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->etat = 0;
            $user->save();
            return response()->json([
                'message' => 'Utilisateur bloqué avec succès',
                'User' => $user
            ], 200);
        } catch (\Exception $e) {
            // En cas d'erreur, renvoyez une réponse JSON avec un message d'erreur
            return response()->json([
                'message' => 'Erreur lors du blocage de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Debloquer un utilisateur
     */
    public function debloquer($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->etat = 1;
            $user->save();
            return response()->json([
                'message' => 'Utilisateur débloqué avec succès',
                'User' => $user,
                'status'=>200
            ],);
        } catch (\Exception $e) { 
            // En cas d'erreur, renvoyez une réponse JSON avec un message d'erreur
            return response()->json([
                'message' => 'Erreur lors du déblocage de l\'utilisateur',
                'error' => $e->getMessage(),
                'status'=>500
            ]);
        }
    }
    /** 
     * Liste Participants bloquer
     */
    public function listeVendeurBolquer()
    {
        try {
            $users = User::whereHas('roles', fn($query) => $query->where('nom', 'Vendeur'))
                ->where(['etat' => 0, 'usercreate' => Auth::id()])
                ->get();
            return response()->json([
                'Vendeur' => $users,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            // En cas d'erreur, renvoyez une réponse JSON avec un message d'erreur
            return response()->json([
                'message' => 'Erreur lors de la récupération des vendeurs bloqués',
                'error' => $e->getMessage(),
                'status'=>500
            ]);
        }
    }
}
