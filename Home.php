<?php
require_once 'security.php';
require_once 'db.php';
session_start();

// Vérifier connexion
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// Récupérer les livres suivis si connecté
$followed_books = [];
if ($isLoggedIn) {
    $stmt = $pdo->prepare("SELECT id_livre FROM bibliotheque WHERE id_utilisateur = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $followed_books = $stmt->fetchAll(PDO::FETCH_COLUMN);
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BookUnity - Book List</title>
    <link rel="stylesheet" href="css/stylesHome.css">
   
</head>


<body class="<?= $darkModeClass ?? '' ?>">
    <header class="header">
    <a href="HomeTraitement.php" class="logo-link">
        <div class="logo-circle" >B</div>
        <span class="logo-text">BookUnity</span>
    </a>

    <?php if ($isLoggedIn): ?>
        <a href="UserLibrary.php" class="action-button">📚 Library</a>
        <a href="Logout.php" class="main-button">Logout</a>
        <a href="Support.php" class="main-button"> Support</a>
        <a href="UserProfile.php" class="main-button"> Profile</a>
    <?php else: ?>
        <a href="Login.php" class="main-button">Login</a>
    <?php endif; ?>

    <button id="toggle-dark-mode" class="main-button">🌓 Dark Mode</button>




</header>

<main class="page-container">
    <h1 class="page-title">📚 Novels list</h1>

    <!-- Barre de recherche -->
    <form method="GET" action="HomeTraitement.php" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Rechercher un livre par titre"
               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
        <button type="submit">Research</button>
    </form>

    <!-- Filtres -->
    <form method="get" action="HomeTraitement.php" class="filter-form">
        <label for="langue" class="filter-label">Language:</label>
        <select name="langue" id="langue" class="filter-select">
            <option value="">-- All --</option>
            <?php foreach ($langues as $langue): ?>
                <option value="<?= htmlspecialchars($langue) ?>" <?= ($selectedLangue === $langue) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($langue) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="filter-genres">
            <span class="filter-label">Genres:</span>
            <div class="genres-container">
                <?php foreach ($genres as $genre): ?>
                    <label class="genre-item">
                        <input type="checkbox" name="genres[]" value="<?= $genre['id'] ?>"
                            <?= in_array($genre['id'], $selectedGenres) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($genre['genre']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="filter-button">Filter</button>
    </form>

    <?php if (empty($livres)): ?>
        <p>No books found for the selected filters.</p>
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
                        <img src="images/default_cover.jpg" alt="Image unavailable" class="book-cover">
                    <?php endif; ?>

                    <?php if (!empty($livre['auteur'])): ?>
                        <div class="book-author">by <?= htmlspecialchars($livre['auteur']) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($genresParLivre[$livre['id']])): ?>
                        <div class="book-genres" style="margin-bottom: 10px;">Genres: <?= htmlspecialchars(implode(', ', $genresParLivre[$livre['id']])) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($livre['description'])): ?>
                        <div class="book-description"><?= nl2br(htmlspecialchars($livre['description'])) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($livre['url_livre'])): ?>
                        <a href="<?= htmlspecialchars($livre['url_livre']) ?>" target="_blank" class="read-button">Read Now</a>
                    <?php endif; ?>

                    <?php if ($isLoggedIn): ?>
                        <?php if (in_array($livre['id'], $followed_books)): ?>
                            <button class="action-button" disabled>✅ Followed</button>
                        <?php else: ?>
                            <form action="AddToLibrary.php" method="post">
                                <input type="hidden" name="livre_id" value="<?= $livre['id'] ?>">
                                <button type="submit" class="action-button">➕ Follow</button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <p style="color: red; font-size: 0.9em;">Login to add this novel to your library</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<script src="dark-mode.js"></script>
</body>
</html>
