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
            if (isset($_POST['link']) && !empty($_POST['link'])) {
                $link = $_POST['link'];
        
                // Si vous avez également un champ 'technology_id', vous pouvez le spécifier ici
                if (isset($_POST['technology_id'])) {
                    $technologyArray = json_decode($_POST['technology_id']);
                    if (is_array($technologyArray)) {
                        $inserted = false; // Cette variable nous aidera à savoir si au moins une insertion a été réussie
                        foreach ($technologyArray as $technologyId) {
                            // Vérifiez d'abord si le lien existe déjà dans la base de données pour cette technologie
                            $checkSql = 'SELECT COUNT(*) FROM Ressources WHERE link = :link AND technology_id = :technology_id';
                            $checkSth = $conn->prepare($checkSql);
                            $checkSth->bindParam(':link', $link, PDO::PARAM_STR);
                            $checkSth->bindParam(':technology_id', $technologyId, PDO::PARAM_INT);
                            $checkSth->execute();
                            $count = $checkSth->fetchColumn();
        
                            if ($count === false) {
                                // Erreur de requête, vous pouvez gérer cela selon votre cas
                                echo json_encode(['status' => 'failure', 'message' => 'Database query error']);
                            } elseif ($count == 0) {
                                // Le lien n'existe pas encore pour cette technologie, procédez à l'insertion
                                $sql = 'INSERT INTO Ressources (link, technology_id) VALUES (:link, :technology_id)';
                                $sth = $conn->prepare($sql);
                                $sth->bindParam(':link', $link, PDO::PARAM_STR);
                                $sth->bindParam(':technology_id', $technologyId, PDO::PARAM_INT);
                                $sth->execute();
                                $inserted = true; // Au moins une insertion a réussi
                            }
                        }
        
                        if ($inserted) {
                            // Signalez à l'utilisateur que l'insertion est un succès
                            echo json_encode(['status' => 'success', 'message' => 'Resource added']);
                        } else {
                            // Tous les liens existaient déjà, signalez-le
                            echo json_encode(['status' => 'failure', 'message' => 'Resource already exists']);
                        }
                    }
                } else {
                    // JE SIGNALE L'UTILISATEUR QUE LA REQUETE EST UN SUCCES
                    echo json_encode(['status' => 'success', 'message' => 'Resource added']);
                }
            } else {
                // JE SIGNALE L'UTILISATEUR QUE LA REQUETE EST UN ECHEC
                echo json_encode(['status' => 'failure', 'message' => 'Link not found']);
            }
            break;

        case 'PUT':
            parse_str(file_get_contents("php://input"), $_PUT);
            if (isset($_PUT['technology_id']) && isset($_PUT['link'])) {
                $technologyArray = json_decode($_PUT['technology_id']);
                if (is_array($technologyArray)) {
                    // Boucle à travers les IDs de technologie et remplacer la ressource pour la technologie 
                    foreach ($technologyArray as $technologyId) {
                        $sql = 'UPDATE Ressources (link, technology_id) SET (:link, :technology_id)  WHERE id = :id';
                        $sth = $conn->prepare($sql);
                        $sth->bindParam(':link', $_PUT['link'], PDO::PARAM_STR);
                        $sth->bindParam(':technology_id', $technologyId, PDO::PARAM_INT);
                        $sth->execute();
                    }
                }
            }
            else {
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