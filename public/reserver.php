<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/config.php';

if (!isset($_SESSION['id'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: connexion.php?redirect={$redirect}");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$annonce_id = (int)$_GET['id'];
$acheteur_id = $_SESSION['id'];

$stmt = $pdo->prepare("
    SELECT a.*, u.pseudo AS vendeur_pseudo 
    FROM annonces a 
    JOIN utilisateurs u ON a.vendeur_id = u.id 
    WHERE a.id = ? AND a.statut = 'EN_VENTE'
");
$stmt->execute([$annonce_id]);
$annonce = $stmt->fetch();

if (!$annonce) {
    die("Annonce introuvable ou deja indisponible.");
}

if ($annonce['vendeur_id'] == $acheteur_id) {
    die("Vous ne pouvez pas reserver votre propre article.");
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_reservation'])) {
    $mode_livraison = $_POST['mode_livraison'] ?? '';
    $mode_valide = false;

    if ($mode_livraison === 'POSTALE' && $annonce['livraison_postale']) {
        $mode_valide = true;
    }
    if ($mode_livraison === 'MAIN' && $annonce['livraison_main']) {
        $mode_valide = true;
    }

    if ($mode_valide) {
        try {
            $pdo->beginTransaction();

            $stmt_reservation = $pdo->prepare("
                INSERT INTO achats (annonce_id, acheteur_id, mode_livraison, statut) 
                VALUES (?, ?, ?, 'EN_ATTENTE')
            ");
            $stmt_reservation->execute([$annonce_id, $acheteur_id, $mode_livraison]);

            $stmt_update = $pdo->prepare("
                UPDATE annonces 
                SET statut = 'RESERVER' 
                WHERE id = ? AND statut = 'EN_VENTE'
            ");
            $stmt_update->execute([$annonce_id]);
            if ($stmt_update->rowCount() === 0) {
                throw new Exception("Annonce plus disponible.");
            }

            $pdo->commit();
            $success = true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $erreur = $e->getMessage();
            if ($erreur === '') {
                $erreur = "Erreur lors de la reservation.";
            }
        }
    } else {
        $erreur = "Veuillez choisir un mode de livraison valide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reservation - E-Bazar</title>
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
            <h2>Reserver ce bien</h2>
            <p>Objet : <strong><?= htmlspecialchars($annonce['titre']) ?></strong></p>
            <p>Prix : <strong><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</strong></p>

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

                <button type="submit" name="valider_reservation" class="btn-valider">Confirmer la reservation</button>
            </form>

        <?php else: ?>
            <div class="facture">
                <h1 style="text-align: center;">RESERVATION CONFIRMEE</h1>
                <hr>
                <p><strong>Vendeur :</strong> <?= htmlspecialchars($annonce['vendeur_pseudo']) ?></p>
                <p><strong>Produit :</strong> <?= htmlspecialchars($annonce['titre']) ?></p>
                <p><strong>Prix :</strong> <?= number_format($annonce['prix'], 2, ',', ' ') ?> €</p>
                <hr>
                <p style="text-align: center; font-style: italic;">Votre reservation est enregistree.</p>
                <a href="mes_achats.php" style="display:block; text-align:center; margin-top:10px;">Voir mes achats</a>
                <a href="index.php" style="display:block; text-align:center; margin-top:10px;">Retour a l'accueil</a>
            </div>
        <?php endif; ?>
    </div>
<footer>
    <p style="text-align:center; padding:20px; margin-top:40px; color:#666;">
        © E-Bazar — 2025
    </p>
</footer>
</body>
</html>
