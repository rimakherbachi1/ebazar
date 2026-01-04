<?php
session_start();
require_once '../config/config.php';
require_once '../controllers/annonce.php';

$controller = new annonceinfo($pdo);
$controller->show();
