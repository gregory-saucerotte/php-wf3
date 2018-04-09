<?php
/*
si le panier est vide : afficher 1 message

sinon afficher un tableau HTML avec pour chaque produit du panier :
- son nom
- prix unitaire
- quantité
- prix total pour le produit

faire une fonction getTotalPanier() qui calcule le montant total du panier et l'utiliser sous le tableau des articles pour afficher le prix total

Remplacer l'affichage de la quantité par un formulaire avec :
- un <input type="number"> pour la quantité, 
- un <input type="hidden"> pour voir l'id du produit dont on modifie la quantité 
- un bouton submit pour modifier la quantité de chaque produit
Faire une fonction modifierQuantitePanier($produitId, $quantite) qui met à jour la quantité du produit si la quantité n'est pas à zéro, ou sinon qui supprime le produit du panier.
Appeler cette fonction quand un des formulaires est envoyé
*/
require_once __DIR__ . '/include/initialisation.php';

//enregistrement de la commande en BDD
/*
- Enregistrer la commande et son detail en bdd
- Afficher un message de confirmation
- vider le panier
*/
//on insère la commande de l'utilisateur dans la bdd
if(isset($_POST['commander'])){
	$query = <<<EOS
INSERT INTO commandes (
	utilisateur_id,
	montant_total
) VALUES (
	:utilisateur_id,
	:montant_total
)
EOS;

	$stmt = $pdo->prepare($query);
	$stmt->bindValue(':utilisateur_id', $_SESSION['utilisateur']['id']);
	$stmt->bindValue(':montant_total', getTotalPanier());
	$stmt->execute();
	$commandeId = $pdo->lastInsertId();//on retrouvera le numero de la commande de l'utilisateur

//on insère les infos de la commande dans la bdd
	$query = <<<EOS
INSERT INTO detail_commande (
	commande_id,
	produit_id,
	prix,
	quantite
) VALUES (
	:commande_id,
	:produit_id,
	:prix,
	:quantite
)
EOS;
	$stmt = $pdo->prepare($query);
	$stmt->bindValue(':commande_id', $commandeId);
	//on boucle sur le panier pour recevoir les commandes de l'utilisateur
	
	foreach ($_SESSION['panier'] as $produitId => $produit) {
		$stmt->bindValue(':produit_id', $produitId);
		$stmt->bindValue(':prix', $produit['prix']);
		$stmt->bindValue(':quantite', $produit['quantite']);
		$stmt->execute();
	}
	setFlashMessage('La commande est enregistrée');
	$_SESSION['panier'] = []; //on vide le panier une fois la commande finie
}


//lorsque le formulaire reçoit des modifs, la fonction met à jour la quantité du produit
if(isset($_POST['modifier-quantite'])){
	modifierQuantitePanier($_POST['produit-id'], $_POST['quantite']);
	setFlashMessage('La quantité a été modifiée');
}

include __DIR__ . '/layout/top.php';
?>
<h1>Panier</h1>
<?php
if(empty($_SESSION['panier'])) : 
?>
<div class="alert alert-info">
	Le panier est vide
</div>

<?php
else : 
?>
	<table class="table">
		<tr>
			<th>Nom produit</th>
			<th>Prix unitaire</th>
			<th>Quantité</th>
			<th>Total</th>
		</tr>
<?php
	foreach ($_SESSION['panier'] as $produitId => $produit ) :
?>
	<tr>
		<td><?= $produit['nom']; ?></td>
		<td><?= prixFr($produit['prix']); ?></td>
		<td>
			<form method="post" class="form-inline">
				<input type="number" name="quantite" value="<?= $produit['quantite']; ?>" class="form-control col-sm-2" min="0">
				<input type="hidden" name="produit-id" value="<?= $produitId; ?>">
				<button type="submit" class="btn btn-primary" name="modifier-quantite">Modifier</button>
			</form>
		</td>
		<td><?= prixFr($produit['prix'] * $produit['quantite']); ?></td>

	</tr>
<?php
	endforeach;
?>
	<tr>
		<th colspan="3">Total commande</th>
		<td><?= prixFr(getTotalPanier()); ?></td>
	</tr>
</table>
	<?php
	if(isUserConnected()) :
	?>
		<form method="post">
			<p class="text-right">
				<button type="submit" name="commander" class="btn btn-primary">
					Valider la commande
				</button>
			</p>
		</form>
	<?php
	else :
	?>
		<div class="alert alert-info">
			Vous devez vous connecter ou vous inscrire pour valider la commande
		</div>

	<?php
	endif;
	?>	
<?php
endif; 
?>
	

<?php

include __DIR__ . '/layout/bottom.php';
?>