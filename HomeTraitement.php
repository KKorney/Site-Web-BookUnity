<?php
require_once 'db.php';

// Récupérer les paramètres
$search = $_GET['search'] ?? '';
$selectedLangue = $_GET['langue'] ?? null;
$selectedGenres = $_GET['genres'] ?? [];

if (!is_array($selectedGenres)) {
    $selectedGenres = [$selectedGenres];
}

// Récupérer langues et genres pour les filtres
try {
    $stmt = $pdo->query("SELECT DISTINCT langue FROM langue");
    $langues = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->query("SELECT * FROM genre");
    $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur lors de la récupération des filtres : " . $e->getMessage());
}

// Construire la requête principale avec recherche + filtres
$query = "
    SELECT DISTINCT l.*, a.nom AS auteur
    FROM livre l
    LEFT JOIN langue lang ON l.langue_id = lang.id
    LEFT JOIN livre_genre lg ON lg.livre_id = l.id
    LEFT JOIN auteur a ON l.auteur_id = a.id
";

$conditions = [];
$params = [];

// Recherche sur le titre
if ($search !== '') {
    $conditions[] = "l.titre LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

// Filtre langue
if ($selectedLangue) {
    $conditions[] = "lang.langue = :langue";
    $params[':langue'] = $selectedLangue;
}

// Filtre genres avec paramètres nommés dynamiques
if (!empty($selectedGenres)) {
    $placeholders = [];
    foreach ($selectedGenres as $index => $genreId) {
        $key = ":genre" . $index;
        $placeholders[] = $key;
        $params[$key] = $genreId;
    }
    $conditions[] = "lg.genre_id IN (" . implode(',', $placeholders) . ")";
}

// Ajouter WHERE si conditions présentes
if ($conditions) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

// Préparer et exécuter
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$livres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les genres pour chaque livre affiché
$livreIds = array_column($livres, 'id');

$genresParLivre = [];
if (!empty($livreIds)) {
    // Préparer une requête pour récupérer genres par livre
    $placeholders = implode(',', array_fill(0, count($livreIds), '?'));

    $sqlGenres = "
        SELECT lg.livre_id, g.genre 
        FROM livre_genre lg
        JOIN genre g ON lg.genre_id = g.id
        WHERE lg.livre_id IN ($placeholders)
    ";
    $stmtGenres = $pdo->prepare($sqlGenres);
    $stmtGenres->execute($livreIds);
    $genresResults = $stmtGenres->fetchAll(PDO::FETCH_ASSOC);

    foreach ($genresResults as $row) {
        $genresParLivre[$row['livre_id']][] = $row['genre'];
    }
}

include 'Home.php';
