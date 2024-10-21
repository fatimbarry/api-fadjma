<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\Sanctum;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $users = User::simplePaginate(2);
            return response()->json(['success' => true, 'data' => $users]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'sexe' => 'required|in:Homme,Femme',
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'date_de_naissance' => 'required|date',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role_id' => 'required|exists:roles,id',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        // Préparation des données à insérer
        $data = $request->only([
            'sexe', 'prenom', 'nom', 'date_de_naissance', 'email', 'password'
        ]);
        $data['password'] = Hash::make($data['password']);

        // Gestion de l'upload de l'image
        if ($request->hasFile('image_path')) {
            $photoName = time() . '.' . $request->image_path->extension();
            $photoPath = $request->file('image_path')->storeAs('photosUsers', $photoName, 'public');
            $data['image_path'] = $photoPath;
        }

        // Création de l'utilisateur
        $user = User::create($data);

        // Attribution du rôle
        $role = Role::find($request->role_id);
        $user->roles()->attach($role->id);

        // Retourner l'utilisateur avec ses rôles
        return response()->json([
            'success' => true,
            'data' => $user->load('roles') // Charger les rôles associés à l'utilisateur
        ], 201);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
{
    try {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $user]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
    }
}


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found'], 404);
    }

    try {
        $validator = Validator::make($request->all(), [
            'sexe' => 'required|in:Homme,Femme',
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'date_de_naissance' => 'required|date',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => ['required', 'confirmed', Password::defaults()],
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $user->sexe = $request->input('sexe');
        $user->prenom = $request->input('prenom');
        $user->nom = $request->input('nom');
        $user->date_de_naissance = $request->input('date_de_naissance');
        $user->email = $request->input('email');

        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        $role = Role::find($request->input('role_id'));
        $user->roles()->sync([$role->id]);

        return response()->json(['success' => true, 'data' => $user]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found'], 404);
            }

            $user->delete();
            $user->roles()->detach();

            return response()->json(['success' => true, 'message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            // Log the exception to get more details
            Log::error('Error deleting user: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to delete user'], 500);
        }
    }

    public function getUser(Request $request) {
        $user = $request->user();
        $role = $user->roles()->first();

        return response()->json([
            'prenom' => $user->prenom,
            'nom' => $user->nom,
            'image_path' => $user->image_path,
            'role' => $role->name
//            'role' => $role ? $role->name : 'Aucun rôle'
        ]);
    }
}
