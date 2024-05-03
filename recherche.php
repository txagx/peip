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
// Récupérer les tags sélectionnés depuis la requête GET
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["tags"])) {
    $selected_tags = $_GET["tags"];

    // Construire la requête SQL pour récupérer les recettes correspondant aux tags sélectionnés
    $sql = "SELECT DISTINCT r.* FROM Recette r JOIN Recette_Tag rt ON r.idRecette = rt.idRecette WHERE rt.idTag IN (";
    $sql .= implode(",", $selected_tags) . ")";
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Résultats de la recherche :</h2>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li><a href='recette.php?id=" . $row['idRecette'] . "'>" . $row['nom'] . "</a></li>"; // Ajout du lien vers la page de la recette
        }
        echo "</ul>";
    } else {
        echo "<p>Aucune recette trouvée pour les tags sélectionnés.</p>";
    }
}
?>

