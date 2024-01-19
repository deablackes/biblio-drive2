<?php
// Démarrer une session PHP
session_start();

// Inclure le fichier de connexion à la base de données avec PDO
include("connexion.php");

// Vérifier si le formulaire de recherche a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['recherche'])) {
    $recherche = $_POST['recherche'];

    // Récupérer la liste des livres selon la recherche (par titre ou nom d'auteur)
    $requete_liste_livres = "SELECT livre.*, auteur.nom AS nom_auteur, auteur.prenom AS prenom_auteur
                             FROM livre
                             INNER JOIN auteur ON livre.noauteur = auteur.noauteur
                             WHERE livre.titre LIKE :recherche
                                OR auteur.nom LIKE :recherche
                                OR auteur.prenom LIKE :recherche";
    $stmt_liste_livres = $connexion->prepare($requete_liste_livres);
    $stmt_liste_livres->bindValue(':recherche', '%' . $recherche . '%', PDO::PARAM_STR);
    $stmt_liste_livres->execute();
    $liste_livres = $stmt_liste_livres->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Récupérer la liste complète des livres
    $requete_liste_livres = "SELECT livre.*, auteur.nom AS nom_auteur, auteur.prenom AS prenom_auteur
                             FROM livre
                             INNER JOIN auteur ON livre.noauteur = auteur.noauteur";
    $stmt_liste_livres = $connexion->query($requete_liste_livres);
    $liste_livres = $stmt_liste_livres->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Livres</title>
    <!-- Styles Bootstrap depuis CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h1 class="mb-4">Liste des Livres</h1>

    <!-- Vérifier si la liste des livres n'est pas vide -->
    <?php if (!empty($liste_livres)): ?>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Titre</th>
                    <th scope="col">Auteur</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Parcourir la liste des livres et afficher chaque entrée dans le tableau -->
                <?php foreach ($liste_livres as $livre): ?>
                    <tr>
                        <th scope="row"><?= $livre["nolivre"]; ?></th>
                        <td><?= $livre["titre"]; ?></td>
                        <td><?= $livre["nom_auteur"] . ' ' . $livre["prenom_auteur"]; ?></td>
                        <td>
                            <!-- Lien vers la page de détails du livre -->
                            <a href="detail_livre.php?id=<?= $livre["nolivre"]; ?>" class="btn btn-primary btn-sm">Détail du livre</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <!-- Message s'il n'y a aucun livre disponible -->
        <p>Aucun livre disponible.</p>
    <?php endif; ?>
</div>

<!-- Ajoutez vos scripts JavaScript ici si nécessaire -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
