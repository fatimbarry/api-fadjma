<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        try {
            // Valider les données d'entrée
            $data = $request->validate([
            'sexe' => 'required|in:Homme,Femme',
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'date_de_naissance' => 'required|date',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role_id' => 'required|exists:roles,id',
            ]);

            $user = new User();
            $user->sexe = $data['sexe'];
            $user->prenom = $data['prenom'];
            $user->nom = $data['nom'];
            $user->date_de_naissance = $data['date_de_naissance'];
            $user->email = $data['email'];
            $user->password = Hash::make($data['password']);

            $user->save();
            $user->roles()->attach($data['role_id']);

            return response()->json(['message' => 'User registered successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registration failed: ' . $e->getMessage()], 400);
        }
    }

    public function login()
    {
        return view('auth.login');
    }

    public function logout(Request $request)
    {
        // Supprimer le token d'accès actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'You have successfully logged out!']);
    }

    public function store(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            // Attempt to authenticate the user
            if (Auth::attempt($credentials)) {
                // Get the authenticated user
                $user = Auth::user();

                // Create an API token for the user
                $token = $user->createToken('token-name', ['*'])->plainTextToken;

                // Return JSON response
                return response()->json([
                    'message' => 'You are logged in.',
                    'user' => $user,
                    'token' => $token,
                    'tokenExpiry' => now()->addMinutes(60)->format('Y-m-d H:i:s'),
                ]);
            }

            // Authentication failed
            return response()->json([
                'message' => 'Credentials do not match our records.',
            ], 401);
        } catch (\Exception $e) {
            // Handle exceptions here
            return response()->json([
                'message' => 'Error during login: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Le mot de passe actuel est incorrect'], 401);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Mot de passe changé avec succès']);
    }
}
