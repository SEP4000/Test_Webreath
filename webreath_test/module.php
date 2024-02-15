<?php
// Vérification si on a un id pour le module qu'on souhaite afficher
if (!isset($_GET["module_id"]) || empty($_GET["module_id"])) {
    // Je n'ai pas d'id
    header("Location: article.php");
    exit;
}

// Je récupère l'id
$id = $_GET["module_id"];

// Connexion à la base de données
require_once "connect.php";

// On va chercher le module dans la base de données
$sql_module = "SELECT * FROM `modules` WHERE `module_id` = :id";
$sql_donnees = "SELECT * FROM `donnees` WHERE `module_id` = :id ORDER BY `date_mesure` DESC"; // Modification ici pour trier par date_mesure DESC
$sql_etat = "SELECT * FROM `etat_module` WHERE `module_id` = :id";

// On prépare la requête pour le module
$requete_module = $db->prepare($sql_module);
$requete_module->bindValue(":id", $id, PDO::PARAM_INT);
$requete_module->execute();
$module = $requete_module->fetch();

// Vérification si le module existe
if (!$module) {
    http_response_code(404);
    echo "Ce module n'existe pas";
    exit;
}

// On a un module
// On définit le titre
$titre = strip_tags($module["nom_module"]);

// On prépare la requête pour les données
$requete_donnees = $db->prepare($sql_donnees);
$requete_donnees->bindValue(":id", $id, PDO::PARAM_INT);
$requete_donnees->execute();
$donnees = $requete_donnees->fetchAll();

// On prépare la requête pour l'état du module
$requete_etat = $db->prepare($sql_etat);
$requete_etat->bindValue(":id", $id, PDO::PARAM_INT);
$requete_etat->execute();
$etat_module = $requete_etat->fetch();

// Générer aléatoirement l'état de fonctionnement
$etat_fonctionnement_aleatoire = rand(0, 1) ? 'En marche' : 'En arrêt';

// Utiliser l'état aléatoire ou récupéré de la base de données si disponible
$etat_fonctionnement_affichage = isset($etat_module["etat_fonctionnement"]) ? $etat_module["etat_fonctionnement"] : $etat_fonctionnement_aleatoire;

// Définir l'unité en fonction du type de module
$unite_mesuree = '';
// Pour s'assurer que les espaces inutiles autour du nom du module sont supprimés
$nom_module_trimmed = trim($module["nom_module"]);
if ($nom_module_trimmed === "Module de la température") {
    $unite_mesuree = "(°C)";
} elseif ($nom_module_trimmed === "Module de la vitesse de voiture") {
    $unite_mesuree = "(km/h)";
} elseif ($nom_module_trimmed === "Module des calories") {
    $unite_mesuree = "(cal)";
}

include "header.php";
include "navbar.php";
?>

<!-- Afficher une notification si le module est en dysfonctionnement -->
<?php if ($etat_fonctionnement_affichage === "En arrêt"): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        Attention : Le module est en dysfonctionnement (En arrêt) !
    </div>
<?php endif; ?>
<br>
<!-- Bouton pour déclencher la création de données -->
<form class="text-center" action="trigger_generate.php" method="post">
    <button type="submit" class="btn btn-info">Déclencher la génération de données</button>
</form>
<br>
<h1 class="text-center"><?= strip_tags($module["nom_module"]) ?></h1>
<br>

<!-- Affichage des informations du module -->
<section class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date d'installation</th>
                <th>Description</th>
                <th>État de fonctionnement</th>
                <th>Durée de fonctionnement</th>
                <th>Nombre de données envoyées</th>
                <th>Date dernière mise à jour</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $module["date_installation"] ?></td>
                <td><?= strip_tags($module["description"]) ?></td>
                <td><?= $etat_fonctionnement_affichage ?></td>
                <td><?= $etat_module["duree_fonctionnement"] ?></td>
                <td><?= $etat_module["nombre_donnees_envoyees"] ?></td>
                <td><?= $etat_module["date_derniere_mise_a_jour"] ?></td>
            </tr>
        </tbody>
    </table>
</section>

<br>
<h2 class="text-center">Données associées à <?= strip_tags($module["nom_module"]) ?></h2>
<br>
<!-- Affichage des informations des données associées au module -->
<section class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <!-- Incorporation de l'unité de mesure ici -->
                <th>Valeur mesurée <?= $unite_mesuree ?></th> 
                <th>Date de mesure</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($donnees as $donnee) : ?>
                <tr>
                    <td><?= $donnee["valeur_mesuree"] ?></td>
                    <td><?= $donnee["date_mesure"] ?></td>
                    <!-- Bouton pour modifier les informations de la donnée -->
                    <td>
                        <a type="button" class="btn btn-warning" href="modifier_donnee.php?donnee_id=<?= $donnee["donnee_id"] ?>">Modifier</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<br>
<h2 class="text-center">Graphique des données en fonction de la date de mesure et de la valeur msurée</h2>
<br>

<?php
// Récupération des données pour le graphique
$labels = array();
$values = array();

foreach ($donnees as $donnee) {
    $labels[] = $donnee["date_mesure"];
    $values[] = $donnee["valeur_mesuree"];
}

// Conversion en format JSON pour une utilisation en JavaScript
$labels_json = json_encode($labels);
$values_json = json_encode($values);
?>

<!-- Ajout du conteneur pour le graphique -->
<div>
    <canvas id="myChart"></canvas>
</div>
<br>

<!-- Script JavaScript pour créer le graphique -->

<script>
    var labels = <?php echo $labels_json; ?>;
    var values = <?php echo $values_json; ?>;
    
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Valeurs mesurées',
                data: values,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        
    });
</script>

<!-- Affichage des données reçues du serveur WebSocket -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.3.1/socket.io.js"></script>
<script>
    const socket = io();
    socket.on('update', (data) => {
        // Mettre à jour les données affichées sur la page en fonction des données reçues du serveur WebSocket
        console.log('Nouvelles données reçues :', data);
        // Mettre à jour les éléments HTML avec les nouvelles données
        // Par exemple, si vous avez un tbody avec l'ID "realtime-data-table-body" :
        const tbody = document.getElementById("realtime-data-table-body");
        tbody.innerHTML = ''; // Effacer le contenu existant du tbody
        // Ajouter les nouvelles lignes de données au tbody
        data.forEach((item) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.valeur_mesuree}</td>
                <td>${item.date_mesure}</td>
            `;
            tbody.appendChild(row);
        });
    });
</script>

<!-- Affichage des données de l'historique des données du module -->
<br>
<h2 class="text-center">Historique des données</h2>
<br>
<section class="container">
    <div  id="realtime-data-container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <!-- Incorporation de l'unité de mesure ici -->
                    <th scope="col">Valeur mesurée <?= $unite_mesuree ?></th> 
                    <th scope="col">Date de mesure</th>
                </tr>
            </thead>
            <tbody id="realtime-data-table-body">
                <!-- Les données en temps réel seront affichées ici -->
                <?php foreach ($donnees as $donnee) : ?>
                    <tr>
                     <td><?= $donnee["valeur_mesuree"] ?></td>   
                     <td><?= $donnee["date_mesure"] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<br>


<?php include "footer.php"; ?>