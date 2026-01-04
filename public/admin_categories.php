<?php
session_start();

require_once '../config/config.php';
require_once '../controllers/admin_categories.php';

$controller = new admincategorie($pdo);
$controller->index();
