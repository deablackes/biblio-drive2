<?php
// Inclusion du fichier de connexion à la base de données
require_once 'connexion.php';

// Vérification si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $mel = $_POST['mel'];
    $motdepasse = password_hash($_POST['motdepasse'], PASSWORD_DEFAULT);
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $codepostal = $_POST['codepostal'];
    $profil = 'membre'; // Profil par défaut pour les utilisateurs inscrits

    // Requête SQL pour l'insertion des données dans la table utilisateur
    $sql = "INSERT INTO utilisateur (mel, motdepasse, nom, prenom, adresse, ville, codepostal, profil) 
            VALUES (:mel, :motdepasse, :nom, :prenom, :adresse, :ville, :codepostal, :profil)";
    
    // Préparation de la requête
    $stmt = $connexion->prepare($sql);
    
    // Liaison des paramètres
    $stmt->bindParam(':mel', $mel);
    $stmt->bindParam(':motdepasse', $motdepasse);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->bindParam(':ville', $ville);
    $stmt->bindParam(':codepostal', $codepostal);
    $stmt->bindParam(':profil', $profil);

    // Exécution de la requête
    if ($stmt->execute()) {
        // Redirection vers la page de succès après l'inscription
        header('Location: inscription_succes.php');
        exit;
    } else {
        // Affichage d'un message en cas d'erreur lors de l'inscription
        echo "Erreur lors de l'inscription. Veuillez réessayer.";
    }
}
?>
<!-- Inclusion de la feuille de style Bootstrap depuis CDN -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
    crossorigin="anonymous">

<!-- Contenu HTML pour le formulaire d'inscription -->
<div class="d-flex justify-content-center align-items-center" style="height: 80vh;">
    <form method="POST" action="inscription.php" class="col-md-6">
        <!-- Titre du formulaire -->
        <h2 class="mb-4">Inscription</h2>

        <!-- Champ de saisie pour le nom -->
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" class="form-control" required>
        </div>

        <!-- Champ de saisie pour le prénom -->
        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" class="form-control" required>
        </div>

        <!-- Champ de saisie pour l'adresse e-mail -->
        <div class="form-group">
            <label for="mel">Adresse e-mail :</label>
            <input type="email" name="mel" class="form-control" required>
        </div>

        <!-- Champ de saisie pour le mot de passe -->
        <div class="form-group">
            <label for="motdepasse">Mot de passe :</label>
            <input type="password" name="motdepasse" class="form-control" required>
        </div>

        <!-- Champ de saisie pour l'adresse -->
        <div class="form-group">
            <label for="adresse">Adresse :</label>
            <input type="text" name="adresse" class="form-control" required>
        </div>

        <!-- Champ de saisie pour la ville -->
        <div class="form-group">
            <label for="ville">Ville :</label>
            <input type="text" name="ville" class="form-control" required>
        </div>

        <!-- Champ de saisie pour le code postal -->
        <div class="form-group">
            <label for="codepostal">Code postal :</label>
            <input type="text" name="codepostal" class="form-control" required>
        </div>

        <!-- Bouton de soumission du formulaire -->
        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>
</div>
