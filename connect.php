<?php
// connexion.php

$host = "localhost";
$user = "root";
$password = "";  // Mets ton mot de passe ici si nécessaire
$dbname = "test"; // Mets le nom de ta base ici

$conn = new mysqli($host, $user, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Facultatif : forcer l'encodage en UTF-8
$conn->set_charset("utf8");
?>

<link rel="stylesheet" href="theme.css">

