<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$DB_HOTE = "localhost";
$DB_NOM = "projet";
$DB_UTILISATEUR = "projet";
$DB_MOTDEPASSE = "tejorp";


try {
    $pdo = new PDO(
        "mysql:host=$DB_HOTE;dbname=$DB_NOM;charset=utf8",
        $DB_UTILISATEUR,
        $DB_MOTDEPASSE,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
} catch (Exception $e) {
    die("Erreur de connexion à la base : " . $e->getMessage());
}

function ebazar_photo_src($chemin, $fallback = 'image/haut.jpg') {
    $path = trim((string) ($chemin ?? ''));
    if ($path === '') {
        return $fallback;
    }

    $path = str_replace('\\', '/', $path);
    if (preg_match('#^(https?:)?//#', $path) || strpos($path, 'data:') === 0) {
        return $path;
    }

    $path = preg_replace('#^(\.\./)+#', '', $path);
    if (strpos($path, 'public/') === 0) {
        $path = substr($path, strlen('public/'));
    }
    $path = ltrim($path, '/');

    if (strpos($path, 'uploads/') === 0 || strpos($path, 'image/') === 0) {
        return $path;
    }
    if (strpos($path, '/') === false) {
        return 'uploads/' . $path;
    }

    return $path;
}
