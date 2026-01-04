<?php

require_once '../models/user.php';

class inscription
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function inscription()
    {
        $erreur = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $pseudo = trim($_POST['pseudo']);
            $email = trim($_POST['email']);
            $motdepasse = trim($_POST['motdepasse']);

            if (empty($pseudo) || empty($email) || empty($motdepasse)) {
                $erreur = "Tous les champs doivent être remplis.";
            } else {

                $existant = $this->userModel->findByEmailOrPseudo($email, $pseudo);

                if ($existant) {
                    if ($existant['email'] === $email) {
                        $erreur = "Un compte existe déjà avec cet email.";
                    } elseif ($existant['pseudo'] === $pseudo) {
                        $erreur = "Ce pseudo est déjà pris.";
                    }
                } else {

                    if ($this->userModel->create($pseudo, $email, $motdepasse)) {
                        header("Location: connexion.php?inscription=ok");
                        exit();
                    } else {
                        $erreur = "Erreur lors de l’inscription.";
                    }
                }
            }
        }

        require '../views/inscription.php';
    }
}
