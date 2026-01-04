<?php

require_once '../models/achat.php';

class mesachat
{
    private $achatModel;

    public function __construct($pdo)
    {
        $this->achatModel = new Achat($pdo);
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $achatId = (int)($_POST['achat_id'] ?? 0);

            try {
                if (isset($_POST['confirmer_reception'])) {
                    $this->achatModel->confirmerReception($achatId, $current_user_id);
                }

                if (isset($_POST['confirmer_reservation'])) {
                    $this->achatModel->confirmerReservation($achatId, $current_user_id);
                }

                if (isset($_POST['annuler_reservation'])) {
                    $this->achatModel->annulerReservation($achatId, $current_user_id);
                }

                header("Location: mes_achats.php");
                exit();

            } catch (Exception $e) {
                $erreur = "Erreur lors du traitement.";
            }
        }

        $statuts_achats_affiches = ['EN_ATTENTE'];
        $statut_mapping = ['EN_ATTENTE' => 'En cours'];
        $achats_par_statut = [];

        foreach ($statuts_achats_affiches as $statut) {
            $achats_par_statut[$statut] =
                $this->achatModel->getAchatsParStatut($current_user_id, $statut);
        }

        require '../views/mes_achats.php';
    }
}
