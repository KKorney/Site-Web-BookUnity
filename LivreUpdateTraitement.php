<?php
require_once 'db.php';
require_once 'security.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $titre = protectionXSS($_POST['titre']);
    $url_image = protectionXSS($_POST['url_image']);
    $url_livre = protectionXSS($_POST['url_livre']);
    $description = protectionXSS($_POST['description']);
    $langue_id = (int)$_POST['langue_id'];
    $auteur_id = !empty($_POST['auteur_id']) ? (int)$_POST['auteur_id'] : null;
    $genres = !empty($_POST['genres']) ? $_POST['genres'] : [];

    try {
        // Mettre à jour le livre
        $stmt = $pdo->prepare("UPDATE livre SET titre = :titre, url_image = :url_image, url_livre = :url_livre, description = :description, langue_id = :langue_id, auteur_id = :auteur_id WHERE id = :id");
        $stmt->execute([
            ':titre' => $titre,
            ':url_image' => $url_image,
            ':url_livre' => $url_livre,
            ':description' => $description,
            ':langue_id' => $langue_id,
            ':auteur_id' => $auteur_id,
            ':id' => $id
        ]);

        // Mettre à jour les genres liés
        $pdo->prepare("DELETE FROM livre_genre WHERE livre_id = :livre_id")->execute([':livre_id' => $id]);

        foreach ($genres as $genre_id) {
            $pdo->prepare("INSERT INTO livre_genre (livre_id, genre_id) VALUES (:livre_id, :genre_id)")
                ->execute([':livre_id' => $id, ':genre_id' => $genre_id]);
        }

        header("Location: AdminDashboard.php?success=" . urlencode("Book updated successfully."));
        exit;

    } catch (PDOException $e) {
        error_log("Error during update: " . $e->getMessage());
        header("Location: AdminDashboard.php?error=" . urlencode("Error during update, please try again."));
        exit;
    }
} else {
    header("Location: AdminDashboard.php?error=" . urlencode("Method not allowed."));
    exit;
}
