<?php
require_once 'db.php';
require_once 'security.php';

// Activer PDO en mode exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if ($action === 'add') {
            $genre = isset($_POST['genre']) ? trim($_POST['genre']) : '';
            if ($genre === '') {
                header('Location: ManageGenre.php?error=' . urlencode("Le genre ne peut pas être vide."));
                exit;
            }

            $genreFormatted = ucwords(strtolower($genre));

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM genre WHERE genre = :genre");
            $stmt->execute([':genre' => $genreFormatted]);
            if ($stmt->fetchColumn()) {
                header('Location: ManageGenre.php?error=' . urlencode("Ce genre existe déjà."));
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO genre (genre) VALUES (:genre)");
            $stmt->execute([':genre' => $genreFormatted]);
            header('Location: ManageGenre.php?success=' . urlencode("Genre ajouté avec succès : " . $genreFormatted));
            exit;
        }

        if ($action === 'update') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $genre = isset($_POST['genre']) ? trim($_POST['genre']) : '';

            if ($id === 0 || $genre === '') {
                header('Location: ManageGenre.php?error=' . urlencode("ID ou genre invalide."));
                exit;
            }

            $genreFormatted = ucwords(strtolower($genre));

            $stmt = $pdo->prepare("UPDATE genre SET genre = :genre WHERE id = :id");
            $stmt->execute([
                ':genre' => $genreFormatted,
                ':id' => $id
            ]);
            header('Location: ManageGenre.php?success=' . urlencode("Genre mis à jour avec succès."));
            exit;
        }

        if ($action === 'delete') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            if ($id === 0) {
                header('Location: ManageGenre.php?error=' . urlencode("ID invalide."));
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM genre WHERE id = :id");
            $stmt->execute([':id' => $id]);
            header('Location: ManageGenre.php?success=' . urlencode("Genre supprimé avec succès."));
            exit;
        }

        header('Location: ManageGenre.php?error=' . urlencode("Action invalide."));
        exit;
    } else {
        header('Location: ManageGenre.php?error=' . urlencode("Méthode non autorisée."));
        exit;
    }
} catch (PDOException $e) {
    error_log("Erreur ManageGenreTraitement: " . $e->getMessage());
    header('Location: ManageGenre.php?error=' . urlencode("Erreur de traitement."));
    exit;
}
