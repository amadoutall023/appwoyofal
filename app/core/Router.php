<?php

namespace App\Core;

use App\Core\App; // Pour App::get()


require_once "../app/config/middlewares.php"; // pour runMiddleWare()

class Router
{
    private static ?Router $instance = null;

    public static function getInstance(): Router {
        if (self::$instance === null) {
            self::$instance = new Router();
        }
        return self::$instance;
    }

    public static function resolve(array $routes): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestUri = rtrim($requestUri, '/') ?: '/';

        if (isset($routes[$requestUri])) {
            $route = $routes[$requestUri];

            $controllerClass = $route['controller'];
            $method = $route['method'];
            $middlewares = $route['middlewares'] ?? [];

            runMiddleware($middlewares);

            $controller = App::get($controllerClass);

            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                self::render404("Méthode $method inexistante dans $controllerClass");
            }
        } else {
            self::render404("Route $requestUri non trouvée");
        }
    }

    private static function render404(string $message = 'Page non trouvée'): void
    {
        http_response_code(404);
        echo json_encode([
            'statut' => 'error',
            'code' => 404,
            'message' => $message
        ]);
        exit;
    }
}
