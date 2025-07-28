<?php
namespace App\Controller;

use App\Core\Abstract\AbstractController;

class HomeController extends AbstractController {

    public function index() {
        echo "Bienvenue sur la page d'accueil!";
    }

    public function create() {
        echo "Page de création";
    }

    public function store() {
        echo "Enregistrement des données";
    }

    public function edit() {
        echo "Page d'édition";
    }

    public function show() {
        echo "Affichage détaillé";
    }
}
