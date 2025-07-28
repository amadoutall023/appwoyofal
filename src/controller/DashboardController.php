<?php
namespace App\Controller;

use App\Core\Abstract\AbstractController;

class DashboardController extends AbstractController {

    public function index() {
        echo "Dashboard";
    }

    public function create() {
        echo "Création dashboard";
    }

    public function store() {
        echo "Enregistrement dashboard";
    }

    public function edit() {
        echo "Édition dashboard";
    }

    public function show() {
        echo "Affichage dashboard";
    }
}
