<?php
require_once __DIR__ . '/../include/initialisation.php';
adminSecurity();//sécurité accès

$query = 'DELETE FROM categorie WHERE id=' . $_GET['id'];
$pdo->exec($query);

setFlashMessage('La catégorie est bien supprimée');

header('Location: categories.php');
die;
