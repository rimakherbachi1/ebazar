<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration – Annonces</title>

    <link rel="stylesheet" href="css/accuill.css?v=99">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/admin_annonces.css">

    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@200;300;400&display=swap" rel="stylesheet">
</head>

<body>

<header class="navbar">
    <div class="logo">
        <span>E-Bazar</span><span class="dot">●</span>
    </div>
    <div>
        <a href="deconnexion.php">Deconnexion</a>
    </div>
</header>

<nav>
    <a href="admin_utilisateurs.php"><p>UTULISATEURS</p></a>
    <a href="admin_annonces.php"><p>ANNONCES</p></a>
    <a href="admin_categories.php"><p>CATEGORIES</p></a>
</nav>

<main class="admin-container">

    <div class="texte">
        <p>Administration</p>
        <h2>Annonces Actives (<?= count($annonces) ?>)</h2>
    </div>

    <?php if (!empty($success)): ?>
        <div class="message-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($erreur)): ?>
        <div class="message-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <div class="produits">

        <?php if (!empty($annonces)): ?>
            <?php foreach ($annonces as $annonce): ?>
                <div class="produit">

                    <?php $photo_src = ebazar_photo_src($annonce['photo_principale']); ?>

                    <div class="image-container">
                        <img src="<?= htmlspecialchars($photo_src) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
                    </div>

                    <p onclick="window.location.href='annonce.php?id=<?= $annonce['id'] ?>'">
                        <?= htmlspecialchars($annonce['titre']) ?>
                    </p>

                    <p>Vendeur: <?= htmlspecialchars($annonce['vendeur_pseudo']) ?></p>

                    <h2><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</h2>

                    <div class="admin-action-bar">
                        <form method="POST"
                              action="admin_annonces.php"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer (statut SUPPRIME) cette annonce ID: <?= $annonce['id'] ?> ?');">

                            <input type="hidden" name="annonce_id" value="<?= $annonce['id'] ?>">

                            <button type="submit"
                                    name="supprimer_annonce"
                                    class="delete-btn">
                                Supprimer
                            </button>

                        </form>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-results">
                Aucune annonce trouvée dans le statut "EN_VENTE".
            </p>
        <?php endif; ?>

    </div>

</main>

</body>
</html>
