<?php

namespace App\Core\middlewares;

class Auth{
    public function __invoke(){
        if(!isset($_SESSION['user'])){
            header('Location: /login');
            exit();
        }
    }
}