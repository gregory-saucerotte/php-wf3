<?php
require_once __DIR__ . '/../include/initialisation.php';
adminSecurity();//sécurité accès

$query = 'SELECT photo FROM produits WHERE id =' . $_GET['id'];
$stmt = $pdo->query($query);
$photo = $stmt->fetchColumn();

//on supprime l'image du répertoire photo s'il y en a 1
if(!empty($photo)){
	unlink(PHOTO_DIR . $photoActuelle);
}

$query = 'DELETE FROM produits WHERE id=' . $_GET['id'];
$pdo->exec($query);

setFlashMessage('Le produit est bien supprimé');

header('Location: produits.php');
die;


