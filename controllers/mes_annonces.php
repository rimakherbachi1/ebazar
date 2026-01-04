<?php
require_once '../models/annonce.php';

class mesannonces
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
        $erreur = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_annonce'])) {
            $annonceId = (int)($_POST['annonce_id'] ?? 0);
            if ($annonceId > 0) {
                try {
                    if ($this->annonceModel->supprimerAnnonceUser($annonceId, $current_user_id)) {
                        $success = "Annonce supprimée.";
                    } else {
                        $erreur = "Impossible de supprimer cette annonce.";
                    }
                } catch (Exception $e) {
                    $erreur = "Erreur lors de la suppression de l'annonce.";
                }
            } else {
                $erreur = "Annonce invalide.";
            }
        }

        $statuts_affiches = ['EN_VENTE', 'SUPPRIME'];
        $annonces_par_statut = [];

        foreach ($statuts_affiches as $statut) {
            $annonces_par_statut[$statut] = $this->annonceModel->getAnnoncesParStatut($current_user_id, $statut);
        }

        require '../views/mes_annonces.php';
    }
}
