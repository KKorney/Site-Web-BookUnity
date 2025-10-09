<?php
require_once 'db.php';
require_once 'security.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['livre_id'])) {
    $livre_id = intval($_POST['livre_id']);
    $user_id = intval($_SESSION['user_id']);

    // Vérifier si le livre existe
    $stmt = $pdo->prepare("SELECT id FROM livre WHERE id = :id");
    $stmt->execute([':id' => $livre_id]);
    if (!$stmt->fetch()) {
        die("Livre non trouvé.");
    }

    // Vérifier si déjà dans la bibliothèque
    $stmt = $pdo->prepare("SELECT * FROM bibliotheque WHERE id_livre = :livre_id AND id_utilisateur = :user_id");
    $stmt->execute([':livre_id' => $livre_id, ':user_id' => $user_id]);

    if (!$stmt->fetch()) {
        // Ajouter
        $stmt = $pdo->prepare("INSERT INTO bibliotheque (id_livre, id_utilisateur) VALUES (:livre_id, :user_id)");
        $stmt->execute([':livre_id' => $livre_id, ':user_id' => $user_id]);
    }
}

header('Location: HomeTraitement.php');
exit;
