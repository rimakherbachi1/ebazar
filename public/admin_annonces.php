<?php
include '../config/config.php'; 

$erreur = '';
$success = '';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_annonce'])) {
    
    $annonce_id_a_supprimer = (int)($_POST['annonce_id'] ?? 0);

    if ($annonce_id_a_supprimer > 0) {
        try {
            $stmt_delete = $pdo->prepare("UPDATE annonces SET statut = 'SUPPRIME' WHERE id = ?");
            $stmt_delete->execute([$annonce_id_a_supprimer]);
            
            $success = "L'annonce ID {$annonce_id_a_supprimer} a été marquée comme 'SUPPRIMÉE'.";
        } catch (Exception $e) {
            $erreur = "Erreur lors de la suppression de l'annonce : " . $e->getMessage();
        }
    } else {
        $erreur = "ID d'annonce invalide.";
    }
}


try {
    $statut_cible = 'EN_VENTE'; 
    
    $stmt_annonces = $pdo->prepare("
        SELECT 
            a.id, a.titre, a.prix, a.date_creation, a.statut,
            (SELECT chemin FROM photos WHERE annonce_id = a.id ORDER BY position ASC LIMIT 1) AS photo_principale,
            u.pseudo AS vendeur_pseudo
        FROM annonces a
        JOIN utilisateurs u ON a.vendeur_id = u.id
        WHERE a.statut = ?
        ORDER BY a.date_creation DESC
    ");
    $stmt_annonces->execute([$statut_cible]);
    $annonces = $stmt_annonces->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $annonces = [];
    $erreur .= ($erreur ? " " : "") . "Erreur de chargement des annonces : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration – Annonces</title>
    <link rel="stylesheet" href="../css/accuill.css?v=99">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/admin_annonces.css">
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@200;300;400&display=swap" rel="stylesheet">
    
</head>

<body>

<header class="navbar">
    <div class="logo">
        <span>E-Bazar</span><span class="dot">●</span>
        <input type="text" placeholder="Que cherchez-vous ?">
    </div>
    <div>
        <a href="profil.php"><button class="icon"><img src="../image/comptenoir.png" alt="Compte"></button></a>
    </div>
</header>

<nav>
    <a href="admin_utilisateurs.php" ><p>UTULISATEURS</p></a>
    <a href="admin_annonces.php"><p>ANNONCES</p></a>
    <a href="admin_categories.php"> <p>CATEGORIES</p></a>
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
                    <div class="image-container" >
                        <?php if ($annonce['photo_principale']): ?>
                             <img src="../<?= $annonce['photo_principale'] ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
                        <?php else: ?>
                            <img src="../image/default.jpg" alt="Image par défaut">
                        <?php endif; ?>
                    </div>

                    <p onclick="window.location.href='annonce.php?id=<?= $annonce['id'] ?>'"><?= htmlspecialchars($annonce['titre']) ?></p>
                    <p>Vendeur: <?= htmlspecialchars($annonce['vendeur_pseudo']) ?></p>
                    <h2><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</h2>

                    <div class="admin-action-bar">
                         <form method="POST" action="admin_annonces.php" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer (statut SUPPRIME) cette annonce ID: <?= $annonce['id'] ?> ?');">
                            <input type="hidden" name="annonce_id" value="<?= $annonce['id'] ?>">
                            <button type="submit" name="supprimer_annonce" class="delete-btn">Supprimer</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-results">Aucune annonce trouvée dans le statut "<?= htmlspecialchars($statut_cible) ?>".</p>
        <?php endif; ?>
    </div>

</main>

</body>
</html>