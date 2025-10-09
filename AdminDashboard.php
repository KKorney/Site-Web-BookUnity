<?php
session_start();
require_once 'db.php';



// Récupération des livres avec jointures pour affichage
$search = $_GET['search'] ?? '';

try {
    $query = "
        SELECT l.id, l.titre, l.url_image, l.url_livre, a.nom AS auteur, lang.langue,
       GROUP_CONCAT(g.genre SEPARATOR ', ') AS genres
        FROM livre l
        LEFT JOIN auteur a ON l.auteur_id = a.id
        LEFT JOIN langue lang ON l.langue_id = lang.id
        LEFT JOIN livre_genre lg ON lg.livre_id = l.id
        LEFT JOIN genre g ON lg.genre_id = g.id
    ";

    $params = [];
    if ($search !== '') {
        $query .= " WHERE l.titre LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }
    $query .= " GROUP BY l.id ORDER BY l.titre ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des livres : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - BookUnity</title>
    <link rel="stylesheet" href="css/stylesAdmin.css">

</head>
<body class="<?= $darkModeClass ?? '' ?>">
<header>
    <h1>Admin Dashboard - BookUnity</h1>
     <a href="AdminDashboard.php" class="logo-link">
        <div class="logo-circle">B</div>
        <span class="logo-text">BookUnity Admin</span>
    </a>

    <a href="Logout.php" class="main-button">Logout</a>

    <div>
        <a href="AdminMessages.php" class="main-button"> Messages</a>
    </div>
  
    <div>
    <button id="toggle-dark-mode" class="main-button">🌓 Dark Mode</button>
    </div>

</header>

    <div class="container">
        
        

        <!-- SearchBar -->
        <form method="get" style="margin-bottom: 10px;">
            <input type="text" name="search" placeholder="Research by title..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="manage-button">Research</button>
        </form>

        <!-- Action buttons -->
        <div class="actions">
            <a href="LivreCreate.php" class="manage-button">➕ Add a Novel</a>
            <a href="ManageGenre.php" class="manage-button">➕ Manage Genres</a>
            <a href="ManageAuteur" class="manage-button">➕ Manage Authors</a>
            <a href="ManageLangue.php" class="manage-button">➕ Manage Language</a>
        </div>

        <!-- Delete Book -->
        <form method="post" action="DeleteBookTraitement.php" onsubmit="return confirm('Are you sure that you want to delete this book ? Title of the novel should be exact ');">
            <input type="text" name="titre_supprimer" placeholder="Exact title is needed to delete">
            <button type="submit">🗑️ DELETE BOOK</button>
        </form>

        <!-- Update Book -->
        <form method="get" action="LivreUpdate.php">
            <input type="text" name="titre_update" placeholder="Title to modifie">
            <button type="submit">✏️ UPDATE BOOK</button>
        </form>

        <!-- Delete User -->
        <form method="post" action="DeleteUserTraitement.php" onsubmit="return confirm('Are you sure that you want to delete this user ?');">
            <input type="text" name="email_user_supprimer" placeholder="Email of user you want to delete">
            <button type="submit">🗑️ DELETE USER</button>
        </form>

        <!-- Liste des livres -->
        <table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Autheur</th>
            <th>Language</th>
            <th>Genres</th>
            <th style="max-width: 150px;">Image Path</th>
            <th style="max-width: 150px;">Novel URL</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($livres): ?>
            <?php foreach ($livres as $livre): ?>
                <tr>
                    <td><?= htmlspecialchars($livre['titre']) ?></td>
                    <td><?= htmlspecialchars($livre['auteur'] ?? 'Inconnu') ?></td>
                    <td><?= htmlspecialchars($livre['langue'] ?? 'Inconnue') ?></td>
                    <td><?= htmlspecialchars($livre['genres'] ?? 'Aucun') ?></td>
                    <td style="max-width: 150px; overflow-wrap: break-word; font-size: 0.8em;">
                        <?= htmlspecialchars($livre['url_image'] ?? 'Non spécifié') ?>
                    </td>
                    <td style="max-width: 150px; overflow-wrap: break-word; font-size: 0.8em;">
                        <?= htmlspecialchars($livre['url_livre'] ?? 'Non spécifié') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center;">No novel found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
    </div>
<script src="dark-mode.js"></script>
</body>
</html>
