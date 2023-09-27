<?php 


switch ($methodrequest) {
  
    // AFFICHE LES RESSOURCES  
    case 'GET':
        if(isset($uriParts[1])){
            $sql = 'SELECT * FROM Ressources WHERE id = :id';
            $sth = $conn->prepare($sql);
            $sth->bindParam(':id', $uriParts[1], PDO::PARAM_INT);
            $sth->execute();    
            // JE RECUPERE LES RESULTATS DE LA REQUETE SONT FORME DE TABLEAU.
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
            
            // JE RENVOI LES RESULTATS EN FORMAT JSON
            echo json_encode(['status' => 'success', 'data' => $result]);
        }
        else {
            $sql = 'SELECT * FROM Ressources';
            $sth = $conn->prepare($sql);
            $sth -> execute();
            //JE RECUPERE LES RESULTATS
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);

            //JE RENVOI LES SULTATS EN FORMAT JSON

            echo json_encode(['status' => 'success', 'data' => $result]);
        }
        break;

    case 'POST':
        if(isset($_POST['link']) && !empty($_POST['link'])){

        
            // Préparer la requête SQL d'insertion
            $sql = 'INSERT INTO Ressources(link) VALUES(:link)';
            $sth = $conn->prepare($sql);
            $sth->bindParam(':link', $_POST['link'], PDO::PARAM_STR);
            $sth->execute();
            
            // JE SIGNALE L'UTILISATEUR QUE LA REQUETE EST UN SUCCES
            echo json_encode(['status' => 'success', 'message' => 'Ressource added']);
        } else {
            // JE SIGNALE L'UTILISATEUR QUE LA REQUETE EST UN ECHEC
            echo json_encode(['status' => 'failure', 'message' => 'Link not found']);
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