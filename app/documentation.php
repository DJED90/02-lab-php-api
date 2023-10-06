
<?php
// Définit le type de contenu HTML
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation</title>
    <!-- Include Bootstrap CSS (you may need to adjust the path) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1>API Documentation</h1>

        <h2>Ressources API</h2>
        <p><strong>GET /ressources/{id}</strong> - Obtenir une ressource par ID</p>
        <p><strong>GET /ressources</strong> - Obtenir toutes les ressources</p>
        <p><strong>POST /ressources</strong> - Ajouter une nouvelle ressource</p>
        <p><strong>PUT /ressources/{id}</strong> - Mettre à jour une ressource par ID</p>
        <p><strong>DELETE /ressources/{id}</strong> - Supprimer une ressource par ID</p>

        <h2>Technology API</h2>
        <p><strong>GET /technology/{id}</strong> - Obtenir une technologie par ID</p>
        <p><strong>GET /technology</strong> - Obtenir toutes les technologies</p>
        <p><strong>POST /technology</strong> - Ajouter une nouvelle technologie</p>
        <p><strong>PUT /technology/{id}</strong> - Mettre à jour une technologie par ID</p>
        <p><strong>DELETE /technology/{id}</strong> - Supprimer une technologie par ID</p>

        <h2>Categories API</h2>
        <p><strong>GET /categories/{id}</strong> - Obtenir une catégorie par ID</p>
        <p><strong>GET /categories</strong> - Obtenir toutes les catégories</p>
        <p><strong>POST /categories</strong> - Ajouter une nouvelle catégorie</p>
        <p><strong>PUT /categories/{id}</strong> - Mettre à jour une catégorie par ID</p>
        <p><strong>DELETE /categories/{id}</strong> - Supprimer une catégorie par ID</p>

        <h2>Nouvelles fonctionnalités API</h2>

        <!-- Exemple GET Request -->
        <h4>Exemple de requête GET pour une nouvelle fonctionnalité</h4>
        <pre><code>GET /nouvelle-fonctionnalite/1</code></pre>
        <p>Obtenez des détails sur une nouvelle fonctionnalité spécifique en utilisant son ID.</p>

        <!-- Exemple POST Request -->
        <h4>Exemple de requête POST pour une nouvelle fonctionnalité</h4>
        <pre><code>POST /nouvelle-fonctionnalite</code></pre>
        <p>Ajoutez une nouvelle fonctionnalité en incluant les données requises dans le corps de la requête.</p>

        <!-- Exemple PUT Request -->
        <h4>Exemple de requête PUT pour une nouvelle fonctionnalité</h4>
        <pre><code>PUT /nouvelle-fonctionnalite/2</code></pre>
        <p>Mettez à jour une nouvelle fonctionnalité spécifique en incluant les données mises à jour dans le corps de la requête.</p>

        <!-- Exemple DELETE Request -->
        <h4>Exemple de requête DELETE pour une nouvelle fonctionnalité</h4>
        <pre><code>DELETE /nouvelle-fonctionnalite/3</code></pre>
        <p>Supprimez une nouvelle fonctionnalité spécifique par son ID.</p>
    </div>

    <!-- Include Bootstrap JS (you may need to adjust the path) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
