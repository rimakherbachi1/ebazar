<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/config.php'; 

if (!isset($_SESSION['id'])) {
    header("Location: connexion.php");
    exit();
}

$current_user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmer_reception'])) {
    $achat_id_to_update = (int)$_POST['achat_id'];
    
    try {
        $pdo->beginTransaction();

        $stmt_update_achat = $pdo->prepare("
            UPDATE achats 
            SET statut = 'RECU', date_reception = NOW() 
            WHERE id = ? AND acheteur_id = ?
        ");
        $stmt_update_achat->execute([$achat_id_to_update, $current_user_id]);

        $stmt_update_annonce = $pdo->prepare("
            UPDATE annonces SET statut = 'LIVRE' 
            WHERE id = (SELECT annonce_id FROM achats WHERE id = ?)
        ");
        $stmt_update_annonce->execute([$achat_id_to_update]);

        $pdo->commit();
        header("Location: mes_achats.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $erreur = "Erreur lors de la confirmation.";
    }
}

$statuts_achats_affiches = ['EN_ATTENTE', 'RECU'];
$achats_par_statut = [];

$statut_mapping = [
    'EN_ATTENTE' => 'ACHETÉ (En cours)',
    'RECU' => 'RECU'
];

foreach ($statuts_achats_affiches as $statut_achat) {
    $sql_achats = "
        SELECT 
            ac.id AS achat_id, 
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
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@200;300;400;600&display=swap" rel="stylesheet">
   
</head>

<body>
<header class="navbar">
   <div class="logo">
        <a href="index.php"><span>E-Bazar</span><span class="dot">●</span></a>
        <input type="text" id="barre-recherche" placeholder="Que cherchez-vous ? ">
   </div>
    <div>
        <a href="profil.php"><button class="icon"><img src="../image/comptenoir.png" alt="Compte"></button></a>
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

    <section class="product-suggestions">
        <div class="tab-header">
            <?php $is_first = true; ?>
            <?php foreach ($statuts_achats_affiches as $statut_achat): ?>
                <h2 class="tab-link <?= $is_first ? 'active' : '' ?>" data-tab="<?= strtolower($statut_achat) ?>-content">
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
                            <div class="produit">
                                <div class="image-container">
                                    <img src="../<?= $achat['photo_principale'] ?: 'image/default.jpg' ?>" alt="Produit">
                                </div>
                                <p><strong><?= htmlspecialchars($achat['titre']) ?></strong></p>
                                <p class="status-tag">Livraison : <?= $achat['mode_livraison'] === 'POSTALE' ? 'Poste' : 'Main propre' ?></p>
                                <h2><?= number_format($achat['prix'], 2, ',', ' ') ?> €</h2>

                                <?php if ($statut_achat === 'EN_ATTENTE'): ?>
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