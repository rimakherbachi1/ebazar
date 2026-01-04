<?php
session_start();

require_once '../config/config.php';
require_once '../controllers/mes_ventes.php';

$controller = new mesventes($pdo);
$controller->index();
