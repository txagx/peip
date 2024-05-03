<?php
session_start();
require_once 'config.php'; // Inclure le fichier de configuration de la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si le formulaire d'inscription a été soumis

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
        header('Location: ../index.php'); // Remplacez 'formulaire_inscription.php' par le chemin de votre formulaire d'inscription
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="topbar">
        <a href='index.php'><p>INDEX</p></a>
        <a href='register.html'><p>REGISTER</p></a>
        <a href='login.php'><p>LOGIN</p></a>
        <a href='ajoutDB.php'><p>RECETTES</p></a>
        <a href='recherches.php'><p>POSTER</p></a>
    </div>

    <div class="main">
    <h2>Inscription</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="pseudo">Nom d'utilisateur:</label><br>
        <input type="text" id="pseudo" name="pseudo" required><br><br>
        
        <label for="mdp">Mot de passe:</label><br>
        <input type="password" id="mdp" name="mdp" required><br><br>
        
        <input type="submit" value="S'inscrire" id="end">
    </form>
    <img src="pizza.webp" alt="photo pizza wola" id="petitepizza">
    </div>

    <div class="bgwrap">
        <img src="registerBG.avif" alt="Fond d'écran composé de plusieurs dessins de pizzas.">
    </div>
</body>
</html>
