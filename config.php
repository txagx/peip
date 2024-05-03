<?php

// Connexion à la base de données

$chemin_fichier = "config.txt";
$lignes = file($chemin_fichier, FILE_IGNORE_NEW_LINES);

$server_name = "";
$login = "";
$password = "";
$nom_db = "";

foreach ($lignes as $ligne) {
    list($cle, $valeur) = explode(" : ", $ligne);
    switch ($cle) {
        case 'server_name':
            $server_name = trim($valeur);
            break;
        case 'login':
            $login = trim($valeur);
            break;
        case 'password':
            $password = trim($valeur);
            break;
        case 'nom_db':
            $nom_db = trim($valeur);
            break;
        default:
            break;
    }
}

$conn = new mysqli($server_name, $login, $password, $nom_db);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}



try {
    // Connexion à la base de données MySQL en utilisant PDO
    $db = new PDO("mysql:host=$server_name;dbname=$nom_db", $login, $password);
    // Définir le mode d'erreur PDO sur Exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // En cas d'erreur de connexion, afficher l'erreur
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
    exit;
}
?>
