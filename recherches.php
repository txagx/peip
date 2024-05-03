<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de la recherche</title>
    <link href="style-recherches.css" rel="stylesheet" type="text/css">
</head>
<body>
<a href='index.php'><h1>INDEX</h1></a><br>
<a href='ajoutDB.html'>ajoutDB</a><br>
<a href='login.php'>login</a><br>
<a href='register.php'>register</a><br>
<a href='recherches.php'>recherche</a><br>
<a href='welcome.php'>profil</a><br>

<h2>Recherche de recettes :</h2>

<form method="get" action="recherches.php">
    <label for="text">Recherche par texte :</label><br>
    <input type="text" id="text" name="text"><br><br>
    
    <h3>Sélectionnez des tags pour la recherche :</h3>
    <div class="tags-container">
        <?php
        // Connexion à la base de données
        require_once 'config.php';
        $nb_col = 10;

        // Récupérer les tags disponibles depuis la base de données
        $sql = "SELECT * FROM Tag";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $num_tags = $result->num_rows;
            $num_columns = ceil($num_tags / $nb_col); // Calculer le nombre de colonnes nécessaires

            for ($i = 0; $i < $num_columns; $i++) {
                echo "<div class='tags-column'>";
                for ($j = 0; $j < $nb_col && $row = $result->fetch_assoc(); $j++) {
                    echo '<input type="checkbox" name="tags[]" value="' . $row['idTag'] . '"> ' . $row['libelle'] . '<br>';
                }
                echo "</div>";
            }
        } else {
            echo "Aucun tag trouvé.";
        }
        ?>
    </div>
    <br>
    <input type="submit" value="Rechercher">
</form>

<?php
// Vérifier si une recherche est effectuée
if ($_SERVER["REQUEST_METHOD"] == "GET" && (isset($_GET["text"]) || isset($_GET["tags"]))) {
    // Récupérer le texte de recherche et les tags sélectionnés
    $text = isset($_GET["text"]) ? $_GET["text"] : "";
    $selected_tags = isset($_GET["tags"]) ? $_GET["tags"] : array();

    // Construire la requête SQL pour récupérer les recettes correspondant aux critères de recherche
    $sql = "SELECT DISTINCT r.* FROM Recette r ";
    
    // Si des tags sont sélectionnés, ajouter la jointure avec la table Recette_Tag
    if (!empty($selected_tags)) {
        $sql .= "JOIN Recette_Tag rt ON r.idRecette = rt.idRecette ";
    }

    $conditions = array();

    // Ajouter la condition pour la recherche par texte si un texte est spécifié
    if (!empty($text)) {
        $conditions[] = "r.nom LIKE '%" . $text . "%'";
    }

    // Ajouter la condition pour les tags sélectionnés
    foreach ($selected_tags as $tag_id) {
        $conditions[] = "rt.idTag = " . intval($tag_id);
    }

    // S'il y a au moins une condition, ajouter WHERE à la requête SQL
    if (!empty($conditions)) {
        $sql .= "WHERE " . implode(" OR ", $conditions);
    }

    // Exécuter la requête SQL si au moins une condition est spécifiée
    if (!empty($text) || !empty($selected_tags)) {
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<h2>Résultats de la recherche :</h2>";
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li><a href='recette.php?id=" . $row['idRecette'] . "'>" . $row['nom'] . "</a></li>"; // Ajout du lien vers la page de la recette
            }
            echo "</ul>";
        } else {
            echo "<p>Aucune recette trouvée pour les critères de recherche spécifiés.</p>";
        }
    } else {
        echo "<p>Veuillez spécifier au moins un critère de recherche.</p>";
    }
}
?>

</body>
</html>
