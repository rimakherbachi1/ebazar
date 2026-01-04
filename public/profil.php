<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/config.php'; 

if (!isset($_SESSION['id'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: connexion.php?redirect={$redirect}");
    exit();
}

$current_user_id = $_SESSION['id'];
$user_info = null;
$nombre_annonces = 0; 
$message_profil = []; // Tableau pour stocker les messages de succès ou d'erreur

// --- DÉBUT DU TRAITEMENT DU FORMULAIRE DE MODIFICATION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // NOTE: Pour que le formulaire fonctionne correctement, l'action doit être "profil.php"
    // et les champs doivent être nommés comme ci-dessous.
    $new_pseudo = trim($_POST['pseudo'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_mot_de_passe = $_POST['mot_de_passe'] ?? '';
    
    $update_fields = [];
    $execute_params = ['user_id' => $current_user_id];
    $has_errors = false;

    // 1. Validation de la non-vacuité et du format de l'email
    if (empty($new_pseudo) || empty($new_email)) {
        $message_profil['error'][] = "Le pseudo et l'email ne peuvent pas être vides.";
        $has_errors = true;
    }

    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message_profil['error'][] = "Le format de l'adresse email n'est pas valide.";
        $has_errors = true;
    }
    
    // 2. Préparation des champs de mise à jour
    if (!$has_errors) {
        
        $update_fields[] = "pseudo = :pseudo";
        $execute_params['pseudo'] = $new_pseudo;

        $update_fields[] = "email = :email";
        $execute_params['email'] = $new_email;

        // Validation et hashage du mot de passe s'il est fourni
        if (!empty($new_mot_de_passe)) {
            if (strlen($new_mot_de_passe) < 6) { 
                 $message_profil['error'][] = "Le mot de passe doit contenir au moins 6 caractères.";
                 $has_errors = true;
            } else {
                 $update_fields[] = "mot_de_passe = :mot_de_passe";
                 $execute_params['mot_de_passe'] = password_hash($new_mot_de_passe, PASSWORD_DEFAULT);
            }
        }
    }

    // 3. Exécution de la mise à jour
    if (!$has_errors && !empty($update_fields)) {
        try {
            $sql_update = "UPDATE utilisateurs SET " . implode(', ', $update_fields) . " WHERE id = :user_id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute($execute_params);
            
            $message_profil['success'] = "Vos informations de profil ont été mises à jour avec succès !";
            
            // Recharger la session si le pseudo a été modifié (important pour le header)
            if ($new_pseudo !== ($_SESSION['pseudo'] ?? '')) {
                 $_SESSION['pseudo'] = $new_pseudo;
            }

        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                 $message_profil['error'][] = "Ce pseudo ou cette adresse email est déjà utilisé(e).";
            } else {
                 $message_profil['error'][] = "Erreur de base de données lors de la mise à jour.";
            }
        }
    }
}
// --- FIN DU TRAITEMENT ---


// Récupération des données utilisateur (à faire APRES le POST pour afficher les données actualisées)
try {
    $stmt_user = $pdo->prepare("SELECT pseudo, email, date_creation FROM utilisateurs WHERE id = ?");
    $stmt_user->execute([$current_user_id]);
    $user_info_fetched = $stmt_user->fetch(PDO::FETCH_ASSOC);
    
    if (!$user_info_fetched) {
        session_destroy();
        header("Location: connexion.php");
        exit();
    }
    $user_info = $user_info_fetched;
    
    // Récupération des données postées en cas d'erreur de validation pour ne pas vider les champs
    $current_pseudo = $_POST['pseudo'] ?? $user_info['pseudo'];
    $current_email = $_POST['email'] ?? $user_info['email'];


    $stmt_count = $pdo->prepare("SELECT COUNT(id) FROM annonces WHERE vendeur_id = ?");
    $stmt_count->execute([$current_user_id]);
    $nombre_annonces = $stmt_count->fetchColumn();

} catch (Exception $e) {
    $user_info = ['pseudo' => 'Erreur', 'email' => 'Erreur', 'date_creation' => date('Y-m-d H:i:s')];
    $nombre_annonces = 'Erreur';
    $current_pseudo = 'Erreur';
    $current_email = 'Erreur';
}

$solde_actuel = 0.00;
$date_inscription = date('d/m/Y', strtotime($user_info['date_creation']));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil – E-Bazar</title>
    <link rel="stylesheet" href="css/accuill.css?v=99">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/profil.css">
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Poppins:wght@200;300;400&display=swap" rel="stylesheet">
    
</head>

<body>

<header class="navbar">
   <div class="logo">
        <a href="index.php"><span>E-Bazar</span><span class="dot">●</span></a>
       </div>

    <div>
        <?php if (!isset($_SESSION['id'])): ?>
            <a href="connexion.php"><strong>Se connecter</strong></a>
            <a href="inscription.php" class="btn-outline">S'inscrire</a>
        <?php else: ?>
            <a href="profil.php"><button class="icon"><img src="image/comptenoir.png" alt="Compte"></button></a>
            <a href="deconnexion.php">Deconnexion</a>
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
        <h2>Profil de <?= htmlspecialchars($user_info['pseudo']) ?></h2>
    </div>

    <?php if (!empty($message_profil['success'])): ?>
        <div class="message-success"><?= htmlspecialchars($message_profil['success']) ?></div>
    <?php elseif (!empty($message_profil['error'])): ?>
        <div class="message-error">
            <?php foreach ($message_profil['error'] as $err): ?>
                <p><?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="profile-grid">
        
        <div class="stat">
            
            <h4>Statistiques</h4>
            <p>Membre depuis : <strong><?= $date_inscription ?></strong></p>
            <p>Annonces postées : <strong><?= htmlspecialchars($nombre_annonces) ?></strong></p>
        </div>

        <div class="profile-details">
            <h3>Informations du Compte</h3>
            
            <form method="POST" action="profil.php"> 
                
                <div class="form-group">
                    <label for="pseudo">Pseudo :</label>
                    <input type="text" id="pseudo" name="pseudo" value="<?= htmlspecialchars($current_pseudo) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Adresse Email :</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($current_email) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="mot_de_passe">Nouveau Mot de Passe :</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Laisser vide pour ne pas changer">
                </div>

                <button type="submit" class="modifier-btn">Modifier le profil</button>

            </form>
        </div>
    </div>

</main>
<footer>
    <p style="text-align:center; padding:20px; margin-top:40px; color:#666;">
        © E-Bazar — 2025
    </p>
</footer>

<script src="js/app.js" defer></script>
</body>
</html>
