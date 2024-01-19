<?php require_once('connexion.php')?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Métadonnées de la page -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Inclusion de la feuille de style Bootstrap depuis CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Inclusion de la feuille de style locale -->
    <link rel="stylesheet" type="text/css" href="style.css">
    
    <!-- Inclusion du script Bootstrap depuis CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Titre de la page -->
    <title>Ajouter un membre - Bibliodrive</title>
</head>
<body>

<!-- Contenu principal de la page -->
<div class="container mt-5">
    <?php
    // Démarrage de la session
    session_start();

    // Vérification de l'autorisation d'accès en tant qu'administrateur
    if (!$_SESSION["adminUser"] || !isset($_SESSION["adminUser"])) {
        echo "Accès non autorisé."; // Refuse l'accès à un utilisateur curieux, même s'il requête l'API en POST
        exit;
    }

    // Inclusion du fichier d'authentification et du header d'administration
    require("authentification.php");
    require("admin-header.html");

    // Traitement du formulaire de soumission (ajout d'un membre)
    if (isset($_POST["ajoutermembre"])) {
        $nom = $_POST["nom"];
        $prenom = $_POST["prenom"];
        $adresse = $_POST["adresse"];
        $email = $_POST["email"];
        $motdepasse = password_hash($_POST["motdepasse"], PASSWORD_DEFAULT);

        try {
            // Préparation de la requête d'insertion dans la base de données
            $req = $connexion->prepare("INSERT INTO utilisateur(nom, prenom, adresse, mel, motdepasse, profil) VALUES (:nom, :prenom, :adresse, :email, :motdepasse, 'client')");
            $req->bindParam(":nom", $nom);
            $req->bindParam(":prenom", $prenom);
            $req->bindParam(":adresse", $adresse);
            $req->bindParam(":email", $email);
            $req->bindParam(":motdepasse", $motdepasse);

            // Exécution de la requête
            if ($req->execute()) {
                echo '<div class="alert alert-success">Le membre a été ajouté avec succès.</div>';
            } else {
                echo '<div class="alert alert-danger">Une erreur s\'est produite lors de l\'ajout du membre.</div>';
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Erreur : ' . $e->getMessage() . '</div>';
        }
    }
    ?>

    <!-- Titre principal de la page -->
    <h1 class="big-title">Ajouter un membre</h1>

    <!-- Formulaire d'ajout de membre -->
    <form method="post" class="form-admin">
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" class="form-control" id="prenom" name="prenom" required>
        </div>
        <div class="form-group">
            <label for="adresse">Adresse :</label>
            <input type="text" class="form-control" id="adresse" name="adresse" required>
        </div>
        <div class="form-group">
            <label for="email">E-mail :</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="motdepasse">Mot de passe :</label>
            <input type="password" class="form-control" id="motdepasse" name="motdepasse" required>
        </div>
        <!-- Bouton de soumission du formulaire -->
        <button type="submit" class="btn btn-primary" name="ajoutermembre">Ajouter le membre</button>
    </form>
</div>

</body>
</html>
