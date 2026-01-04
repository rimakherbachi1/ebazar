<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Accueil – E-Bazar</title>
<link rel="stylesheet" href="css/accuill.css?v=99">
<link rel="stylesheet" href="css/header.css">
</head>
<body>
<header class="navbar">
    <div class="logo">
        <a href="index.php"><span>E-Bazar</span><span class="dot">●</span></a>
        <input type="text" id="barre-recherche" placeholder="Que cherchez-vous ?">
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
<?php foreach ($categories_nav as $cat): ?>
    <a href="?id=<?= $cat['id'] ?>"><p><?= htmlspecialchars($cat['nom']) ?> (<?= $cat['nombre_annonces'] ?>)</p></a>
<?php endforeach; ?>
</nav>

<main>
<div class="texte">
    <p><?= $sous_titre ?></p>
    <h2><?= $titre_section ?></h2>
</div>

<div class="produits">
<?php if (empty($annonces)): ?>
    <p><?= $categorie_id ? "Aucune annonce trouvée dans cette catégorie." : "Aucune annonce n'est actuellement disponible." ?></p>
<?php else: ?>
    <?php foreach ($annonces as $annonce): ?>
        <div class="produit" onclick="window.location.href='annonce.php?id=<?= $annonce['id'] ?>'">
            <div class="image-container">
                <img src="<?= htmlspecialchars(ebazar_photo_src($annonce['photo_principale'])) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
            </div>
            <p><?= htmlspecialchars($annonce['titre']) ?></p>
            <h2><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</h2>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<?php if ($categorie_id && $nombre_pages > 1): ?>
<div class="pagination">
    <?php for ($i=1; $i<=$nombre_pages; $i++): ?>
        <a href="?id=<?= $categorie_id ?>&page=<?= $i ?>" class="<?= ($i==$page)?'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

</main>
<footer>
<p style="text-align:center; padding:20px; margin-top:40px; color:#666;">
    © E-Bazar — 2025
</p>
</footer>
<script src="js/app.js" defer></script>
</body>
</html>
