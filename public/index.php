<?php
include '../config/config.php';


$where_clause = "a.statut = 'EN_VENTE'";
$execute_params = [];
$titre_section = "Les dernières annonces mises en ligne";
$sous_titre = "Nouveautés";

if (isset($_SESSION['id'])) {
    $where_clause .= " AND a.vendeur_id != :user_id";
    $execute_params['user_id'] = $_SESSION['id'];
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $categorie_id = (int)$_GET['id'];
    
    $stmt_cat_info = $pdo->prepare("SELECT nom FROM categories WHERE id = ?");
    $stmt_cat_info->execute([$categorie_id]);
    $categorie_info = $stmt_cat_info->fetch(PDO::FETCH_ASSOC);

    if ($categorie_info) {
        $nom_categorie = htmlspecialchars($categorie_info['nom']);
        
        $where_clause .= " AND a.categorie_id = :cat_id";
        $execute_params['cat_id'] = $categorie_id;
        
        $titre_section = "Annonces dans la catégorie " . $nom_categorie;
        $sous_titre = "Catégorie " . $nom_categorie;
    }
}

$sql_annonces = "
    SELECT a.*, 
        (SELECT chemin FROM photos WHERE annonce_id = a.id ORDER BY position ASC LIMIT 1) AS photo_principale
    FROM annonces a
    WHERE {$where_clause}
    ORDER BY a.id DESC
    LIMIT 4
";

$stmt_annonces = $pdo->prepare($sql_annonces);
$stmt_annonces->execute($execute_params);
$annonces = $stmt_annonces->fetchAll(PDO::FETCH_ASSOC);


$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil – E-Bazar</title>
    <link rel="stylesheet" href="../css/accuill.css?v=99">
    <link rel="stylesheet" href="../css/header.css">
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@200;300;400&display=swap" rel="stylesheet">
</head>

<body>

<header class="navbar">
    <div class="logo">
        <span>E-Bazar</span><span class="dot">●</span>
        <input type="text" placeholder="Que cherchez-vous ?">
    </div>

    <div>
        <?php if (!isset($_SESSION['id'])): ?>
            <a href="connexion.php"><strong>Se connecter</strong></a>
            <a href="inscription.php" class="btn-outline">S'inscrire</a>
        <?php else: ?>
            <a href="profil.php"><button class="icon"><img src="../image/comptenoir.png" alt="Compte"></button></a>
        <?php endif; ?>
    </div>
</header>

<nav>
    <?php foreach ($categories as $cat): ?>
        <a href="?id=<?= $cat['id'] ?>">
            <p><?= htmlspecialchars($cat['nom']) ?></p>
        </a>
    <?php endforeach; ?>
</nav>

<main>

    <div class="texte">
        <p><?= $sous_titre ?></p>
        <h2><?= $titre_section ?></h2>
    </div>

    <div class="produits">

        <?php if (empty($annonces) && isset($categorie_id)): ?>
             <p>Aucune annonce trouvée dans cette catégorie.</p>
        <?php elseif (empty($annonces)): ?>
             <p>Aucune annonce n'est actuellement disponible.</p>
        <?php else: ?>
            <?php foreach ($annonces as $annonce): ?>
                <div class="produit" onclick="window.location.href='annonce.php?id=<?= $annonce['id'] ?>'">

                    <div class="image-container">
                        <?php if ($annonce['photo_principale']): ?>
                             <img src="../<?= $annonce['photo_principale'] ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
                        <?php else: ?>
                            <img src="../image/default.jpg" alt="Image par défaut">
                        <?php endif; ?>
                    </div>

                    <p><?= htmlspecialchars($annonce['titre']) ?></p>
                    <h2><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</h2>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

</main>

</body>
</html>