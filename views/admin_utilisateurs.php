<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration – Utilisateurs</title>

    <link rel="stylesheet" href="css/accuill.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/admin_utilisateurs.css">
    <link rel="stylesheet" href="css/admin.css">

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
        <p>Administration</p>
        <h2>Gestion des Utilisateurs (<?= count($utilisateurs) ?>)</h2>
    </div>

    <?php if (!empty($success)): ?>
        <div class="message-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($erreur)): ?>
        <div class="message-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <div class="users-list">

        <?php if (!empty($utilisateurs)): ?>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pseudo</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Inscription</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['pseudo']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= date('d/m/Y', strtotime($user['date_creation'])) ?></td>
                            <td>
                                <form method="POST"
                                      action="admin_utilisateurs.php"
                                      style="display:inline;"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer l\'utilisateur <?= $user['pseudo'] ?> ? Cela supprimera toutes ses annonces et achats !');">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit"
                                            name="supprimer_utilisateur"
                                            class="delete-btn">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun autre utilisateur trouvé dans la base de données.</p>
        <?php endif; ?>

    </div>

</main>

</body>
</html>
