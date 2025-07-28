<?php

require_once __DIR__ . '/../vendor/autoload.php';

function prompt(string $message): string {
    echo $message;
    return trim(fgets(STDIN));
}

function writeEnvIfNotExists(array $config): void {
    $envPath = __DIR__ . '/../.env';

    if (!file_exists($envPath)) {
        $env = <<<ENV
DB_DRIVER={$config['driver']}
DB_HOST={$config['host']}
DB_PORT={$config['port']}
DB_NAME={$config['dbName']}
DB_USER={$config['user']}
DB_PASSWORD={$config['pass']}
APP_URL=http://localhost:8082
ENV;
        file_put_contents($envPath, $env);
        echo ".env généré avec succès.\n";
    } else {
        echo "Le fichier .env existe déjà.\n";
    }
}

// Saisie des paramètres
$driver = strtolower(prompt("Quel SGBD utiliser ? (mysql / pgsql) : "));
if (!in_array($driver, ['mysql', 'pgsql'])) {
    exit("SGBD non supporté.\n");
}
$host = prompt("Hôte (default: 127.0.0.1) : ") ?: "127.0.0.1";
$port = prompt("Port : ") ?: ($driver === 'pgsql' ? "5432" : "3306");
$user = prompt("Utilisateur (default: root) : ") ?: "root";
$pass = prompt("Mot de passe : ");
$dbName = prompt("Nom de la base à créer : ");
$config = compact('driver', 'host', 'port', 'user', 'pass', 'dbName');

// Création base
try {
    $pdo = new PDO(
        $driver === 'pgsql' ? "$driver:host=$host;port=$port;dbname=postgres" : "$driver:host=$host;port=$port",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($driver === 'mysql') {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    } else {
        $exists = $pdo->query("SELECT 1 FROM pg_database WHERE datname = '$dbName'")->fetch();
        if (!$exists) $pdo->exec("CREATE DATABASE \"$dbName\";");
    }
    echo "✅ Base `$dbName` prête.\n";

    $pdo = new PDO("$driver:host=$host;port=$port;dbname=$dbName", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($driver === 'pgsql') {
        $pdo->exec("
            DO \$\$
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'statut_enum') THEN
                    CREATE TYPE statut_enum AS ENUM ('success', 'error');
                END IF;
            END
            \$\$;
        ");
    }

    $tables = [
        // CLIENT
        "CREATE TABLE IF NOT EXISTS client (
            id " . ($driver === 'pgsql' ? "SERIAL" : "INT AUTO_INCREMENT") . " PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            prenom VARCHAR(100) NOT NULL,
            telephone VARCHAR(20) NOT NULL,
            email VARCHAR(100) NOT NULL,
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );",
        // COMPTEUR
        "CREATE TABLE IF NOT EXISTS compteur (
            id " . ($driver === 'pgsql' ? "SERIAL" : "INT AUTO_INCREMENT") . " PRIMARY KEY,
            numero_compteur VARCHAR(50) NOT NULL UNIQUE,
            client_id INT NOT NULL,
            statut VARCHAR(20) DEFAULT 'actif',
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE
        );",
        // ACHAT
        "CREATE TABLE IF NOT EXISTS achat (
            id " . ($driver === 'pgsql' ? "SERIAL" : "INT AUTO_INCREMENT") . " PRIMARY KEY,
            reference VARCHAR(100) NOT NULL,
            numero_compteur VARCHAR(50) NOT NULL,
            code_recharge VARCHAR(100) NOT NULL,
            montant DECIMAL(10,2) NOT NULL,
            nbre_kwt DECIMAL(10,2) NOT NULL,
            tranche VARCHAR(50) NOT NULL,
            prix_unitaire DECIMAL(10,2) NOT NULL,
            date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            statut VARCHAR(20) DEFAULT 'success',
            client_id INT NOT NULL,
            FOREIGN KEY (numero_compteur) REFERENCES compteur(numero_compteur),
            FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE
        );",
        // LOG ACHAT
        "CREATE TABLE IF NOT EXISTS log_achat (
            id " . ($driver === 'pgsql' ? "SERIAL" : "INT AUTO_INCREMENT") . " PRIMARY KEY,
            date_heure TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            localisation VARCHAR(255),
            adresse_ip VARCHAR(45),
            statut " . ($driver === 'pgsql' ? "statut_enum" : "VARCHAR(20) CHECK (statut IN ('success','error'))") . ",
            numero_compteur VARCHAR(50),
            code_recharge VARCHAR(100),
            nbre_kwt DECIMAL(10,2),
            message_erreur TEXT
        );"
    ];

    foreach ($tables as $sql) $pdo->exec($sql);
    echo "✅ Tables créées.\n";

    writeEnvIfNotExists($config);

} catch (PDOException $e) {
    die("❌ Erreur : " . $e->getMessage());
}
