<?php

function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

function isVendeur(): bool
{
    return isset($_SESSION['user']) && $_SESSION['user']['typePerson'] === 'VENDEUR';
}

function isClient(): bool
{
    return isset($_SESSION['user']) && $_SESSION['user']['typePerson'] === 'CLIENT';
}

function auth(): bool
{
    return isLoggedIn();
}

function guest(): bool
{
    return !isLoggedIn();
}

function vendeur(): bool
{
    return isVendeur();
}

function client(): bool
{
    return isClient();
}

function runMiddleware($middlewares): void
{
    if (!$middlewares) {
        return;
    }

    foreach ($middlewares as $middleware) {
        if (!function_exists($middleware)) {
            header("Location: /login");
            exit;
        }
        
        if (!$middleware()) {
            header("Location: /login");
            exit;
        }
    }
}
