<?php
require_once __DIR__ . '/../include/initialisation.php';
adminSecurity();//sécurité accès

//lister les catégories dans un tableau HTML

//le requêtage ici
$query = 'SELECT * FROM categorie';
$stmt = $pdo->query($query);
$categorie = $stmt->fetchAll();



include __DIR__ . '/../layout/top.php';
?>
<h1>Gestion catégories</h1>

<p><a class="btn btn-outline-primary" href="categorie-edit.php">Ajouter une catégorie</a></p>

<!--Le tableau HTML ici-->
<table class="table">
	<tr>
		<th>Id</th>
		<th>Nom</th>
		<th width="100px"></th>
		<th width ="50px"></th>
	</tr>
	<?php
	//une boucle pour avoir un tr avec 2 td pour chaque categorie
	/*foreach($categorie as $article){
		echo '<tr>' . '<td>' . $article['id'] . '</td>';
		echo '<td>' . $article['nom'] . '</td>' . '</tr>';
	}*/
	foreach ($categorie AS $article) :
	?>
		<tr>
			<td><?= $article['id']; ?></td>
			<td><?= $article['nom']; ?></td>
			<td>
				<a class="btn btn-outline-primary" href="categorie-edit.php?id=<?= $article['id']; ?>">Modifier</a>
			</td>
			<td>
				<a class="btn btn-outline-danger" href="categorie-delete.php?id=<?= $article['id']; ?>">X</a>
			</td>
		</tr>


	<?php
	endforeach;
	?>
</table>
<?php
include __DIR__ . '/../layout/bottom.php';
?>