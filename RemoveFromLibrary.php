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

    // Supprime le livre de la bibliothèque de l'utilisateur
    $stmt = $pdo->prepare("DELETE FROM bibliotheque WHERE id_livre = :livre_id AND id_utilisateur = :user_id");
    $stmt->execute([':livre_id' => $livre_id, ':user_id' => $user_id]);
}

header("Location: UserLibrary.php");
exit;
