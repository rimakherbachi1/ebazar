<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - E-Bazar</title>
    <link rel="stylesheet" href="css/connexion.css">
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
        <img src="image/conn.jpg" alt="Connexion E-Bazar">
    </div>

    <div class="login-form">
        <h2>Connexion</h2>

        <?php if (!empty($erreur)) : ?>
            <p class="form-error"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
            <input type="email" name="email" placeholder="Adresse e-mail" required>
            <input type="password" name="motdepasse" placeholder="Mot de passe" required>
            <button type="submit" class="login-btn">Connexion</button>
        </form>

        <a href="inscription.php">
            <button class="signup-alt-btn">Créer un compte</button>
        </a>
    </div>

</main>

</body>
</html>
