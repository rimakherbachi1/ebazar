<?php
include '../config/config.php'; 

if (!isset($_SESSION['id'])) {
    header("Location: connexion.php");
    exit();
}

$current_user_id = $_SESSION['id'];

$statuts_achats_affiches = ['EN_ATTENTE', 'RECU'];
$achats_par_statut = [];

$statut_mapping = [
    'EN_ATTENTE' => 'ACHETÉ (En cours)',
    'RECU' => 'RECU'
];

foreach ($statuts_achats_affiches as $statut_achat) {
    $sql_achats = "
        SELECT 
            a.id AS annonce_id, 
            a.titre, 
            a.prix,
            ac.date_achat,
            ac.statut AS statut_achat,
            ac.mode_livraison,
            (SELECT chemin FROM photos WHERE annonce_id = a.id ORDER BY position ASC LIMIT 1) AS photo_principale
        FROM achats ac
        JOIN annonces a ON ac.annonce_id = a.id
        WHERE 
            ac.acheteur_id = :user_id
            AND ac.statut = :statut_achat
        ORDER BY ac.date_achat DESC
    ";
    
    $stmt_achats = $pdo->prepare($sql_achats);
    
    $stmt_achats->execute([
        'user_id' => $current_user_id,
        'statut_achat' => $statut_achat
    ]);
    
    $achats_par_statut[$statut_achat] = $stmt_achats->fetchAll(PDO::FETCH_ASSOC);
}


$categories_nav = $pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Achats - E-Bazar</title>
    <link rel="stylesheet" href="../css/accuill.css?v=99">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/detaill_produit.css">
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
    <a href="profil.php" ><p>Mon profil</p></a>
    <a href="mes_ventes.php"><p>Mes Ventes</p></a>
    <a href="mes_achats.php"> <p>Mes Achats</p></a>
    <a href="mes_annonces.php"><p>Mes Annonces</p></a>
</nav>

<main>

    <div class="texte">
        <p>Achats</p>
        <h2>Mes Articles Achetés</h2>
    </div>

    <section class="product-suggestions">

        <div class="tab-header">
            <?php $is_first = true; ?>
            <?php foreach ($statuts_achats_affiches as $statut_achat): ?>
                <h2 
                    class="tab-link <?= $is_first ? 'active' : '' ?>" 
                    data-tab="<?= strtolower($statut_achat) ?>-content">
                    <?= htmlspecialchars($statut_mapping[$statut_achat]) ?> (<?= count($achats_par_statut[$statut_achat]) ?>)
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
                            <div class="produit" >
                                <div class="image-container">
                                    <?php if ($achat['photo_principale']): ?>
                                        <img src="../<?= $achat['photo_principale'] ?>" alt="<?= htmlspecialchars($achat['titre']) ?>">
                                    <?php else: ?>
                                        <img src="../image/default.jpg" alt="Image par défaut">
                                    <?php endif; ?>
                                </div>
                                <p><?= htmlspecialchars($achat['titre']) ?></p>
                                <p class="status-tag">Statut: <strong><?= htmlspecialchars($statut_mapping[$statut_achat]) ?></strong></p>
                                <p class="status-tag">Livraison: <?= htmlspecialchars($achat['mode_livraison']) ?></p>
                                <h2><?= number_format($achat['prix'], 2, ',', ' ') ?> €</h2>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-results">Vous n'avez aucun achat dans le statut "<?= htmlspecialchars($statut_mapping[$statut_achat]) ?>".</p>
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