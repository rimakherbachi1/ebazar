<?php
session_start();
require_once '../config/config.php';
require_once '../controllers/mes_annonces.php';

$controller = new mesannonces($pdo);
$controller->index();
