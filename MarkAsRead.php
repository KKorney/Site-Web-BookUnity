<?php
session_start();
require_once 'db.php';
require_once 'security.php';

if (empty($_SESSION['user_id'])) {
    header("Location: Login.php?error=" . urlencode("Connectez-vous pour accéder à cette fonctionnalité."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['livre_id'])) {
    $user_id = intval($_SESSION['user_id']);
    $livre_id = intval($_POST['livre_id']);

    // Récupérer la valeur actuelle de finie_lire
    $stmt = $pdo->prepare("SELECT finie_lire FROM bibliotheque WHERE id_livre = :livre_id AND id_utilisateur = :user_id");
    $stmt->execute([':livre_id' => $livre_id, ':user_id' => $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $nouveau_status = $result['finie_lire'] ? 0 : 1; // toggle
        $stmt = $pdo->prepare("UPDATE bibliotheque SET finie_lire = :status WHERE id_livre = :livre_id AND id_utilisateur = :user_id");
        $stmt->execute([':status' => $nouveau_status, ':livre_id' => $livre_id, ':user_id' => $user_id]);
    }
}

header("Location: UserLibrary.php");
exit;
