<?php
$pdo = new PDO(//chemin de connexion pr la bdd// dbname est le nom de la bdd a laquelle on veut se connecter
	'mysql:host=localhost;dbname=boutique',
	'root',//nom utilisateur de la bdd
	'root', //mot de passe de l'utilisateur
	[//tableau d'options
		PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, //gestion des erreurs
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', //gestion utf8 mysql
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC //résultat fetch() par défaut en tableau associatif
	]
);
?>