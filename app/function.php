<?php
global $mysqlConnection;
function connect_db()
{
    $host = 'localhost'; // Adresse du serveur de base de données
    $db = getenv('MYSQL_DATABASE'); // Nom de la base de données
    $user = getenv('MYSQL_USER'); // Nom d'utilisateur de la base de données
    $pass = getenv('MYSQL_PASSWORD'); // Mot de passe de la base de données
    $dsn = 'mysql:host=mysql;dbname=phpapi;charset=utf8';
    $mysqlConnection = new PDO($dsn, $user, $pass);
    return $mysqlConnection ;
}


?>