<?php
// Exécutez le script generate_data.php
exec("php generate_data.php");

// Redirigez l'utilisateur vers la page article.php
header("Location: article.php"); 
exit;
?>
