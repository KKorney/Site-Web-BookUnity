<?php
session_start();
require_once 'security.php';
require_once 'db.php';

// Activer PDO pour lever les erreurs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fonction de redirection avec erreur
function redirectWithError(string $message): void {
    header("Location: Register.php?error=" . urlencode($message));
    exit;
}

// Nettoyage
$userData = [];

// Vérification Email
if (!empty($_POST['email'])) {
    if (emailValidate($_POST['email'])) {
        $email = protectionXSS($_POST['email']);

        // Vérifier si déjà pris
        $sql = "SELECT email FROM person WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingUser) {
            $userData['email'] = $email;
        } else {
            redirectWithError("Cet email existe déjà.");
        }
    } else {
        redirectWithError("Format d'email invalide.");
    }
} else {
    redirectWithError("L'email est obligatoire.");
}

// Vérification mot de passe
if (!empty($_POST['mot_pass']) && !empty($_POST['motDePassConfirmation'])) {
    if ($_POST['mot_pass'] === $_POST['motDePassConfirmation']) {
        $password = $_POST['mot_pass'];
        $hash = password_hash($password, PASSWORD_DEFAULT);
    } else {
        redirectWithError("Les mots de passe ne correspondent pas.");
    }
} else {
    redirectWithError("Le mot de passe et sa confirmation sont obligatoires.");
}

// Vérification Nom
if (!empty($_POST['nom'])) {
    $nom = protectionXSS($_POST['nom']);
} else {
    redirectWithError("Le nom est obligatoire.");
}

// Vérification Âge
if (!empty($_POST['age']) && is_numeric($_POST['age']) && (int)$_POST['age'] > 0) {
    $age = (int)$_POST['age'];
} else {
    redirectWithError("Un âge valide est obligatoire.");
}

// Transaction pour insertion
try {
    $pdo->beginTransaction();

    // Insertion dans person
    $sqlPerson = "INSERT INTO person (email, mot_pass) VALUES (:email, :mot_pass)";
    $stmtPerson = $pdo->prepare($sqlPerson);
    $stmtPerson->execute([
        'email' => $userData['email'],
        'mot_pass' => $hash
    ]);

    $personId = $pdo->lastInsertId();

    // Insertion dans utilisateurs
    $sqlUser = "INSERT INTO utilisateur (nom, age, person_id) VALUES (:nom, :age, :person_id)";
    $stmtUser = $pdo->prepare($sqlUser);
    $stmtUser->execute([
        'nom' => $nom,
        'age' => $age,
        'person_id' => $personId
    ]);

    $pdo->commit();

    header("Location: Login.php?success=" . urlencode("Inscription réussie, vous pouvez vous connecter."));
    exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Erreur BookUnity RegisterTraitement: " . $e->getMessage());
    redirectWithError("Erreur lors de l'inscription, veuillez réessayer.");
}
?>
