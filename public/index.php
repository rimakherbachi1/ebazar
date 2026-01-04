<?php
require_once '../config/config.php';
require_once '../controllers/index.php';

$controller = new index($pdo);
$controller->index();
