<?php
require_once 'security.php';
require_once 'db.php';
session_start();

// Vérifier connexion utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type'], $_POST['message'])) {
    $type = ($_POST['type'] === 'Request') ? 'Request' : 'Signal';
    $message = trim($_POST['message']);

    if (empty($message)) {
        $error = "Please enter a message.";
    } else {
        // Vérifier le nombre de messages non résolus de ce type
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM message 
            WHERE id_utilisateur = :user_id AND type = :type AND etat = 0
        ");
        $stmt->execute(['user_id' => $user_id, 'type' => $type]);
        $count = $stmt->fetchColumn();

        if ($count >= 3) {
            $error = "You already have 3 unresolved '$type' messages. Please wait until they are resolved before sending new ones.";
        } else {
            // Insérer dans la table message
            $stmt = $pdo->prepare("
                INSERT INTO message (id_utilisateur, type) 
                VALUES (:user_id, :type)
            ");
            $stmt->execute([
                'user_id' => $user_id,
                'type' => $type
            ]);
            $message_id = $pdo->lastInsertId();

            // Insérer dans la table user_message
            $stmt = $pdo->prepare("
                INSERT INTO user_message (message_id, id_utilisateur, message) 
                VALUES (:message_id, :user_id, :message)
            ");
            $stmt->execute([
                'message_id' => $message_id,
                'user_id' => $user_id,
                'message' => protectionXSS($message)
            ]);

            $success = "Your '$type' message has been sent successfully.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support - BookUnity</title>
    <link rel="stylesheet" href="css/stylesSupport.css">
    
</head>

<body class="<?= $darkModeClass ?? '' ?>">
<header class="header">
    <a href="HomeTraitement.php" class="logo-link">
        <div class="logo-circle">B</div>
        <span class="logo-text">BookUnity</span>
    </a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="UserLibrary.php" class="action-button">📚 Library</a>
        <a href="Logout.php" class="main-button">Logout</a>
    <?php else: ?>
        <a href="Login.php" class="main-button">Login</a>
    <?php endif; ?>

    
    <button id="toggle-dark-mode" class="main-button">🌓 Dark Mode</button>

</header>

<main>
    <div class="support-container">
        <h2>Support for a problem or Request for a novel</h2>
        <p>If you want a novel request, please include in your message the title of the novel and the URL link of the website where this novel can be found.</p>

        <?php if ($error): ?>
            <div class="message-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="type">Type of request:</label>
            <select name="type" id="type" required>
                <option value="Request">Request</option>
                <option value="Signal">Signal</option>
            </select>

            <label for="message">Your message:</label>
            <textarea name="message" id="message" required></textarea>

            <button type="submit" class="action-button">Send</button>
        </form>
    </div>
</main>
<script src="dark-mode.js"></script>
</body>
</html>
