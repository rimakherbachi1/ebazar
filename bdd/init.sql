-- -----------------------------------------------------
--   INITIALISATION DE LA BASE ebazar
-- -----------------------------------------------------
DROP TABLE IF EXISTS achats;
DROP TABLE IF EXISTS photos;
DROP TABLE IF EXISTS annonces;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS utilisateurs;

-- -----------------------------------------------------
--   TABLE UTILISATEURS
-- -----------------------------------------------------
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('UTILISATEUR', 'ADMIN') NOT NULL DEFAULT 'UTILISATEUR',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Ajout de l'administrateur par défaut
-- Mot de passe = admin
INSERT INTO utilisateurs (pseudo ,email, mot_de_passe, role) VALUES (
    'admin1',
    'admin@ebazar.local',
    '$2y$10$0e7cVE3D0pY5bY2B2ixO9u6crBqv3TRPZwUuuUspz6FHa3uLAX932', 
    'ADMIN'
);

-- -----------------------------------------------------
--   TABLE CATEGORIES
-- -----------------------------------------------------
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Catégories de base
INSERT INTO categories (nom) VALUES
('Homme'),
('Femme'),
('Enfant'),
('Electronique'),
('Maison'),
('Autre');

-- -----------------------------------------------------
--   TABLE ANNONCES
-- -----------------------------------------------------
CREATE TABLE annonces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendeur_id INT NOT NULL,
    categorie_id INT NOT NULL,
    titre VARCHAR(30) NOT NULL,
    description TEXT NOT NULL,
    prix DECIMAL(10,2) NOT NULL CHECK (prix >= 0),
    livraison_postale TINYINT(1) NOT NULL DEFAULT 0,
    livraison_main TINYINT(1) NOT NULL DEFAULT 0,
    statut ENUM('EN_VENTE', 'VENDU','RESERVER', 'LIVRE', 'SUPPRIME') 
        NOT NULL DEFAULT 'EN_VENTE',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (vendeur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE INDEX idx_annonces_statut_vendeur ON annonces (statut, vendeur_id);
CREATE INDEX idx_annonces_statut_categorie ON annonces (statut, categorie_id);

-- -----------------------------------------------------
--   TABLE PHOTOS
-- -----------------------------------------------------
CREATE TABLE photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    annonce_id INT NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    position INT NOT NULL DEFAULT 1,

    FOREIGN KEY (annonce_id) REFERENCES annonces(id) ON DELETE CASCADE
);

CREATE INDEX idx_photos_annonce_position ON photos (annonce_id, position);

-- -----------------------------------------------------
--   TABLE ACHATS
-- -----------------------------------------------------
CREATE TABLE achats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    annonce_id INT NOT NULL,
    acheteur_id INT NOT NULL,
    mode_livraison ENUM('POSTALE', 'MAIN') NOT NULL,
    date_achat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_reception DATETIME NULL,
    statut ENUM('EN_ATTENTE', 'RECU') NOT NULL DEFAULT 'EN_ATTENTE',

    FOREIGN KEY (annonce_id) REFERENCES annonces(id) ON DELETE CASCADE,
    FOREIGN KEY (acheteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

CREATE INDEX idx_achats_acheteur_statut ON achats (acheteur_id, statut);
CREATE INDEX idx_achats_annonce ON achats (annonce_id);
