<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BookUnity</title>
    <link rel="stylesheet" href="css/stylesLogin.css">
    
</head>
<body class="<?= $darkModeClass ?? '' ?>">
<header>
    <button id="toggle-dark-mode" class="main-button">🌓 Dark Mode</button>


</header>

<main class="page-container">
    <section class="bordure">
        <h2>Login</h2>

        <?php if (!empty($_GET['error'])): ?>
            <div style="color: red; margin-bottom: 1rem;">
                <?= htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form action="LoginTraitement.php" method="post" class="filter-form" style="max-width: 400px; margin: 0 auto;">
            <label for="email" class="filter-label">Email:</label>
            <input type="email" id="email" name="email" placeholder="example@gmail.com" class="input-style block filter-select" required>

            <label for="password" class="filter-label">Password:</label>
            <input type="password" id="mot_pass" name="mot_pass" placeholder="Enter your password" class="input-style block filter-select" required>

            <button type="submit" class="filter-button">Login</button>
        </form>
    </section>
</main>

<a href="Register.php" class="create-account-button">Create Account</a>

<script src="dark-mode.js"></script>
</body>
</html>
