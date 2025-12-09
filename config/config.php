<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$DB_HOTE = "localhost";
$DB_NOM = "ebazar";
$DB_UTILISATEUR = "root";
$DB_MOTDEPASSE = "m.HN010423@";


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
