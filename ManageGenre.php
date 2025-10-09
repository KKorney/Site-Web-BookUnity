<?php
require_once 'db.php';
require_once 'security.php';

// Activer PDO en mode exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupérer les genres existants
$stmt = $pdo->query("SELECT id, genre FROM genre ORDER BY genre ASC");
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Manage genres</title>
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
    <h2>Manage genres</h2>

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

    <h3>Add a Genre</h3>
    <form action="ManageGenreTraitement.php" method="post">
        <input type="hidden" name="action" value="add">
        <label for="genre">New Genre :</label>
        <input type="text" id="genre" name="genre" placeholder="Ex : Action or High Fantasy" required>
        <input type="submit" value="Ajouter le Genre" class="create-button">
    </form>

    <h3>Existing Genres</h3>
    <table>
        <tr>
            <th>Genre</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($genres as $genre): ?>
            <tr>
                <td>
                    <form action="ManageGenreTraitement.php" method="post" style="display:flex; gap:5px;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $genre['id'] ?>">
                        <input type="text" name="genre" value="<?= htmlspecialchars($genre['genre']) ?>" required style="flex:1;">
                        <button type="submit" class="update-button">Update</button>
                    </form>
                </td>
                <td class="actions">
                    <form action="ManageGenreTraitement.php" method="post" onsubmit="return confirm('Are you sure you want to delete this genre ?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $genre['id'] ?>">
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
