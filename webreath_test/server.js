const server = require('http').createServer();
const io = require('socket.io')(server);
// Vérification d'avoir installer le module MySql de Node.js pour se connecter à PhpMyAdmin avec Node.js
const mysql = require('mysql'); 

// Configuration de la connexion à la base de données pour le serveur Node.js
const connection = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'webreath'
});

// Connexion à la base de données
connection.connect((err) => {
    if (err) {
        console.error('Erreur de connexion à la base de données :', err);
        return;
    }
    console.log('Connecté à la base de données');
});

io.on('connection', (socket) => {
    console.log('Connexion établie');

    // Fonction pour récupérer les données de la base de données
    const getDataFromDatabase = () => {
        // Requête SQL pour récupérer les 10 dernières entrées car je veux faire 
        //un historique pour les 10 dernierres données 
        const sql = "SELECT * FROM donnees ORDER BY date_mesure DESC LIMIT 10"; 
        connection.query(sql, (err, results) => {
            if (err) {
                console.error('Erreur lors de la récupération des données :', err);
                return;
            }
            // Émettre les données au client WebSocket
            socket.emit('update', results);
        });
    };

    // Appeler la fonction pour récupérer les données lors de la connexion initiale
    getDataFromDatabase();

    // Planifier l'appel de la fonction toutes les 5 minutes
    setInterval(getDataFromDatabase, 300000); // Mettre à jour toutes les 5 minutes
});


server.listen(3000, () => { // Lancer le serveur Node.js
    console.log('Serveur à l\'écoute sur le port 3000');
});
