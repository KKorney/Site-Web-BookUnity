<?php
session_start();
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="css/stylesRegister.css">
</head>
<body class="<?= $darkModeClass ?? '' ?>">
<header>



    <button id="toggle-dark-mode" class="main-button">🌓 Dark Mode</button>
</header>



    <main>
        <section class="bordure">
            <h2>Form for inscription</h2>

                <?php if (!empty($error)): ?>
                <div style="color: red;">
                <p><?= htmlspecialchars($error); ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                <div style="color: green;">
                <p><?= htmlspecialchars($success); ?></p>
                </div>
                <?php endif; ?>

            <!-- Affichage du message de confirmation UNIQUEMENT si aucune erreur -->
                <?php if (empty($errors) && !empty($userData['email'])): ?>
                <div style="color: green;">
                <p>Thank you, <?= htmlspecialchars($userData['email']); ?> Your account was succesfully created </p>
                
                </div>
                    <?php endif; ?>

            <!-- Formulaire -->
            <form action="RegisterTraitement.php" method="post"> 
               
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="example@gmail.com" class="input-style block" >

            

                <input type="password" id="mot_pass" name="mot_pass" placeholder="Write your password" class="input-style block" >
                <input type="password" id="motDePassConfirmation" name="motDePassConfirmation" placeholder="Confirm your password" class="input-style block" >


                <label for="nom">Name:</label>
                <input type="text" id="nom" name="nom" placeholder="Your username" class="input-style block" required>

                <label for="age">Age:</label>
                <select id="age" name="age" class="input-style block" required>
                <option value="">-- Select your age --</option>
                <?php for ($i = 13; $i <= 100; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?> years</option>
                <?php endfor; ?>
                </select>


                <input type="submit" value="Submit">
            </form>


          
        </section>
    </main>


<script src="dark-mode.js"></script>
</body>

</html>