<?php
session_start();
require_once 'db.php';
require_once 'security.php';

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['user_id'])) {
    header("Location: Login.php?error=" . urlencode("Connectez-vous pour accéder à votre bibliothèque."));
    exit;
}

$userId = $_SESSION['user_id'];

$filter = $_GET['filter'] ?? 'all';

$query = "
    SELECT l.id, l.titre, l.url_image, l.url_livre, l.description, b.est_favoris, b.finie_lire
    FROM bibliotheque b
    INNER JOIN livre l ON b.id_livre = l.id
    WHERE b.id_utilisateur = :user_id
";

if ($filter === 'favoris') {
    $query .= " AND b.est_favoris = 1";
} elseif ($filter === 'finis') {
    $query .= " AND b.finie_lire = 1";
}

$query .= " ORDER BY b.date_ajout DESC";

$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $userId]);
$livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title> Your Library - BookUnity</title>
    <link rel="stylesheet" href="css/stylesLibrary.css">
    
</head>
<body class="<?= $darkModeClass ?? '' ?>">
    <header class="header">
        <a href="HomeTraitement.php" class="logo-link">
            <div class="logo-circle">B</div>
            <span class="logo-text">BookUnity</span>
        </a>

        <a href="Logout.php" class="main-button">Logout</a>
         <button id="toggle-dark-mode" class="main-button">🌓 Dark Mode</button>
    </header>

    <main class="page-container">
        <h1 class="page-title">📚 Your Library</h1>

        <div class="filter-bar">
            <a href="UserLibrary.php?filter=all" class="filter-link <?= $filter === 'all' ? 'active' : '' ?>">All</a>
            <a href="UserLibrary.php?filter=favoris" class="filter-link <?= $filter === 'favoris' ? 'active' : '' ?>">Favorite</a>
            <a href="UserLibrary.php?filter=finis" class="filter-link <?= $filter === 'finis' ? 'active' : '' ?>">Finished</a>
        </div>



        <?php if (empty($livres)): ?>
            <p> Your library is empty, you have no favorite or finifed novels</p>
        <?php else: ?>
            <div class="books-grid">
                <?php foreach ($livres as $livre): ?>
                    <div class="book-item">
                        <div class="book-title"><?= htmlspecialchars($livre['titre']) ?></div>

                        <?php if (!empty($livre['url_image'])): ?>
                            <img src="<?= htmlspecialchars($livre['url_image']) ?>"
                                 alt="<?= htmlspecialchars($livre['titre']) ?>"
                                 class="book-cover">
                        <?php else: ?>
                            <img src="images/default_cover.jpg" alt="Image indisponible" class="book-cover">
                        <?php endif; ?>

                        <?php if (!empty($livre['description'])): ?>
                            <div class="book-description"><?= nl2br(htmlspecialchars($livre['description'])) ?></div>
                        <?php endif; ?>

                        <?php if (!empty($livre['url_livre'])): ?>
                            <a href="<?= htmlspecialchars($livre['url_livre']) ?>" target="_blank" class="read-button">📖 Read now</a>
                        <?php endif; ?>

                        <div class="action-buttons">
                            <form action="MarkAsRead.php" method="post">
                                <input type="hidden" name="livre_id" value="<?= $livre['id'] ?>">
                             <?php if (!$livre['finie_lire']): ?>
                                 <button type="submit" class="btn btn-finish">✅ Mark as finised</button>
                             <?php else: ?>
                             <button type="submit" class="btn" style="background: #95a5a6; color: white;">↩️ Mark as unfinished</button>
                            <?php endif; ?>
                            </form>

                            <form action="ToggleFavorite.php" method="post">
                            <input type="hidden" name="livre_id" value="<?= $livre['id'] ?>">
                            <button type="submit" class="btn" style="background: <?= $livre['est_favoris'] ? '#f39c12' : '#ccc' ?>;">
                            <?= $livre['est_favoris'] ? '★ Favori' : '☆ Ajouter aux favoris' ?>
                            </button>
                            </form>


                            <form action="RemoveFromLibrary.php" method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer ce livre de votre bibliothèque ?');">
                                <input type="hidden" name="livre_id" value="<?= $livre['id'] ?>">
                                <button type="submit" class="btn btn-delete">🗑️ Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    <script src="dark-mode.js"></script>
</body>
</html>
