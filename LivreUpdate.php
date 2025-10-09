<?php
require_once 'db.php';
require_once 'security.php';

// Activer PDO exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vérifier si le titre est fourni
if (empty($_GET['titre_update'])) {
    die("Non title gieven");
}

$titre_recherche = protectionXSS($_GET['titre_update']);

// Récupérer le livre correspondant
$stmt = $pdo->prepare("SELECT * FROM livre WHERE titre = :titre LIMIT 1");
$stmt->execute([':titre' => $titre_recherche]);
$livre = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$livre) {
    die("Novel not found");
}

// Récupérer les genres
$stmt = $pdo->query("SELECT id, genre FROM genre ORDER BY genre ASC");
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les genres liés au livre
$stmt = $pdo->prepare("SELECT genre_id FROM livre_genre WHERE livre_id = :livre_id");
$stmt->execute([':livre_id' => $livre['id']]);
$genres_livre = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Récupérer les auteurs
$stmt = $pdo->query("SELECT id, nom FROM auteur ORDER BY nom ASC");
$auteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les langues
$stmt = $pdo->query("SELECT id, langue FROM langue ORDER BY langue ASC");
$langues = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mettre à jour le livre</title>
    <link rel="stylesheet" href="css/stylesAdmin.css">
   
</head>
<body>

<header>

 <a href="AdminDashboard.php" class="logo-link">
        <div class="logo-circle">B</div>
        <span class="logo-text">BookUnity Admin</span>
    </a>


</header>

<div class="container">
    <h2>Update of the novel</h2>
    <form action="LivreUpdateTraitement.php" method="post">
        <input type="hidden" name="id" value="<?= $livre['id'] ?>">

        <table>
            <tr>
                <th>Titre</th>
                <th>Image</th>
                <th>URL</th>
                <th>Description</th>
            </tr>
            <tr>
                <td><input type="text" name="titre" value="<?= htmlspecialchars($livre['titre']) ?>" required style="width: 100%;"></td>
                <td><input type="text" name="url_image" value="<?= htmlspecialchars($livre['url_image']) ?>" style="width: 100%;"></td>
                <td><input type="text" name="url_livre" value="<?= htmlspecialchars($livre['url_livre']) ?>" style="width: 100%;"></td>
                <td><textarea name="description" rows="5" style="width: 100%;"><?= htmlspecialchars($livre['description']) ?></textarea></td>
            </tr>
        </table>

        <label>Genres liés :</label>
        <div class="checkbox-group">
            <?php foreach ($genres as $genre): ?>
                <div class="checkbox-item">
                    <input type="checkbox" name="genres[]" value="<?= $genre['id'] ?>"
                        <?= in_array($genre['id'], $genres_livre) ? 'checked' : '' ?>
                        id="genre_<?= $genre['id'] ?>">
                    <label for="genre_<?= $genre['id'] ?>"><?= htmlspecialchars($genre['genre']) ?></label>
                </div>
            <?php endforeach; ?>
        </div>

        <label for="langue_id">Language :</label>
        <select id="langue_id" name="langue_id" required>
            <?php foreach ($langues as $langue): ?>
                <option value="<?= $langue['id'] ?>" <?= $langue['id'] == $livre['langue_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($langue['langue']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="auteur_id">Autheur :</label>
        <select id="auteur_id" name="auteur_id">
            <option value="">-- None --</option>
            <?php foreach ($auteurs as $auteur): ?>
                <option value="<?= $auteur['id'] ?>" <?= $auteur['id'] == $livre['auteur_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($auteur['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="update-button">Update the novel</button>
    </form>
</div>

</body>
</html>
