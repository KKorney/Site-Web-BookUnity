<?php
require_once 'db.php';
require_once 'security.php';

// Activer PDO en mode exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupérer les langues existantes
$stmt = $pdo->query("SELECT id, langue FROM langue ORDER BY langue ASC");
$langues = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Manage Languages</title>
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
    <h2>Manage languages</h2>

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

    <h3>Add a language</h3>
    <form action="ManageLangueTraitement.php" method="post">
        <input type="hidden" name="action" value="add">
        <label for="langue">Langue :</label>
        <input type="text" id="langue" name="langue" placeholder="Ex : Français" required>
        <input type="submit" value="Ajouter la Langue" class="create-button">
    </form>

    <h3>Existing Languages</h3>
    <table>
        <tr>
            <th>Language</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($langues as $langue): ?>
            <tr>
                <td>
                    <form action="ManageLangueTraitement.php" method="post" style="display:flex; gap:5px;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= $langue['id'] ?>">
                        <input type="text" name="langue" value="<?= htmlspecialchars($langue['langue']) ?>" required style="flex:1;">
                        <button type="submit" class="update-button">Update</button>
                    </form>
                </td>
                <td class="actions">
                    <form action="ManageLangueTraitement.php" method="post" onsubmit="return confirm('Are you sure that you want to delete this language?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $langue['id'] ?>">
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
