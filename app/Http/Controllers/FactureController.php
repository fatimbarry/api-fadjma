<?php
namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;


class FactureController extends Controller
{
    // Créer une nouvelle facture
    public function create(Request $request)
    {
        // Valider les données de la requête
        $request->validate([
            'vente_id' => 'required|exists:ventes,id',
        ]);

        try {
            // Créer une nouvelle facture associée à une vente
            $facture = new Facture();
            $facture->vente_id = $request->vente_id;
            $facture->client_id = $request->client_id;
            $facture->date_facture = $request->date_facture;
            $facture->numero_facture = 'FAC-' . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $facture->montant_total = Vente::find($request->vente_id)->montant_total;
            $facture->save();

            return response()->json(['success' => true, 'data' => $facture], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Afficher une facture spécifique
    public function show($id)
    {
        try {
            $facture = Facture::with('vente.medicaments')->findOrFail($id);

            return response()->json(['success' => true, 'data' => $facture], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    // Générer un PDF pour une facture
    public function generatePDF($id)
    {
        try {
            // Récupérer la facture avec les détails de la vente
            $facture = Facture::with('vente.medicaments')->findOrFail($id);

            // Charger la vue pour le PDF
            $pdf = PDF::loadView('factures.pdf', compact('facture'));

            // Retourner le PDF à télécharger
            return $pdf->download('facture_' . $facture->numero_facture . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Autres méthodes selon vos besoins...
}
