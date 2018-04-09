<?php
/*lister les commandes dans un tableau HTML :
- id de la commande
- nom prénom de l'utilisateur (jointure à prévoir)
- montant formaté
- créer une fonction pour formater la date de la commande (function date() et strtotime() de PHP)
- statut de la commande
- reprendre la fonction dateFr pour formater la date du statut (function date() et strtotime() de PHP)

- Passer le statut en liste déroulante avec les 3 statuts disponibles, avec 1 bouton de modif
- Traiter le changement de statut en mettant à jour statut et date_statut dans la table commande
*/
require_once __DIR__ . '/../include/initialisation.php';
adminSecurity();//sécurité accès

//lister les catégories dans un tableau HTML

//le requêtage ici
/* version du prof pour la requete
$query = "SELECT c.*, concat_ws(' ', u.prenom, u.nom) AS utilisateur"
. ' FROM commande c'
. ' JOIN utilisateur u ON c.utilisateur_id = u.id'
;

*/
//modification du statut dans la base de bdd
if(isset($_POST['modifier-statut'])){
	$query = 'UPDATE commandes SET statut = :statut, date_statut = now() WHERE id = :id';
	$stmt = $pdo->prepare($query);
	$stmt->bindValue(':statut', $_POST['statut']);
	$stmt->bindValue(':id', $_POST['commande-id']);
	$stmt->execute();

	setFlashMessage('Le statut est modifié');

}

$query = <<<EOS
SELECT c.*, u.nom AS user_nom, u.prenom AS user_surname
FROM commandes c
JOIN utilisateur u ON c.utilisateur_id = u.id
EOS;
$stmt = $pdo->query($query);
$commandes = $stmt->fetchAll();


$statuts = ['en cours', 'envoyée', 'livrée'];


include __DIR__ . '/../layout/top.php';
?>
<h1>Gestion commandes</h1>

<table class="table">
	<tr>
		<th>ID commande</th>
		<th>Nom</th>
		<th>Prénom</th>
		<th>Montant total</th>
		<th>Date commande</th>
		<th>Statut</th>
		<th>Date MAJ statut</th>
		<th width="100px"></th>
		<th width ="50px"></th>
	</tr>
	<?php
	
	foreach ($commandes AS $commande) :
	?>
		<tr>
			<td><?= $commande['id']; ?></td>
			<td><?= $commande['user_nom']; ?></td>
			<td><?= $commande['user_surname']; ?></td>
			<td><?= prixFr($commande['montant_total']); ?></td>
			<td><?= dateFr($commande['date_commande']); ?></td>
			<td>
				<div class="row">
					<div class="col-md-2">
						<form method="post" class="form-inline">
							<select name="statut" class="form-control">
								<?php
								foreach ($statuts as $statut) :
									$selected = ($statut == $commande['statut'])
									? 'selected'
									: ''
									;
								?>
								<option value="<?= $statut; ?>"<?= $selected; ?>>
									<?= ucfirst($statut); ?>						
								</option>								
								<?php
								endforeach;
								?>					
							</select>
							<input type="hidden" name="commande-id" value="<?= $commande['id']; ?>">
							<button type="submit" name="modifier-statut" class="btn btn-primary">Modifier</button>
						</form>
					</div>
				</div>
			</td>
			<td><?= dateFr($commande['date_statut']); ?></td>

			<td>
				<a class="btn btn-outline-primary" href="commande-edit.php?id=<?= $commande['id']; ?>">Modifier</a>
			</td>
			
		</tr>


	<?php
	endforeach;
	?>
</table>
<?php
include __DIR__ . '/../layout/bottom.php';
?>