<?php
session_start();
require_once '../config/config.php';
require_once '../controllers/reserver.php';

$controller = new reserver($pdo);
$controller->show();
