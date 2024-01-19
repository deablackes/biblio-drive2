<?php
session_start();

// Inclure le fichier de connexion à la base de données avec PDO
include("connexion.php");

// Vérifier si l'ID du livre est passé en paramètre
if (isset($_GET['id'])) {
    $id_livre = $_GET['id'];

    // Récupérer les détails du livre
    $requete_detail_livre = "SELECT livre.*, auteur.nom AS nom_auteur, auteur.prenom AS prenom_auteur
                             FROM livre
                             INNER JOIN auteur ON livre.noauteur = auteur.noauteur
                             WHERE nolivre = :id_livre";
    $stmt_detail_livre = $connexion->prepare($requete_detail_livre);
    $stmt_detail_livre->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
    $stmt_detail_livre->execute();
    $detail_livre = $stmt_detail_livre->fetch(PDO::FETCH_ASSOC);
} else {
    // Rediriger si l'ID du livre n'est pas spécifié
    header("Location: liste_livre.php");
    exit();
}

// Gérer l'ajout au panier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouterAuPanier'])) {
    $quantite = $_POST['quantite'];

    // Créer un tableau pour stocker les livres dans le panier
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = array();
    }

    // Ajouter le livre au panier avec la quantité
    $_SESSION['panier'][] = array(
        'id_livre' => $detail_livre["nolivre"],
        'titre' => $detail_livre["titre"],
        'quantite' => $quantite,
    );

    // Rediriger vers la page du panier
    header("Location: panier.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail du Livre</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .custom-thumbnail {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4">
    <h1 class="mb-4">Détail du Livre</h1>

    <?php if ($detail_livre): ?>
        <div class="card">
            <img src="covers/<?= $detail_livre["image"]; ?>" class="card-img-top custom-thumbnail" alt="<?= $detail_livre["titre"]; ?>">
            <div class="card-body">
                <h5 class="card-title"><?= $detail_livre["titre"]; ?></h5>
                <p class="card-text"><strong>Auteur :</strong> <?= $detail_livre["nom_auteur"] . ' ' . $detail_livre["prenom_auteur"]; ?></p>
                <p class="card-text"><strong>Résumé :</strong> <?= $detail_livre["resume"]; ?></p>
                <p class="card-text"><strong>Année de parution :</strong> <?= $detail_livre["anneeparution"]; ?></p>
                <p class="card-text"><strong>ISBN13 :</strong> <?= $detail_livre["isbn13"]; ?></p>

                <form action="<?= $_SERVER['PHP_SELF'] . '?id=' . $id_livre; ?>" method="post">
                    <input type="hidden" name="produit_id" value="<?= $detail_livre["nolivre"]; ?>">
                    <div class="form-row align-items-center">
                        <div class="col-auto">
                            <label for="quantite" class="sr-only">Quantité</label>
                            <input type="number" class="form-control" id="quantite" name="quantite" value="1" min="1">
                        </div>
                        <div class="col-auto">
                            <button type="submit" name="ajouterAuPanier" class="btn btn-primary">Ajouter au Panier</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p class="mt-3">Livre non trouvé.</p>
    <?php endif; ?>
</div>

<!-- Ajoutez vos scripts JavaScript ici si nécessaire -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
