<?php
require_once 'security.php';
require_once 'db.php';
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: Login.php');
    exit;
}

$email = $_SESSION['email'];

// Récupérer l'id dans person via l'email
$sqlPerson = "SELECT id, email, mot_pass FROM person WHERE email = :email AND role = 'user'";
$stmtPerson = $pdo->prepare($sqlPerson);
$stmtPerson->execute(['email' => $email]);
$person = $stmtPerson->fetch(PDO::FETCH_ASSOC);

if (!$person) {
    echo "User not found or you are admin (admin is not editable here).";
    exit;
}

$userId = $person['id'];

// Récupérer les infos dans utilisateur
$sqlUser = "SELECT * FROM utilisateur WHERE person_id = :person_id";
$stmtUser = $pdo->prepare($sqlUser);
$stmtUser->execute(['person_id' => $userId]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Profile entry in 'utilisateur' not found for this user.";
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim($_POST['nom'] ?? '');
    $newAge = intval($_POST['age'] ?? -1);

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($newName) || $newAge < 0) {
        $error = "Please enter a valid name and a non-negative age.";
    } else {
        // Update nom et age
        $updateStmt = $pdo->prepare("UPDATE utilisateur SET nom = :nom, age = :age WHERE person_id = :person_id");
        $updateStmt->execute([
            'nom' => $newName,
            'age' => $newAge,
            'person_id' => $userId
        ]);
        $success = "Profile updated successfully.";

        // Gestion changement de mot de passe si demandé
        if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $error = "To change your password, fill in all password fields.";
            } elseif (!password_verify($currentPassword, $person['mot_pass'])) {
                $error = "The current password is incorrect.";
            } elseif ($newPassword !== $confirmPassword) {
                $error = "The new passwords do not match.";
            } elseif (strlen($newPassword) < 6) {
                $error = "The new password must be at least 6 characters.";
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updatePassStmt = $pdo->prepare("UPDATE person SET mot_pass = :mot_pass WHERE id = :id");
                $updatePassStmt->execute([
                    'mot_pass' => $hashedPassword,
                    'id' => $userId
                ]);
                $success = "Profile and password updated successfully.";
            }
        }
    }

    // Refresh user data after update
    $stmtUser->execute(['person_id' => $userId]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile - BookUnity</title>
    <link rel="stylesheet" href="css/stylesProfile.css">
    
</head>

<header class="header">
    <a href="HomeTraitement.php" class="logo-link">
        <div class="logo-circle">B</div>
        <span class="logo-text">BookUnity</span>
    </a>
    <a href="Logout.php" class="main-button">Logout</a>
    <button id="toggle-dark-mode" class="main-button">🌓 Dark Mode</button>
</header>

<main>
    <form class="profile-form" method="post">
        <h2>📝 Update Profile</h2>

        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <label for="nom">Name:</label>
        <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>

        <label for="age">Age:</label>
        <input type="number" name="age" id="age" value="<?= htmlspecialchars($user['age']) ?>" min="0" required>

        <h3 style="margin-top:20px;">🔑 Change Password</h3>
        <label for="current_password">Current Password:</label>
        <input type="password" name="current_password" id="current_password">

        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password">

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" id="confirm_password">

        <button type="submit">Update Profile</button>
    </form>
</main>
<script src="dark-mode.js"></script>
</body>
</html>
