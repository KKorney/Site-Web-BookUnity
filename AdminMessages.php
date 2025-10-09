<?php
require_once 'security.php';
require_once 'db.php';
session_start();

// Vérifier que l'admin est connecté
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: Login.php');
    exit;
}

// Récupérer filtres
$typeFilter = isset($_GET['type']) && in_array($_GET['type'], ['Request', 'Signal']) ? $_GET['type'] : '';
$etatFilter = isset($_GET['etat']) && in_array($_GET['etat'], ['0', '1']) ? $_GET['etat'] : '';

// Préparer la requête SQL dynamique avec jointure sur `message`
$sql = "
    SELECT um.id AS user_message_id, um.message AS user_message_content, um.created_at AS user_message_date,
           m.id AS message_id, m.type, m.etat, m.created_at AS message_date,
           u.nom, p.email
    FROM user_message um
    JOIN message m ON um.message_id = m.id
    JOIN utilisateur u ON um.id_utilisateur = u.id
    JOIN person p ON u.person_id = p.id
    WHERE 1
";

$params = [];

if ($typeFilter) {
    $sql .= " AND m.type = :type ";
    $params['type'] = $typeFilter;
}

if ($etatFilter !== '') {
    $sql .= " AND m.etat = :etat ";
    $params['etat'] = $etatFilter;
}

$sql .= " ORDER BY um.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - User Messages</title>
    <link rel="stylesheet" href="css/stylesAdmin.css">
    
</head>
<body>
<header class="header">
    <a href="AdminDashboard.php" class="logo-link">
        <div class="logo-circle">B</div>
        <span class="logo-text">BookUnity Admin</span>
    </a>
    

    <a href="Logout.php" class="main-button">Logout</a>

 <div>
    <button id="toggle-dark-mode" class="main-button">🌓 Dark Mode</button>
    </div>


</header>

<main>
    <h1 style="text-align:center;">📩 User Messages Management</h1>

    <form method="get" class="filter-form">
        <label for="type">Filter by Type:</label>
        <select name="type" id="type">
            <option value="">All</option>
            <option value="Request" <?= $typeFilter === 'Request' ? 'selected' : '' ?>>Request</option>
            <option value="Signal" <?= $typeFilter === 'Signal' ? 'selected' : '' ?>>Signal</option>
        </select>

        <label for="etat">Filter by Status:</label>
        <select name="etat" id="etat">
            <option value="">All</option>
            <option value="0" <?= $etatFilter === '0' ? 'selected' : '' ?>>Unresolved</option>
            <option value="1" <?= $etatFilter === '1' ? 'selected' : '' ?>>Resolved</option>
        </select>

        <button type="submit">Filter</button>
    </form>

    <?php if (empty($messages)): ?>
        <p style="text-align:center;">No messages found with the selected filters.</p>
    <?php else: ?>
        <table class="message-table">
            <thead>
                <tr>
                    <th>Message ID</th>
                    <th>User Email</th>
                    <th>User Name</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?= htmlspecialchars($msg['message_id']) ?></td>
                        <td><?= htmlspecialchars($msg['email']) ?></td>
                        <td><?= htmlspecialchars($msg['nom']) ?></td>
                        <td><?= htmlspecialchars($msg['type']) ?></td>
                        <td><?= htmlspecialchars($msg['user_message_date']) ?></td>
                        <td><?= nl2br(htmlspecialchars($msg['user_message_content'])) ?></td>
                        <td class="<?= $msg['etat'] ? 'resolved' : 'unresolved' ?>">
                            <?= $msg['etat'] ? 'Resolved' : 'Unresolved' ?>
                        </td>
                        <td>
                            <?php if (!$msg['etat']): ?>
                                <form method="get" action="AdminReply.php" style="margin:0;">
                                    <input type="hidden" name="message_id" value="<?= htmlspecialchars($msg['message_id']) ?>">
                                    <button type="submit" class="respond-button">Respond</button>
                                </form>
                            <?php else: ?>
                                <span style="color:gray;">No Action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
<script src="dark-mode.js"></script>
</body>
</html>
