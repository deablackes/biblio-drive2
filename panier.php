<?php
// Démarrer une session PHP
session_start();

// Inclure le fichier de connexion à la base de données avec PDO
require_once('connexion.php');

// Inclure le fichier d'authentification
require("authentification.php");

// Inclure l'en-tête du site
require("entete.html");

// Traitement du formulaire (emprunt et retrait)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retirer un livre du panier
    if (isset($_POST['retirer_livre']) && isset($_POST['nolivre_retirer'])) {
        $nolivre_retirer = $_POST['nolivre_retirer'];
        // Filtrer le panier pour exclure le livre à retirer
        $_SESSION["panier"] = array_filter($_SESSION["panier"], function ($livre) use ($nolivre_retirer) {
            return $livre != $nolivre_retirer;
        });
    }

    // Emprunter les livres du panier
    if (isset($_POST["emprunt_livre"]) && isset($_SESSION["connected"]) && count($_SESSION["panier"]) > 0) {
        $date_emprunt = date("Y-m-d");
        $date_retour = date('Y-m-d', strtotime($date_emprunt . ' + 30 days'));

        // Boucle sur chaque livre dans le panier
        foreach ($_SESSION["panier"] as $livre) {
            // Prépare la requête pour insérer l'emprunt dans la base de données
            $requete = $connexion->prepare("INSERT INTO emprunter(mel, nolivre, dateemprunt, dateretour) 
                                            VALUES(:email, :nolivre, :dateemprunt, :dateretour)");

            // Associe les valeurs aux paramètres
            $requete->bindValue(":email", $_SESSION["email"], PDO::PARAM_STR);
            $requete->bindValue(":nolivre", $livre, PDO::PARAM_INT);
            $requete->bindValue(":dateemprunt", $date_emprunt);
            $requete->bindValue(":dateretour", $date_retour);

            // Exécute la requête pour chaque livre
            $requete->execute();
        }

        // Vide le panier après l'emprunt
        $_SESSION["panier"] = array();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Les liens vers les fichiers CSS et JavaScript -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <!-- Assurez-vous de remplacer "style.css" par le chemin réel de votre fichier CSS -->
    <title>Panier - Bibliodrive</title>
</head>
<body>

<div class="container mt-4">
    <h1 class="mb-4">Votre panier</h1>

    <div class="row">
        <div class="col-md-8">
            <?php
            if (!isset($_SESSION["panier"])) {
                echo "<p class='text-muted'>Vous n'êtes pas encore connecté. Connectez-vous pour ajouter des livres dans votre panier.</p>";
            } else {
                if (count($_SESSION["panier"]) > 0) {
                    foreach ($_SESSION["panier"] as $livre) {
                        $requete = $connexion->prepare("
                            SELECT nolivre, nom, prenom, titre, anneeparution
                            FROM livre
                            JOIN auteur ON livre.noauteur = auteur.noauteur
                            WHERE nolivre = :nolivre;
                        ");

                        $requete->bindValue(":nolivre", $livre, PDO::PARAM_INT);

                        $requete->setFetchMode(PDO::FETCH_OBJ);
                        $requete->execute();
                        while ($info_panier = $requete->fetch()) {
                            echo '<div class="card mb-3">';
                            echo '<div class="card-body">';
                            echo "<h5 class='card-title'>" . $info_panier->nom . " " . $info_panier->prenom . " - " . $info_panier->titre . " (" . $info_panier->anneeparution . ")</h5>";
                            echo '<form method="post" action="panier.php">';
                            echo '<input type="hidden" name="retirer_livre" value="true">';
                            echo '<input type="hidden" name="nolivre_retirer" value="' . $info_panier->nolivre . '">';
                            echo '<button type="submit" class="btn btn-danger">Retirer du panier</button>';
                            echo '</form>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }

                    echo '<form method="post">';
                    echo '<input type="hidden" name="emprunt_livre" value="true">';
                }
            }
            ?>
        </div>

        <?php if (count($_SESSION["panier"]) > 0): ?>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success btn-block mb-3">Emprunter les livres</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Ajoutez vos scripts JavaScript ici si nécessaire -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
