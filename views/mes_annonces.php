<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Annonces - E-Bazar</title>
    <link rel="stylesheet" href="css/accuill.css?v=99">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/detaill_produit.css">
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
        <p>Gestion</p>
        <h2>Mes Annonces</h2>
    </div>

    <?php if (!empty($success)): ?>
        <div class="message-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($erreur)): ?>
        <div class="message-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <section class="product-suggestions">

        <div class="tabs-control-wrapper">
            <div class="tab-header">
                <?php $is_first = true; ?>
                <?php foreach ($statuts_affiches as $statut): ?>
                    <h2 
                        class="tab-link <?= $is_first ? 'active' : '' ?>" 
                        data-tab="<?= strtolower($statut) ?>-content">
                        <?= htmlspecialchars(str_replace('_', ' ', $statut)) ?> (<?= count($annonces_par_statut[$statut]) ?>)
                    </h2>
                    <?php $is_first = false; ?>
                <?php endforeach; ?>
            </div>
            
            <a href="ajouter_annonce.php" class="ajouter_annonce">
                + Ajouter une annonce
            </a>
        </div>


        <?php $is_first = true; ?>
        <?php foreach ($statuts_affiches as $statut): ?>
            <div id="<?= strtolower($statut) ?>-content" class="tab-content <?= $is_first ? 'active' : '' ?>">
                <?php if (!empty($annonces_par_statut[$statut])): ?>
                    <div class="produits">
                        <?php foreach ($annonces_par_statut[$statut] as $annonce_statut): ?>
                            <?php $photo_src = ebazar_photo_src($annonce_statut['photo_principale']); ?>
                            <div class="produit" >
                                <div class="image-container">
                                    <img src="<?= htmlspecialchars($photo_src) ?>" alt="<?= htmlspecialchars($annonce_statut['titre']) ?>">
                                </div>
                                <p><?= htmlspecialchars($annonce_statut['titre']) ?></p>
                                <p class="status-tag">Statut: <?= htmlspecialchars(str_replace('_', ' ', $annonce_statut['statut'])) ?></p>
                                <?php if ($statut === 'EN_VENTE'): ?>
                                    <form method="POST" class="card-actions" onsubmit="return confirm('Confirmer la suppression de cette annonce ?');">
                                        <input type="hidden" name="annonce_id" value="<?= $annonce_statut['id'] ?>">
                                        <button type="submit" name="supprimer_annonce" class="delete-btn">Supprimer</button>
                                    </form>
                                <?php endif; ?>
                                <h2><?= number_format($annonce_statut['prix'], 2, ',', ' ') ?> €</h2>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-results">Vous n'avez aucune annonce dans le statut "<?= htmlspecialchars(str_replace('_', ' ', $statut)) ?>".</p>
                <?php endif; ?>
            </div>
            <?php $is_first = false; ?>
        <?php endforeach; ?>

    </section>

</main>
<footer>
    <p style="text-align:center; padding:20px; margin-top:40px; color:#666;">
        © E-Bazar — 2025
    </p>
</footer>
<script src="js/app.js" defer></script>

<script src="js/table.js" ></script>
</body>
</html>
