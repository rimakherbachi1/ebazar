<?php
require_once '../models/achat.php';

class achater {
    private $pdo;
    private $achatModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->achatModel = new Achat($pdo);
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
        $facture = null;
        $erreur = null;

        $annonce = $this->achatModel->getAnnonce($annonce_id);
        if (!$annonce) {
            die("Annonce introuvable ou déjà vendue.");
        }

        if ($annonce['vendeur_id'] == $acheteur_id) {
            die("Vous ne pouvez pas acheter votre propre article.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_achat'])) {
            $mode_livraison = $_POST['mode_livraison'] ?? '';
            $mode_valide = false;

            if ($mode_livraison === 'POSTALE' && $annonce['livraison_postale']) $mode_valide = true;
            if ($mode_livraison === 'MAIN' && $annonce['livraison_main']) $mode_valide = true;

            if ($mode_valide) {
                try {
                    $result = $this->achatModel->effectuerAchat($annonce_id, $acheteur_id, $mode_livraison);
                    $facture = [
                        'vendeur' => $annonce['vendeur_pseudo'],
                        'acheteur' => $result['acheteur'],
                        'objet' => $annonce['titre'],
                        'prix' => $annonce['prix'],
                        'livraison' => ($mode_livraison === 'POSTALE') ? 'Livraison Postale' : 'Remise en main propre',
                        'date' => date('d/m/Y H:i')
                    ];
                    $success = true;
                } catch (Exception $e) {
                    $erreur = $e->getMessage();
                }
            } else {
                $erreur = "Veuillez choisir un mode de livraison valide.";
            }
        }

        require_once '../views/achat.php';
    }
}
