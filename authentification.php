<?php require_once('connexion.php')?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <title>Connexion - Bibliodrive</title>
</head>
<body>

<div class="container mt-5">
    <?php
    // Initialisation des variables de sessions.
    if (!isset($_SESSION["connected"])) {
        $_SESSION["connected"] = FALSE;
        $_SESSION["adminUser"] = FALSE;
    }

    // Vérifier les identifiants renseignés.
    if (isset($_POST["email"])) {
        $requete = $connexion->prepare("SELECT motdepasse, profil FROM utilisateur WHERE mel = :email");
        $requete->bindValue(":email", $_POST["email"], PDO::PARAM_STR);
        $requete->execute();
        $requete->setFetchMode(PDO::FETCH_OBJ);

        $utilisateur = $requete->fetch();
        if ($utilisateur) {
            if (password_verify($_POST["mdp"], $utilisateur->motdepasse)) { // Si ça correspond, l'utilisateur est connecté
                $_SESSION["email"] = $_POST["email"];
                $_SESSION["connected"] = TRUE;
                $_SESSION["panier"] = array();
                if ($utilisateur->profil == "admin") $_SESSION["adminUser"] = TRUE; //S'il est un admin, attribue le rôle admin.
            }
        } else { // Sinon, on fait en sorte de lui faire savoir que son mail ou mdp est mauvais.
            $erreur_connexion = TRUE;
            $email_renseigne = $_POST["email"];
        }
    }

    // Déconnecter l'utilisateur
    if (isset($_POST["logoff"])) {
        $_SESSION["connected"] = FALSE;
        unset($_SESSION["email"]);
        unset($_SESSION["panier"]);

        if ($_SESSION["adminUser"]) { // Si l'utilisateur est un admin, on le redirige dans la page d'accueil pour éviter tout conflit.
            $_SESSION["adminUser"] = FALSE;
            header("Location: accueil.php");
            exit;
        }
    }

    // Vérifier si utilisateur authentifié.
    if ($_SESSION["connected"]) {
        // Requête pour récupérer les informations de l'utilisateur
        $requete_info_user = $connexion->prepare("SELECT mel, nom, prenom, adresse, profil FROM utilisateur WHERE mel = :email");
        $requete_info_user->bindValue(":email", $_SESSION["email"], PDO::PARAM_STR);
        $requete_info_user->execute();
        $result_info_user = $requete_info_user->fetch(PDO::FETCH_ASSOC);
    
        // Requête pour récupérer le nombre de livres empruntés par l'utilisateur
        $requete_nb_emprunts = $connexion->prepare("SELECT COUNT(*) AS nb_emprunts FROM emprunter WHERE mel = :email");
        $requete_nb_emprunts->bindValue(":email", $_SESSION["email"], PDO::PARAM_STR);
        $requete_nb_emprunts->execute();
        $result_nb_emprunts = $requete_nb_emprunts->fetch(PDO::FETCH_ASSOC);
        $nb_emprunts = $result_nb_emprunts["nb_emprunts"];
        ?>
        <div class="alert alert-success">
            <p class="titre-form">Bonjour, <br><?= $result_info_user["nom"] . ' ' . $result_info_user["prenom"]; ?></p>
            <p class="email-set"><?= $result_info_user["mel"]; ?></p>
            <p class="info-emprunts">Nombre de livres empruntés : <?= $nb_emprunts; ?></p>
            <?php
            if ($result_info_user["profil"] == "client") {
                echo '<p class="adresse-set">' . $result_info_user["adresse"] . '</p>';
            } else {
                echo '<p class="admin-account">Vous êtes Administrateur</p>';
            }
            ?>
            <form method="post" class="form-login">
                <input type="hidden" name="logoff" value="true">
                <button class="btn btn-danger">Se déconnecter</button>
            </form>
        </div>
        <?php
    } else {
        ?>
       <div class="row justify-content-end">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-center">Connexion</h5>
                    <form method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <?php
                            if (isset($email_renseigne)) {
                                echo '<input class="form-control" type="email" name="email" id="email" placeholder="Email" autocomplete="off" value="' . $email_renseigne . '" required>';
                            } else {
                                echo '<input class="form-control" type="email" name="email" id="email" placeholder="Email" autocomplete="off" required>';
                            }
                            ?>
                            <div class="invalid-feedback">
                                Veuillez entrer une adresse e-mail valide.
                            </div>
                        </div>
                        <div class="mb-3">
                            <input class="form-control" type="password" name="mdp" id="mdp" placeholder="Mot de passe" autocomplete="off" required>
                            <div class="invalid-feedback">
                                Veuillez entrer votre mot de passe.
                            </div>
                        </div>
                        <?php
                        if (isset($erreur_connexion)) {
                            echo '<p class="text-danger text-center">Votre email ou mot de passe est incorrect.</p>';
                        }
                        ?>
                        <button class="btn btn-primary btn-block" type="submit">Se connecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
    <?php } ?>
</div>

</body>
</html>
