# Test_Webreath

Réalisation du test pour Webreathe

Après avoir nommé une base de donné : webreath    sur le port 3306 sur PhpMyAdmin et avoir importer le fichier sql fournis dedans, on pourra commencer le test.

Afin de pouvoir réaliser ce test, on doit commencer sur la page Accueil.php pour ensuite naviguer sur le site.

Pour pouvoir déclencher la génération de données de manière aléatoire et qu'ils se mettent dans la base de donnée, il faut faire quelques manipulations :

**Aller dans le planificateur de tâches :** **image**

- Cliquer sur **Créer une tâche...**
- Lui donner nom dans **Général**
- Dand **Déclencheurs** lui définir un temps , j'ai décidé qu'il fallait le faire qu'une fois, seulement quand on  clique sur le bouton 'Déclencher la génération de données' dans Module.php. Et je veux que cela se fasse toutes les 5 minutes durant 15 minutes (vous pouvez changer cela si vous voulez)
- Dans **Actions**, il faudra définir qu'elle programme il faudra enclencher, d'abord avec quels applications il devra lui faire, puis quels script il devra enclencher. Comme c'est un fichier Php, il faudra d'abord donner le chemin vers l'application Php et ensuite le lien vers le fichier generate_data.php (attention il faudra laisser un espace entre les deux), par exemple pour moi, dans **Programme/Script**, j'ai mis ça : C:\laragon\bin\php\php-8.2.1-Win32-vs16-x64\php.exe C:\laragon\www\php\webreath_test\generate_data.php
- Après avoir fait tout ça on pourra finir la création de la tâche.

  Ainsi, on vient de définir la tâche, on peut exécuter la tâche pour créer des données aléatoire qui se mettent dans la table donnée pour les modules déjà crées, on peut aussi le faire en cliquant sur le bouton **Déclencher la génération de données**
