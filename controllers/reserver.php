<?php
require_once '../models/reserver.php';

class reserver {
    private $pdo;
    private $reservationModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->reservationModel = new reserveation($pdo);
    }

    public function show() {
        if (!isset($_SESSION['id'])) {
            $redirect = urlencode($_SERVER['REQUEST_URI']);
            header("Location: connexion.php?redirect={$redirect}");
            exit();
        }

        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header("Location: index.php");
            exit();
        }

        $annonce_id = (int)$_GET['id'];
        $acheteur_id = $_SESSION['id'];
        $success = false;
        $erreur = null;

        $annonce = $this->reservationModel->getAnnonce($annonce_id);
        if (!$annonce) {
            die("Annonce introuvable ou déjà indisponible.");
        }

        if ($annonce['vendeur_id'] == $acheteur_id) {
            die("Vous ne pouvez pas réserver votre propre article.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_reservation'])) {
            $mode_livraison = $_POST['mode_livraison'] ?? '';
            $mode_valide = false;

            if ($mode_livraison === 'POSTALE' && $annonce['livraison_postale']) $mode_valide = true;
            if ($mode_livraison === 'MAIN' && $annonce['livraison_main']) $mode_valide = true;

            if ($mode_valide) {
                try {
                    $this->reservationModel->reserverAnnonce($annonce_id, $acheteur_id, $mode_livraison);
                    $success = true;
                } catch (Exception $e) {
                    $erreur = $e->getMessage();
                    if ($erreur === '') $erreur = "Erreur lors de la réservation.";
                }
            } else {
                $erreur = "Veuillez choisir un mode de livraison valide.";
            }
        }

        require_once '../views/reserver.php';
    }
}
