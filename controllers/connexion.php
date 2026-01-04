<?php

require_once '../models/user.php';

class connexion
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function login()
    {
        $erreur = "";
        $redirect = $_GET['redirect'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email = trim($_POST['email']);
            $motdepasse = trim($_POST['motdepasse']);
            $redirect = trim($_POST['redirect'] ?? '');

            $user = $this->userModel->connexion($email);

            if ($user && password_verify($motdepasse, $user['mot_de_passe'])) {

                $_SESSION['id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['pseudo'] = $user['pseudo'];

                if (!empty($redirect)) {
                    header("Location: $redirect");
                    exit();
                }

                if ($user['role'] === 'ADMIN') {
                    header("Location: admin_utilisateurs.php");
                } else {
                    header("Location: index.php");
                }
                exit();

            } else {
                $erreur = "Identifiants incorrects.";
            }
        }

        require '../views/connexion.php';
    }
}
