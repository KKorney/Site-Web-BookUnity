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
            $langue = !empty($_POST['langue']) ? protectionXSS($_POST['langue']) : '';

            if (!$langue) {
                header("Location: ManageLangue.php?error=" . urlencode("Le nom de la langue est obligatoire."));
                exit;
            }

            $langue = ucwords(strtolower($langue));

            $stmt = $pdo->prepare("INSERT INTO langue (langue) VALUES (:langue)");
            $stmt->execute([':langue' => $langue]);

            header("Location: ManageLangue.php?success=" . urlencode("Langue ajoutée avec succès."));
            exit;
        }

        if ($action === 'update') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $langue = !empty($_POST['langue']) ? protectionXSS($_POST['langue']) : '';

            if ($id <= 0 || !$langue) {
                header("Location: ManageLangue.php?error=" . urlencode("Informations manquantes pour la mise à jour."));
                exit;
            }

            $langue = ucwords(strtolower($langue));

            $stmt = $pdo->prepare("UPDATE langue SET langue = :langue WHERE id = :id");
            $stmt->execute([
                ':langue' => $langue,
                ':id' => $id
            ]);

            header("Location: ManageLangue.php?success=" . urlencode("Langue mise à jour avec succès."));
            exit;
        }

        if ($action === 'delete') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            if ($id <= 0) {
                header("Location: ManageLangue.php?error=" . urlencode("Langue invalide pour suppression."));
                exit;
            }

            // Empêcher la suppression si des livres utilisent cette langue
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM livre WHERE langue_id = :id");
            $checkStmt->execute([':id' => $id]);
            $livresCount = $checkStmt->fetchColumn();

            if ($livresCount > 0) {
                header("Location: ManageLangue.php?error=" . urlencode("Impossible de supprimer : cette langue est liée à des livres."));
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM langue WHERE id = :id");
            $stmt->execute([':id' => $id]);

            header("Location: ManageLangue.php?success=" . urlencode("Langue supprimée avec succès."));
            exit;
        }

        header("Location: ManageLangue.php?error=" . urlencode("Action non reconnue."));
        exit;
    } else {
        header("Location: ManageLangue.php?error=" . urlencode("Méthode non autorisée."));
        exit;
    }
} catch (PDOException $e) {
    error_log("Erreur ManageLangueTraitement: " . $e->getMessage());
    header("Location: ManageLangue.php?error=" . urlencode("Erreur lors du traitement, réessayez."));
    exit;
}
