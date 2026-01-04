<?php

class annonce
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAnnoncesByStatut($statut)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                a.id, a.titre, a.prix, a.date_creation, a.statut,
                (SELECT chemin 
                 FROM photos 
                 WHERE annonce_id = a.id 
                 ORDER BY position ASC 
                 LIMIT 1) AS photo_principale,
                u.pseudo AS vendeur_pseudo
            FROM annonces a
            JOIN utilisateurs u ON a.vendeur_id = u.id
            WHERE a.statut = ?
            ORDER BY a.date_creation DESC
        ");

        $stmt->execute([$statut]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function supprimerAnnonce($annonceId)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE annonces SET statut = 'SUPPRIME' WHERE id = ?"
        );
        return $stmt->execute([$annonceId]);
    }
    public function getByVendeurAndStatut($userId, $statut)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                a.id, a.titre, a.prix, a.statut,
                (SELECT chemin FROM photos 
                 WHERE annonce_id = a.id 
                 ORDER BY position ASC 
                 LIMIT 1) AS photo_principale
            FROM annonces a
            WHERE a.vendeur_id = :user_id
              AND a.statut = :statut
            ORDER BY a.date_creation DESC
        ");

        $stmt->execute([
            'user_id' => $userId,
            'statut'  => $statut
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAnnoncesParStatut($userId, $statut)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                a.id, a.titre, a.prix, a.statut,
                (SELECT chemin FROM photos 
                 WHERE annonce_id = a.id 
                 ORDER BY position ASC LIMIT 1) AS photo_principale
            FROM annonces a
            WHERE a.vendeur_id = :user_id
              AND a.statut = :statut
            ORDER BY a.date_creation DESC
        ");

        $stmt->execute([
            'user_id' => $userId,
            'statut'  => $statut
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function supprimerAnnonceUser($annonceId, $userId)
    {
        $stmt = $this->pdo->prepare("
            UPDATE annonces
            SET statut = 'SUPPRIME'
            WHERE id = ? AND vendeur_id = ? AND statut = 'EN_VENTE'
        ");
        $stmt->execute([$annonceId, $userId]);

        return $stmt->rowCount() > 0;
    }
    public function getAnnonces($current_user_id = null, $categorie_id = null, $page = 1, $annonces_par_page = 10) {
        $limite = 4;
        $offset = 0;
        $execute_params = [];
        $where_clause = "a.statut = 'EN_VENTE'";

        if ($current_user_id) {
            $where_clause .= " AND a.vendeur_id != :user_id";
            $execute_params['user_id'] = $current_user_id;
        }

        if ($categorie_id) {
            $where_clause .= " AND a.categorie_id = :cat_id";
            $execute_params['cat_id'] = $categorie_id;
            $limite = $annonces_par_page;
            $offset = ($page - 1) * $annonces_par_page;
        }

        $sql = "
            SELECT a.*, 
                (SELECT chemin FROM photos WHERE annonce_id = a.id ORDER BY position ASC LIMIT 1) AS photo_principale
            FROM annonces a
            WHERE {$where_clause}
            ORDER BY a.id DESC
            LIMIT {$limite} OFFSET {$offset}
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($execute_params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalAnnonces($current_user_id = null, $categorie_id = null) {
        $execute_params = [];
        $where_clause = "a.statut = 'EN_VENTE'";

        if ($current_user_id) {
            $where_clause .= " AND a.vendeur_id != :user_id";
            $execute_params['user_id'] = $current_user_id;
        }

        if ($categorie_id) {
            $where_clause .= " AND a.categorie_id = :cat_id";
            $execute_params['cat_id'] = $categorie_id;
        }

        $sql = "SELECT COUNT(a.id) FROM annonces a WHERE {$where_clause}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($execute_params);
        return $stmt->fetchColumn();
    }

    public function getCategories($current_user_id = null) {
        $user_exclusion = $current_user_id ? " AND a.vendeur_id != {$current_user_id}" : "";
        $sql = "
            SELECT c.id, c.nom, COUNT(a.id) AS nombre_annonces
            FROM categories c
            LEFT JOIN annonces a ON a.categorie_id = c.id AND a.statut = 'EN_VENTE' {$user_exclusion}
            GROUP BY c.id, c.nom
            ORDER BY c.nom ASC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategorieNom($categorie_id) {
        $stmt = $this->pdo->prepare("SELECT nom FROM categories WHERE id = ?");
        $stmt->execute([$categorie_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? htmlspecialchars($row['nom']) : null;
    }
    public function getAnnonceById($annonce_id) {
        $stmt = $this->pdo->prepare("
            SELECT a.*, u.pseudo AS vendeur_pseudo
            FROM annonces a
            JOIN utilisateurs u ON a.vendeur_id = u.id
            WHERE a.id = ? AND a.statut = 'EN_VENTE'
        ");
        $stmt->execute([$annonce_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPhotos($annonce_id) {
        $stmt = $this->pdo->prepare("
            SELECT chemin 
            FROM photos 
            WHERE annonce_id = ? 
            ORDER BY position ASC
        ");
        $stmt->execute([$annonce_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getAutresAnnoncesVendeur($vendeur_id, $current_id) {
        $stmt = $this->pdo->prepare("
            SELECT a.id, a.titre, a.prix,
            (SELECT chemin FROM photos WHERE annonce_id = a.id ORDER BY position ASC LIMIT 1) AS photo_principale
            FROM annonces a
            WHERE a.vendeur_id = :vendeur_id AND a.statut = 'EN_VENTE' AND a.id != :current_id
            ORDER BY a.date_creation DESC LIMIT 4
        ");
        $stmt->execute(['vendeur_id' => $vendeur_id, 'current_id' => $current_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnnoncesSimilaires($categorie_id, $current_id, $current_user_id = null) {
        $sql = "
            SELECT a.id, a.titre, a.prix,
            (SELECT chemin FROM photos WHERE annonce_id = a.id ORDER BY position ASC LIMIT 1) AS photo_principale
            FROM annonces a
            WHERE a.categorie_id = :cat_id AND a.statut = 'EN_VENTE' AND a.id != :current_id
        ";
        $params = ['cat_id' => $categorie_id, 'current_id' => $current_id];

        if ($current_user_id) {
            $sql .= " AND a.vendeur_id != :user_id";
            $params['user_id'] = $current_user_id;
        }

        $sql .= " ORDER BY RAND() LIMIT 4";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategorie() {
        return $this->pdo->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
    public function ajouterAnnonce($data, $photos, $vendeur_id) {
        $errors = [];

        $titre = trim($data['titre'] ?? '');
        $description = trim($data['description'] ?? '');
        $prix = trim($data['prix'] ?? '');
        $categorie_id = (int)($data['categorie_id'] ?? 0);
        $livraison_postale = isset($data['livraison_postale']) ? 1 : 0;
        $livraison_main = isset($data['livraison_main']) ? 1 : 0;

        if (empty($titre) || empty($description) || $categorie_id === 0) {
            $errors[] = "Veuillez remplir le titre, la description et choisir une catégorie.";
        }

        if (strlen($titre) < 5 || strlen($titre) > 30) {
            $errors[] = "Le titre doit faire entre 5 et 30 caractères.";
        }

        if (strlen($description) < 5 || strlen($description) > 200) {
            $errors[] = "La description doit faire entre 5 et 200 caractères.";
        }

        if (!is_numeric($prix) || $prix < 0) {
            $errors[] = "Le prix doit être un nombre positif (ou zéro pour un don).";
        }

        if (!$livraison_postale && !$livraison_main) {
            $errors[] = "Veuillez choisir au moins un mode de livraison.";
        }

        $chemins_photos = [];
        $dossier_uploads = __DIR__ . '/../public/uploads/';
        $limite_taille = 204800;
        $max_photos = 5;

        if (empty($photos['name'][0])) {
            $errors[] = "Veuillez télécharger au moins une photo.";
        } else {
            $nombre_fichiers = count($photos['name']);
            for ($i = 0; $i < min($nombre_fichiers, $max_photos); $i++) {
                if ($photos['error'][$i] === UPLOAD_ERR_OK) {
                    $taille_fichier = $photos['size'][$i];
                    $type_fichier = $photos['type'][$i];

                    if ($type_fichier !== 'image/jpeg') {
                        $errors[] = "La photo " . ($i + 1) . " doit être au format JPEG.";
                        continue;
                    }

                    if ($taille_fichier > $limite_taille) {
                        $errors[] = "La photo " . ($i + 1) . " dépasse la taille limite de 200 Kio.";
                        continue;
                    }

                    $nom_unique = uniqid('annonce_') . '_' . basename($photos['name'][$i]);
                    $chemin_destination = $dossier_uploads . $nom_unique;

                    if (move_uploaded_file($photos['tmp_name'][$i], $chemin_destination)) {
                        $chemins_photos[] = 'uploads/' . $nom_unique;
                    } else {
                        $errors[] = "Erreur lors du déplacement du fichier " . ($i + 1);
                    }
                } elseif ($photos['error'][$i] != UPLOAD_ERR_NO_FILE) {
                    $errors[] = "Erreur serveur lors de l'upload de la photo " . ($i + 1);
                }
            }

            if (empty($chemins_photos) && empty($errors)) {
                $errors[] = "Aucune photo valide n'a pu être téléchargée.";
            }
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                INSERT INTO annonces 
                (vendeur_id, categorie_id, titre, description, prix, livraison_postale, livraison_main) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$vendeur_id, $categorie_id, $titre, $description, $prix, $livraison_postale, $livraison_main]);

            $annonce_id = $this->pdo->lastInsertId();

            $stmt_photo = $this->pdo->prepare("INSERT INTO photos (annonce_id, chemin, position) VALUES (?, ?, ?)");
            foreach ($chemins_photos as $position => $chemin) {
                $stmt_photo->execute([$annonce_id, $chemin, $position + 1]);
            }

            $this->pdo->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ['errors' => ["Erreur base de données : " . $e->getMessage()]];
        }
    }

}
