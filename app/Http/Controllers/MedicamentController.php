<?php

namespace App\Http\Controllers;

use App\Models\Groupe_Medoc;
use App\Models\Medicament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicamentController extends Controller
{
    /**
     * Afficher une liste de tous les médicaments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Charger la relation 'groupe' pour inclure les informations du groupe associé
            $medicaments = Medicament::with('groupe')->get();

            return response()->json($medicaments);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function create()
    {
        try {
            $grpMedocs = Groupe_Medoc::all();
            return response()->json(['success' => true, 'data' => $grpMedocs ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Enregistrer un nouveau médicament.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [


                'grpemedoc_id' => 'required|exists:grpe_medocs,id',
                'nom' => 'required|string|max:255',
                'description' => 'nullable|string',
                'dosage' => 'required|string',
                'prix' => 'required|numeric',
                'stock_quantite'=>'required|numeric',
                'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'composition' => 'required|string',
                'fabricant' => 'required|string',
                'type_consommation' => 'required|string',
                'date_expiration' => 'required|date',
                'posologie' => 'nullable|string',
                'ingredients_actifs' => 'required|string',
                'effets_secondaires' => 'nullable|string',
                'forme_pharmaceutique' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
            }

            $data = $request->all();

            if ($request->hasFile('image_path')) {
                $photoName = time().'.'.$request->image_path->extension();
                $photoPath = $request->file('image_path')->storeAs('photosMedocs', $photoName, 'public');
                $data['image_path'] = $photoPath;
            }

            Medicament::create($data);

            return response()->json(['success' => true, 'message' => 'Medicament created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    /**
     * Afficher un médicament spécifique.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $medicament = Medicament::find($id);
        if (!$medicament) {
            return response()->json(['message' => 'Médicament non trouvé'], 404);
        }
        return response()->json(['data' => $medicament], 200);
    }

    /**
     * Mettre à jour un médicament spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $medicament = Medicament::find($id);
        if (!$medicament) {
            return response()->json(['error' => 'Médicament non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'code_medicament' => 'unique:medicaments,code_medicament,'.$id,
            'grpemedoc_id' => 'exists:grpe_medocs,id',
            'nom' => 'string|max:255',
            'description' => 'nullable|string',
            'dosage' => 'string',
            'prix' => 'numeric',
            'image_path' => 'nullable|string',
            'composition' => 'string',
            'fabricant' => 'string',
            'type_consommation' => 'string',
            'date_expiration' => 'date',
            'posologie' => 'nullable|string',
            'ingredients_actifs' => 'string',
            'effets_secondaires' => 'nullable|string',
            'forme_pharmaceutique' => 'string',
            'stock_quantite' => 'integer', // Ajout de la validation pour stock_quantite
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $validatedData = $validator->validated();

        // Si une nouvelle image est téléchargée
        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('photosMedocs', 'public');
            $validatedData['image_path'] = $imagePath;
        }

        $medicament->update($validatedData);

        return response()->json($medicament);
    }


    /**
     * Supprimer un médicament spécifique.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try {

            $medicament = Medicament::find($id);
            $medicament->delete();


            return response()->json(['success' => true, 'message' => 'Medicament deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Rechercher des médicaments par nom.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        $medicaments = Medicament::where('nom', 'like', "%{$query}%")->get();
        return response()->json($medicaments);
    }

    /**
     * Lister les médicaments par groupe.
     *
     * @param  int  $grpemedoc_id
     * @return \Illuminate\Http\Response
     */
    public function listByGroup($grpemedoc_id)
    {
        $medicaments = Medicament::where('grpemedoc_id', $grpemedoc_id)->get();
        return response()->json($medicaments);
    }

    /**
     * Obtenir les médicaments expirés ou proche de l'expiration.
     *
     * @return \Illuminate\Http\Response
     */
    public function getExpiringMedicaments()
    {
        $expiringDate = now()->addMonths(3);
        $medicaments = Medicament::where('date_expiration', '<=', $expiringDate)->get();
        return response()->json($medicaments);
    }
}
