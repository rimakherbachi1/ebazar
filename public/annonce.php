<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/config.php'; 

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $annonce_id = (int)$_GET['id'];
} else {
    header("Location: index.php");
    exit();
}

$stmt_annonce = $pdo->prepare("
    SELECT 
        a.*, 
        u.pseudo AS vendeur_pseudo 
    FROM annonces a
    JOIN utilisateurs u ON a.vendeur_id = u.id
    WHERE a.id = ? AND a.statut = 'EN_VENTE'
");
$stmt_annonce->execute([$annonce_id]);
$annonce = $stmt_annonce->fetch(PDO::FETCH_ASSOC);

if (!$annonce) {
    die("Erreur : Cette annonce n'existe pas ou n'est plus disponible.");
}

$stmt_photos = $pdo->prepare("
    SELECT chemin 
    FROM photos 
    WHERE annonce_id = ? 
    ORDER BY position ASC
");
$stmt_photos->execute([$annonce_id]);
$photos = $stmt_photos->fetchAll(PDO::FETCH_COLUMN); 
$photo_principale = !empty($photos) ? $photos[0] : 'image/default.jpg';


$vendeur_id = $annonce['vendeur_id'];
$annonce_principale_id = $annonce['id']; 

$stmt_autres_annonces = $pdo->prepare("
    SELECT 
        a.id, a.titre, a.prix,
        (SELECT chemin FROM photos WHERE annonce_id = a.id ORDER BY position ASC LIMIT 1) AS photo_principale
    FROM annonces a
    WHERE 
        a.vendeur_id = :vendeur_id
        AND a.statut = 'EN_VENTE'
        AND a.id != :current_id
    ORDER BY a.date_creation DESC
    LIMIT 4 
");

$stmt_autres_annonces->execute([
    'vendeur_id' => $vendeur_id,
    'current_id' => $annonce_principale_id
]);
$autres_annonces = $stmt_autres_annonces->fetchAll(PDO::FETCH_ASSOC);


$categorie_actuelle_id = $annonce['categorie_id'];

$sql_similaires = "
    SELECT 
        a.id, a.titre, a.prix,
        (SELECT chemin FROM photos WHERE annonce_id = a.id ORDER BY position ASC LIMIT 1) AS photo_principale
    FROM annonces a
    WHERE 
        a.categorie_id = :cat_id
        AND a.statut = 'EN_VENTE'
        AND a.id != :current_id";

$params_similaires = [
    'cat_id' => $categorie_actuelle_id,
    'current_id' => $annonce_principale_id
];

if (isset($_SESSION['id'])) {
    $sql_similaires .= " AND a.vendeur_id != :user_id";
    $params_similaires['user_id'] = $_SESSION['id'];
}

$sql_similaires .= " ORDER BY RAND() LIMIT 4";

$stmt_similaires = $pdo->prepare($sql_similaires);
$stmt_similaires->execute($params_similaires);
$annonces_similaires = $stmt_similaires->fetchAll(PDO::FETCH_ASSOC);



$categories = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($annonce['titre']) ?> - E-Bazar</title>
    <link rel="stylesheet" href="../css/accuill.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/detaill_produit.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>

    <header class="navbar">
        <div class="logo">
            <span>E-Bazar</span><span class="dot">●</span>
            <input type="text" id="barre-recherche" placeholder="Que cherchez-vous ? " >
        </div>
        <div>
            <?php if (!isset($_SESSION['id'])): ?>
                <a href="connexion.php"><strong>Se connecter</strong></a>
                <a href="inscription.php" class="btn-outline">S'inscrire</a>
            <?php else: ?>
                <a href="profil.php"><button class="icon"><img src="../image/comptenoir.png" alt="Profil"></button></a>
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
                        <?php 
                        foreach ($photos as $index => $photo_chemin): 
                        ?>
                            <img 
                                src="../<?= htmlspecialchars($photo_chemin) ?>" 
                                alt="Miniature <?= $index + 1 ?>" 
                                class="thumbnail-item <?= ($index === 0) ? 'active' : '' ?>"
                                data-src="../<?= htmlspecialchars($photo_chemin) ?>"
                            >
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="main-image-container">
                        <img id="main-image" src="../<?= htmlspecialchars($photo_principale) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
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
                    <button class="acheter-button">Acheter</button>
                    <button class="reserver-button">Réserver</button>
                </div>
            </div>
        </div>
        
        
        <section class="product-suggestions">

            <div class="tab-header">
                <h2 
                    class="tab-link active" 
                    data-tab="vendeur-content">
                    Autres annonces de <?= htmlspecialchars($annonce['vendeur_pseudo']) ?>
                </h2>
                <h2 
                    class="tab-link" 
                    data-tab="similaires-content">
                    Annonces qui pourraient vous intéresser
                </h2>
            </div>


            <div id="vendeur-content" class="tab-content active">
                <?php if (!empty($autres_annonces)): ?>
                    <div class="produits">
                        <?php foreach ($autres_annonces as $autre_annonce): ?>
                            <div class="produit" onclick="window.location.href='annonce.php?id=<?= $autre_annonce['id'] ?>'">
                                <div class="image-container">
                                    <?php if ($autre_annonce['photo_principale']): ?>
                                        <img src="../<?= $autre_annonce['photo_principale'] ?>" alt="<?= htmlspecialchars($autre_annonce['titre']) ?>">
                                    <?php else: ?>
                                        <img src="../image/default.jpg" alt="Image par défaut">
                                    <?php endif; ?>
                                </div>
                                <p><?= htmlspecialchars($autre_annonce['titre']) ?></p>
                                <h2><?= number_format($autre_annonce['prix'], 2, ',', ' ') ?> €</h2>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-results">Ce vendeur n'a pas d'autres annonces en vente.</p>
                <?php endif; ?>
            </div>
        
            <div id="similaires-content" class="tab-content">
                <?php if (!empty($annonces_similaires)): ?>
                    <div class="produits">
                        <?php foreach ($annonces_similaires as $annonce_similaire): ?>
                            <div class="produit" onclick="window.location.href='annonce.php?id=<?= $annonce_similaire['id'] ?>'">
                                <div class="image-container">
                                    <?php if ($annonce_similaire['photo_principale']): ?>
                                        <img src="../<?= $annonce_similaire['photo_principale'] ?>" alt="<?= htmlspecialchars($annonce_similaire['titre']) ?>">
                                    <?php else: ?>
                                        <img src="../image/default.jpg" alt="Image par défaut">
                                    <?php endif; ?>
                                </div>
                                <p><?= htmlspecialchars($annonce_similaire['titre']) ?></p>
                                <h2><?= number_format($annonce_similaire['prix'], 2, ',', ' ') ?> €</h2>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-results">Aucune annonce similaire trouvée dans la même catégorie.</p>
                <?php endif; ?>
            </div>

        </section>
        
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mainImage = document.getElementById('main-image');
            const thumbnails = document.querySelectorAll('.thumbnail-item');

            if (mainImage && thumbnails.length > 0) {
                thumbnails.forEach(thumbnail => {
                    thumbnail.addEventListener('click', function() {
                        const newSrc = this.getAttribute('data-src');
                        mainImage.src = newSrc;
                        
                        thumbnails.forEach(item => item.classList.remove('active'));
                        this.classList.add('active');
                    });
                });
            }
            
            const tabLinks = document.querySelectorAll('.tab-link');
            const tabContents = document.querySelectorAll('.tab-content');

            tabLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    tabLinks.forEach(l => l.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));

                    this.classList.add('active');
                    document.getElementById(targetTab).classList.add('active');
                });
            });
        });
    </script>

</body>
</html>