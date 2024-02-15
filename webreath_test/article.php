<?php
require_once "connect.php";

// Requête pour récupérer les noms de modules uniques
$sql_distinct = "SELECT DISTINCT `nom_module` FROM `modules`";
$requete_distinct = $db->query($sql_distinct);
$modules_distinct = $requete_distinct->fetchAll(PDO::FETCH_COLUMN);

$titre = "Liste des modules";
include "header.php";
include "navbar.php";
?>

<br>
<h1 class="text-center">Liste des modules</h1>
<br>

<!-- Affichage de la liste des modules dans un tableau -->
<section class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col">Nom du module</th>
                <th scope="col">Date d'installation</th>
                <th scope="col">Description</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($modules_distinct as $nom_module) : ?>
                <?php
                // Requête pour récupérer toutes les informations associées à un nom de module donné
                $sql = "SELECT * FROM `modules` WHERE `nom_module` = :nom_module ORDER BY `date_installation` DESC";
                $requete = $db->prepare($sql);
                $requete->bindValue(":nom_module", $nom_module, PDO::PARAM_STR);
                $requete->execute();
                $modules = $requete->fetchAll();
                ?>
                <?php foreach ($modules as $module) : ?>
                    <!-- Aller vers la page d'information des données de chaque module en cliquant sur le nom du module -->
                    <tr>
                        <td><a type="button" class="btn btn-success" href="module.php?module_id=<?= $module["module_id"] ?>"><?php echo strip_tags($module["nom_module"]); ?></a></td>
                        <td><?php echo $module["date_installation"]; ?></td>
                        <td><?php echo strip_tags($module["description"]); ?></td>
                        <td>
                            <!-- Bouton pour modifier les informations du module -->
                            <a type="button" class="btn btn-warning" href="modifier_module.php?module_id=<?= $module["module_id"] ?>">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<br><br><br><br><br><br><br><br>

<?php include "footer.php"; ?>
