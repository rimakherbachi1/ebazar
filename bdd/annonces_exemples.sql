-- 1) Annonce Femme
INSERT INTO annonces 
(vendeur_id, categorie_id, titre, description, prix, livraison_postale, livraison_main, statut)
VALUES
(1, 1, 'Robe d\'été neuve', 'Robe légère parfaite pour l\'été, taille M, très bon état.', 25.00, 1, 1, 'en_vente');

INSERT INTO photos (annonce_id, chemin, position)
VALUES (LAST_INSERT_ID(), 'robe.jpg', 1);



-- 2) Annonce Homme
INSERT INTO annonces 
(vendeur_id, categorie_id, titre, description, prix, livraison_postale, livraison_main, statut)
VALUES
(1, 2, 'Veste en jean', 'Veste en jean bleu, taille L, excellent état.', 40.00, 0, 1, 'en_vente');

INSERT INTO photos (annonce_id, chemin, position)
VALUES (LAST_INSERT_ID(), 'veste.jpg', 1);



-- 3) Annonce Enfant
INSERT INTO annonces 
(vendeur_id, categorie_id, titre, description, prix, livraison_postale, livraison_main, statut)
VALUES
(1, 3, 'Jeu éducatif enfant', 'Jeu éducatif complet pour enfants 4-7 ans.', 12.00, 1, 0, 'en_vente');

INSERT INTO photos (annonce_id, chemin, position)
VALUES (LAST_INSERT_ID(), 'jeu.jpg', 1);



-- 4) Annonce Maison
INSERT INTO annonces 
(vendeur_id, categorie_id, titre, description, prix, livraison_postale, livraison_main, statut)
VALUES
(1, 4, 'Lampe de décoration', 'Lampe moderne, fonctionne parfaitement.', 18.50, 1, 1, 'en_vente');

INSERT INTO photos (annonce_id, chemin, position)
VALUES (LAST_INSERT_ID(), 'lampe.jpg', 1);



-- 5) Annonce Électronique
INSERT INTO annonces 
(vendeur_id, categorie_id, titre, description, prix, livraison_postale, livraison_main, statut)
VALUES
(1, 5, 'Casque Bluetooth', 'Casque sans fil, autonomie 20h, très bon état.', 55.00, 1, 0, 'en_vente');

INSERT INTO photos (annonce_id, chemin, position)
VALUES (LAST_INSERT_ID(), 'casque.jpg', 1);



-- 6) Annonce Autre
INSERT INTO annonces 
(vendeur_id, categorie_id, titre, description, prix, livraison_postale, livraison_main, statut)
VALUES
(1, 6, 'Tasse artisanale', 'Tasse japonaise faite main, unique.', 15.00, 1, 1, 'en_vente');

INSERT INTO photos (annonce_id, chemin, position)
VALUES (LAST_INSERT_ID(), 'tasse.jpg', 1);
