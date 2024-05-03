<?php
echo "<a href='index.php'><h1>INDEX</h1></a><br>";
session_start();
require_once 'config.php'; // Inclure le fichier de configuration de la base de données

if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header('Location: recette.php?id='.$_POST['idRecette']); // Remplacez 'formulaire_commentaire.php' par le chemin de votre formulaire
    exit;
}

if(isset($_POST['commentaire']) && isset($_POST['note'])) {
    // Récupérer les données du formulaire
    $commentaire = $_POST['commentaire'];
    $note = $_POST['note'];
    $idRecette = $_POST['idRecette'];
    
    // Récupérer l'ID de l'utilisateur actuellement connecté
    $idUtilisateur = $_SESSION['idUtilisateur']; // Assurez-vous de stocker l'ID de l'utilisateur dans la session lors de la connexion

    // Récupérer l'ID de la recette à laquelle le commentaire est associé (vous devrez peut-être ajouter cette fonctionnalité dans votre application)
    $idRecette = (int)$idRecette; // Remplacez 1 par l'ID de la recette associée

    


    // Préparer la requête d'insertion
    $query = "INSERT INTO Commentaire (texte, note, idUtilisateur, idRecette) VALUES (:texte, :note, :idUtilisateur, :idRecette)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':texte', $commentaire);
    $stmt->bindParam(':note', $note);
    $stmt->bindParam(':idUtilisateur', $idUtilisateur);
    $stmt->bindParam(':idRecette', $idRecette);
    
    // Exécuter la requête d'insertion
    try {
        $stmt->execute();
        echo "Le commentaire a été ajouté avec succès.";
    } catch(PDOException $e) {
        // En cas d'erreur lors de l'exécution de la requête, afficher l'erreur
        echo "Erreur lors de l'ajout du commentaire: " . $e->getMessage();
    }
} else {
    // Rediriger l'utilisateur si les données du formulaire ne sont pas définies
    header('Location: recette.php?id='.$idRecette); // Remplacez 'formulaire_commentaire.php' par le chemin de votre formulaire
    exit;
}
header('Location: recette.php?id='.$idRecette); // Remplacez 'formulaire_commentaire.php' par le chemin de votre formulaire
    exit;
?>
