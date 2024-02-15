<?php
// Vérification si on a un id de donnée
if (!isset($_GET["donnee_id"]) || empty($_GET["donnee_id"])) {
    // Redirection vers une autre page si l'id de la donnée est manquant
    header("Location: article.php");
    exit;
}

// Récupération de l'id de la donnée depuis l'URL
$donnee_id = $_GET["donnee_id"];

// Connexion à la base de données
require_once "connect.php";

// Récupération des informations de la donnée à modifier
$sql = "SELECT * FROM `donnees` WHERE `donnee_id` = :donnee_id";
$requete = $db->prepare($sql);
$requete->bindValue(":donnee_id", $donnee_id, PDO::PARAM_INT);
$requete->execute();
$donnee = $requete->fetch();

// Vérification si la donnée existe
if (!$donnee) {
    // Redirection si la donnée n'existe pas
    http_response_code(404);
    echo "Cette donnée n'existe pas";
    exit;
}

// Traitement du formulaire de modification de la donnée
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification des données envoyées
    if (isset($_POST["valeur_mesuree"], $_POST["date_mesure"]) && !empty($_POST["valeur_mesuree"]) && !empty($_POST["date_mesure"])) {
        // Vérification si la valeur mesurée est un nombre
        if (!is_numeric($_POST["valeur_mesuree"])) {
            // Affichage d'un message d'erreur si la valeur mesurée n'est pas un nombre
            $message = "La valeur mesurée doit être un nombre.";
        } else {
            // Mise à jour des valeurs
            $valeur_mesuree = $_POST["valeur_mesuree"];
            $date_mesure = $_POST["date_mesure"];

            // Requête de mise à jour
            $sql_update = "UPDATE `donnees` SET `valeur_mesuree` = :valeur_mesuree, `date_mesure` = :date_mesure WHERE `donnee_id` = :donnee_id";
            $requete_update = $db->prepare($sql_update);
            $requete_update->bindValue(":valeur_mesuree", $valeur_mesuree, PDO::PARAM_STR);
            $requete_update->bindValue(":date_mesure", $date_mesure, PDO::PARAM_STR);
            $requete_update->bindValue(":donnee_id", $donnee_id, PDO::PARAM_INT);
            $requete_update->execute();

            // Redirection vers la page du module après la modification
            header("Location: module.php?module_id=" . $donnee["module_id"]);
            exit;
        }
    } else {
        // Affichage d'un message d'erreur si des champs sont manquants
        $message = "Veuillez remplir tous les champs.";
    }
}

// Inclusion du fichier d'en-tête et du menu de navigation
include "header.php";
include "navbar.php";
?>

<div class="container">
    <br>
    <h1 class="text-center">Modifier la donnée</h1>
    <br>
    <!-- Affichage du message d'erreur si il y en a un -->
    <?php if (isset($message)) : ?>
        <div class="alert alert-danger" role="alert">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire de modification de la donnée -->
    <form method="post">
        <div class="mb-3">
            <label for="valeur_mesuree" class="form-label">Valeur mesurée</label>
            <input type="number" class="form-control" name="valeur_mesuree" id="valeur_mesuree" value="<?= $donnee["valeur_mesuree"] ?>">
        </div>
        <div class="mb-3">
            <label for="date_mesure" class="form-label">Date de mesure</label>
            <input type="datetime-local" class="form-control" name="date_mesure" id="date_mesure" value="<?= $donnee["date_mesure"] ?>">
        </div>
        <button type="submit" class="btn btn-warning">Modifier</button>
    </form>
</div>
<br><br><br><br><br><br><br><br><br>

<?php include "footer.php"; ?>
