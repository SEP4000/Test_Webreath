<?php
// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification si les données nécessaires sont présentes
    if (
        isset($_POST["module_id"], $_POST["valeur_mesuree"], $_POST["date_mesure"]) &&
        !empty($_POST["module_id"]) && !empty($_POST["valeur_mesuree"]) && !empty($_POST["date_mesure"])
    ) {
        //le formulaire est complet
        //on récupère les données en les protégeant
        $module_id = $_POST["module_id"];
        $valeur_mesuree = $_POST["valeur_mesuree"];
        $date_mesure = $_POST["date_mesure"];

        // Vérification si la valeur mesurée est un nombre
        if (!is_numeric($valeur_mesuree)) {
            // Affichage d'un message d'erreur si la valeur mesurée n'est pas un nombre
            $message = "La valeur mesurée doit être un nombre.";
        } else {
            //on enregistre les données en se connectant à la base
            require_once "connect.php";

            //on écrit la requête pour ajouter la donnée
            $sql = "INSERT INTO `donnees` (`module_id`, `valeur_mesuree`, `date_mesure`) VALUES (:module_id, :valeur_mesuree, :date_mesure)";
            //on prepare la requête
            $query = $db->prepare($sql);
            //on injecte les paramètres
            $query->bindValue(":module_id", $module_id, PDO::PARAM_INT);
            $query->bindValue(":valeur_mesuree", $valeur_mesuree, PDO::PARAM_STR);
            $query->bindValue(":date_mesure", $date_mesure, PDO::PARAM_STR);
            //on execute la requête
            if ($query->execute()) {
                // Redirection vers la page article.php après l'ajout réussi
                header("Location: article.php");
                exit;
            } else {
                //en cas d'erreur, on affiche un message d'erreur
                $message = "Une erreur est survenue lors de l'ajout de la donnée.";
            }
        }
    } else {
        //on affiche un message d'erreur si tous les champs ne sont pas remplis
        $message = "Veuillez remplir tous les champs.";
    }
}

//on inclut le header
include_once "header.php";
//on inclut le navbar
include_once "navbar.php";

$titre = "Ajouter une donnée";
?>

<div class="container">
    <br>
    <h1 class="text-center">Ajouter une donnée</h1>
    <br>
<!-- Affichage du message d'erreur si il y en a un -->
    <?php if (isset($message)) : ?>
        <div class="alert alert-danger" role="alert">
            <?= $message ?>
        </div>
    <?php endif; ?>

<!-- Formulaire pour ajouter une donnée -->
    <form method="post">
        <div class="mb-3">
            <label for="module_id" class="form-label">Module</label>
            <select class="form-select" name="module_id" id="module_id">
                <?php
                //on récupère la liste des modules depuis la base de données
                require_once "connect.php";
                $sql = "SELECT * FROM `modules`";
                $requete = $db->query($sql);
                $modules = $requete->fetchAll(PDO::FETCH_ASSOC);

                //on affiche les options du select avec les modules disponibles
                foreach ($modules as $module) {
                    echo "<option value='" . $module['module_id'] . "'>" . $module['nom_module'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="valeur_mesuree" class="form-label">Valeur mesurée</label>
            <input type="number" class="form-control" name="valeur_mesuree" id="valeur_mesuree">
        </div>
        <div class="mb-3">
            <label for="date_mesure" class="form-label">Date de mesure</label>
            <input type="datetime-local" class="form-control" name="date_mesure" id="date_mesure">
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>
<br><br><br><br><br>

<?php
//on inclut le footer
include_once "footer.php";
?>
