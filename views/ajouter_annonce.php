<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une annonce - E-Bazar</title>
    <link rel="stylesheet" href="css/accuill.css?v=99">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/ajouter_annonce.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@200;300;400&display=swap" rel="stylesheet">
    
</head>

<body>
<header class="navbar">
   <div class="logo">
        <a href="index.php"><span>E-Bazar</span><span class="dot">●</span></a>
       <input type="text" id="barre-recherche" placeholder="Que cherchez-vous ? ">
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
        <p>Vente</p>
        <h2>Ajouter une Nouvelle Annonce</h2>
    </div>

    <div class="form-container">
        
        <?php if (!empty($erreur)): ?>
            <ul class="error-list">
                <?php foreach ($erreur as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success-message"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="titre">Titre de l'annonce (min 5, max 30 caractères) :</label>
                <input type="text" id="titre" name="titre" required maxlength="30" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="categorie_id">Catégorie :</label>
                <select id="categorie_id" name="categorie_id" required>
                    <option value="">-- Choisir une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (isset($_POST['categorie_id']) && (int)$_POST['categorie_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description (min 5, max 200 caractères) :</label>
                <textarea id="description" name="description" rows="5" required maxlength="200"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="prix">Prix (€) :</label>
                <input type="number" id="prix" name="prix" step="0.01" min="0" required value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Options de Livraison :</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="livraison_postale" name="livraison_postale" value="1" <?= isset($_POST['livraison_postale']) ? 'checked' : '' ?>>
                    <label for="livraison_postale">Livraison postale</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="livraison_main" name="livraison_main" value="1" <?= isset($_POST['livraison_main']) ? 'checked' : '' ?>>
                    <label for="livraison_main">Remise en main propre</label>
                </div>
            </div>

            <div class="form-group">
                <label for="photos">Photos (Max. 5 fichiers JPEG, 200 Kio chacun) :</label>
                <input type="file" id="photos" name="photos[]" accept="image/jpeg" multiple required>
            </div>

            <button type="submit" class="submit-btn">Soumettre l'Annonce</button>
        </form>
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
