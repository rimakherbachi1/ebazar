<?php
include '../config/config.php'; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil – E-Bazar</title>
    <link rel="stylesheet" href="../css/accuill.css?v=99">
    <link rel="stylesheet" href="../css/header.css">
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@200;300;400&display=swap" rel="stylesheet">
    
</head>

<body>

<header class="navbar">
    <div class="logo">
        <span>E-Bazar</span><span class="dot">●</span>
        <input type="text" placeholder="Que cherchez-vous ?">
    </div>
    <div>
        <a href=""><button class="icon"><img src="../image/comptenoir.png" alt="Compte"></button></a>
       
    </div>
</header>

<nav>
    <a href="admin_utilisateurs.php" ><p>UTULISATEURS</p></a>
    <a href="admin_annonces.php"><p>ANNONCES</p></a>
    <a href="admin_categories.php"> <p>CATEGORIES</p></a>
</nav>


</body>
</html>