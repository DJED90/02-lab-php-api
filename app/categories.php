<?php 


switch ($methodrequest) {
  
    // AFFICHE LES CATEGORIES    
    case 'GET':
        if(isset($uriParts[1])){
            $sql = 'SELECT * FROM categories WHERE id = :id';
            $sth = $conn->prepare($sql);
            $sth->bindParam(':id', $uriParts[1], PDO::PARAM_INT);
            $sth->execute();
            // JE RECUPERE LES RESULTATS DE LA REQUETE SONT FORME DE TABLEAU.
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
            
            // JE RENVOI LES RESULTATS EN FORMAT JSON
            echo json_encode(['status' => 'success', 'data' => $result]);
        }
        else {
            $sql = 'SELECT * FROM categories';
            $sth = $conn->prepare($sql);
            $sth -> execute();
            //JE RECUPERE LES RESULTATS
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);

            //JE RENVOI LES SULTATS EN FORMAT JSON

            echo json_encode(['status' => 'success', 'data' => $result]);
        }
        break;

        case 'POST':
            if (isset($_POST['name']) && !empty($_POST['name'])) {
                $name = $_POST['name'];
        
                // Vérifiez d'abord si le nom de la catégorie existe déjà dans la base de données
                $checkSql = 'SELECT COUNT(*) FROM categories WHERE name = :name';
                $checkSth = $conn->prepare($checkSql);
                $checkSth->bindParam(':name', $name, PDO::PARAM_STR);
                $checkSth->execute();
                $count = $checkSth->fetchColumn();
        
                if ($count === false) {
                    // Erreur de requête, vous pouvez gérer cela selon votre cas
                    echo json_encode(['status' => 'failure', 'message' => 'Database query error']);
                } elseif ($count == 0) {
                    // Le nom de la catégorie n'existe pas encore, procédez à l'insertion
                    $sql = 'INSERT INTO categories (name) VALUES (:name)';
                    $sth = $conn->prepare($sql);
                    $sth->bindParam(':name', $name, PDO::PARAM_STR);
                    $sth->execute();
        
                    // Signalez à l'utilisateur que l'insertion est un succès
                    echo json_encode(['status' => 'success', 'message' => 'Category added']);
                } else {
                    // Le nom de la catégorie existe déjà, signalez-le
                    echo json_encode(['status' => 'failure', 'message' => 'Category already exists']);
                }
            } else {
                // JE SIGNALE L'UTILISATEUR QUE LA REQUETE EST UN ECHEC
                echo json_encode(['status' => 'failure', 'message' => 'Name not found']);
            }
            break;
        

        case 'PUT':
            parse_str(file_get_contents("php://input"), $_PUT);
            if(isset($uriParts[1]) && isset($_PUT['name']) && !empty($_PUT['name'])){
                $sql = 'UPDATE categories SET name = :name WHERE id = :id';
                $sth = $conn->prepare($sql);
                $sth->bindParam(':id', $uriParts[1], PDO::PARAM_INT);
                $sth->bindParam(':name', $_PUT['name'], PDO::PARAM_STR);
                $sth->execute();
                echo json_encode(['status' => 'success', 'message' => 'Category updated']);
            } else {
                echo json_encode(['status' => 'failure', 'message' => 'Invalid request']);
            }
            break;

    case 'DELETE':
        if(isset($uriParts[1])){
            $sql = 'DELETE FROM categories WHERE id = :id';
            $sth = $conn->prepare($sql);
            $sth->bindParam(':id', $uriParts[1], PDO::PARAM_INT);
            $sth->execute();
            echo json_encode(['status' => 'success', 'message' => 'Category deleted']);
        }
        else {
            echo json_encode(['status' => 'failure','messsage'=>'Id not found for deleting']);
        }
        break;
}
?>