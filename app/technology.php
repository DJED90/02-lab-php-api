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
            GROUP_CONCAT(DISTINCT c.id) AS categories_ids,
            GROUP_CONCAT(DISTINCT r.id) AS technology_resource_ids
        FROM
            technology t
        LEFT JOIN
            Ressources r ON t.id = r.technology_id
        LEFT JOIN
            technology_categories tc ON t.id = tc.technology_id
        LEFT JOIN
            categories c ON tc.categories_id = c.id
        WHERE t.id = :id
        GROUP BY t.id, t.name, t.logo_path, t.logoname';

        $sth = $conn->prepare($sql);
        $sth->bindParam(':id', $uriParts[1], PDO::PARAM_INT);
        $sth->execute();

        // JE RECUPERE LES RESULTATS DE LA REQUETE SONT FORME DE TABLEAU.
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        // Si GROUP_CONCAT retourne NULL, on met une chaîne vide
        if ($result['categories_ids'] === null) {
            $result['categories_ids'] = '';
        }

        if ($result['technology_resource_ids'] === null) {
            $result['technology_resource_ids'] = '';
        }

        // JE RENVOI LES RESULTATS EN FORMAT JSON
        echo json_encode(['status' => 'success', 'data' => $result]);
    } else {
        $sql =
            'SELECT
            t.id AS technology_id,
            t.name AS technology_name,
            t.logo_path AS technology_logo_path,
            t.logoname AS technology_logoname,
            GROUP_CONCAT(DISTINCT c.id) AS categories_ids,
            GROUP_CONCAT(DISTINCT r.id) AS technology_resource_ids
        FROM
            technology t
        LEFT JOIN
            Ressources r ON t.id = r.technology_id
        LEFT JOIN
            technology_categories tc ON t.id = tc.technology_id
        LEFT JOIN
            categories c ON tc.categories_id = c.id
        GROUP BY t.id, t.name, t.logo_path, t.logoname';

        $sth = $conn->prepare($sql);
        $sth->execute();

        // JE RECUPERE LES RESULTATS DE LA REQUETE SONT FORME DE TABLEAU.
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        // Si GROUP_CONCAT retourne NULL pour certaines lignes, on met une chaîne vide
        foreach ($result as &$row) {
            if ($row['categories_ids'] === null) {
                $row['categories_ids'] = '';
            }

            if ($row['technology_resource_ids'] === null) {
                $row['technology_resource_ids'] = '';
            }
        }

        //JE RENVOI LES SULTATS EN FORMAT JSON
        echo json_encode(['status' => 'success', 'data' => $result]);
    }
    break;


    case 'POST':
        if (isset($_POST['name']) && !empty($_POST['name'])) {

            // Vérifiez d'abord si le nom existe déjà dans la base de données
            $checkSql = 'SELECT COUNT(*) FROM technology WHERE name = :name';
            $checkSth = $conn->prepare($checkSql);
            $checkSth->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
            $checkSth->execute();
            $count = $checkSth->fetchColumn();
            if ($count === false){
                // Erreur de requête, vous pouvez gérer cela selon votre cas
                echo json_encode(['status' => 'failure', 'message' => 'Database query error']);
            }
            elseif($count > 0){
                // Le nom existe déjà dans la base de données, signalez-le
                echo json_encode(['status' => 'failure', 'message' => 'Technology name already exists']);
            }
            else{
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
                    $targetDirectoryLocal = '/var/www/html/logo/'; // Répertoire de destination sur le serveur
                    // Chemin complet du fichier de destination
                    $targetFilePath = $targetDirectoryLocal . basename($_FILES['logo']['name']);
                    //Chemin complet du fichier de destination serveur
                    $targetFile = $targetDirectory . basename($_FILES['logo']['name']);
                    // Vérifiez si le fichier est une image
                    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                    if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                        //upload du fichier dans le repertoire
                        if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFilePath)) {
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
                    else {
                        echo json_encode(['status' => 'failure', 'message' => 'Failed to upload file']);
                    }
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
            }
        } else {
            // JE SIGNALE L'UTILISATEUR QUE LA REQUETE EST UN ECHEC
            echo json_encode(['status' => 'failure', 'message' => 'Name not found']);
        }
        break;

    case 'PUT':
        $headers = getallheaders();
        $logo = file_get_contents("php://input");
        
        // Obtenez l'ID de la technologie à partir de l'URL
        $technologyId = $uriParts[1];
            // Récupérez le nom de la technologie à partir de la base de données
            $sql = 'SELECT name FROM technology WHERE id = :id';
            $sth = $conn->prepare($sql);
            $sth->bindParam(':id', $technologyId, PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetch(PDO::FETCH_ASSOC);
       
 
        if ($result) {
            $technologyName = $result['name']; // Obtenez le nom de la technologie
    
            // Vérifiez si le fichier est une image
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->buffer($logo);
            
            if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif']) && isset($technologyId)) {
                
                if (isset($headers['name']) && !empty($headers['name'])) {
                    $conn->beginTransaction();
                    $targetDirectory = '/var/www/html/logo/'; // Répertoire de destination sur le serveur
                    $technologyName = $headers['name']; // Obtenez le nom de la technologie
                    // Utilisez une expression régulière pour extraire l'extension
                    if (preg_match('/\/([a-zA-Z]+)$/', $mime, $matches)) {
                        $extensionExtraite = $matches[1];
                    } else {
                        echo json_encode(['status' => 'failed', 'message' => 'Extension not found']);
                    }
                    $logofile = file_put_contents($targetDirectory.$technologyName . "." . $extensionExtraite, $logo); // Enregistre le contenu binaire dans le fichier temporaire
                    //On récupère le nom du fichier pour l'inscrire sur la base de données 
                    $logoname = $technologyName . "." . $extensionExtraite;
                    //On récupère le repertoire du fichier pour l'inscrire sur la base de données
                    $host = $_SERVER['HTTP_HOST'];
                    $targetDirectory = $host . '/logo/';
                    $logopath = $targetDirectory . $logoname ; 
                    // Mettez à jour la table 'technology' avec le nouveau nom du fichier
                    $sql1 = 'UPDATE technology SET name = :name, logoname = :logo, logo_path= :logopath WHERE id = :id';
                    $sth1 = $conn->prepare($sql1);
                    $sth1->bindParam(':id', $technologyId, PDO::PARAM_INT);
                    $sth1->bindParam(':name', $headers['name'], PDO::PARAM_STR);
                    $sth1->bindParam(':logo', $logoname, PDO::PARAM_STR); // Stockez le nom du fichier dans la colonne "logo"
                    $sth1->bindParam(':logopath', $logopath, PDO::PARAM_STR); 
                    $sth1->execute();
    
    
                    // Mettez à jour la table 'technology_categories' (si nécessaire)
                    if (isset($headers['categories_id']) && !empty($headers['categories_id'])) {
                        $sql2 = 'UPDATE technology_categories SET categories_id = :categories_id WHERE technology_id = :id';
                        $sth2 = $conn->prepare($sql2);
                        $sth2->bindParam(':id', $technologyId, PDO::PARAM_INT);
                        $sth2->bindParam(':categories_id', $headers['categories_id'], PDO::PARAM_INT);
                        $sth2->execute();
                    }
    
                    // Validez la transaction
                    $conn->commit();
                    echo json_encode(['status' => 'success', 'message' => 'Technology updated']);
                } else {
                    echo json_encode(['status' => 'failure', 'message' => 'Invalid request']);
                }
            } else {
                echo json_encode(['status' => 'failure', 'message' => 'Invalid image format']);
            }
        } else {
            echo json_encode(['status' => 'failure', 'message' => 'Technology not found']);
        }
        break;

        case 'DELETE':
            if (isset($uriParts[1])) {
                $technologyId = $uriParts[1];
        
                // Récupérer les informations de la technologie à partir de la base de données
                $sql = 'SELECT logoname FROM technology WHERE id = :id';
                $sth = $conn->prepare($sql);
                $sth->bindParam(':id', $technologyId, PDO::PARAM_INT);
                $sth->execute();
                $technologyData = $sth->fetch(PDO::FETCH_ASSOC);
        
                if ($technologyData) {
                    $logoFileName = $technologyData['logoname'];
        
                    // Supprimer le logo du serveur s'il existe
                    $logoPath = '/var/www/html/logo/' . $logoFileName; // Chemin du logo sur le serveur
                    if (file_exists($logoPath)) {
                        unlink($logoPath);
                    }
        
                    // Supprimer les ressources associées
                    $deleteResourcesSql = 'DELETE FROM Ressources WHERE technology_id = :id';
                    $deleteResourcesSth = $conn->prepare($deleteResourcesSql);
                    $deleteResourcesSth->bindParam(':id', $technologyId, PDO::PARAM_INT);
                    $deleteResourcesSth->execute();
        
                    // Supprimer la technologie de la base de données
                    $deleteTechnologySql = 'DELETE FROM technology WHERE id = :id';
                    $deleteTechnologySth = $conn->prepare($deleteTechnologySql);
                    $deleteTechnologySth->bindParam(':id', $technologyId, PDO::PARAM_INT);
                    $deleteTechnologySth->execute();
        
                    echo json_encode(['status' => 'success', 'message' => 'Technology and associated resources deleted']);
                } else {
                    echo json_encode(['status' => 'failure', 'message' => 'Technology not found']);
                }
            } else {
                echo json_encode(['status' => 'failure', 'message' => 'Id not found for deleting']);
            }
            break;
    
}
?>
