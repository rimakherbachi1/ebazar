<?php

require_once '../models/categorie.php';

class admincategorie
{
    private $categoryModel;

    public function __construct($pdo)
    {
        $this->categoryModel = new categorie($pdo);
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_categorie'])) {
            $nom = trim($_POST['nom_categorie'] ?? '');

            if ($nom === '') {
                $erreur = "Le nom de la catégorie ne peut pas être vide.";
            } elseif ($this->categoryModel->existsByName($nom)) {
                $erreur = "Cette catégorie existe déjà.";
            } else {
                if ($this->categoryModel->create($nom)) {
                    $success = "Catégorie '{$nom}' ajoutée avec succès !";
                } else {
                    $erreur = "Erreur lors de l'ajout en base de données.";
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['renommer_categorie'])) {
            $id = (int)($_POST['categorie_id'] ?? 0);
            $nouveauNom = trim($_POST['nouveau_nom'] ?? '');

            if ($id <= 0 || $nouveauNom === '') {
                $erreur = "Veuillez choisir une categorie et saisir un nouveau nom.";
            } elseif ($this->categoryModel->existsByNameExcept($nouveauNom, $id)) {
                $erreur = "Cette categorie existe deja.";
            } else {
                if ($this->categoryModel->rename($id, $nouveauNom)) {
                    $success = "Categorie renommee avec succes.";
                } else {
                    $erreur = "Erreur lors du renommage.";
                }
            }
        }

        $categories = $this->categoryModel->getAll();

        require '../views/admin_categories.php';
    }
}
