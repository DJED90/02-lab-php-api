<?php 


switch ($methodrequest) {
  
    // AFFICHE LES TECHNOLOGIES ET LES CATEGORIES/RESSOURCES ASSOCIÉS
    case 'GET':
        if(isset($uriParts[1])){
            $sql = 'SELECT t.name AS technology_name, c.name AS category_name, r.link AS resource_link
            FROM technology AS t
            LEFT JOIN categories AS c ON t.categories_id = c.id
            LEFT JOIN Ressources AS r ON t.Ressources_id = r.id
            WHERE t.id = :id';
            
            $sth = $conn->prepare($sql);
            $sth->bindParam(':id', $uriParts[1], PDO::PARAM_INT);
            $sth->execute();    
        
            // JE RECUPERE LES RESULTATS DE LA REQUETE SONT FORME DE TABLEAU.
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
            
            // JE RENVOI LES RESULTATS EN FORMAT JSON
            echo json_encode(['status' => 'success', 'data' => $result]);
        }
        else {
            $sql = 'SELECT t.name AS technology_name, c.name AS category_name, r.link AS resource_link
            FROM technology AS t
            LEFT JOIN categories AS c ON t.categories_id = c.id
            LEFT JOIN Ressources AS r ON t.Ressources_id = r.id;            
            ';
            $sth = $conn->prepare($sql);
            $sth -> execute();
            //JE RECUPERE LES RESULTATS
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);

            //JE RENVOI LES SULTATS EN FORMAT JSON

            echo json_encode(['status' => 'success', 'data' => $result]);
        }
        break;

    case 'POST':
        if(isset($_POST['name']) && !empty($_POST['name'])){
            
            // Préparer la requête SQL d'insertion
            $sql = 'INSERT INTO technology(name) VALUES(:name)';
            $sth = $conn->prepare($sql);
            $sth->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
            $sth->execute();
            // JE SIGNALE L'UTILISATEUR QUE LA REQUETE EST UN SUCCES
            echo json_encode(['status' => 'success', 'message' => 'Technology added']);

            if(isset($_FILES['logo']) && !empty($_FILES['logo']['name'])){
                $host = $_SERVER['HTTP_HOST'];
                // Répertoire de destination pour les images
                $targetDirectory = $host . '/logo/';
                $targetFile = $targetDirectory . basename($_FILES['logo']['name']);
                // Vérifiez si le fichier est une image
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                if(in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])){
                // Le fichier a été téléchargé avec succès, enregistrez-le dans la base de données
                $sql = 'INSERT INTO technology(logoname, logo_path) VALUES(:name, :logo)';
                $sth = $conn->prepare($sql);
                $sth->bindParam(':name', ($_FILES['logo']['name']), PDO::PARAM_STR);
                $sth->bindParam(':logo', $targetFile, PDO::PARAM_STR);
                $sth->execute();
    
                // Signalez à l'utilisateur que la requête est un succès
                echo json_encode(['status' => 'success', 'message' => 'Technology added with logo']);
                }
            }
            if(isset($_POST['categories_id'])){
                $categoriesArray = json_decode($_POST['categories_id']);
                if(is_array($categoriesArray)){
                    // BOUCLE A TRAVERS LE TABLEAU ET INSERE CHAQUE VALEUR DANS LA BASE DE DONNEES
                    foreach ($categoriesArray as $categoryId){
                        $sql = 'INSERT INTO technology(categories_id) VALUES(:categories_id)';
                        $sth = $conn->prepare($sql);
                        $sth->bindParam(':categories_id', $categoryId, PDO::PARAM_INT);
                        $sth->execute();
                    }
                }
                // JE SIGNALE L'UTILISATEUR QUE LA REQUETE EST UN SUCCES
                echo json_encode(['status' => 'success', 'message' => 'Categorie linked']);
            }
            if(isset($_POST['Ressources_id'])){
                $RessourcesArray = json_decode($_POST['categories_id']);
                if(is_array($RessourcesArray)){
                    foreach($RessourcesArray as $RessourcesId){
                        $sql = 'INSERT INTO technology(Ressources_id) VALUES(:Ressources_id)';
                        $sth = $conn->prepare($sql);
                        $sth->bindParam(':Ressources_id', $RessourcesId, PDO::PARAM_STR);
                        $sth->execute();
                    }
                }
                // JE SIGNALE L'UTILISATEUR QUE LA REQUETE EST UN SUCCES
                echo json_encode(['status' => 'success', 'message' => 'Ressource linked']);
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
        if(isset($uriParts[1])){
            $sql = 'DELETE FROM Ressources WHERE id = :id';
            $sth = $conn->prepare($sql);
            $sth->bindParam(':id', $uriParts[1], PDO::PARAM_INT);
            $sth->execute();
            echo json_encode(['status' => 'success', 'message' => 'Ressource deleted']);
        }
        else {
            echo json_encode(['status' => 'failure','messsage'=>'Id not found for deleting']);
        }
        break;
}
?>