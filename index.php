<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des recettes</title>
</head>
<body>
<a href='index.php'><h1>INDEX</h1></a><br>
<a href='ajoutDB.html'>ajoutDB</a><br>
<a href='login.php'>login</a><br>
<a href='register.php'>register</a><br>
<a href='recherches.php'>recherche</a><br>
<a href='welcome.php'>profil</a><br>

<?php
session_start();
require_once 'config.php';

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

// Nombre de recettes par page
$recettesParPage = 20;

// Récupérer le numéro de la page à afficher
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $pageCourante = $_GET['page'];
} else {
    $pageCourante = 1;
}

// Calculer l'indice de départ
$indiceDepart = ($pageCourante - 1) * $recettesParPage;

// Requête SQL pour récupérer les recettes avec pagination
$sql = "SELECT idRecette, nbpersonne FROM Recette LIMIT $indiceDepart, $recettesParPage";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h1>Liste des recettes :</h1>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li><a href='recette.php?id=" . $row["idRecette"] . "'>Recette " . $row["idRecette"] . "</a></li>";
    }
    echo "</ul>";

    // Afficher les liens de pagination
    $sqlTotalRecettes = "SELECT COUNT(*) AS totalRecettes FROM Recette";
    $resultTotalRecettes = $conn->query($sqlTotalRecettes);
    $rowTotalRecettes = $resultTotalRecettes->fetch_assoc();
    $totalRecettes = $rowTotalRecettes['totalRecettes'];
    $totalPages = ceil($totalRecettes / $recettesParPage);

    echo "<div>";
    for ($i = 1; $i <= $totalPages; $i++) {
        echo "<a href='index.php?page=$i'>$i</a> ";
    }
    echo "</div>";
} else {
    echo "Aucune recette trouvée.";
}

$conn->close();
?>
</body>
</html>
