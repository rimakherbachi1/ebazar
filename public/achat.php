<?php
session_start();
require_once '../config/config.php';
require_once '../controllers/achat.php';

$controller = new achater($pdo);
$controller->show();
