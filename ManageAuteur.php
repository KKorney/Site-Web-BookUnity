<?php
require_once 'db.php';
require_once 'security.php';

// Activer PDO en mode exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupérer les auteurs existants
$stmt = $pdo->query("SELECT id, nom FROM auteur ORDER BY nom ASC");
$auteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Manage authors</title>
    <link rel="stylesheet" href="css/stylesManage.css">
   
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
    <h2>Manage authors</h2>

    <?php if (!empty($_GET['success'])): ?>
        <div style="color: green; border: 1px solid green; padding: 5px; margin-top: 10px;">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['error'])): ?>
        <div style="color: red; border: 1px solid red; padding: 5px; margin-top: 10px;">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <h3>Add an author</h3>
    <form action="ManageAuteurTraitement.php" method="post">
        <input type="hidden" name="action" value="add">
        <label for="nom">Author's name :</label>
        <input type="text" id="nom" name="nom" placeholder="Ex : Brandon Sanderson" required>
        <input type="submit" value="Add an autheur" class="create-button">
    </form>

    <h3>Existing authors</h3>
    <table>
        <tr>
            <th>Author</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($auteurs as $auteur): ?>
            <tr>
                <td>
                    <form action="ManageAuteurTraitement.php" method="post" style="display:flex; gap:5px;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $auteur['id'] ?>">
                        <input type="text" name="nom" value="<?= htmlspecialchars($auteur['nom']) ?>" required style="flex:1;">
                        <button type="submit" class="update-button">Update</button>
                    </form>
                </td>
                <td class="actions">
                    <form action="ManageAuteurTraitement.php" method="post" onsubmit="return confirm('Are you suren that you want to delete this autheur ?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $auteur['id'] ?>">
                        <button type="submit" class="delete-button">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<script src="dark-mode.js"></script>
</body>
</html>
