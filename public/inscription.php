<?php

include '../config/config.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pseudo = trim($_POST['pseudo']);
    $email = trim($_POST['email']);
    $motdepasse = trim($_POST['motdepasse']);

    if (empty($pseudo) ||  empty($email) || empty($motdepasse)) {
        $erreur = "Tous les champs doivent être remplis.";
    } else {

        
$check = $pdo->prepare("SELECT id, pseudo, email FROM utilisateurs WHERE email = ? OR pseudo = ?");
$check->execute([$email, $pseudo]); 
if ($check->rowCount() > 0) {
    
    $utilisateur_existant = $check->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur_existant['email'] === $email) {
        $erreur = "Un compte existe déjà avec cet email.";
       } elseif ($utilisateur_existant['pseudo'] === $pseudo) {
        $erreur = "Ce pseudo est déjà pris. Veuillez en choisir un autre.";
       }
        } else {

            $hash = password_hash($motdepasse, PASSWORD_DEFAULT);

            $insert = $pdo->prepare("
                INSERT INTO utilisateurs (pseudo,  email, mot_de_passe) 
                VALUES (?, ?, ?)
            ");

            if ($insert->execute([$pseudo, $email, $hash])) {
                header("Location: connexion.php?inscription=ok");
                exit();
            } else {
                $erreur = "Erreur lors de l’inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - E-Bazar</title>

    <link rel="stylesheet" href="/css/inscription.css?v=2">
    <link rel="stylesheet" href="/css/header.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@100;300;400;700&display=swap" rel="stylesheet">
</head>

<body>

<header class="navbar">
    <div class="logo">
        <span>E-Bazar</span><span class="dot">●</span>
    </div>

    <div>
        <a href="connexion.php"><strong>Se connecter</strong></a>
        <a href="inscription.php" class="btn-outline">S'inscrire</a>
    </div>
</header>

<main class="login-container">

    <div class="image-section">
        <img src="../image/conn.jpg" alt="Inscription E-Bazar">
        <h2 class="overlay-text">Rejoignez E-Bazar.</h2>
    </div>

    <div class="login-form">
        <h2>Inscription</h2>

        <?php if (!empty($erreur)): ?>
            <p style="color:red; margin-bottom:10px; margin-left:150px;">
                <?= $erreur ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <input type="text" name="pseudo" placeholder="Pseudo" required>

            <input type="email" name="email" placeholder="Adresse e-mail" required>

            <input type="password" name="motdepasse" placeholder="Mot de passe" required>

            <button type="submit" class="signin-btn">Créer mon compte</button>

        </form>
    </div>

</main>

</body>
</html>
