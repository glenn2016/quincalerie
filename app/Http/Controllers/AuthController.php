<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Role;
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
            return response()->json(
                [
                    'error' => 'Une erreur s\'est produite l\'ors de la déconnexion',
                    'message'=> $e->getMessage()
                ]
            );
        }
    }
}
