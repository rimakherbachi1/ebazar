<?php
include '../config/config.php';

$annonces_par_page = 10;
$limite = 4; 
$page = 1;
$offset = 0;

$where_clause = "a.statut = 'EN_VENTE'";
$execute_params = [];
$titre_section = "Les dernières annonces mises en ligne";
$sous_titre = "Nouveautés";
$user_id_exclusion = '';

if (isset($_SESSION['id'])) {
    $current_user_id = $_SESSION['id'];
    $where_clause .= " AND a.vendeur_id != :user_id_main";
    $execute_params['user_id_main'] = $current_user_id;
    $user_id_exclusion = " AND a.vendeur_id != " . $current_user_id;
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

        $limite = $annonces_par_page;
        
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = max(1, (int)$_GET['page']); // page >= 1
            $offset = ($page - 1) * $limite;
        }
    }
}

$total_annonces = 0;
if (isset($categorie_id)) {
    $sql_count_total = "SELECT COUNT(a.id) 
                        FROM annonces a
                        WHERE a.statut = 'EN_VENTE'
                        AND a.categorie_id = :cat_id
                        {$user_id_exclusion}";
    
    $stmt_count_params = ['cat_id' => $categorie_id];
    
    if (isset($current_user_id)) {
        $sql_count_params = ['cat_id' => $categorie_id];
         if (isset($current_user_id)) {
             $sql_count_params['user_id_count'] = $current_user_id;
             $sql_count_total .= " AND a.vendeur_id != :user_id_count";
         }
         
         $stmt_count = $pdo->prepare($sql_count_total);
         $stmt_count->execute($sql_count_params);
         $total_annonces = $stmt_count->fetchColumn();
    } else {
         $stmt_count = $pdo->prepare($sql_count_total);
         $stmt_count->execute(['cat_id' => $categorie_id]);
         $total_annonces = $stmt_count->fetchColumn();
    }
    
    $nombre_pages = ceil($total_annonces / $annonces_par_page);
    
    if ($offset >= $total_annonces && $total_annonces > 0) {
        header("Location: ?id={$categorie_id}&page=1");
        exit();
    }
}


$sql_annonces = "
    SELECT a.*, 
        (SELECT chemin FROM photos WHERE annonce_id = a.id ORDER BY position ASC LIMIT 1) AS photo_principale
    FROM annonces a
    WHERE {$where_clause}
    ORDER BY a.id DESC
    LIMIT {$limite} OFFSET {$offset}
";

$stmt_annonces = $pdo->prepare($sql_annonces);
$stmt_annonces->execute($execute_params);
$annonces = $stmt_annonces->fetchAll(PDO::FETCH_ASSOC);


$sql_categories = "
    SELECT 
        c.id, 
        c.nom, 
        COUNT(a.id) AS nombre_annonces
    FROM categories c
    LEFT JOIN annonces a ON c.id = a.categorie_id 
        AND a.statut = 'EN_VENTE'
        {$user_id_exclusion} 
    GROUP BY c.id, c.nom
    ORDER BY c.nom ASC
";
$categories = $pdo->query($sql_categories)->fetchAll(PDO::FETCH_ASSOC);
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
        <a href="index.php"><span>E-Bazar</span><span class="dot">●</span></a>
       <input type="text" id="barre-recherche" placeholder="Que cherchez-vous ? ">
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
            <p><?= htmlspecialchars($cat['nom']) ?> (<?= $cat['nombre_annonces'] ?>)</p>
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
    
    <?php if (isset($categorie_id) && $nombre_pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $nombre_pages; $i++): ?>
            <a href="?id=<?= $categorie_id ?>&page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

</main>

</body>
</html>