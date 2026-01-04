<?php

require_once '../models/Annonce.php';

class adminannonce
{
    private $annonceModel;

    public function __construct($pdo)
    {
        $this->annonceModel = new annonce($pdo);
    }

    public function adminannonce()
    {
        $erreur = '';
        $success = '';

        if (!isset($_SESSION['id'])) {
            $redirect = urlencode($_SERVER['REQUEST_URI']);
            header("Location: connexion.php?redirect=$redirect");
            exit();
        }

        if ($_SESSION['role'] !== 'ADMIN') {
            header("Location: index.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_annonce'])) {
            $annonceId = (int)($_POST['annonce_id'] ?? 0);

            if ($annonceId > 0) {
                if ($this->annonceModel->supprimerAnnonce($annonceId)) {
                    $success = "L'annonce ID {$annonceId} a été marquée comme supprimée.";
                } else {
                    $erreur = "Erreur lors de la suppression.";
                }
            } else {
                $erreur = "ID d'annonce invalide.";
            }
        }

        $statut_cible = 'EN_VENTE';
        $annonces = $this->annonceModel->getAnnoncesByStatut($statut_cible);

        require '../views/admin_annonces.php';
    }
}
