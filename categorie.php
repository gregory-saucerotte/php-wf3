<?php
// afficher nom de la catégorie dont on a reçu l'id dans l'url en titre de la page 
// lister les produits appartenant à la catégorie avec leur photo s'ils en ont une 
require_once __DIR__ . '/include/initialisation.php';

include __DIR__ . '/layout/top.php';


$query = 'SELECT nom FROM categorie WHERE id = ' . $_GET['id'];
$stmt = $pdo->query($query);
$title = $stmt->fetch();



$query = 'SELECT * FROM produits WHERE categorie_id = ' . $_GET['id'];
$stmt = $pdo->query($query);
$produits = $stmt->fetchAll();//on récolte les résultats de la requête dans un tableau
?>

<h1><?= $title['nom']; ?></h1>



	<?php
	 	foreach ($produits as $produit) :
	 		//si j'ai 1 img pr le produit elle sera afficher
	 		$src = (!empty($produit['photo']))
	 		? PHOTO_WEB . $produit['photo']
	 		//sinon elle sera générée aléatoirement avec la constante PHOTO_DEFAULT
	 		: PHOTO_DEFAULT
	 	;
	?>
	
		<div class="card float-left col-md-3 offset-md-1" style="width: 18rem;">
		  <?php 
		  echo '<img class="card-img-top" src="'. PHOTO_WEB . $produit['photo'] . '" alt="Image du produit">';
		  ?>
		  <div class="card-body">
		    <h5 class="card-title text-center"><?= $produit['nom']; ?></h5>
		    <p class="card-text"><?= $produit['description']; ?></p>
		    <p class="card-text"><?= prixFr($produit['prix']); ?></p>
		    <p class="card-text text-center">
		    	<a class="btn btn-primary" href="produit.php?id=<?= $produit['id']; ?>">Voir</a>
			</p>
		  </div>
		</div>
	


<?php
endforeach;

include __DIR__ . '/layout/bottom.php';
?>