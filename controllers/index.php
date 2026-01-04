<?php
require_once '../models/annonce.php';

class index {
    private $pdo;
    private $annonceModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->annonceModel = new Annonce($pdo);
    }

    public function index() {
        $current_user_id = $_SESSION['id'] ?? null;
        $categorie_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
        $annonces_par_page = 10;

        $annonces = $this->annonceModel->getAnnonces($current_user_id, $categorie_id, $page, $annonces_par_page);
        $categories_nav = $this->annonceModel->getCategories($current_user_id);

        $titre_section = "Les dernières annonces mises en ligne";
        $sous_titre = "Nouveautés";
        if ($categorie_id) {
            $nom_categorie = $this->annonceModel->getCategorieNom($categorie_id);
            if ($nom_categorie) {
                $titre_section = "Annonces dans la catégorie " . $nom_categorie;
                $sous_titre = "Catégorie " . $nom_categorie;
            }
        }

        $total_annonces = $categorie_id ? $this->annonceModel->getTotalAnnonces($current_user_id, $categorie_id) : 0;
        $nombre_pages = $total_annonces ? ceil($total_annonces / $annonces_par_page) : 1;

        require_once '../views/index.php';
    }
}
