<?php
echo "<a href='index.php'><h1>INDEX</h1></a><br>";
session_start();
require_once 'config.php'; // Inclure le fichier de configuration de la base de données

if(isset($_POST['pseudo']) && isset($_POST['mdp'])) {
    // Récupérer les données du formulaire
    $pseudo = $_POST['pseudo'];
    $mdp = $_POST['mdp'];

    // Vérifier si l'utilisateur existe déjà dans la base de données
    $query = "SELECT * FROM Utilisateur WHERE pseudo = :pseudo";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':pseudo', $pseudo);
    $stmt->execute();
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$existingUser) {
        // Ajouter l'utilisateur à la base de données
        $query = "INSERT INTO Utilisateur (pseudo, mdp) VALUES (:pseudo, :mdp)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':mdp', $mdp);
        $stmt->execute();

        echo "Inscription réussie. Vous pouvez maintenant vous connecter.";
    } else {
        echo "Ce nom d'utilisateur est déjà pris. Veuillez en choisir un autre.";
    }
} else {
    // Rediriger l'utilisateur si les données du formulaire ne sont pas définies
    header('Location: index.php'); // Remplacez 'formulaire_inscription.php' par le chemin de votre formulaire d'inscription
    exit;
}
header('Location: index.php'); // Remplacez 'formulaire_inscription.php' par le chemin de votre formulaire d'inscription
    exit;
?>
