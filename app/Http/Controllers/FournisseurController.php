<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FournisseurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fournisseurs = Fournisseur::with('medicaments')->get();
        return response()->json($fournisseurs);
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
        $validator = Validator::make($request->all(), [
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:fournisseurs',
            'telephone' => 'nullable|string|max:20',
            'medicaments' => 'required|array',
            'medicaments.*.id' => 'required|exists:medicaments,id',
            'medicaments.*.prix' => 'required|numeric|min:0',
            'medicaments.*.quantite' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();

        try {
            $fournisseur = Fournisseur::create($request->only('prenom', 'nom', 'email', 'telephone'));

            foreach ($request->medicaments as $medicament) {
                $fournisseur->medicaments()->attach($medicament['id'], [
                    'prix' => $medicament['prix'],
                    'quantite' => $medicament['quantite']
                ]);
            }

            DB::commit();

            return response()->json($fournisseur->load('medicaments'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de l\'ajout du fournisseur', 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $fournisseur = Fournisseur::with('medicaments')->findOrFail($id);
        return response()->json($fournisseur);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'prenom' => 'sometimes|required|string|max:255',
            'nom' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:fournisseurs,email,' . $id,
            'telephone' => 'nullable|string|max:20',
            'medicaments' => 'sometimes|required|array',
            'medicaments.*.id' => 'required_with:medicaments|exists:medicaments,id',
            'medicaments.*.prix' => 'required_with:medicaments|numeric|min:0',
            'medicaments.*.quantite' => 'required_with:medicaments|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();

        try {
            $fournisseur = Fournisseur::findOrFail($id);
            $fournisseur->update($request->only('prenom', 'nom', 'email', 'telephone'));

            if ($request->has('medicaments')) {
                $fournisseur->medicaments()->sync([]);
                foreach ($request->medicaments as $medicament) {
                    $fournisseur->medicaments()->attach($medicament['id'], [
                        'prix' => $medicament['prix'],
                        'quantite' => $medicament['quantite']
                    ]);
                }
            }

            DB::commit();

            return response()->json($fournisseur->load('medicaments'));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de la mise à jour du fournisseur', 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $fournisseur = Fournisseur::findOrFail($id);
            $fournisseur->medicaments()->detach();
            $fournisseur->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Fournisseur supprimé avec succès.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de la suppression du fournisseur', 'message' => $e->getMessage()], 500);
        }
    }

}
