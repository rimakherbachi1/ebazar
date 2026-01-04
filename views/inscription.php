<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - E-Bazar</title>

    <link rel="stylesheet" href="css/inscription.css">
    <link rel="stylesheet" href="css/header.css">
</head>

<body>

<header class="navbar">
    <div class="logo">
        <a href="index.php">E-Bazar<span class="dot">●</span></a>
    </div>

    <div>
        <a href="connexion.php"><strong>Se connecter</strong></a>
        <a href="inscription.php" class="btn-outline">S'inscrire</a>
    </div>
</header>

<main class="login-container">

    <div class="image-section">
        <img src="image/conn.jpg" alt="Inscription E-Bazar">
        <h2 class="overlay-text">Rejoignez E-Bazar.</h2>
    </div>

    <div class="login-form">
        <h2>Inscription</h2>

        <?php if (!empty($erreur)) : ?>
            <p class="form-error"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="pseudo" placeholder="Pseudo" required>
            <input type="email" name="email" placeholder="Adresse e-mail" required>
            <input type="password" name="motdepasse" placeholder="Mot de passe" required>

            <button type="submit" class="signin-btn">
                Créer mon compte
            </button>
        </form>
    </div>

</main>

</body>
</html>
