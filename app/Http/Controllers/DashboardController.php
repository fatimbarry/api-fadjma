<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use App\Models\Fournisseur;
use App\Models\User;
use App\Models\Client;
use App\Models\Vente;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Méthode qui calcule le nombre de médicaments disponibles
    public function nombreMedicamentsDisponibles()
    {
        $count = Medicament::where('stock_quantite', '>', 0)->count();
        return response()->json(['nombre_medicaments_disponibles' => $count]);
    }

    // Méthode qui calcule le nombre de médicaments en pénurie
    public function nombreMedicamentsPenurie()
    {
        $count = Medicament::where('stock_quantite', '<=', 0)->count();
        return response()->json(['nombre_medicaments_penurie' => $count]);
    }

    // Méthode qui calcule le nombre total de médicaments
    public function nombreTotalMedicaments()
    {
        $count = Medicament::count();
        return response()->json(['nombre_total_medicaments' => $count]);
    }

    // Méthode qui calcule le nombre total de groupes de médicaments
    public function nombreTotalGroupesMedicaments()
    {
        $count = DB::table('grpe_medocs')->count(); // Assure-toi que le nom de la table est correct
        return response()->json(['nombre_total_groupes_medicaments' => $count]);
    }

    // Méthode qui calcule le nombre de quantités de médicaments vendus
    public function nombreQuantiteMedicamentsVendus()
    {
        $totalQuantite = DB::table('ventes')
            ->join('medicament_vente', 'ventes.id', '=', 'medicament_vente.vente_id')
            ->sum('medicament_vente.quantite');
        return response()->json(['nombre_quantite_medicaments_vendus' => $totalQuantite]);
    }

    // Méthode qui calcule le nombre de fournisseurs
    public function nombreFournisseurs()
    {
        $count = Fournisseur::count();
        return response()->json(['nombre_fournisseurs' => $count]);
    }

    // Méthode qui calcule le nombre d'utilisateurs
    public function nombreUtilisateurs()
    {
        $count = User::count();
        return response()->json(['nombre_utilisateurs' => $count]);
    }

    // Méthode qui calcule le nombre de clients
    public function nombreClients()
    {
        $count = Client::count();
        return response()->json(['nombre_clients' => $count]);
    }

    // Méthode qui détermine l'article le plus fréquent
    public function articleFrequent()
    {
        $article = DB::table('medicament_vente')
            ->select('medicament_id', DB::raw('SUM(quantite) as total_quantite'))
            ->groupBy('medicament_id')
            ->orderBy('total_quantite', 'DESC')
            ->first();
        return response()->json([
            'article_frequent' => $article ? [
                'medicament_id' => $article->medicament_id,
                'total_quantite' => $article->total_quantite
            ] : null
        ]);
    }

    // Méthode qui calcule le revenu annuel
    public function revenuAnnuel()
    {
        $revenu = DB::table('ventes')
            ->whereYear('date_vente', '=', date('Y'))
            ->sum('montant_total');
        return response()->json(['revenu_annuel' => $revenu]);
    }

    // Méthode qui calcule le nombre de factures générées
    public function nombreFacturesGenerer()
    {
        $count = DB::table('factures')->count(); // Assure-toi que le nom de la table est correct
        return response()->json(['nombre_factures_generer' => $count]);
    }
}
