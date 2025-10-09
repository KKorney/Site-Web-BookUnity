<?php
require_once 'security.php';
require_once 'db.php';
session_start();

function redirectWithError(string $message): void {
    header("Location: Login.php?error=" . urlencode($message));
    exit;
}

if (!empty($_POST['email']) && !empty($_POST['mot_pass'])) {

    $email = protectionXSS($_POST['email']);
    $password = $_POST['mot_pass'];

    $stmt = $pdo->prepare("SELECT id, email, mot_pass, role FROM person WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['mot_pass'])) {

            if ($user['role'] === 'admin') {
                $_SESSION['user_id'] = $user['id']; // L'admin utilisera son ID dans `person`
            } else {
                $stmtUtil = $pdo->prepare("SELECT id FROM utilisateur WHERE person_id = :person_id");
                $stmtUtil->execute(['person_id' => $user['id']]);
                $utilisateur = $stmtUtil->fetch(PDO::FETCH_ASSOC);

                if (!$utilisateur) {
                    redirectWithError("No match user found.");
                }

                $_SESSION['user_id'] = $utilisateur['id'];
            }

            // Déplacer ici pour que ce soit défini pour tous les rôles
            $_SESSION['email'] = $user['email']; 
            $_SESSION['role'] = $user['role'];  

            if ($user['role'] === 'admin') {
                header("Location: AdminDashboard.php");
            } else {
                header("Location: HomeTraitement.php");
            }
            exit;

        } else {
            redirectWithError("Incorrect password.");
        }
    } else {
        redirectWithError("Email not found.");
    }
} else {
    redirectWithError("All fields are required.");
}
