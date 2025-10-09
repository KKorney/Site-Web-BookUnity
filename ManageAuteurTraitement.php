<?php
session_start();
require_once 'db.php';
require_once 'security.php';

// Activer PDO en mode exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if ($action === 'add') {
            $nom = !empty($_POST['nom']) ? protectionXSS($_POST['nom']) : '';

            if (!$nom) {
                header("Location: ManageAuteur.php?error=" . urlencode("Le nom de l'auteur est obligatoire."));
                exit;
            }

            // Format : chaque mot commence par une majuscule
            $nom = ucwords(strtolower($nom));

            $stmt = $pdo->prepare("INSERT INTO auteur (nom) VALUES (:nom)");
            $stmt->execute([':nom' => $nom]);

            header("Location: ManageAuteur.php?success=" . urlencode("Auteur ajouté avec succès."));
            exit;
        }

        if ($action === 'update') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $nom = !empty($_POST['nom']) ? protectionXSS($_POST['nom']) : '';

            if ($id <= 0 || !$nom) {
                header("Location: ManageAuteur.php?error=" . urlencode("Informations manquantes pour la mise à jour."));
                exit;
            }

            $nom = ucwords(strtolower($nom));

            $stmt = $pdo->prepare("UPDATE auteur SET nom = :nom WHERE id = :id");
            $stmt->execute([
                ':nom' => $nom,
                ':id' => $id
            ]);

            header("Location: ManageAuteur.php?success=" . urlencode("Auteur mis à jour avec succès."));
            exit;
        }

        if ($action === 'delete') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            if ($id <= 0) {
                header("Location: ManageAuteur.php?error=" . urlencode("Auteur invalide pour suppression."));
                exit;
            }

            // Supprimer l'auteur uniquement si aucun livre ne dépend de lui
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM livre WHERE auteur_id = :id");
            $checkStmt->execute([':id' => $id]);
            $livresCount = $checkStmt->fetchColumn();

            if ($livresCount > 0) {
                header("Location: ManageAuteur.php?error=" . urlencode("Impossible de supprimer : cet auteur est lié à des livres."));
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM auteur WHERE id = :id");
            $stmt->execute([':id' => $id]);

            header("Location: ManageAuteur.php?success=" . urlencode("Auteur supprimé avec succès."));
            exit;
        }

        header("Location: ManageAuteur.php?error=" . urlencode("Action non reconnue."));
        exit;
    } else {
        header("Location: ManageAuteur.php?error=" . urlencode("Méthode non autorisée."));
        exit;
    }
} catch (PDOException $e) {
    error_log("Erreur ManageAuteurTraitement: " . $e->getMessage());
    header("Location: ManageAuteur.php?error=" . urlencode("Erreur lors du traitement, réessayez."));
    exit;
}
