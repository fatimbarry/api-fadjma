<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FournisseurController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\Groupe_MedocController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [AuthController::class, 'register']);
// Login
Route::post('/login', [AuthController::class,'store'])->name('auth.store');

Route::middleware('auth:sanctum')->group(function () {


    Route::controller(AuthController::class)->group(function () {
        Route::post('logout','logout');
        Route::post('refresh', 'refresh');
        Route::put('/change-password', 'changePassword');
    });


    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index');
        Route::post('/users/store', 'store');
        Route::get('/users/show/{id}', 'show');
        Route::put('/users/update/{id}', 'update');
        Route::delete('/users/delete/{id}', 'destroy');
        Route::get('/users/getuser', 'getUser');
    });


    // Routes pour Groupe_MedocController
    Route::controller(Groupe_MedocController::class)->group(function () {
        Route::get('/groupe_medoc', 'index');
        Route::post('/groupe_medoc/store', 'store');
        Route::get('/groupe_medoc/show/{id}', 'show');
        Route::put('/groupe_medoc/update/{id}', 'update');
        Route::delete('/groupe_medoc/delete/{id}', 'destroy');
    });


    Route::controller(MedicamentController::class)->group(function () {
        Route::get('/medicaments', 'index');
        Route::post('/medicaments/store', 'store');
        Route::get('/medicaments/show/{id}', 'show');
        Route::post('/medicaments/update/{id}', 'update');
        Route::delete('/medicaments/delete/{id}', 'destroy');
        Route::get('medicaments/search','search');
        Route::get('medicaments/group/{grpemedoc_id}', 'listByGroup');
        Route::get('medicaments/expiring', 'getExpiringMedicaments');

    });

    Route::get('/factures/create', [FactureController::class, 'create']);
    Route::get('/factures/show/{id}', [FactureController::class, 'show']);
    Route::get('/factures/{id}/pdf', [FactureController::class, 'generatePDF']);


    Route::controller(VenteController::class)->group(function () {
        Route::get('/ventes', 'index');
        Route::post('/ventes/store',  'store');
        Route::get('/ventes/show/{id}', 'show');
        Route::put('/ventes/update/{id}', 'update');
        Route::delete('/ventes/delete/{id}', 'destroy');
        Route::post('ventes/search-by-date', [VenteController::class, 'searchByDate']);
        Route::post('/rapport', [VenteController::class, 'rapport']);
    });


    Route::controller(ClientController::class)->group(function () {

        Route::get('/clients',  'index'); // Liste de tous les clients
        Route::post('/clients/store', 'store'); // Créer un nouveau client
        Route::get('/clients/show/{id}', 'show'); // Afficher un client spécifique
        Route::put('/clients/update/{id}', 'update'); // Mettre à jour un client spécifique
        Route::delete('/clients/delete/{id}', 'destroy'); // Supprimer un client spécifique
    });

// Groupe pour les routes des fournisseurs
    Route::controller(FournisseurController::class)->group(function () {
        Route::get('/fournisseurs',  'index'); // Liste des fournisseurs
        Route::get('/fournisseurs/show/{id}',  'show'); // Détails d'un fournisseur spécifique
        Route::post('/fournisseurs/store', 'store'); // Ajouter un nouveau fournisseur
        Route::put('/fournisseurs/update/{id}',  'update'); // Mettre à jour un fournisseur
        Route::delete('/fournisseurs/delete/{id}',  'destroy'); // Supprimer un fournisseur
    });



    Route::get('/medicaments-disponibles', [DashboardController::class, 'nombreMedicamentsDisponibles']);
    Route::get('/medicaments-penurie', [DashboardController::class, 'nombreMedicamentsPenurie']);
    Route::get('/total-medicaments', [DashboardController::class, 'nombreTotalMedicaments']);
    Route::get('/total-groupes-medicaments', [DashboardController::class, 'nombreTotalGroupesMedicaments']);
    Route::get('/quantite-medicaments-vendus', [DashboardController::class, 'nombreQuantiteMedicamentsVendus']);
    Route::get('/fournisseurs', [DashboardController::class, 'nombreFournisseurs']);
    Route::get('/utilisateurs', [DashboardController::class, 'nombreUtilisateurs']);
    Route::get('/Nbreclients', [DashboardController::class, 'nombreClients']);
    Route::get('/article-frequent', [DashboardController::class, 'articleFrequent']);
    Route::get('/revenu-annuel', [DashboardController::class, 'revenuAnnuel']);
    Route::get('/factures-generer', [DashboardController::class, 'nombreFacturesGenerer']); // Nouvelle route








});

