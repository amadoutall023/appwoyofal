<?php
// routes/route.web.php

use App\Controller\HomeController;
use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Controller\AchatController;
use App\Controller\ClientController;
use App\Controller\CompteurController;
use App\Controller\LogAchatController;

// Toutes tes routes web ici
$routes = [

    // ============= PAGES WEB =============
    
    // Page d'accueil
    '/' => [
        'controller' => HomeController::class,
        'method' => 'index',
        'middlewares' => []
    ],

    // ============= AUTHENTIFICATION =============
    
    // Page de connexion
    '/login' => [
        'controller' => AuthController::class,
        'method' => 'login',
        'middlewares' => []
    ],

    // Traitement connexion
    '/auth' => [
        'controller' => AuthController::class,
        'method' => 'index',
        'middlewares' => []
    ],

    // Déconnexion
    '/logout' => [
        'controller' => AuthController::class,
        'method' => 'logout',
        'middlewares' => ['auth']
    ],

    // Inscription
    '/register' => [
        'controller' => AuthController::class,
        'method' => 'create',
        'middlewares' => []
    ],

    // Traitement inscription
    '/register/store' => [
        'controller' => AuthController::class,
        'method' => 'store',
        'middlewares' => []
    ],

    // Profil utilisateur
    '/profile' => [
        'controller' => AuthController::class,
        'method' => 'show',
        'middlewares' => ['auth']
    ],

    // Édition profil
    '/profile/edit' => [
        'controller' => AuthController::class,
        'method' => 'edit',
        'middlewares' => ['auth']
    ],

    // ============= DASHBOARD =============
    
    '/dashboard' => [
        'controller' => DashboardController::class,
        'method' => 'index',
        'middlewares' => ['auth']
    ],

    '/dashboard/create' => [
        'controller' => DashboardController::class,
        'method' => 'create',
        'middlewares' => ['auth']
    ],

    '/dashboard/store' => [
        'controller' => DashboardController::class,
        'method' => 'store',
        'middlewares' => ['auth']
    ],

    '/dashboard/edit' => [
        'controller' => DashboardController::class,
        'method' => 'edit',
        'middlewares' => ['auth']
    ],

    '/dashboard/show' => [
        'controller' => DashboardController::class,
        'method' => 'show',
        'middlewares' => ['auth']
    ],

    // ============= GESTION DES CLIENTS =============
    
    // Liste des clients
    '/clients' => [
        'controller' => ClientController::class,
        'method' => 'index',
        'middlewares' => ['auth']
    ],

    // Formulaire création client
    '/clients/create' => [
        'controller' => ClientController::class,
        'method' => 'create',
        'middlewares' => ['auth']
    ],

    // Enregistrer nouveau client
    '/clients/store' => [
        'controller' => ClientController::class,
        'method' => 'store',
        'middlewares' => ['auth']
    ],

    // Voir détails client
    '/clients/show' => [
        'controller' => ClientController::class,
        'method' => 'show',
        'middlewares' => ['auth']
    ],

    // Éditer client
    '/clients/edit' => [
        'controller' => ClientController::class,
        'method' => 'edit',
        'middlewares' => ['auth']
    ],

    // ============= GESTION DES COMPTEURS =============
    
    // Liste des compteurs
    '/compteurs' => [
        'controller' => CompteurController::class,
        'method' => 'index',
        'middlewares' => ['auth']
    ],

    // Formulaire création compteur
    '/compteurs/create' => [
        'controller' => CompteurController::class,
        'method' => 'create',
        'middlewares' => ['auth']
    ],

    // Enregistrer nouveau compteur
    '/compteurs/store' => [
        'controller' => CompteurController::class,
        'method' => 'store',
        'middlewares' => ['auth']
    ],

    // Voir détails compteur
    '/compteurs/show' => [
        'controller' => CompteurController::class,
        'method' => 'show',
        'middlewares' => ['auth']
    ],

    // Éditer compteur
    '/compteurs/edit' => [
        'controller' => CompteurController::class,
        'method' => 'edit',
        'middlewares' => ['auth']
    ],

    // Vérifier compteur
    '/compteurs/verifier' => [
        'controller' => CompteurController::class,
        'method' => 'verifier',
        'middlewares' => []
    ],

    // ============= GESTION DES ACHATS =============
    
    // Liste des achats
    '/achats' => [
        'controller' => AchatController::class,
        'method' => 'index',
        'middlewares' => ['auth']
    ],

    // Formulaire d'achat
    '/achats/create' => [
        'controller' => AchatController::class,
        'method' => 'create',
        'middlewares' => ['auth']
    ],

    // Enregistrer achat
    '/achats/store' => [
        'controller' => AchatController::class,
        'method' => 'store',
        'middlewares' => ['auth']
    ],

    // Voir détails achat
    '/achats/show' => [
        'controller' => AchatController::class,
        'method' => 'show',
        'middlewares' => ['auth']
    ],

    // Éditer achat
    '/achats/edit' => [
        'controller' => AchatController::class,
        'method' => 'edit',
        'middlewares' => ['auth']
    ],

    // ============= API ENDPOINTS =============
    
    // API Liste des achats
    '/api/achats' => [
        'controller' => AchatController::class,
        'method' => 'store',
        'middlewares' => []
    ],
    
    // Achat de crédit principal
    '/api/acheter' => [
        'controller' => AchatController::class,
        'method' => 'acheter',
        'middlewares' => []
    ],

    // Historique des achats
    '/api/historique' => [
        'controller' => AchatController::class,
        'method' => 'historique',
        'middlewares' => []
    ],

    // ============= LOGS ET STATISTIQUES =============
    
    // Liste des logs
    '/logs' => [
        'controller' => LogAchatController::class,
        'method' => 'index',
        'middlewares' => ['auth']
    ],

    // Formulaire création log
    '/logs/create' => [
        'controller' => LogAchatController::class,
        'method' => 'create',
        'middlewares' => ['auth']
    ],

    // Enregistrer log
    '/logs/store' => [
        'controller' => LogAchatController::class,
        'method' => 'store',
        'middlewares' => ['auth']
    ],

    // Voir détails log
    '/logs/show' => [
        'controller' => LogAchatController::class,
        'method' => 'show',
        'middlewares' => ['auth']
    ],

    // Éditer log
    '/logs/edit' => [
        'controller' => LogAchatController::class,
        'method' => 'edit',
        'middlewares' => ['auth']
    ],

    // Consulter logs par période
    '/logs/consulter' => [
        'controller' => LogAchatController::class,
        'method' => 'consulter',
        'middlewares' => ['auth']
    ],

    // Statistiques des transactions
    '/logs/statistiques' => [
        'controller' => LogAchatController::class,
        'method' => 'statistiques',
        'middlewares' => ['auth']
    ],

    // ============= PAGES GÉNÉRIQUES =============
    
    // Création générique
    '/create' => [
        'controller' => HomeController::class,
        'method' => 'create',
        'middlewares' => ['auth']
    ],

    // Enregistrement générique
    '/store' => [
        'controller' => HomeController::class,
        'method' => 'store',
        'middlewares' => ['auth']
    ],

    // Édition générique
    '/edit' => [
        'controller' => HomeController::class,
        'method' => 'edit',
        'middlewares' => ['auth']
    ],

    // Affichage générique
    '/show' => [
        'controller' => HomeController::class,
        'method' => 'show',
        'middlewares' => ['auth']
    ],

];
