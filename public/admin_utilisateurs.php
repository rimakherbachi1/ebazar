<?php
session_start();

require_once '../config/config.php';
require_once '../controllers/admin_utilisateurs.php';

$controller = new adminutilisateurs($pdo);
$controller->index();
