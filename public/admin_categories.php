<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/config.php'; 

$erreur = '';
$success = '';

// Vérification de la session et du rôle ADMIN (Sécurité)
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: index.php");
    exit();
}

// --- 1. TRAITEMENT DU FORMULAIRE D'AJOUT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_categorie'])) {
    
    $nom_categorie = trim($_POST['nom_categorie'] ?? '');

    if (empty($nom_categorie)) {
        $erreur = "Le nom de la catégorie ne peut pas être vide.";
    } else {
        try {
            // Vérifier si la catégorie existe déjà
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE nom = ?");
            $stmt_check->execute([$nom_categorie]);
            
            if ($stmt_check->fetchColumn() > 0) {
                $erreur = "Cette catégorie existe déjà.";
            } else {
                // Insertion dans la base de données
                $stmt_insert = $pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
                $stmt_insert->execute([$nom_categorie]);
                $success = "Catégorie '{$nom_categorie}' ajoutée avec succès !";
            }
        } catch (Exception $e) {
            $erreur = "Erreur lors de l'ajout en base de données.";
        }
    }
}


// --- 2. RÉCUPÉRATION DE TOUTES LES CATÉGORIES (pour l'affichage) ---
try {
    $stmt_categories = $pdo->query("SELECT id, nom, date_creation FROM categories ORDER BY nom ASC");
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
    $erreur .= ($erreur ? " " : "") . "Erreur de chargement des catégories.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration – Catégories</title>
    <link rel="stylesheet" href="../css/accuill.css?v=99">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/admin_categories.css">
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
        <h2>Gestion des Catégories</h2>
    </div>

    <?php if (!empty($success)): ?>
        <div class="message-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($erreur)): ?>
        <div class="message-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <div class="action-form">
        <h3>Ajouter une Catégorie</h3>
        <form method="POST" action="admin_categories.php">
            <input type="text" name="nom_categorie" placeholder="Nom de la nouvelle catégorie" required value="<?= htmlspecialchars($_POST['nom_categorie'] ?? '') ?>">
            <button type="submit" name="ajouter_categorie">Ajouter Catégorie</button>
        </form>
    </div>
    
    <div class="categories-list">
        <h3>Catégories Actuelles (<?= count($categories) ?>)</h3>
        
        <?php if (!empty($categories)): ?>
            <table class="categories-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Création</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= htmlspecialchars($cat['id']) ?></td>
                            <td><?= htmlspecialchars($cat['nom']) ?></td>
                            <td><?= date('d/m/Y', strtotime($cat['date_creation'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune catégorie trouvée dans la base de données.</p>
        <?php endif; ?>
    </div>

</main>

</body>
</html>