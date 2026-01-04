<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Finaliser l'achat - E-Bazar</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/achat.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@200;300;400;600&display=swap" rel="stylesheet">
</head>
<body>

    <header class="navbar">
        <div class="logo"><span>E-Bazar</span><span class="dot">●</span></div>
        <div>
            <a href="profil.php">Mon profil</a>
            <a href="deconnexion.php">Deconnexion</a>
        </div>
    </header>

    <div class="container-achat">
        <?php if (!$success): ?>
            <h2>Finaliser votre achat</h2>
            <p>Objet : <strong><?= htmlspecialchars($annonce['titre']) ?></strong></p>
            <p>Total : <strong><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</strong></p>

            <?php if (isset($erreur)): ?>
                <p class="error"><?= $erreur ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Mode de livraison :</label>
                    <select name="mode_livraison" required>
                        <?php if ($annonce['livraison_postale']): ?>
                            <option value="POSTALE">Livraison Postale</option>
                        <?php endif; ?>
                        <?php if ($annonce['livraison_main']): ?>
                            <option value="MAIN">Remise en main propre</option>
                        <?php endif; ?>
                    </select>
                </div>

                <button type="submit" name="valider_achat" class="btn-valider">Confirmer l'achat</button>
            </form>

        <?php else: ?>
            <div class="facture">
                <h1 style="text-align: center;">RECAPITULATIF D'ACHAT</h1>
                <hr>
                <p><strong>Date :</strong> <?= $facture['date'] ?></p>
                <p><strong>Vendeur :</strong> <?= htmlspecialchars($facture['vendeur']) ?></p>
                <p><strong>Acheteur :</strong> <?= htmlspecialchars($facture['acheteur']) ?></p>
                <hr>
                <p><strong>Produit :</strong> <?= htmlspecialchars($facture['objet']) ?></p>
                <p><strong>Mode de livraison :</strong> <?= $facture['livraison'] ?></p>
                <h2 style="text-align: right;">Total : <?= number_format($facture['prix'], 2, ',', ' ') ?> €</h2>
                <hr>
                <p style="text-align: center; font-style: italic;">Merci pour votre achat sur E-Bazar !</p>
                <button onclick="window.print()" class="btn-valider">Imprimer le recapitulatif</button>
                <a href="index.php" style="display:block; text-align:center; margin-top:10px;">Retour à l'accueil</a>
            </div>
        <?php endif; ?>
    </div>
    
</body>
</html>
