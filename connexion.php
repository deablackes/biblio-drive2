<?php
// Bloc de code pour établir la connexion à la base de données MySQL
try {
    // Chaîne de connexion à la base de données
    $dns = 'mysql:host=localhost;dbname=biblio-drive2'; // dbname : nom de la base
    $utilisateur = 'root'; // Nom de l'utilisateur de la base de données
    $motDePasse = ''; // Mot de passe de l'utilisateur (vide dans cet exemple)

    // Tentative de création d'une instance PDO pour établir la connexion
    $connexion = new PDO($dns, $utilisateur, $motDePasse);
} catch (Exception $e) {
    // En cas d'erreur lors de la connexion, affichage du message d'erreur
    echo "Connexion à la base de donnée biblio impossible : ", $e->getMessage();
    // Arrêt du script en cas d'échec de la connexion
    die();
}
?>
