<?php

include '../config/config.php';

$erreur = "";
$redirect = '';

if (isset($_GET['redirect'])) {
    $redirect = trim($_GET['redirect']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $motdepasse = trim($_POST['motdepasse']);
    $redirect = trim($_POST['redirect'] ?? $redirect);

    $req = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $req->execute([$email]);
    $user = $req->fetch();

    if ($user && password_verify($motdepasse, $user['mot_de_passe'])) {

        $_SESSION['id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['pseudo'] = $user['pseudo'];

        $redirect_safe = '';
        if (!empty($redirect)
            && strpos($redirect, '://') === false
            && strpos($redirect, "\n") === false
            && strpos($redirect, "\r") === false
            && substr($redirect, 0, 2) !== '//') {
            $redirect_safe = $redirect;
        }

        if (!empty($redirect_safe)) {
            header("Location: {$redirect_safe}");
            exit();
        }

        if ($user['role'] === 'ADMIN') {
            header("Location: admin_utilisateurs.php");
            exit();
        } else { 
            header("Location: index.php");
            exit();
        }

    } else {
        $erreur = "Identifiants incorrects.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - E-Bazar</title>

    <link rel="stylesheet" href="css/connexion.css">
    <link rel="stylesheet" href="css/header.css">
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@200;300;400&display=swap" rel="stylesheet">
</head>

<body>

    <header class="navbar">
       <div class="logo">
        <a href="index.php"><span>E-Bazar</span><span class="dot">●</span></a>
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

            <?php if (!empty($erreur)): ?>
                <p class="form-error">
                    <?= $erreur ?>
                </p>
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
