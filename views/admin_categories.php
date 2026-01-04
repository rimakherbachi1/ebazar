<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration – Catégories</title>

    <link rel="stylesheet" href="css/accuill.css?v=99">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/admin_categories.css">

    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@200;300;400&display=swap" rel="stylesheet">
</head>

<body>

<header class="navbar">
    <div class="logo">
        <span>E-Bazar</span><span class="dot">●</span>
    </div>
    <div>
        <a href="deconnexion.php">Deconnexion</a>
    </div>
</header>

<nav>
    <a href="admin_utilisateurs.php"><p>UTULISATEURS</p></a>
    <a href="admin_annonces.php"><p>ANNONCES</p></a>
    <a href="admin_categories.php"><p>CATEGORIES</p></a>
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
            <input type="text" name="nom_categorie"
                   placeholder="Nom de la nouvelle catégorie"
                   required
                   value="<?= htmlspecialchars($_POST['nom_categorie'] ?? '') ?>">
            <button type="submit" name="ajouter_categorie">Ajouter Catégorie</button>
        </form>
    </div>

    <div class="action-form">
        <h3>Renommer une categorie</h3>
        <form method="POST" action="admin_categories.php">
            <select name="categorie_id" required>
                <option value="">-- Choisir une categorie --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"
                        <?= (isset($_POST['categorie_id']) && (int)$_POST['categorie_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="text" name="nouveau_nom"
                   placeholder="Nouveau nom"
                   required
                   value="<?= htmlspecialchars($_POST['nouveau_nom'] ?? '') ?>">

            <button type="submit" name="renommer_categorie">Renommer</button>
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
