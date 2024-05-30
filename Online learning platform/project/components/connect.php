<?php

// Paramètres de connexion à la base de données
$servername = "localhost"; // Adresse du serveur
$username = "root"; // Nom d'utilisateur
$password = ""; // Mot de passe
$dbname = "course_db"; // Nom de la base de données
$port = 3308; // Port MySQL (facultatif)

try {
    // Création d'une nouvelle connexion PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;port=$port", $username, $password);
    // Configuration du mode d'affichage des erreurs PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Affichage d'un message d'erreur en cas d'échec de la connexion
    echo "Connection failed: " . $e->getMessage();
    // Arrêt de l'exécution du script
    exit();
}

// Fonction pour générer un identifiant unique
function unique_id() {
    $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $rand = array();
    $length = strlen($str) - 1;
    for ($i = 0; $i < 20; $i++) {
        $n = mt_rand(0, $length);
        $rand[] = $str[$n];
    }
    return implode($rand);
}

?>
