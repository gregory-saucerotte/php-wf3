<?php

require_once __DIR__ . '/include/initialisation.php';

$query = 'SELECT * FROM produits WHERE id = ' . $_GET['id'];
$stmt = $pdo->query($query);
$produit = $stmt->fetch();

$src = (!empty($produit['photo']))//on affiche la photo
	 		? PHOTO_WEB . $produit['photo']
	 		//sinon elle sera générée aléatoirement avec la constante PHOTO_DEFAULT
	 		: PHOTO_DEFAULT
	 	;
//fonction permettant de récupérer dans la session le produit choisi et sa quantité
if(!empty($_POST)){
	ajoutPanier($produit, $_POST['quantite']);
	setFlashMessage('Le produit a été ajouté au panier');
}

include __DIR__ . '/layout/top.php';
?>
<h1><?= $produit['nom']; ?></h1>

<div class='row'>
	<div class='col-md-3'>
		<img src="<?= $src ; ?>" height="200px">
		<p><?= prixFr($produit['prix']); ?></p>
		<form method="post" class='form-inline'>
			<label>Qté</label>
			<select name="quantite" class="form-control">
				<?php //on créé la variable $i pour afficher la quantité dans le sélecteur
					for($i = 1; $i <= 10; $i++) :
				?>
				<!-- on prend la valeur de la quantité en value, et ensuite on le met entre les balises option pour affichage -->
				<option value="<?= $i; ?>"><?= $i; ?></option>
				<?php 
			    endfor;
				?>
			</select>
			<button type="submit" class="btn btn-primary">
				Ajouter au panier
			</button>
		</form>
	</div>
	<div class='col-md-9'>
		<p><?= $produit['description']; ?></p>
	</div>
	
</div>


<?php
include __DIR__ . '/layout/bottom.php';
?>