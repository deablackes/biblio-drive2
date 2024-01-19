<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Titre de la page -->
    <title>Ajouter un livre - Bibliodrive</title>
    
    <!-- Inclusion de la feuille de style Bootstrap depuis CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Styles CSS spécifiques pour la page -->
    <style>
        body {
            margin: 20px;
        }

        .big-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-admin {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .button-general {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .ajout_succes {
            color: #28a745;
            margin-top: 20px;
        }

        .ajout_erreur {
            color: #dc3545;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php
        // Démarrage de la session
        session_start();

        // Vérification de l'autorisation d'accès en tant qu'administrateur
        if (!$_SESSION["adminUser"] || !isset($_SESSION["adminUser"])) {
            echo "Accès non autorisé."; // Refuse l'accès à un utilisateur non autorisé
            exit;
        }

        // Inclusion du fichier d'authentification et du header d'administration
        require("authentification.php");
        require("admin-header.html");

        // Traitement du formulaire de soumission (ajout d'un livre)
        if (isset($_POST["noauteur"])) {
            $noauteur = $_POST["noauteur"];
            $titre = $_POST["titre"];
            $ISBN13 = $_POST["ISBN13"];
            $annee_parution = $_POST["annee_parution"];
            $resume = $_POST["resume"];
            $cover = $_FILES["cover"];

            try {
                // Préparation de la requête d'insertion dans la base de données
                $req = $connexion->prepare("
                INSERT INTO 
                livre(noauteur, titre, isbn13, anneeparution, resume, dateajout, image) 
                VALUES(:noauteur, :titre, :ISBN13, :annee_parution, :resume, :dateajout, :cover)
                ");

                // Liaison des valeurs aux paramètres de la requête
                $req->bindValue(":noauteur", $noauteur, PDO::PARAM_INT);
                $req->bindValue(":titre", $titre);
                $req->bindValue(":ISBN13", $ISBN13);
                $req->bindValue(":annee_parution", $annee_parution);
                $req->bindValue(":resume", $resume);
                $req->bindValue(":dateajout", date("Y-m-d"));
                $req->bindValue(":cover", $cover['name']);

                // Exécution de la requête
                $req->execute();
                $book_added = TRUE;

                // Déplacement de la couverture dans le dossier approprié pour l'affichage sur le site
                move_uploaded_file($cover['tmp_name'], "images/covers/".$cover['name']);
            } catch(Exception $e) {
                $erreur = $e;
                $book_added = FALSE;
            }
        }
    ?>
    
    <!-- Titre principal de la page -->
    <h1 class="big-title">Ajouter un livre</h1>

    <!-- Formulaire d'ajout de livre -->
    <form method="post" class="form-admin" enctype="multipart/form-data">
        <div class="form-group">
            <label for="auteur">Auteur :</label>
            <!-- Liste déroulante pour sélectionner l'auteur -->
            <select name="noauteur" id="auteur" class="form-control" required>
                <option value="" disabled selected>---- Sélectionner ----</option>
                <?php
                // Récupération des auteurs depuis la base de données
                $req = $connexion->query("SELECT noauteur,nom FROM auteur");
                $req->setFetchMode(PDO::FETCH_OBJ);

                // Affichage des options de la liste déroulante
                while($auteur = $req->fetch()) {
                    echo "<option value=\"{$auteur->noauteur}\">{$auteur->nom}</option>";
                }
                ?>
            </select>
        </div>

        <!-- Champ pour le titre du livre -->
        <div class="form-group">
            <label for="titre">Titre :</label>
            <input type="text" name="titre" id="titre" class="form-control" autocomplete="off" required>
        </div>

        <!-- Champ pour l'ISBN13 du livre -->
        <div class="form-group">
            <label for="ISBN13">ISBN13 :</label>
            <input type="text" name="ISBN13" id="ISBN13" class="form-control" autocomplete="off" required>
        </div>

        <!-- Champ pour l'année de parution du livre -->
        <div class="form-group">
            <label for="annee_parution">Année de parution :</label>
            <input type="text" name="annee_parution" id="annee_parution" class="form-control" autocomplete="off" required>
        </div>

        <!-- Champ pour le résumé du livre -->
        <div class="form-group">
            <label for="resume">Résumé :</label>
            <textarea name="resume" id="resume" class="form-control" autocomplete="off" rows="7" required></textarea>
        </div>

        <!-- Champ pour l'ajout de la couverture du livre -->
        <div class="form-group">
            <label for="cover">Image :</label>
            <input type="file" id="cover" name="cover" class="form-control" accept="image/png, image/jpeg" autocomplete="off" required/>
        </div>

        <!-- Bouton de soumission du formulaire -->
        <input type="submit" value="Ajouter le livre" class="button-general">

        <!-- Affichage du message de succès ou d'erreur après la soumission du formulaire -->
        <?php
        if(isset($book_added)) {
            if ($book_added) 
                echo '<p class="ajout_succes">Livre ajouté avec succès !</p>';
            else
                echo '<p class="ajout_erreur">Une erreur est survenue lors de l\'ajout du livre : '. $erreur . '</p>';
        }
        ?>
    </form>
</body>
</html>
