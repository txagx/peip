<?php
session_start();
require_once 'config.php';

if(isset($_POST['pseudo']) && isset($_POST['mdp'])) {
    $pseudo = $_POST['pseudo'];
    $mdp = $_POST['mdp'];

    // Vérifiez les informations d'identification dans la table Utilisateur
    $query = "SELECT * FROM Utilisateur WHERE pseudo = :pseudo AND mdp = :mdp";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':pseudo', $pseudo);
    $stmt->bindParam(':mdp', $mdp);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user) {
        $_SESSION['loggedin'] = true;
        $_SESSION['idUtilisateur'] = $user['idUtilisateur'];
        $_SESSION['pseudo'] = $user['pseudo'];
        header('Location: welcome.php');
        exit;
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php if(isset($error)) { ?>
        <p><?php echo $error; ?></p>
    <?php } ?>

    <div class="topbar">
            <a href='index.php'><p>INDEX</p></a>
            <a href='register.html'><p>REGISTER</p></a>
            <a href='login.php'><p>LOGIN</p></a>
            <a href='ajoutDB.html'><p>RECETTES</p></a>
            <a href='recherches.php'><p>POSTER</p></a>
        </div>

    <div class="main">
    <h2>Connexion</h2><br>
    <form method="post" action="">
        <label>Nom d'utilisateur:</label>
        <input type="text" name="pseudo"><br><br>
        <label>Mot de passe:</label>
        <input type="password" name="mdp"><br><br>
        <input type="submit" value="Se connecter" id="end">
    </form>
    </div>

    <div class="bgwrap">
        <img src="registerBG.avif" alt="pizza">
    </div>
</body>
</html>
