<?php
session_start();

echo !isset($_SESSION['loggedin']);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['loggedin'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: login.php');
    exit;
}

echo "<a href='index.php'><h1>INDEX</h1></a><br>";
require_once 'config.php'; // Inclure le fichier de configuration de la base de données
echo '<a href="index.php">Go back home<a><br>';

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

$nbpersonne = $_POST['nbpersonne'];
$tpsPrep = $_POST['tpsPrep'];
$tpsRep = $_POST['tpsRep'];
$tpsCuis = $_POST['tpsCuis'];
$nom = $_POST['nom'];
$etape = $_POST['etape'];
$ustentile = $_POST['ustentile'];
$tag = $_POST['tag'];
$aliment = $_POST['aliment'];
$idUtilisateur = $_SESSION['idUtilisateur'];

echo $nom;

$etape = explode(";", $etape);
$ustentile = explode(";", $ustentile);
$tag = explode(";", $tag);
$aliment = explode(";", $aliment);

$num_etape = count($etape);
$num_ustentile = count($ustentile);
$num_tag = count($tag);
$num_aliment = count($aliment);

// Récupérer les informations de l'image
$imageName = $_FILES['image']['name'];
$imageTmpName = $_FILES['image']['tmp_name'];
$imageSize = $_FILES['image']['size'];
$imageError = $_FILES['image']['error'];

// Générer un nom de fichier unique
$uniqueFilename = uniqid('image_'); // Par exemple : image_abcdef123.jpg

// Chemin complet de l'image sur le serveur avec le nouveau nom
$imagePath = 'uploads/' . $uniqueFilename;

// Insertion de la recette
$sql = "INSERT INTO recette (nbpersonne, tpsPrep, tpsRep, tpsCuis, nom, idUtilisateur) VALUES ('$nbpersonne', '$tpsPrep', '$tpsRep','$tpsCuis', '$nom', '$idUtilisateur')";

echo 'ajout recette';

if ($conn->query($sql) === TRUE) {
    echo "La recette a été ajoutée avec succès !<br>";
    // Récupération de l'ID de la recette nouvellement insérée
    $idRecette = mysqli_insert_id($conn);

    // Vérifier si une image a été téléchargée
    if ($imageError === 0) {
        // Déplacer l'image téléchargée vers l'emplacement de stockage sur le serveur
        if (move_uploaded_file($imageTmpName, $imagePath)) {
            // Insérer le chemin d'accès de l'image dans la base de données
            $insertImagePathQuery = "UPDATE Recette SET image_path = '$imagePath' WHERE idRecette = $idRecette";
            
            if ($conn->query($insertImagePathQuery) === TRUE) {
                echo "Le chemin d'accès de l'image a été ajouté avec succès à la base de données.";
            } else {
                echo "Erreur lors de l'insertion du chemin d'accès de l'image dans la base de données : " . $conn->error;
            }
        } else {
            echo "Une erreur s'est produite lors du téléchargement de l'image.";
        }
    } else {
        echo "Une erreur s'est produite lors du téléchargement de l'image : " . $imageError;
    }

    

    // Insertion des étapes
    for ($i = 0; $i < $num_etape; $i++) {
        $step = $etape[$i];
        
        $sql = "INSERT INTO etape (numEtape, texte, idRecette) VALUES ('$i', '$step', '$idRecette')";

        if ($conn->query($sql) === TRUE) {
            echo "L'étape $i a été ajoutée avec succès !<br>";
        } else {
            echo "Erreur lors de l'insertion de l'étape $i : " . $conn->error . "<br>";
        }
    }

    // Insertion des ustensiles
    for ($i = 0; $i < $num_ustentile; $i++) {
        $libU = $ustentile[$i];
        $sql = "INSERT INTO ustensiles (libelle, pathImg) VALUES ('$libU', '$idRecette')";

        if ($conn->query($sql) === TRUE) {
            echo "L'ustensile $libU a été ajouté avec succès !<br>";
        } else {
            echo "Erreur lors de l'insertion de l'ustensile $libU : " . $conn->error . "<br>";
        }

        $temp = mysqli_insert_id($conn);
        $sql = "INSERT INTO utilise (idUstensiles, idRecette) VALUES ('$temp', '$idRecette')";  

        if ($conn->query($sql) === TRUE) {
            echo "La relation $libU -> $idRecette a été ajouté avec succès !<br>";
        } else {
            echo "Erreur lors de l'insertion de la relation $libU -> $idRecette : " . $conn->error . "<br>";
        }
    }

    // Insertion des tag
    for ($i = 0; $i < $num_tag; $i++) {
        $libU = strtolower($tag[$i]); // Convertir en minuscules
        
        // Vérifier si le tag existe déjà
        $tagExistsQuery = "SELECT idTag FROM Tag WHERE LOWER(libelle) = '$libU'";
        $result = $conn->query($tagExistsQuery);
        
        if ($result->num_rows > 0) {
            // Le tag existe déjà, récupérer son id
            $row = $result->fetch_assoc();
            $temp = $row['idTag'];
            echo "Le tag $libU existe déjà avec l'id $temp !";
        } else {
            // Le tag n'existe pas, l'insérer dans la table Tag
            $insertTagQuery = "INSERT INTO Tag (libelle) VALUES ('$libU')";
            if ($conn->query($insertTagQuery) === TRUE) {
                $temp = $conn->insert_id;
                echo "Le tag $libU a été ajouté avec succès avec l'id $temp !";
            } else {
                echo "Erreur lors de l'insertion du tag $libU : " . $conn->error;
                continue; // Passer à l'itération suivante si une erreur se produit
            }
        }
        
        // Maintenant, insérer la relation dans la table Recette_Tag
        $insertRelationQuery = "INSERT INTO Recette_Tag (idRecette, idTag) VALUES ('$idRecette', '$temp')";  
        if ($conn->query($insertRelationQuery) === TRUE) {
            echo "La relation $libU -> $idRecette a été ajoutée avec succès !";
        } else {
            echo "Erreur lors de l'insertion de la relation $libU -> $idRecette : " . $conn->error;
        }
    }
    
        // Insertion des aliment
    for ($i = 0; $i < $num_aliment; $i++) {
        $libU = $aliment[$i];
        $sql = "INSERT INTO Aliment (nom) VALUES ('$libU')";

        if ($conn->query($sql) === TRUE) {
            echo "L'aliment $libU a été ajouté avec succès !<br>";
        } else {
            echo "Erreur lors de l'insertion de l'aliment $libU : " . $conn->error . "<br>";
        }

        $temp = mysqli_insert_id($conn);
        $sql = "INSERT INTO Recette_Aliment (idRecette, idAliment) VALUES ('$idRecette', '$temp')";  

        if ($conn->query($sql) === TRUE) {
            echo "La relation $libU -> $idRecette a été ajouté avec succès !<br>";
        } else {
            echo "Erreur lors de l'insertion de la relation $libU -> $idRecette : " . $conn->error . "<br>";
        }
    }
} else {
    echo "Erreur lors de l'insertion de la recette : " . $conn->error . "<br>";
}

// Fermeture de la connexion à la base de données
$conn->close();
?>
