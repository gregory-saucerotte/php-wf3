<?php
//faire la page qui liste les produits dans un tableau HTML
require_once __DIR__ . '/../include/initialisation.php';
adminSecurity();
//on récupère tous les produits de la table produits, et les noms de catégorie dans la table catégorie - p et c sont les alias de produits et catégorie
//on joint les tables avec un alias categorie_nom pour faire apparaitre le nom de la catéorie des produits et non plus le n° de la catégorie
$query = <<<EOS
SELECT p.*, c.nom AS categorie_nom
FROM produits p
JOIN categorie c ON p.categorie_id = c.id
EOS;
$stmt = $pdo->query($query);
$produits = $stmt->fetchAll();



include __DIR__ . '/../layout/top.php';
?>
<h1>Gestion des produits</h1>

<p><a class="btn btn-outline-primary" href="produit-edit.php">Ajouter un produit</a></p>

<!--Le tableau HTML ici-->
<table class="table">
	<tr>
		<th>ID</th>
		<th>Nom</th>
		<th>Référence</th>
		<th>Catégorie</th>
		<th>Prix</th>
		<th width="100px"></th>
		<th width ="50px"></th>
	</tr>
	<?php
	
	foreach ($produits AS $produit) :
	?>
		<tr>
			<td><?= $produit['id']; ?></td>
			<td><?= $produit['nom']; ?></td>
			<td><?= $produit['reference']; ?></td>
			<td><?= $produit['categorie_nom']; ?></td>
			<td><?= prixFR($produit['prix']); ?></td>
			<td>
				<a class="btn btn-outline-primary" href="produit-edit.php?id=<?= $produit['id']; ?>">Modifier</a>
			</td>
			<td>
				<a class="btn btn-outline-danger" href="produit-delete.php?id=<?= $produit['id']; ?>">X</a>
			</td>
		</tr>


	<?php
	endforeach;
	?>
</table>
<?php
include __DIR__ . '/../layout/bottom.php';
?>

