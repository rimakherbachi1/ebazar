<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Achats - E-Bazar</title>
    <link rel="stylesheet" href="css/accuill.css?v=99">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/detaill_produit.css">
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@200;300;400;600&display=swap" rel="stylesheet">
</head>

<body>
<header class="navbar">
   <div class="logo">
        <a href="index.php"><span>E-Bazar</span><span class="dot">●</span></a>
   </div>
    <div>
        <a href="profil.php"><button class="icon"><img src="image/comptenoir.png" alt="Compte"></button></a>
        <a href="deconnexion.php">Deconnexion</a>
    </div>
</header>

<nav>
    <a href="profil.php"><p>Mon profil</p></a>
    <a href="mes_ventes.php"><p>Mes Ventes</p></a>
    <a href="mes_achats.php" class="active"> <p>Mes Achats</p></a>
    <a href="mes_annonces.php"><p>Mes Annonces</p></a>
</nav>

<main>
    <div class="texte">
        <p>Espace personnel</p>
        <h2>Suivi de mes commandes</h2>
    </div>

    <section class="product-suggestions achats-section">
        <div class="tab-header">
            <?php $is_first = true; ?>
            <?php foreach ($statuts_achats_affiches as $statut_achat): ?>
                <h2 class="tab-link <?= $is_first ? 'active' : '' ?>" data-tab="<?= strtolower($statut_achat) ?>-content">
                    <?= htmlspecialchars($statut_mapping[$statut_achat] ?? 'En cours') ?> (<?= count($achats_par_statut[$statut_achat]) ?>)
                </h2>
                <?php $is_first = false; ?>
            <?php endforeach; ?>
        </div>

        <?php $is_first = true; ?>
        <?php foreach ($statuts_achats_affiches as $statut_achat): ?>
            <div id="<?= strtolower($statut_achat) ?>-content" class="tab-content <?= $is_first ? 'active' : '' ?>">
                <?php if (!empty($achats_par_statut[$statut_achat])): ?>
                    <div class="produits">
                        <?php foreach ($achats_par_statut[$statut_achat] as $achat): ?>
                            <div class="produit">
                                <?php $photo_src = ebazar_photo_src($achat['photo_principale']); ?>
                                <div class="image-container">
                                    <img src="<?= htmlspecialchars($photo_src) ?>" alt="Produit">
                                </div>
                                <p><strong><?= htmlspecialchars($achat['titre']) ?></strong></p>
                                <?php $statut_label = ($achat['statut_annonce'] === 'RESERVER') ? 'Réservé' : 'Acheté'; ?>
                                <p class="status-tag">Statut : <?= $statut_label ?></p>
                                <p class="status-tag">Livraison : <?= $achat['mode_livraison'] === 'POSTALE' ? 'Poste' : 'Main propre' ?></p>
                                <h2><?= number_format($achat['prix'], 2, ',', ' ') ?> €</h2>

                                <?php if ($achat['statut_annonce'] === 'RESERVER'): ?>
                                    <form method="POST" onsubmit="return confirm('Confirmez-vous l\'achat de cet objet ?');">
                                        <input type="hidden" name="achat_id" value="<?= $achat['achat_id'] ?>">
                                        <button type="submit" name="confirmer_reservation" class="btn-recu">
                                            Confirmer l'achat
                                        </button>
                                    </form>

                                    <form method="POST" onsubmit="return confirm('Annuler la réservation ?');">
                                        <input type="hidden" name="achat_id" value="<?= $achat['achat_id'] ?>">
                                        <button type="submit" name="annuler_reservation" class="btn-annuler">
                                            Annuler la réservation
                                        </button>
                                    </form>

                                <?php elseif ($achat['statut_annonce'] === 'VENDU' && $achat['statut_achat'] === 'EN_ATTENTE'): ?>
                                    <form method="POST" onsubmit="return confirm('Confirmez-vous avoir reçu cet objet ?');">
                                        <input type="hidden" name="achat_id" value="<?= $achat['achat_id'] ?>">
                                        <button type="submit" name="confirmer_reception" class="btn-recu">
                                            Confirmer la réception
                                        </button>
                                    </form>
                                <?php endif; ?>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-results">Aucun article dans cette catégorie.</p>
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

</body>
</html>
