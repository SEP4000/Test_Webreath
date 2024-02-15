<?php
// Connexion à la base de données
require_once "connect.php";

// Fonction pour générer des données aléatoires
function generateRandomData() {
    return array(
        'temperature' => rand(-15, 25), // La température aléatoire entre -15 et 25 degrés Celsius
        'vitesse' => rand(30, 90), // La vitesse de la voiture aléatoire entre 30 et 90 km/h
        'calorie' => rand(1000, 4000) // La quantité de calories aléatoire entre 1000 et 4000
    );
}

// Fonction pour insérer les données dans la base de données
function insertDataIntoDatabase($module_id, $data, $type) {
    global $db;

    // Insérez les données dans la table correspondante avec la date de la mesure qui doit étre la date courante
    $sql = "INSERT INTO `donnees` (`module_id`, `valeur_mesuree`, `date_mesure`) VALUES (:module_id, :valeur_mesuree, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":module_id", $module_id, PDO::PARAM_INT);
    $stmt->bindParam(":valeur_mesuree", $data, PDO::PARAM_STR);
    $stmt->execute();
}

try {
    // Récupérez les modules depuis la base de données
    $sql = "SELECT * FROM `modules`";
    $stmt = $db->query($sql);
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Générer et insérer des données pour chaque module
    foreach ($modules as $module) {
        $module_id = $module['module_id'];
        $data = generateRandomData();
        $type = '';

        // Déterminer à quel module doit aller cette donnée pour la base de données en fonction de son ID
        switch ($module_id) {
            case 1:
                $type = 'temperature';
                break;
            case 2:
                $type = 'vitesse';
                break;
            case 3:
                $type = 'calorie';
                break;
            default:
                continue; // Ignorer les modules avec des ID non reconnus
        }

        // Insérer les données dans la base de données
        insertDataIntoDatabase($module_id, $data[$type], $type);
    }

    echo "Données générées avec succès.";
} catch (PDOException $e) {
    echo "Erreur lors de la génération et de l'insertion des données : " . $e->getMessage();
}
?>
