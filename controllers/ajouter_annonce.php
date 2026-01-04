<?php

require_once '../models/Annonce.php';

class ajouterannonce
{
    private $annonceModel;

    public function __construct($pdo)
    {
        $this->annonceModel = new Annonce($pdo);
    }

    public function index()
    {
        session_start();

        if (!isset($_SESSION['id'])) {
            $redirect = urlencode($_SERVER['REQUEST_URI']);
            header("Location: connexion.php?redirect={$redirect}");
            exit();
        }

        $current_user_id = $_SESSION['id'];
        $errors = [];
        $success = "";

        $categories = $this->annonceModel->getCategorie();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->annonceModel->ajouterAnnonce($_POST, $_FILES['photos'] ?? [], $current_user_id);

            if (isset($result['errors'])) {
                $errors = $result['errors'];
            } else {
                $success = "Annonce ajoutée avec succès !";
                header("Location: mes_annonces.php");
                exit();
            }
        }

        require '../views/ajouter_annonce.php';
    }
}
