<?php

namespace App\Core;

use ReflectionClass;
use ReflectionParameter;
use Exception;

class DIContainer
{
    private static ?DIContainer $instance = null;
    private array $services = [];
    private array $instances = [];
    private string $servicesPath = __DIR__ . '/../config/services.yml';

    private function __construct()
    {
        $this->loadServices();
    }

    public static function getInstance(): DIContainer
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadServices(): void
    {
        if (file_exists($this->servicesPath)) {
            $yamlContent = file_get_contents($this->servicesPath);
            $this->services = $this->parseYaml($yamlContent);
        } else {
            throw new Exception("Le fichier services.yml est introuvable dans : " . $this->servicesPath);
        }
    }

    private function parseYaml(string $yamlContent): array
    {
        $lines = explode("\n", $yamlContent);
        $result = [];
        $currentCategory = null;

        foreach ($lines as $line) {
            $originalLine = $line;
            $line = trim($line);

            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            if (strpos($line, ':') !== false && strpos($originalLine, '  ') !== 0) {
                $currentCategory = trim(str_replace(':', '', $line));
                $result[$currentCategory] = [];
            } elseif (strpos($originalLine, '  ') === 0 && strpos($line, ':') !== false) {
                $parts = explode(':', $line, 2);
                $key = trim($parts[0]);
                $value = trim($parts[1]);

                if ($currentCategory) {
                    $result[$currentCategory][$key] = $value;
                }
            }
        }

        return $result;
    }

    public function get(string $identifier): object
    {
        // Debug: afficher l'identifiant demandé
        error_log("DIContainer: Tentative de résolution de '{$identifier}'");
        
        if (isset($this->instances[$identifier])) {
            return $this->instances[$identifier];
        }

        $className = $this->resolveClassName($identifier);

        if (!class_exists($className)) {
            throw new Exception("La classe {$className} n'existe pas");
        }

        $instance = $this->createInstance($className);

        $this->instances[$identifier] = $instance;
        $this->instances[$className] = $instance;

        return $instance;
    }

    private function resolveClassName(string $identifier): string
    {
        // Debug: afficher les services disponibles
        error_log("DIContainer: Services disponibles: " . print_r(array_keys($this->services), true));
        
        // 1. Recherche directe dans les services
        foreach ($this->services as $category => $services) {
            if (is_array($services) && isset($services[$identifier])) {
                error_log("DIContainer: Trouvé '{$identifier}' dans la catégorie '{$category}'");
                return $services[$identifier];
            }
        }

        // 2. Si c'est déjà un nom de classe complet
        if (class_exists($identifier)) {
            error_log("DIContainer: '{$identifier}' est une classe existante");
            return $identifier;
        }

        // 3. Recherche par nom de classe (sans namespace)
        $shortName = basename(str_replace('\\', '/', $identifier));
        foreach ($this->services as $category => $services) {
            if (is_array($services)) {
                foreach ($services as $key => $className) {
                    // Recherche par clé (pour les interfaces)
                    if ($key === $shortName) {
                        error_log("DIContainer: Trouvé '{$identifier}' par clé: '{$className}'");
                        return $className;
                    }
                    // Recherche par nom de classe (pour les classes)
                    $shortClassName = basename(str_replace('\\', '/', $className));
                    if ($shortClassName === $shortName) {
                        error_log("DIContainer: Trouvé '{$identifier}' par nom court: '{$className}'");
                        return $className;
                    }
                }
            }
        }

        // 4. Recherche par valeur dans les services
        foreach ($this->services as $category => $services) {
            if (is_array($services)) {
                foreach ($services as $key => $className) {
                    if ($className === $identifier) {
                        error_log("DIContainer: Trouvé '{$identifier}' par valeur");
                        return $className;
                    }
                }
            }
        }

        // Debug: afficher tous les services pour diagnostic
        error_log("DIContainer: Tous les services: " . print_r($this->services, true));
        
        // Debug temporaire supprimé
        
        throw new Exception("Impossible de résoudre l'identifiant: {$identifier}. Vérifiez votre fichier services.yml");
    }

    private function createInstance(string $className): object
    {
        error_log("DIContainer: Création d'une instance de '{$className}'");
        
        $reflection = new ReflectionClass($className);

        $constructor = $reflection->getConstructor();
        if (!$constructor) {
            return new $className();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependencies[] = $this->resolveDependency($parameter);
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    private function resolveDependency(ReflectionParameter $parameter): object
    {
        $type = $parameter->getType();
        
        if (!$type || $type->isBuiltin()) {
            throw new Exception("Impossible de résoudre la dépendance pour le paramètre: " . $parameter->getName());
        }

        $className = $type->getName();
        error_log("DIContainer: Résolution de la dépendance '{$className}' pour le paramètre '{$parameter->getName()}'");
        
        // Cas spécial pour PDO
        if ($className === 'PDO') {
            return $this->createPDO();
        }
        
        return $this->get($className);
    }

    private function createPDO(): \PDO 
    {
        // Inclure les constantes d'environnement
        require_once __DIR__ . '/../config/env.php';
        
        // Valeurs par défaut pour PostgreSQL avec Docker
        $dsn = defined('dsn') && !empty(dsn) ? dsn : 'pgsql:host=localhost;port=5432;dbname=appnew_db;charset=utf8';
        $user = defined('DB_USER') && !empty(DB_USER) ? DB_USER : 'postgres';
        $password = defined('DB_PASSWORD') ? DB_PASSWORD : '';
        
        // Debug : vérifier les valeurs
        error_log("DSN utilisé: " . $dsn);
        error_log("User utilisé: " . $user);
        error_log("Password défini: " . (!empty($password) ? 'OUI' : 'NON'));
        
        try {
            return new \PDO(
                $dsn,
                $user,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (\PDOException $e) {
            error_log("Erreur PDO: " . $e->getMessage());
            throw new \Exception("Erreur de connexion à la base de données PostgreSQL: " . $e->getMessage());
        }
    }

    public function bind(string $abstract, string $concrete): void
    {
        if (!isset($this->services['bindings'])) {
            $this->services['bindings'] = [];
        }
        $this->services['bindings'][$abstract] = $concrete;
    }

    public function reload(): void
    {
        $this->services = [];
        $this->instances = [];
        $this->loadServices();
    }

    // Méthode utile pour débugger
    public function getRegisteredServices(): array
    {
        return $this->services;
    }
}