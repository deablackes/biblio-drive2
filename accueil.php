<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Inclusion de la feuille de style externe -->
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- Inclusion du script Bootstrap depuis CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- Titre de la page -->
    <title>Accueil - Bibliodrive</title>
    <script src="js/tarteaucitron/tarteaucitron.js"></script>

<script>

  tarteaucitron.init({

    "privacyUrl": "", /* URL de la page de la politique de vie privée */

    "hashtag": "#tarteaucitron", /* Ouvrir le panneau contenant ce hashtag */

    "cookieName": "tarteaucitron", /* Nom du Cookie */

    "orientation": "middle", /* Position de la bannière (top - bottom) */

    "showAlertSmall": false, /* Voir la bannière réduite en bas à droite */

    "cookieslist": false, /* Voir la liste des cookies */

    "adblocker": false, /* Voir une alerte si un bloqueur de publicités est détecté */

    "AcceptAllCta": true, /* Voir le bouton accepter tout (quand highPrivacy est à true) */

    "highPrivacy": true, /* Désactiver le consentement automatique : OBLIGATOIRE DANS l'UE */

    "handleBrowserDNTRequest": false, /* Si la protection du suivi du navigateur est activée, tout interdire */

    "removeCredit": false, /* Retirer le lien vers tarteaucitron.js */

    "moreInfoLink": true, /* Afficher le lien "voir plus d'infos" */

    "useExternalCss": false, /* Si false, tarteaucitron.css sera chargé */

    //"cookieDomain": ".my-multisite-domaine.fr", /* Cookie multisite - cas où SOUS DOMAINE */

    "readmoreLink": "/cookiespolicy" /* Lien vers la page "Lire plus" A FAIRE OU PAS  */

  });

  (tarteaucitron.job = tarteaucitron.job || []).push('youtube');
  <div class="youtube_player" videoID="PJCvmeRILLk" width="560" height="315" theme="light" rel="0" controls="1" showinfo="1" autoplay="0"></div>


</script>
</head>
<body>

<header>
    <?php
    // Démarrage de la session
    session_start();

    // Inclusion du fichier d'authentification
    require("authentification.php");

    // Vérification du statut de l'utilisateur (admin ou non) pour déterminer l'inclusion du header approprié
    if($_SESSION["adminUser"]) require("admin-header.html");
    else require("entete.html");

    // Inclusion du fichier de connexion à la base de données
    require_once('connexion.php');
    ?>
</header>

<?php
// Vérification si l'utilisateur est un administrateur
if($_SESSION["adminUser"]){
    // Affichage d'un titre spécifique pour le panneau d'administration
    echo '<h1 class="big-title text-center display-4">Admin panel</h1>';
    // Arrêt de l'exécution du script pour éviter l'affichage du reste de la page
    exit;
} else {
    // Affichage d'un titre différent pour les utilisateurs non administrateurs
    echo '<h1 class="big-title text-center">Dernières acquisitions</h1>';
}

// Préparation de la requête SQL pour récupérer les images des dernières acquisitions
$req = $connexion->prepare("SELECT image FROM livre ORDER BY dateajout DESC LIMIT 2;");
$req->setFetchMode(PDO::FETCH_OBJ);
$req->execute();
?>

<!-- Carrousel Bootstrap pour afficher les images des dernières acquisitions -->
<div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php
        // Variable pour suivre l'état actif du premier élément du carrousel
        $active = true;
        // Vérification si des résultats ont été obtenus de la base de données
        if($req->rowCount() != 0){
            // Boucle pour parcourir les résultats de la requête
            while($dernier_acqui = $req->fetch()){
                // Affichage d'un élément du carrousel avec l'image correspondante
                echo $active ? '<div class="carousel-item active" data-bs-interval="5000">' : '<div class="carousel-item" data-bs-interval="5000">';
                echo '<img src="covers/'.$dernier_acqui->image.'" class="d-block mx-auto img-thumbnail" alt="Image du livre">';
                echo '</div>';
                // Changement de l'état actif après le premier élément du carrousel
                $active = false;
            }
        }
        ?>
    </div>
</div>

</body>
</html>
