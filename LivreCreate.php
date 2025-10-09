<?php
require_once 'db.php';

// Récupérer les genres existants
$stmt = $pdo->query("SELECT id, genre FROM genre ORDER BY genre ASC");
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les auteurs existants
$stmt = $pdo->query("SELECT id, nom FROM auteur ORDER BY nom ASC");
$auteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les langues existantes
$stmt = $pdo->query("SELECT id, langue FROM langue ORDER BY langue ASC");
$langues = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Add a novel</title>
     <link rel="stylesheet" href="css/stylesAdmin.css">

    
</head>
<body>
<header>
 <a href="AdminDashboard.php" class="logo-link">
        <div class="logo-circle">B</div>
        <span class="logo-text">BookUnity Admin</span>
    </a>
    <div>
    <button id="toggle-dark-mode" class="main-button">🌓 Dark Mode</button>
    </div>

</header>

<div class="container">
    <h2>Add a novel</h2>
    <form action="LivreCreateTraitement.php" method="post">

            <label for="titre">Titre:</label>
            <input type="text" id="titre" name="titre" required placeholder="Ex : Ascension of Chaos">
            <label for="url_image">Image path:</label>
            <input type="text" id="url_image" name="url_image" placeholder="Ex : images/livre.png">
            <label for="url_livre">URL of the novel:</label>
            <input type="text" id="url_livre" name="url_livre" placeholder="Ex : https://">
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" placeholder="Description of the novel"></textarea>


        <label>Genres :</label>
        <div class="checkbox-group">
            <?php foreach ($genres as $genre): ?>
                <div class="checkbox-item">
                    <input type="checkbox" name="genres[]" value="<?= htmlspecialchars($genre['id']) ?>" id="genre_<?= $genre['id'] ?>">
                    <label for="genre_<?= $genre['id'] ?>"><?= htmlspecialchars($genre['genre']) ?></label>
                </div>
            <?php endforeach; ?>
        </div>


         <div>
        <label for="langue_id">Language :</label>
        <select id="langue_id" name="langue_id" required>
         <option value="">-- Select novel's language --</option>
        <?php foreach ($langues as $langue): ?>
        <option value="<?= $langue['id'] ?>"><?= htmlspecialchars($langue['langue']) ?></option>
        <?php endforeach; ?>
        </select>




        <label for="auteur_id">Author :</label>
        <select id="auteur_id" name="auteur_id">
            <option value="">-- Select novel's author --</option>
            <?php foreach ($auteurs as $auteur): ?>
                <option value="<?= htmlspecialchars($auteur['id']) ?>"><?= htmlspecialchars($auteur['nom']) ?></option>
            <?php endforeach; ?>
        </select>

          </div>

        <input type="submit" value="Add a novel" class="create-button">
    </form>
</div>

<?php if (!empty($_GET['error'])): ?>
  <div style="color: red; border: 1px solid red; padding: 5px; margin-bottom: 10px;">
    <?= htmlspecialchars($_GET['error']) ?>
  </div>
<?php endif; ?>

<script src="dark-mode.js"></script>
</body>
</html>
