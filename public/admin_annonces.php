<?php
session_start();

require_once '../config/config.php';
require_once '../controllers/admin_annonces.php';

$controller = new adminannonce($pdo);
$controller->adminannonce();
