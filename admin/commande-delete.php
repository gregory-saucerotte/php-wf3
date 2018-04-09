<?php
require_once __DIR__ . '/../include/initialisation.php';
adminSecurity();

$query = 'DELETE FROM commandes WHERE id=' . $_GET['id'];
$pdo->exec($query);

setFlashMessage('La commande est bien supprimée');

header('Location: commandes.php');
die;