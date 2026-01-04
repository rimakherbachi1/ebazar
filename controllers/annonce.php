<?php
require_once '../models/annonce.php';

class annonceinfo {
    private $pdo;
    private $annonceModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->annonceModel = new Annonce($pdo);
    }

    public function show() {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header("Location: index.php");
            exit();
        }

        $annonce_id = (int)$_GET['id'];
        $current_user_id = $_SESSION['id'] ?? null;

        $annonce = $this->annonceModel->getAnnonceById($annonce_id);
        if (!$annonce) {
            die("Erreur : Cette annonce n'existe pas ou n'est plus disponible.");
        }

        $photos = $this->annonceModel->getPhotos($annonce_id);
        $photo_principale = ebazar_photo_src($photos[0] ?? '');

        $autres_annonces = $this->annonceModel->getAutresAnnoncesVendeur($annonce['vendeur_id'], $annonce_id);
        $annonces_similaires = $this->annonceModel->getAnnoncesSimilaires($annonce['categorie_id'], $annonce_id, $current_user_id);

        $categories = $this->annonceModel->getCategories();

        require_once '../views/annonce.php';
    }
}
