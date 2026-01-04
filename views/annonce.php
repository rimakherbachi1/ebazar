<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($annonce['titre']) ?> - E-Bazar</title>
    <link rel="stylesheet" href="css/accuill.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/detaill_produit.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
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
                <a href="profil.php"><button class="icon"><img src="image/comptenoir.png" alt="Profil"></button></a>
                <a href="deconnexion.php">Deconnexion</a>
            <?php endif; ?>
        </div>
    </header>

    <nav>
        <?php foreach ($categories as $cat): ?>
            <a href="index.php?id=<?= $cat['id'] ?>"><p><?= htmlspecialchars($cat['nom']) ?></p></a>
        <?php endforeach; ?>
    </nav>

    <main>
        <div class="product-container">
            
            <div class="product-image">
                <div class="gallery-wrapper">
                    <?php if (count($photos) > 1): ?>
                    <div class="thumbnails-column"> 
                        <?php foreach ($photos as $index => $photo_chemin): ?>
                            <?php $thumb_src = ebazar_photo_src($photo_chemin); ?>
                            <img 
                                src="<?= htmlspecialchars($thumb_src) ?>" 
                                alt="Miniature <?= $index + 1 ?>" 
                                class="thumbnail-item <?= ($index === 0) ? 'active' : '' ?>"
                                data-src="<?= htmlspecialchars($thumb_src) ?>"
                            >
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="main-image-container">
                        <img id="main-image" src="<?= htmlspecialchars($photo_principale) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
                    </div>
                </div>
            </div>
            
            <div class="product-details">
                <h2><?= htmlspecialchars($annonce['titre']) ?></h2>
                
                <h3>Description</h3>
                <p><?= nl2br(htmlspecialchars($annonce['description'])) ?></p>
                
                <p class="vendeur-info">Vendu par : <strong><?= htmlspecialchars($annonce['vendeur_pseudo']) ?></strong></p>
                
                <div class="div-container">
                    <h3><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</h3>
                </div>
                
                <div class="livraison-options">
                    <?php if ($annonce['livraison_postale']): ?>
                        <p>Livraison postale disponible</p>
                    <?php endif; ?>
                    <?php if ($annonce['livraison_main']): ?>
                        <p>Remise en main propre possible</p>
                    <?php endif; ?>
                </div>
                
                <div class="butt">
                    <?php if (isset($_SESSION['id'])): ?>
                        <a href="achat.php?id=<?= $annonce['id'] ?>" class="acheter-button">Acheter</a>
                        <a href="reserver.php?id=<?= $annonce['id'] ?>" class="reserver-button">Réserver</a>
                    <?php else: ?>
                        <a href="achat.php?id=<?= $annonce['id'] ?>" class="acheter-button">Acheter</a>
                        <a href="reserver.php?id=<?= $annonce['id'] ?>" class="reserver-button">Réserver</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <section class="product-suggestions">
            <div class="tab-header">
                <h2 class="tab-link active" data-tab="vendeur-content">
                    Autres annonces de <?= htmlspecialchars($annonce['vendeur_pseudo']) ?>
                </h2>
                <h2 class="tab-link" data-tab="similaires-content">
                    Annonces similaires
                </h2>
            </div>

            <div id="vendeur-content" class="tab-content active">
                <?php if (!empty($autres_annonces)): ?>
                    <div class="produits">
                        <?php foreach ($autres_annonces as $autre): ?>
                            <?php $autre_photo = ebazar_photo_src($autre['photo_principale']); ?>
                            <div class="produit" onclick="window.location.href='annonce.php?id=<?= $autre['id'] ?>'">
                                <div class="image-container">
                                    <img src="<?= htmlspecialchars($autre_photo) ?>" alt="<?= htmlspecialchars($autre['titre']) ?>">
                                </div>
                                <p><?= htmlspecialchars($autre['titre']) ?></p>
                                <h2><?= number_format($autre['prix'], 2, ',', ' ') ?> €</h2>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-results">Pas d'autres annonces en vente.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <footer>
    <p style="text-align:center; padding:20px; margin-top:40px; color:#666;">
        © E-Bazar — 2025
    </p>
</footer>
    
    
    <script src="js/app.js" defer></script>
    <script src="js/pagination.js" ></script>
</body>
</html>
