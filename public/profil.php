<?php
session_start();

require_once '../config/config.php';
require_once '../controllers/profil.php';

$controller = new profil($pdo);
$controller->index();
