<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';
require_once 'security.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        var_dump($_POST); // DEBUG

        $titre = !empty($_POST['titre']) ? protectionXSS(trim($_POST['titre'])) : null;
        $url_image = !empty($_POST['url_image']) ? protectionXSS(trim($_POST['url_image'])) : null;
        $url_livre = !empty($_POST['url_livre']) ? protectionXSS(trim($_POST['url_livre'])) : null;
        $description = !empty($_POST['description']) ? protectionXSS(trim($_POST['description'])) : null;
        $auteur_id = !empty($_POST['auteur_id']) ? (int)$_POST['auteur_id'] : null;
        $langue_id = !empty($_POST['langue_id']) ? (int)$_POST['langue_id'] : null;
        $genres = isset($_POST['genres']) && is_array($_POST['genres']) ? $_POST['genres'] : [];

        if (!$titre) {
            die("Erreur : Le titre est obligatoire.");
        }

        if (!$langue_id) {
            die("Erreur : La langue est obligatoire.");
        }

        // Vérification chemin relatif image
        if ($url_image && substr($url_image, 0, 7) !== 'images/') {
            $url_image = null;
        }

        // Vérifier que la langue existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM langue WHERE id = ?");
        $stmt->execute([$langue_id]);
        if ($stmt->fetchColumn() == 0) {
            die("Erreur : Langue invalide.");
        }

        $pdo->beginTransaction();

        $sql = "INSERT INTO livre (titre, url_image, url_livre, description, auteur_id, langue_id)
                VALUES (:titre, :url_image, :url_livre, :description, :auteur_id, :langue_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titre' => $titre,
            ':url_image' => $url_image,
            ':url_livre' => $url_livre,
            ':description' => $description,
            ':auteur_id' => $auteur_id,
            ':langue_id' => $langue_id
        ]);

        $livre_id = $pdo->lastInsertId();

        if (!empty($genres)) {
            $sqlGenre = "INSERT INTO livre_genre (livre_id, genre_id) VALUES (:livre_id, :genre_id)";
            $stmtGenre = $pdo->prepare($sqlGenre);

            foreach ($genres as $genre_id) {
                $stmtGenre->execute([
                    ':livre_id' => $livre_id,
                    ':genre_id' => (int)$genre_id
                ]);
            }
        }

        $pdo->commit();

        header("Location: AdminDashboard.php?success=" . urlencode("Livre ajouté avec succès !"));
        exit;

    } else {
        die("Erreur : méthode non autorisée.");
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Erreur PDO : " . $e->getMessage());
}
?>
