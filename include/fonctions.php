<?php
//message succes ou erreur
function setFlashMessage($message, $type = 'success'){
	$_SESSION['flashMessage'] = [
		'message' => $message,
		'type' => $type
	];
}

function displayFlashMessage() {
	if(isset($_SESSION['flashMessage'])){//isset = s'il existe un flashMessage dans la session
		$message = $_SESSION['flashMessage']['message'];
		$type = ($_SESSION['flashMessage']['message'] == 'error')
		? 'danger'//pour la class alert-danger du bootstrap
		: $_SESSION['flashMessage']['type']
	;

	echo '<div class="alert alert-' . $type . '">' . '<h5 class="alert-heading">' . $message . '</h5>' . '</div>'
	;
	unset($_SESSION['flashMessage']);//suppression du msg de la session pour affichage "one shot"
	} 
}
//formulaire
function sanitizeValue(&$value){// & fait référence à la valeur saisie et passera le $value dans la fonction pour nettoyer la saisie
	//trim() supprime les espaces en début et fin de chaine
	//strip_tags() supprime les balises HTML
	$value = trim(strip_tags($value));
}

function sanitizeArray(array &$array){//obligation que ce soit un tableau et rien d'autre avec array// applique la fonction sanitizeValue() sur tous les éléments du tableau
	array_walk($array, 'sanitizeValue');
}

function sanitizePost(){///nettoie les valeurs du tableau $_POST
	sanitizeArray($_POST);
}
//fonction pr savoir si on a utilisateur connecté
function isUserConnected(){
	return isset($_SESSION['utilisateur']);
}

function getUserFullName(){
	if(isUserConnected()){
		return $_SESSION['utilisateur']['prenom'] .' '. $_SESSION['utilisateur']['nom'];
	}
}
//permet d'accéder à la partie admin du site
function isUserAdmin(){
	return isUserConnected() && $_SESSION['utilisateur']['role'] == 'admin';
}

//on sécurise l'accès à la partie admin contre les users
function adminSecurity(){
	if(!isUserAdmin()){
		if(!isUserConnected()){
			header('Location: ' . RACINE_WEB . 'connexion.php');
		}else {
			header('HTTP/1.1 403 Forbidden');
			echo "Vous n'avez pas le droit d'accéder à cette page";
		}
		die;
	}
}
//redéfinition du format du prix
function prixFr($prix){
	return number_format($prix, 2, ',', ' ') . ' €';
}

//fonction pour mettre les dates au bon format
function dateFr($dateSql){
	return date('d/m/Y H:i:s', strtotime($dateSql));
}

//fonction permettant de récupérer le panier de l'user
function ajoutPanier(array $produit, $quantite){
	if(!isset($_SESSION['panier'])){
		$_SESSION['panier'] = [];//par défaut à l'ouverture de la session utilisateur, le panier n'existe pas donc on le créé
	} //si le produit n'est pas encore dans le panier, on l'ajoute
	if(!isset($_SESSION['panier'][$produit['id']])){
		$_SESSION['panier'][$produit['id']] = [
			'nom' =>$produit['nom'],
			'prix' =>$produit['prix'],
			'quantite' => $quantite
		];
		//sinon si le produit est déjà dans le panier, on met à jour la quantité
	} else {
		$_SESSION['panier'][$produit['id']]['quantite'] += $quantite;
	}
}

function getTotalPanier(){//fonction affichant le total du panier

	$total = 0;

	if(isset($_SESSION['panier'])){
		foreach ($_SESSION['panier'] as $produit) {
			$total += $produit['prix'] * $produit['quantite'];
		}
	}

	return $total;
}


function modifierQuantitePanier ($produitId, $quantite){
	if(isset($_SESSION['panier'][$produitId])){
		if($quantite != 0){
			$_SESSION['panier'][$produitId]['quantite'] = $quantite;
		} else {
			unset($_SESSION['panier'][$produitId]);
		}
	}
}














