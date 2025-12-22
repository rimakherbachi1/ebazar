<?php
include '../config/config.php'; 

if (!isset($_SESSION['id'])) {
    header("Location: connexion.php");
    exit();
}

$current_user_id = $_SESSION['id'];

$statuts_affiches = ['RESERVER', 'VENDU', 'LIVRE'];
$annonces_par_statut = [];

foreach ($statuts_affiches as $statut) {
    $sql_annonces_statut = "
        SELECT 
            a.id, a.titre, a.prix, a.statut,
            (SELECT chemin FROM photos WHERE annonce_id = a.id ORDER BY position ASC LIMIT 1) AS photo_principale
        FROM annonces a
        WHERE 
            a.vendeur_id = :user_id
            AND a.statut = :statut
        ORDER BY a.date_creation DESC
    ";
    
    $stmt_annonces_statut = $pdo->prepare($sql_annonces_statut);
    
    $stmt_annonces_statut->execute([
        'user_id' => $current_user_id,
        'statut' => $statut
    ]);
    
    $annonces_par_statut[$statut] = $stmt_annonces_statut->fetchAll(PDO::FETCH_ASSOC);
}


$categories_nav = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Ventes - E-Bazar</title>
    <link rel="stylesheet" href="../css/accuill.css?v=99">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/detaill_produit.css">
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
    <a href="profil.php" ><p>Mon profil</p></a>
    <a href="mes_ventes.php"><p>Mes Ventes</p></a>
    <a href="mes_achats.php"> <p>Mes Achats</p></a>
    <a href="mes_annonces.php"><p>Mes Annonces</p></a>
</nav>

<main>

    <div class="texte">
        <p>Gestion</p>
        <h2>Mes Transactions</h2>
    </div>

    <section class="product-suggestions">

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


        <?php $is_first = true; ?>
        <?php foreach ($statuts_affiches as $statut): ?>
            <div id="<?= strtolower($statut) ?>-content" class="tab-content <?= $is_first ? 'active' : '' ?>">
                <?php if (!empty($annonces_par_statut[$statut])): ?>
                    <div class="produits">
                        <?php foreach ($annonces_par_statut[$statut] as $annonce_statut): ?>
                            <div class="produit" >
                                <div class="image-container">
                                    <?php if ($annonce_statut['photo_principale']): ?>
                                        <img src="../<?= $annonce_statut['photo_principale'] ?>" alt="<?= htmlspecialchars($annonce_statut['titre']) ?>">
                                    <?php else: ?>
                                        <img src="../image/default.jpg" alt="Image par défaut">
                                    <?php endif; ?>
                                </div>
                                <p><?= htmlspecialchars($annonce_statut['titre']) ?></p>
                                <p class="status-tag">Statut: <?= htmlspecialchars(str_replace('_', ' ', $annonce_statut['statut'])) ?></p>
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
 <script>
    document.addEventListener('DOMContentLoaded', function() {
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