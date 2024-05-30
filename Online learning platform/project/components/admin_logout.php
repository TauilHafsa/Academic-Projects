<?php

// Inclusion du fichier de connexion à la base de données
include 'connect.php';

// Suppression du cookie 'tutor_id'
setcookie('tutor_id', '', time() - 1, '/');

// Redirection vers la page de connexion de l'administrateur
header('location:../admin/login.php');

?>
