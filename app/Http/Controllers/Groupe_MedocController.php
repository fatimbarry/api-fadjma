<?php

namespace App\Http\Controllers;

use App\Models\Groupe_Medoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Groupe_MedocController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $gm = Groupe_Medoc::simplePaginate(10);
            return response()->json(['success' => true, 'data' => $gm]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
            $validator = Validator::make($request->all(), [
                'nom' => 'required|unique:grpe_medocs|max:80',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
            }

            $gm = Groupe_Medoc::create($request->all());

            return response()->json(['success' => true, 'data' => $gm], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */

     public function show($id)
{
    try {
        $groupeMedoc = Groupe_Medoc::find($id);

        if (!$groupeMedoc) {
            return response()->json(['success' => false, 'message' => 'Groupe Médicament not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $groupeMedoc], 200);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
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
        try {
            // Trouver l'objet Groupe_Medoc par son ID
            $groupe_Medoc = Groupe_Medoc::find($id);

            // Validation avec exclusion de l'objet en cours
            $validator = Validator::make($request->all(), [
                'nom' => 'required|unique:grpe_medocs,nom,' . $id . '|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
            }

            // Mise à jour de l'enregistrement
            $groupe_Medoc->update($request->only(['nom']));

            // Retourne l'objet mis à jour
            return response()->json(['success' => true, 'data' => $groupe_Medoc]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Groupe_Medoc $groupe_Medoc)
    {
        try {
            $groupe_Medoc->delete();

            return response()->json(['success' => true, 'message' => 'groupe  deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
