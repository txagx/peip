<?php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header('Location: login.php');
    exit;
}

// Récupérer l'identifiant de l'utilisateur connecté
$idUtilisateur = $_SESSION['idUtilisateur'];

// Requête pour récupérer toutes les recettes de l'utilisateur connecté
$query = "SELECT * FROM Recette WHERE idUtilisateur = :idUtilisateur";
$stmt = $db->prepare($query);
$stmt->bindParam(':idUtilisateur', $idUtilisateur);
$stmt->execute();
$recettes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Page d'accueil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Bienvenue, <?php echo $_SESSION['pseudo']; ?>!</h2>
    <h3>Vos recettes :</h3>
    <ul>
        <?php foreach($recettes as $recette) { ?>
            <li class="liste-recettes"><a href="recette.php?id=<?php echo $recette['idRecette']; ?>"><?php echo $recette['nom']; ?></a></li>
        <?php } ?>
    </ul>
    <a href="logout.php">Se déconnecter</a>
</body>
</html>

