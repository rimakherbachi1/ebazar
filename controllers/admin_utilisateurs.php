<?php

require_once '../models/User.php';

class adminutilisateurs
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function index()
    {
        $erreur = '';
        $success = '';

        if (!isset($_SESSION['id'])) {
            $redirect = urlencode($_SERVER['REQUEST_URI']);
            header("Location: connexion.php?redirect={$redirect}");
            exit();
        }

        if ($_SESSION['role'] !== 'ADMIN') {
            header("Location: index.php");
            exit();
        }

        $currentAdminId = $_SESSION['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_utilisateur'])) {
            $userId = (int)($_POST['user_id'] ?? 0);

            if ($userId > 0 && $userId !== $currentAdminId) {
                if ($this->userModel->deleteById($userId)) {
                    $success = "L'utilisateur ID {$userId} a été supprimé ainsi que toutes ses données liées.";
                } else {
                    $erreur = "Erreur lors de la suppression de l'utilisateur.";
                }
            } else {
                $erreur = "ID d'utilisateur invalide ou tentative de suppression de votre propre compte administrateur.";
            }
        }

        $utilisateurs = $this->userModel->getAllExcept($currentAdminId);

        require '../views/admin_utilisateurs.php';
    }
}
