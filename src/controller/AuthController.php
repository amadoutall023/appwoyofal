<?php
namespace App\Controller;

use App\Core\Abstract\AbstractController;

class AuthController extends AbstractController {

    public function index() {
        echo "Page d'authentification";
    }

    public function login() {
        echo "Page de connexion";
    }

    public function logout() {
        echo "Déconnexion";
    }

    public function create() {
        echo "Création de compte";
    }

    public function store() {
        echo "Enregistrement utilisateur";
    }

    public function edit() {
        echo "Édition profil";
    }

    public function show() {
        echo "Affichage profil";
    }
}
