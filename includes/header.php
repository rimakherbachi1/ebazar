<?php
function estAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'ADMIN';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>E-Bazar</title>
    <link rel="stylesheet" href="/css/header.css">
</head>

<body>

<header>
    <nav class="menu">
        <a href="/public/index.php">Accueil</a>

        <?php if (isset($_SESSION['id'])): ?>
            <a href="/public/profil.php">Mon profil</a>
            <a href="/public/depot_annonce.php">Déposer une annonce</a>
            <a href="/public/mes_annonces.php">Mes annonces</a>
            <a href="/public/mes_achats.php">Mes achats</a>
            <a href="/public/mes_ventes.php">Mes ventes</a>
            <a href="/public/deconnexion.php">Déconnexion</a>

            <?php if (estAdmin()): ?>
                <a href="/public/admin/admin_index.php" class="admin">Admin</a>
            <?php endif; ?>

        <?php else: ?>
            <a href="/public/connexion.php">Connexion</a>
            <a href="/public/inscription.php">Inscription</a>
        <?php endif; ?>
    </nav>
</header>

<main>
