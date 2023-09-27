<?php
// Définit le type de contenu JSON
header('Content-Type: application/json'); 

$methodrequest = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
// Divisez la valeur de $uri en un tableau en utilisant le "/"
$uriParts = explode('/', trim($uri, '/'));
//CONNEXION BASE DE DONNEES
require("function.php");
$conn = connect_db();

// ROUTAGE PRINCIPALE
switch ($uriParts[0]) {
    // ---------- TECHNOLOGIES -----------    
    case 'technology':
        require("technology.php");
        break;
    // ---------- CATEGORIES ---------        
    case 'categories':
        require("categories.php");
        break;
    // ---------- RESOURCES ---------   
    case 'ressources':
        require("ressources.php");
        break;
    // Case DEFAULT en cas de route inconnue
    default:
    require("documentation.php");
        echo json_encode(['status' => 'failure', 'message' => 'Route not found']);
        break;
}
// DECONNEXION BDD
$conn = null ;
?>