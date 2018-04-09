<!--on regroupe ici toute la configuration du site-->
<?php
//initialise la session
session_start();

define('RACINE_WEB', '/php/site/');
define('PHOTO_WEB', RACINE_WEB. 'photo/');//chemin racine web pour le dossier photo avec localhost

define('PHOTO_DIR', $_SERVER['DOCUMENT_ROOT'] . '/php/site/photo/');//la racine sur le serveur web du dossier photo
//sous xampp, $_SERVER['DOCUMENT_ROOT'] vaut C:\xampp\htdocs
define('PHOTO_DEFAULT', 'https://dummyimage.com/600x400/000/fff&text=pas+d\'image');//fausse image via le site mais dans l'ideal on se créé une img par défaut via Photoshop
require_once __DIR__ . '/connexion.php';
require_once __DIR__ . '/fonctions.php';

//si un doc est totalement en PHP, on peut ne pas mettre la fin de balise php