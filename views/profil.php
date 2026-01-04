<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil – E-Bazar</title>
    <link rel="stylesheet" href="css/accuill.css?v=99">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/profil.css">
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@200;300;400&display=swap" rel="stylesheet">
    
</head>

<body>

<header class="navbar">
   <div class="logo">
        <a href="index.php"><span>E-Bazar</span><span class="dot">●</span></a>
       </div>

    <div>
        <?php if (!isset($_SESSION['id'])): ?>
            <a href="connexion.php"><strong>Se connecter</strong></a>
            <a href="inscription.php" class="btn-outline">S'inscrire</a>
        <?php else: ?>
            <a href="profil.php"><button class="icon"><img src="image/comptenoir.png" alt="Compte"></button></a>
            <a href="deconnexion.php">Deconnexion</a>
        <?php endif; ?>
    </div>
</header>

<nav>
    <a href="profil.php" ><p>Mon profil</p></a>
    <a href="mes_ventes.php"><p>Mes Ventes</p></a>
    <a href="mes_achats.php"> <p>Mes Achats</p></a>
    <a href="mes_annonces.php"><p>Mes Annonces</p></a>
</nav>
<main>
    <div class="texte">
        <h2>Profil de <?= htmlspecialchars($user_info['pseudo']) ?></h2>
    </div>

    <?php if (!empty($message_profil['success'])): ?>
        <div class="message-success"><?= htmlspecialchars($message_profil['success']) ?></div>
    <?php elseif (!empty($message_profil['error'])): ?>
        <div class="message-error">
            <?php foreach ($message_profil['error'] as $err): ?>
                <p><?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="profile-grid">
        
        <div class="stat">
            
            <h4>Statistiques</h4>
            <p>Membre depuis : <strong><?= $date_inscription ?></strong></p>
            <p>Annonces postées : <strong><?= htmlspecialchars($nombre_annonces) ?></strong></p>
        </div>

        <div class="profile-details">
            <h3>Informations du Compte</h3>
            
            <form method="POST" action="profil.php"> 
                
                <div class="form-group">
                    <label for="pseudo">Pseudo :</label>
                    <input type="text" id="pseudo" name="pseudo" value="<?= htmlspecialchars($current_pseudo) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Adresse Email :</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($current_email) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="mot_de_passe">Nouveau Mot de Passe :</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Laisser vide pour ne pas changer">
                </div>

                <button type="submit" class="modifier-btn">Modifier le profil</button>

            </form>
        </div>
    </div>

</main>
<footer>
    <p style="text-align:center; padding:20px; margin-top:40px; color:#666;">
        © E-Bazar — 2025
    </p>
</footer>

<script src="js/app.js" defer></script>
</body>
</html>