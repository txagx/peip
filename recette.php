
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récupération des éléments d'une table</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<a href='index.php'><h1>INDEX</h1></a><br>
    <?php
    session_start();
    require_once 'config.php'; // Inclure le fichier de configuration de la base de données
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


// Vérification de la présence de l'ID de la recette dans l'URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idRecette = $_GET['id'];

    // Récupération des détails de la recette depuis la base de données
    $sql = "SELECT * FROM Recette WHERE idRecette = $idRecette";
    $result = $conn->query($sql);
}



if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $tpsTotal = (int)$row["tpsPrep"]+(int)$row["tpsCuis"]+(int)$row["tpsRep"];
    echo '<div class="everything">';
    echo '<div class="titre"> ';
    echo '    <p>' . $row['nom'] . '</p>';
    echo '</div>';
    echo '       <div class="image"> ';
    echo '           <div class="img"><img src="'. $row['image_path'] .'"></div>';
    echo '       </div>';
    echo '       <div class="preparation"> ';
    echo '           <p>';
    echo '               Temps total : '.$tpsTotal.'min<br>';
    echo '               Temps de preparation : '. $row["tpsPrep"] . 'min<br>';
    echo '               Temps de cuisson : '. $row["tpsCuis"] .'min <br>';
    echo '               Temps de repos : '. $row["tpsRep"] .'min';
    echo '           </p>';
    echo '           <p>';
    echo '               Nombre de personne :' . $row["nbpersonne"];
    echo '           </p>';
    echo '       </div>';

    $sql_etapes = "SELECT * FROM Etape WHERE idRecette = " . $row["idRecette"];
    $result_etapes = $conn->query($sql_etapes);

    $nbet = 1;

    if ($result_etapes->num_rows > 0) {
        echo '<div class="etape"> ';
        echo '       <div class="etape">';
        echo '           <dl>';
        while ($etape = $result_etapes->fetch_assoc()) {
            echo '<dt><strong>Etape '.$nbet.' :</strong></dt>';
            echo '            <dd>'. $etape["texte"] .'</dd>';
            $nbet++;
        }
        echo "</dl></div></div>";
    } else {
        echo '<div class="etape"> ';
        echo '       <div class="etape">';
        echo '           <dl>';
        echo "<p>Aucune étape trouvée pour cette recette.</p>";
        echo "</dl></div></div>";
    }

    

    // Récupération des ustensiles utilisés dans la recette
    $sql_ustensiles = "SELECT U.libelle FROM Ustensiles U JOIN Utilise UT ON U.idUstensiles = UT.idUstensiles WHERE UT.idRecette = " . $row["idRecette"];
    $result_ustensiles = $conn->query($sql_ustensiles);

    if ($result_ustensiles->num_rows > 0) {
        echo '<div class="ustensile"><dl><dt><strong>Ustensils :</strong></dt>';
        while ($ustensile = $result_ustensiles->fetch_assoc()) {
            echo "<dd>" . $ustensile["libelle"] . "</dd>";
        }
        echo "</dl></div>";
    } else {
        echo '<div class="ustensile"><dl><dt><strong>Ustensils :</strong></dt>';
        echo "<p>Aucun ustensile trouvé pour cette recette.</p>";
        echo "</dl></div>";
    }

    // Récupération des aliments utilisés dans la recette
    $sql_aliment = "SELECT U.nom FROM Aliment U JOIN Recette_Aliment UT ON U.idAliment = UT.idAliment WHERE UT.idRecette = " . $row["idRecette"];
    $result_aliment = $conn->query($sql_aliment);

    if ($result_aliment->num_rows > 0) {
        echo '<div class="ingredient"><dl><dt><strong>Aliments :</strong></dt>';
        while ($aliment = $result_aliment->fetch_assoc()) {
            echo "<dd>" . $aliment['nom'] . "</dd>";
        }
        echo "</dl></div>";
    } else {
        echo '<div class="ingredient"><dl><dt><strong>Aliments :</strong></dt>';
        echo "<p>Aucun aliment trouvé pour cette recette.</p>";
        echo "</dl></div>";
    }

    // Affichage du commentaire sur la recette
    $sql_commentaire = "SELECT * FROM Commentaire WHERE idRecette = " . $row["idRecette"];
    $result_commentaire = $conn->query($sql_commentaire);



    if ($result_commentaire->num_rows > 0) {
        echo '<div class="commentaire"> <dl><dt><strong>Commentaire :</strong></dt><dd>';
        while ($commentaire = $result_commentaire->fetch_assoc()) {

            $query = "SELECT pseudo FROM Utilisateur WHERE idUtilisateur = :idUtilisateur";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':idUtilisateur', $commentaire["idUtilisateur"]);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $pseudo = $result['pseudo'];

            echo '<div><dl><dt>' . $pseudo .' :' . $commentaire["note"] .'/5*</dt><dd>'. $commentaire["texte"] .'</dd></dl></div>';
        }
    } else {
        echo '<div class="commentaire"> <dl><dt><strong>Commentaire :</strong></dt><dd>';
        echo "<p>Aucun commentaire trouvé pour cette recette.</p>";
    }

    echo '<div id="commentaire_et_note">
    <h3>Ajouter un commentaire</h3>
    <form action="traitement_commentaire.php" method="post">
        <label for="commentaire">Commentaire :</label><br>
        <textarea id="commentaire" name="commentaire" rows="4" cols="50"></textarea><br>
        
        <label for="note">Note (sur 5) :</label>
        <input type="number" id="note" name="note" min="1" max="5" require><br>
        <input type="hidden" name="idRecette" value="'.$idRecette.'">

        <input type="submit" value="Envoyer">
    </form>
    </div>';

    echo '</div>';
} else {
    echo "<p>Aucune recette trouvée.</p>";
}

$conn->close();
?>

</body>
</html>
