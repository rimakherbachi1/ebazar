<?php

require_once '../models/annonce.php';

class mesventes
{
    private $annonceModel;

    public function __construct($pdo)
    {
        $this->annonceModel = new Annonce($pdo);
    }

    public function index()
    {
        if (!isset($_SESSION['id'])) {
            $redirect = urlencode($_SERVER['REQUEST_URI']);
            header("Location: connexion.php?redirect={$redirect}");
            exit();
        }

        $current_user_id = $_SESSION['id'];

        $statuts_affiches = ['RESERVER', 'VENDU', 'LIVRE'];
        $annonces_par_statut = [];

        foreach ($statuts_affiches as $statut) {
            $annonces_par_statut[$statut] =
                $this->annonceModel->getByVendeurAndStatut($current_user_id, $statut);
        }

        require '../views/mes_ventes.php';
    }
}
