<?php

require_once '../models/User.php';

class profil
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function index()
    {
        if (!isset($_SESSION['id'])) {
            $redirect = urlencode($_SERVER['REQUEST_URI']);
            header("Location: connexion.php?redirect={$redirect}");
            exit();
        }

        $currentUserId = $_SESSION['id'];
        $message_profil = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $pseudo = trim($_POST['pseudo'] ?? '');
            $email  = trim($_POST['email'] ?? '');
            $mdp    = $_POST['mot_de_passe'] ?? '';

            if ($pseudo === '' || $email === '') {
                $message_profil['error'][] = "Le pseudo et l'email ne peuvent pas être vides.";
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message_profil['error'][] = "Le format de l'adresse email n'est pas valide.";
            }

            $data = [];

            if (empty($message_profil['error'])) {
                $data['pseudo'] = $pseudo;
                $data['email']  = $email;

                if (!empty($mdp)) {
                    if (strlen($mdp) < 6) {
                        $message_profil['error'][] = "Le mot de passe doit contenir au moins 6 caractères.";
                    } else {
                        $data['mot_de_passe'] = password_hash($mdp, PASSWORD_DEFAULT);
                    }
                }
            }

            if (empty($message_profil['error'])) {
                try {
                    $this->userModel->updateProfile($currentUserId, $data);
                    $_SESSION['pseudo'] = $pseudo;
                    $message_profil['success'] = "Vos informations de profil ont été mises à jour avec succès !";
                } catch (PDOException $e) {
                    if ($e->getCode() === '23000') {
                        $message_profil['error'][] = "Ce pseudo ou cette adresse email est déjà utilisé(e).";
                    } else {
                        $message_profil['error'][] = "Erreur lors de la mise à jour du profil.";
                    }
                }
            }
        }

        $user_info = $this->userModel->getById($currentUserId);

        if (!$user_info) {
            session_destroy();
            header("Location: connexion.php");
            exit();
        }

        $nombre_annonces = $this->userModel->countAnnonces($currentUserId);

        $current_pseudo = $_POST['pseudo'] ?? $user_info['pseudo'];
        $current_email  = $_POST['email'] ?? $user_info['email'];
        $date_inscription = date('d/m/Y', strtotime($user_info['date_creation']));

        require '../views/profil.php';
    }
}
