<?php
include '../config/config.php'; 

if (!isset($_SESSION['id'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: connexion.php?redirect={$redirect}");
    exit();
}

$current_user_id = $_SESSION['id'];
$erreur = [];
$success = "";

$categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = trim($_POST['prix'] ?? '');
    $categorie_id = (int)($_POST['categorie_id'] ?? 0);
    $livraison_postale = isset($_POST['livraison_postale']) ? 1 : 0;
    $livraison_main = isset($_POST['livraison_main']) ? 1 : 0;

    
    
    if (empty($titre) || empty($description) || $categorie_id === 0) {
        $erreur[] = "Veuillez remplir le titre, la description et choisir une catégorie.";
    }
    
    if (strlen($titre) < 5 || strlen($titre) > 30) {
        $erreur[] = "Le titre doit faire entre 5 et 30 caractères.";
    }

    if (strlen($description) < 5 || strlen($description) > 200) {
        $erreur[] = "La description doit faire entre 5 et 200 caractères.";
    }
    
    if (!is_numeric($prix) || $prix < 0) {
        $erreur[] = "Le prix doit être un nombre positif (ou zéro pour un don).";
    }
    
    
    if (!$livraison_postale && !$livraison_main) {
        $erreur[] = "Veuillez choisir au moins un mode de livraison.";
    }

    $chemins_photos = [];
    $dossier_uploads = __DIR__ . '/uploads/'; 
    $photos_soumises = $_FILES['photos'] ?? [];
    $limite_taille = 204800; 
    $max_photos = 5; 

    if (empty($photos_soumises['name'][0])) {
        $erreur[] = "Veuillez télécharger au moins une photo.";
    } else {
        
        $nombre_fichiers = count($photos_soumises['name']);
        
        for ($i = 0; $i < min($nombre_fichiers, $max_photos); $i++) { 
            if ($photos_soumises['error'][$i] === UPLOAD_ERR_OK) {
                
                $fichier_temporaire = $photos_soumises['tmp_name'][$i];
                $taille_fichier = $photos_soumises['size'][$i];
                $type_fichier = $photos_soumises['type'][$i];
                
                if ($type_fichier !== 'image/jpeg') {
                    $erreur[] = "La photo " . ($i + 1) . " doit être au format JPEG.";
                    continue; 
                }
                
                if ($taille_fichier > $limite_taille) {
                    $erreur[] = "La photo " . ($i + 1) . " dépasse la taille limite de 200 Kio.";
                    continue; 
                }

                $nom_unique = uniqid('annonce_') . '_' . basename($photos_soumises['name'][$i]);
                $chemin_destination = $dossier_uploads . $nom_unique;
                
                if (move_uploaded_file($fichier_temporaire, $chemin_destination)) {
                    $chemins_photos[] = 'uploads/' . $nom_unique;
                } else {
                    $erreur[] = "Erreur lors du déplacement du fichier " . ($i + 1) . ". Vérifiez les permissions du dossier 'uploads'.";
                }
            } else if ($photos_soumises['error'][$i] != UPLOAD_ERR_NO_FILE) {
                $erreur[] = "Erreur serveur lors de l'upload de la photo " . ($i + 1) . ". Code d'erreur: " . $photos_soumises['error'][$i];
            }
        }
        
       if (empty($chemins_photos) && empty($erreur)) {
             $erreur[] = "Aucune photo valide n'a pu être téléchargée (vérifiez taille/format).";
        }
    }
    if (empty($erreur)) {
        try {
            $pdo->beginTransaction();

            $insert_annonce = $pdo->prepare("
                INSERT INTO annonces (vendeur_id, categorie_id, titre, description, prix, livraison_postale, livraison_main) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $insert_annonce->execute([
                $current_user_id, 
                $categorie_id, 
                $titre, 
                $description, 
                $prix, 
                $livraison_postale, 
                $livraison_main
            ]);
            
            $annonce_id = $pdo->lastInsertId();

            $insert_photo = $pdo->prepare("
                INSERT INTO photos (annonce_id, chemin, position) VALUES (?, ?, ?)
            ");

            foreach ($chemins_photos as $position => $chemin) {
                $insert_photo->execute([$annonce_id, $chemin, $position + 1]);
            }

            $pdo->commit();
            $success = "L'annonce a été ajoutée avec succès !";
            
            header("Location: mes_annonces.php");
            exit();

        } catch (Exception $e) {
            $pdo->rollBack();
            $erreur[] = "Erreur lors de l'enregistrement en base de données : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une annonce - E-Bazar</title>
    <link rel="stylesheet" href="css/accuill.css?v=99">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/ajouter_annonce.css"> 
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
        <p>Vente</p>
        <h2>Ajouter une Nouvelle Annonce</h2>
    </div>

    <div class="form-container">
        
        <?php if (!empty($erreur)): ?>
            <ul class="error-list">
                <?php foreach ($erreur as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success-message"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="titre">Titre de l'annonce (min 5, max 30 caractères) :</label>
                <input type="text" id="titre" name="titre" required maxlength="30" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="categorie_id">Catégorie :</label>
                <select id="categorie_id" name="categorie_id" required>
                    <option value="">-- Choisir une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (isset($_POST['categorie_id']) && (int)$_POST['categorie_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description (min 5, max 200 caractères) :</label>
                <textarea id="description" name="description" rows="5" required maxlength="200"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="prix">Prix (€) :</label>
                <input type="number" id="prix" name="prix" step="0.01" min="0" required value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Options de Livraison :</label>
                <div class="checkbox-group">
                    <input type="checkbox" id="livraison_postale" name="livraison_postale" value="1" <?= isset($_POST['livraison_postale']) ? 'checked' : '' ?>>
                    <label for="livraison_postale">Livraison postale</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="livraison_main" name="livraison_main" value="1" <?= isset($_POST['livraison_main']) ? 'checked' : '' ?>>
                    <label for="livraison_main">Remise en main propre</label>
                </div>
            </div>

            <div class="form-group">
                <label for="photos">Photos (Max. 5 fichiers JPEG, 200 Kio chacun) :</label>
                <input type="file" id="photos" name="photos[]" accept="image/jpeg" multiple required>
            </div>

            <button type="submit" class="submit-btn">Soumettre l'Annonce</button>
        </form>
    </div>

</main>
<script src="js/app.js" defer></script>
</body>
</html>
