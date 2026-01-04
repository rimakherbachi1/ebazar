<?php
session_start();
require_once '../config/config.php';
require_once '../controllers/ajouter_annonce.php';

$controller = new ajouterannonce($pdo);
$controller->index();
