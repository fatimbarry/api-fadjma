<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Medicament;
use App\Models\Facture;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class VenteController extends Controller
{
    /**
     * Afficher une liste de toutes les ventes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ventes = Vente::with(['client', 'medicaments'])->get();
        return response()->json($ventes);
    }

    /**
     * Enregistrer une nouvelle vente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'nullable|exists:clients,id',
            'date_vente' => 'required|date',
            'medicaments' => 'required|array',
            'medicaments.*.id' => 'required|exists:medicaments,id',
            'medicaments.*.quantite' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();

        try {
            // Création de la vente
            $vente = new Vente();
            $vente->client_id = $request->client_id;
            $vente->date_vente = $request->date_vente;
            $vente->montant_total = 0;
            $vente->save();

            $montantTotal = 0;

            // Ajout des médicaments à la vente et mise à jour du stock
            foreach ($request->medicaments as $item) {
                $medicament = Medicament::find($item['id']);
                $prixUnitaire = $medicament->prix;
                $quantiteVendue = $item['quantite'];
                $montantLigne = $prixUnitaire * $quantiteVendue;

                // Vérification du stock disponible
                if ($medicament->stock_quantite=== 0) {
                    throw new \Exception("Le médicament " . $medicament->nom . " n'est plus en stock.");
                }

                if ($medicament->stock_quantite < $quantiteVendue) {
                    throw new \Exception("Stock insuffisant pour le médicament : " . $medicament->nom);
                }

                // Mise à jour de la quantité en stock
                $medicament->stock_quantite-= $quantiteVendue;
                $medicament->save();

                $vente->medicaments()->attach($medicament->id, [
                    'quantite' => $quantiteVendue,
                    'prix_unitaire' => $prixUnitaire
                ]);

                $montantTotal += $montantLigne;
            }

            // Mise à jour du montant total
            $vente->montant_total = $montantTotal;
            $vente->save();

            // Génération de la facture après la vente
            $facture = new Facture();
            $facture->vente_id = $vente->id;
            $facture->client_id = $vente->client_id;
            $facture->numero_facture = 'FAC-' . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $facture->montant_total = $vente->montant_total;
            $facture->date_facture = now();
            $facture->save();

            // Vérification du répertoire de stockage
            if (!Storage::exists('public/factures')) {
                Storage::makeDirectory('public/factures');
            }

            // Génération du PDF
            $pdf = PDF::loadView('factures.pdf', compact('facture'));
            $filePath = 'factures/facture_' . $facture->numero_facture . '.pdf';
            Storage::put('public/' . $filePath, $pdf->output());

            DB::commit();

            return response()->json([
                'success' => true,
                'vente' => $vente->load('medicaments'),
                'facture' => $facture,
                'file_path' => Storage::url($filePath)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erreur lors de l\'enregistrement de la vente',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Afficher une vente spécifique.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vente = Vente::with(['client', 'medicaments'])->find($id);
        return response()->json($vente);
    }

    /**
     * Mettre à jour une vente spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'nullable|exists:clients,id',
            'date_vente' => 'required|date',
            'medicaments' => 'required|array',
            'medicaments.*.id' => 'required|exists:medicaments,id',
            'medicaments.*.quantite' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();

        try {
            $vente = Vente::find($id);
            $vente->client_id = $request->client_id;
            $vente->date_vente = $request->date_vente;

            $vente->medicaments()->detach();

            $montantTotal = 0;

            foreach ($request->medicaments as $item) {
                $medicament = Medicament::find($item['id']);
                $prixUnitaire = $medicament->prix;
                $montantLigne = $prixUnitaire * $item['quantite'];

                $vente->medicaments()->attach($medicament->id, [
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $prixUnitaire
                ]);

                $montantTotal += $montantLigne;
            }

            $vente->montant_total = $montantTotal;
            $vente->save();

            DB::commit();

            return response()->json($vente->load('medicaments'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erreur lors de la mise à jour de la vente', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer une vente spécifique.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $vente = Vente::findOrFail($id);
            $vente->delete();
            return response()->json(['success' => true, 'message' => 'Vente deleted successfully']);

        }catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }





    }

    /**
     * Rechercher des ventes par date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $ventes = Vente::whereBetween('date_vente', [$request->date_debut, $request->date_fin])
                       ->with(['client', 'medicaments'])
                       ->get();

        return response()->json($ventes);
    }

    /**
     * Obtenir un rapport des ventes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function rapport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $rapport = Vente::whereBetween('date_vente', [$request->date_debut, $request->date_fin])
                        ->selectRaw('SUM(montant_total) as total_ventes, COUNT(*) as nombre_ventes')
                        ->first();

        $medicamentsVendus = DB::table('medicament_vente')
            ->join('ventes', 'medicament_vente.vente_id', '=', 'ventes.id')
            ->join('medicaments', 'medicament_vente.medicament_id', '=', 'medicaments.id')
            ->whereBetween('ventes.date_vente', [$request->date_debut, $request->date_fin])
            ->selectRaw('medicaments.nom, SUM(medicament_vente.quantite) as quantite_totale, SUM(medicament_vente.quantite * medicament_vente.prix_unitaire) as montant_total')
            ->groupBy('medicaments.id', 'medicaments.nom')
            ->orderByDesc('montant_total')
            ->get();

        return response()->json([
            'total_ventes' => $rapport->total_ventes,
            'nombre_ventes' => $rapport->nombre_ventes,
            'medicaments_vendus' => $medicamentsVendus
        ]);
    }
}
