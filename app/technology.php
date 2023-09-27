<?php

switch ($methodrequest) {

    // AFFICHE LES TECHNOLOGIES ET LES CATEGORIES/RESSOURCES ASSOCIÉS
    case 'GET':
        if (isset($uriParts[1])) {
            $sql =
                'SELECT
                t.id AS technology_id,
                t.name AS technology_name,
                t.logo_path AS technology_logo_path,
                t.logoname AS technology_logoname,
                c.id AS categories_id,
                r.id AS technology_resource_id
            FROM
                technology t
            LEFT JOIN
                Ressources r ON t.id = r.technology_id
            LEFT JOIN
                technology_categories tc ON t.id = tc.technology_id
            LEFT JOIN
                categories c ON tc.categories_id = c.id
            WHERE t.id = :id';

            $sth = $conn->prepare($sql);
            $sth->bindParam(':id', $uriParts[1], PDO::PARAM_INT);
            $sth->execute();

            // JE RECUPERE LES RESULTATS DE LA REQUETE SONT FORME DE TABLEAU.
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);

            // JE RENVOI LES RESULTATS EN FORMAT JSON
            echo json_encode(['status' => 'success', 'data' => $result]);
        } else {
            $sql =
                'SELECT
                t.id AS technology_id,
                t.name AS technology_name,
                t.logo_path AS technology_logo_path,
                t.logoname AS technology_logoname,
                c.id AS categories_id,
                r.id AS technology_resource_id
            FROM
                technology t
            LEFT JOIN
                Ressources r ON t.id = r.technology_id
            LEFT JOIN
                technology_categories tc ON t.id = tc.technology_id
            LEFT JOIN
                categories c ON tc.categories_id = c.id';
            $sth = $conn->prepare($sql);
            $sth->execute();
            //JE RECUPERE LES RESULTATS
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);

            //JE RENVOI LES SULTATS EN FORMAT JSON

            echo json_encode(['status' => 'success', 'data' => $result]);
        }
        break;

    case 'POST':
        if (isset($_POST['name']) && !empty($_POST['name'])) {

            // Préparer la requête SQL d'insertion
            $sql = 'INSERT INTO technology(name) VALUES(:name)';
            $sth = $conn->prepare($sql);
            $sth->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
            $sth->execute();
            // JE SIGNALE L'UTILISATEUR QUE LA REQUETE EST UN SUCCES
            echo json_encode(['status' => 'success', 'message' => 'Technology added']);

            if (isset($_FILES['logo']) && !empty($_FILES['logo']['name'])) {
                $host = $_SERVER['HTTP_HOST'];
                // Répertoire de destination pour les images
                $targetDirectory = $host . '/logo/';
                $targetFile = $targetDirectory . basename($_FILES['logo']['name']);
                // Vérifiez si le fichier est une image
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    // Le fichier a été téléchargé avec succès, enregistrez-le dans la base de données
                    $sql = 'UPDATE technology SET logoname = :logoname, logo_path = :logo WHERE name = :name';
                    $sth = $conn->prepare($sql);
                    $sth->bindParam(':logoname', $_FILES['logo']['name'], PDO::PARAM_STR);
                    $sth->bindParam(':logo', $targetFile, PDO::PARAM_STR);
                    $sth->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
                    $sth->execute();

                    // Signalez à l'utilisateur que la requête est un succès
                    echo json_encode(['status' => 'success', 'message' => 'Technology updated with logo']);
                }
            }
            if (isset($_POST['categories_id'])) {
                $categoriesArray = json_decode($_POST['categories_id']);
                if (is_array($categoriesArray)) {
                    // BOUCLE A TRAVERS LE TABLEAU ET INSERE CHAQUE VALEUR DANS LA BASE DE DONNEES
                    foreach ($categoriesArray as $categoryId) {
                        $sql = 'INSERT INTO technology_categories (categories_id, technology_id)
                        VALUES (:categories_id, (SELECT id FROM technology WHERE name = :name))';
                        $sth = $conn->prepare($sql);
                        $sth->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
                        $sth->bindParam(':categories_id', $categoryId, PDO::PARAM_INT);
                        $sth->execute();
                    }
                }
                // JE SIGNALE L'UTILISATEUR QUE LA REQUETE EST UN SUCCES
                echo json_encode(['status' => 'success', 'message' => 'Categories linked']);
            }
        } else {
            // JE SIGNALE L'UTILISATEUR QUE LA REQUETE EST UN ECHEC
            echo json_encode(['status' => 'failure', 'message' => 'Name not found']);
        }
        break;

    case 'PUT':
        parse_str(file_get_contents("php://input"), $_PUT);
        if(isset($uriParts[1]) && isset($_PUT['link']) && !empty($_PUT['link'])){
            $sql = 'UPDATE Ressources SET link = :link WHERE id = :id';
            $sth = $conn->prepare($sql);
            $sth->bindParam(':id', $uriParts[1], PDO::PARAM_INT);
            $sth->bindParam(':link', $_PUT['link'], PDO::PARAM_STR);
            $sth->execute();
            echo json_encode(['status' => 'success', 'message' => 'Ressource updated']);
        } else {
            echo json_encode(['status' => 'failure', 'message' => 'Invalid request']);
        }
        break;

    case 'DELETE':
        if (isset($uriParts[1])) {
            $sql = 'DELETE FROM technology WHERE id = :id';
            $sth = $conn->prepare($sql);
            $sth->bindParam(':id', $uriParts[1], PDO::PARAM_INT);
            $sth->execute();
            echo json_encode(['status' => 'success', 'message' => 'technology deleted']);
        } else {
            echo json_encode(['status' => 'failure', 'messsage' => 'Id not found for deleting']);
        }
        break;
}
?>
