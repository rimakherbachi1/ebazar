<?php
session_start();

require_once '../config/config.php';
require_once '../controllers/mes_achats.php';

$controller = new mesachat($pdo);
$controller->index();
