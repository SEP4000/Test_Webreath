<?php
// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification si les données nécessaires sont présentes
    if(
        isset($_POST["nom_module"], $_POST["description"], $_POST["duree_fonctionnement"], $_POST["nombre_donnees_envoyees"], $_POST["date_derniere_mise_a_jour"], $_POST["date_installation"]) 
        && !empty($_POST["nom_module"]) && !empty($_POST["description"]) && !empty($_POST["duree_fonctionnement"]) && !empty($_POST["nombre_donnees_envoyees"]) && !empty($_POST["date_derniere_mise_a_jour"]) && !empty($_POST["date_installation"])
    ){
        // Le formulaire est complet
        // On récupère les données en les protégeant
        // On retire toutes les balises du nom du module
        $nom_module = strip_tags($_POST["nom_module"]);
        // On neutralise toute balise de la description
        $description = htmlspecialchars($_POST["description"]);
        // On récupère les données de l'état du module
        $duree_fonctionnement = $_POST["duree_fonctionnement"];
        $nombre_donnees_envoyees = $_POST["nombre_donnees_envoyees"];
        $date_derniere_mise_a_jour = $_POST["date_derniere_mise_a_jour"];
        $date_installation = $_POST["date_installation"];
        
        // On enregistre les données en se connectant à la base
        require_once "connect.php";
        
        // On commence une transaction pour garantir l'intégrité des données
        $db->beginTransaction();
        
        try {
            // On écrit la requête pour ajouter le module
            $sql_module = "INSERT INTO `modules` (`nom_module`, `description`, `date_installation`) VALUES (:nom_module, :description, :date_installation)";
            // On prépare la requête
            $query_module = $db->prepare($sql_module);
            // On injecte les paramètres
            $query_module->bindValue(":nom_module", $nom_module, PDO::PARAM_STR);
            $query_module->bindValue(":description", $description, PDO::PARAM_STR);
            $query_module->bindValue(":date_installation", $date_installation, PDO::PARAM_STR);
            // On exécute la requête
            $query_module->execute();
            
            // On récupère l'id du dernier module ajouté
            $module_id = $db->lastInsertId();
            
            // On génère aléatoirement l'état de fonctionnement de chaque module
            $etat_fonctionnement_aleatoire = rand(0, 1) ? 'En marche' : 'En arrêt';
            
            // On écrit la requête pour ajouter l'état du module
            $sql_etat = "INSERT INTO `etat_module` (`module_id`, `etat_fonctionnement`, `duree_fonctionnement`, `nombre_donnees_envoyees`, `date_derniere_mise_a_jour`) VALUES (:module_id, :etat_fonctionnement, :duree_fonctionnement, :nombre_donnees_envoyees, :date_derniere_mise_a_jour)";
            // On prépare la requête
            $query_etat = $db->prepare($sql_etat);
            // On injecte les paramètres
            $query_etat->bindValue(":module_id", $module_id, PDO::PARAM_INT);
            // État de fonctionnement aléatoire
            $query_etat->bindValue(":etat_fonctionnement", $etat_fonctionnement_aleatoire, PDO::PARAM_STR); 
            $query_etat->bindValue(":duree_fonctionnement", $duree_fonctionnement, PDO::PARAM_STR);
            $query_etat->bindValue(":nombre_donnees_envoyees", $nombre_donnees_envoyees, PDO::PARAM_INT);
            $query_etat->bindValue(":date_derniere_mise_a_jour", $date_derniere_mise_a_jour, PDO::PARAM_STR);
            // On exécute la requête
            $query_etat->execute();
            
            // On commit la transaction
            $db->commit();
            
            // Redirection vers la page article.php après l'ajout réussi
            header("Location: article.php");
            exit;
        } catch (PDOException $e) {
            // En cas d'erreur, on rollback la transaction et on affiche l'erreur
            $db->rollBack();
            $error_message = "Erreur : " . $e->getMessage();
        }
    } else {
        // On affiche un message d'erreur si tous les champs ne sont pas remplis
        $error_message = "Veuillez remplir tous les champs.";
    }
}

$titre = "Ajouter un module";
// On inclut le header
include_once "header.php";
// On inclut le navbar
include_once "navbar.php";
?>

<div class="container">
    <br>
    <h1 class="text-center">Ajouter un module</h1>
    <br>
    <!-- Affichage du message d'erreur si il y en a un -->
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?= $error_message ?>
        </div>
    <?php endif; ?>
<!-- Formulaire pour ajouter un module -->
    <form method="post">
        <div class="mb-3">
            <label for="nom_module" class="form-label">Nom du module</label>
            <input type="text" class="form-control" id="nom_module" name="nom_module">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>
        <div class="mb-3">
            <label for="duree_fonctionnement" class="form-label">Durée de fonctionnement</label>
            <input type="number" class="form-control" id="duree_fonctionnement" name="duree_fonctionnement">
        </div>
        <div class="mb-3">
            <label for="nombre_donnees_envoyees" class="form-label">Nombre de données envoyées</label>
            <input type="number" class="form-control" id="nombre_donnees_envoyees" name="nombre_donnees_envoyees">
        </div>
        <div class="mb-3">
            <label for="date_installation" class="form-label">Date d'installation</label>
            <input type="date" class="form-control" id="date_installation" name="date_installation">
        </div>
        <div class="mb-3">
            <label for="date_derniere_mise_a_jour" class="form-label">Date dernière mise à jour</label>
            <input type="datetime-local" class="form-control" id="date_derniere_mise_a_jour" name="date_derniere_mise_a_jour">
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>
<br>

<?php
// On inclut le footer
include_once "footer.php";
?>
