<?php
session_start();
require_once '../config/config.php';
require_once '../controllers/connexion.php';

$controller = new connexion($pdo);
$controller->login();
