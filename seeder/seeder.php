<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

try {
    $pdo = new PDO(
        "{$_ENV['DB_DRIVER']}:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']}",
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion OK\n";

    $pdo->beginTransaction();

    // CLIENTS
    $clients = [
        ['Fall', 'Demba', '770000001', 'fall@gmail.com'],
        ['Sow', 'Saly', '770000002', 'sow@gmail.com'],
        ['Ndiaye', 'Fatou', '770000003', 'ndiaye@gmail.com'],
    ];
    $stmt = $pdo->prepare("INSERT INTO client (nom, prenom, telephone, email) VALUES (?, ?, ?, ?)");
    foreach ($clients as $c) $stmt->execute($c);
    echo "✅ Clients insérés\n";

    // COMPTEURS
    $stmt = $pdo->query("SELECT id FROM client");
    $clientIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $compteurs = [
        ['CPT001', $clientIds[0]],
        ['CPT002', $clientIds[1]],
        ['CPT003', $clientIds[2]],
    ];
    $stmt = $pdo->prepare("INSERT INTO compteur (numero_compteur, client_id) VALUES (?, ?)");
    foreach ($compteurs as $c) $stmt->execute($c);
    echo "✅ Compteurs insérés\n";

    // ACHATS
    $achats = [
        ['REF001', 'CPT001', 'RCG001', 1000.0, 50.0, 'T1', 20.0, $clientIds[0]],
        ['REF002', 'CPT002', 'RCG002', 1500.0, 75.0, 'T2', 20.0, $clientIds[1]],
    ];
    $stmt = $pdo->prepare("INSERT INTO achat (reference, numero_compteur, code_recharge, montant, nbre_kwt, tranche, prix_unitaire, client_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($achats as $a) $stmt->execute($a);
    echo "✅ Achats insérés\n";

    // LOGS
    $logs = [
        ['Dakar', '192.168.1.1', 'success', 'CPT001', 'RCG001', 50.0, ''],
        ['Thiès', '192.168.1.2', 'error', 'CPT002', null, null, 'Solde insuffisant'],
    ];
    $stmt = $pdo->prepare("INSERT INTO log_achat (localisation, adresse_ip, statut, numero_compteur, code_recharge, nbre_kwt, message_erreur) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($logs as $l) $stmt->execute($l);

    $pdo->commit();
    echo "🎉 Données insérées.\n";

} catch (PDOException $e) {
    $pdo->rollBack();
    die("❌ Erreur transaction : " . $e->getMessage());
}
