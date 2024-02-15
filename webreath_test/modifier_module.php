<?php
require_once "connect.php";

// Vérification si l'identifiant du module est passé en paramètre
if (!isset($_GET["module_id"]) || empty($_GET["module_id"])) {
    // Redirection vers la page d'accueil si l'identifiant du module est manquant
    header("Location: article.php");
    exit;
}

// Récupération de l'identifiant du module depuis les paramètres de l'URL
$module_id = $_GET["module_id"];

// Récupération des données du module depuis la base de données
$sql_module = "SELECT * FROM `modules` WHERE `module_id` = :module_id";
$query_module = $db->prepare($sql_module);
$query_module->bindValue(":module_id", $module_id, PDO::PARAM_INT);
$query_module->execute();
$module = $query_module->fetch();

// Vérification si le module existe
if (!$module) {
    // Affichage d'un message d'erreur et arrêt du script si le module n'existe pas
    die("Le module n'existe pas.");
}

// Message d'erreur par défaut
$message = "";

// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification si toutes les données nécessaires sont présentes
    if (
        isset($_POST["nom_module"], $_POST["description"], $_POST["duree_fonctionnement"], $_POST["nombre_donnees_envoyees"], $_POST["date_derniere_mise_a_jour"], $_POST["date_installation"]) &&
        !empty($_POST["nom_module"]) && !empty($_POST["description"]) && !empty($_POST["duree_fonctionnement"]) && !empty($_POST["nombre_donnees_envoyees"]) && !empty($_POST["date_derniere_mise_a_jour"]) && !empty($_POST["date_installation"])
    ) {
        //le formulaire est complet
        //on récupeère les données en les protégeant
        //on retire toutes les balises du nom du module
        $nom_module = strip_tags($_POST["nom_module"]);
        //on neutralise toute balise de la description
        $description = htmlspecialchars($_POST["description"]);
        //on récupeère les données de l'état du module
        $duree_fonctionnement = $_POST["duree_fonctionnement"];
        $nombre_donnees_envoyees = $_POST["nombre_donnees_envoyees"];
        $date_derniere_mise_a_jour = $_POST["date_derniere_mise_a_jour"];
        $date_installation = $_POST["date_installation"];

        //on commence une transaction pour garantir l'intégrité des données
        $db->beginTransaction();

        try {
            //on écrit la requête pour modifier le module
            $sql_update_module = "UPDATE `modules` SET `nom_module` = :nom_module, `description` = :description, `date_installation` = :date_installation WHERE `module_id` = :module_id";
            //on prepare la requête
            $query_update_module = $db->prepare($sql_update_module);
            //on injecte les paramètres
            $query_update_module->bindValue(":nom_module", $nom_module, PDO::PARAM_STR);
            $query_update_module->bindValue(":description", $description, PDO::PARAM_STR);
            $query_update_module->bindValue(":date_installation", $date_installation, PDO::PARAM_STR);
            $query_update_module->bindValue(":module_id", $module_id, PDO::PARAM_INT);
            //on execute la requête
            $query_update_module->execute();

            //on écrit la requête pour modifier l'état du module
            $sql_update_etat = "UPDATE `etat_module` SET `duree_fonctionnement` = :duree_fonctionnement, `nombre_donnees_envoyees` = :nombre_donnees_envoyees, `date_derniere_mise_a_jour` = :date_derniere_mise_a_jour WHERE `module_id` = :module_id";
            //on prepare la requête
            $query_update_etat = $db->prepare($sql_update_etat);
            //on injecte les paramètres
            $query_update_etat->bindValue(":duree_fonctionnement", $duree_fonctionnement, PDO::PARAM_STR);
            $query_update_etat->bindValue(":nombre_donnees_envoyees", $nombre_donnees_envoyees, PDO::PARAM_INT);
            $query_update_etat->bindValue(":date_derniere_mise_a_jour", $date_derniere_mise_a_jour, PDO::PARAM_STR);
            $query_update_etat->bindValue(":module_id", $module_id, PDO::PARAM_INT);
            //on execute la requête
            $query_update_etat->execute();

            //on commit la transaction
            $db->commit();

            // Redirection vers la page d'accueil après la modification
            header("Location: article.php");
            exit;
        } catch (PDOException $e) {
            // En cas d'erreur, on rollback la transaction et on affiche l'erreur
            $db->rollBack();
            $message = "Erreur : " . $e->getMessage();
        }
    } else {
        //on affiche un message d'erreur si tous les champs ne sont pas remplis
        $message = "Veuillez remplir tous les champs.";
    }
}

$titre = "Modifier un module";
//on inclut le header
include_once "header.php";
//on inclut le navbar
include_once "navbar.php";
?>

<div class="container">
    <br>
    <h1 class="text-center">Modifier un module</h1>
    <br>
    <!-- Affichage du message d'erreur si il y en a un -->
    <?php if (!empty($message)) : ?>
        <div class="alert alert-danger" role="alert">
            <?= $message ?>
        </div>
    <?php endif; ?>
    <!-- Formulaire de modification du module -->
    <form method="post">
        <div class="mb-3">
            <label for="nom_module" class="form-label">Nom du module</label>
            <input type="text" class="form-control" id="nom_module" name="nom_module" value="<?= htmlspecialchars($module["nom_module"]) ?>">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($module["description"]) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="duree_fonctionnement" class="form-label">Durée de fonctionnement</label>
            <input type="text" class="form-control" id="duree_fonctionnement" name="duree_fonctionnement" value="<?= isset($module["duree_fonctionnement"]) ? $module["duree_fonctionnement"] : '' ?>">
        </div>
        <div class="mb-3">
            <label for="nombre_donnees_envoyees" class="form-label">Nombre de données envoyées</label>
            <input type="number" class="form-control" id="nombre_donnees_envoyees" name="nombre_donnees_envoyees" value="<?= isset($module["nombre_donnees_envoyees"]) ? $module["nombre_donnees_envoyees"] : '' ?>">
        </div>
        <div class="mb-3">
            <label for="date_installation" class="form-label">Date d'installation</label>
            <input type="date" class="form-control" id="date_installation" name="date_installation" value="<?= isset($module["date_installation"]) ? $module["date_installation"] : '' ?>">
        </div>
        <div class="mb-3">
            <label for="date_derniere_mise_a_jour" class="form-label">Date dernière mise à jour</label>
            <input type="datetime-local" class="form-control" id="date_derniere_mise_a_jour" name="date_derniere_mise_a_jour" value="<?= isset($module["date_derniere_mise_a_jour"]) ? $module["date_derniere_mise_a_jour"] : '' ?>">
        </div>
        <button type="submit" class="btn btn-warning">Modifier</button>
    </form>
</div>
<br>

<?php
//on inclut le footer
include_once "footer.php";
?>
