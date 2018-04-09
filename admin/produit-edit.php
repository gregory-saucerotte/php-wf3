<?php
require_once __DIR__ . '/../include/initialisation.php';
adminSecurity();//sécurité accès



$errors = [];
$nom = $description = $reference = $categorieId = $prix = $photoActuelle = '';
$query = 'SELECT * FROM categorie';
$stmt = $pdo->query($query);
$categories = $stmt->fetchAll();


if(!empty($_POST)){//si on a des données venant du formulaire

	sanitizePost(); //on nettoie les données reçues

	//crée des variables à partir d'un tableau (les variables ont les noms des clés dans le tableau)
	extract($_POST);

	$categorieId = $_POST['categorie']; //on définit la catégorie du vetement en variable pour ensuite la retenir dans le form et la poster dans la bdd

	//on teste les erreurs de saisie
	if(empty($_POST['nom'])){
		$errors[] = 'Le nom est obligatoire';
	}
	if(empty($_POST['description'])){
		$errors[] = 'Une description est obligatoire';		
	} 	
	if(empty($_POST['reference'])){
		$errors[] = 'La référence est obligatoire';
	} elseif(strlen($_POST['reference']) > 50) {
		$errors[] = 'La référence ne doit pas faire plus de 50 caractères';
	} else {
		$query = 'SELECT count(*) FROM produits WHERE reference = :reference';
		//en modification on exclut de la vérification le produit que l'on est en train de modifier pour éviter un doublon
		if (isset($_GET['id'])){
			$query .=' AND id != ' . $_GET['id'];
		}


		$stmt = $pdo->prepare($query);
		$stmt->bindValue(':reference', $_POST['reference']);
		$stmt->execute();
		$number = $stmt->fetchColumn();

		if($number !=0){
			$errors[] = 'Il existe déjà un article avec cette référence';
		}
	}
	if(empty($_POST['categorie'])){
		$errors[] = 'La catégorie est obligatoire';
	}
	if(empty($_POST['prix'])){
		$errors[] = 'Le prix est obligatoire';
	}
	//si 1 img est téléchargée
	if (!empty($_FILES['photo']['tmp_name'])){
		if($_FILES['photo']['size'] > 1000000){//limite poids photo
			$errors[] = 'La photo ne doit pas dépasser les 1Mo';
		} 

		$allowedMimeTypes = [
			'image/png',
			'image/jpeg',
			'image/gif'
		];
		//si le format de la photo ne fait pas partie des 3 types proposés, alors on affiche un message d'erreur
		if(!in_array($_FILES['photo']['type'], $allowedMimeTypes)){
			$errors[] = 'La photo doit être une image GIF, PNG, ou JPG';
		}
	}

	if(empty($errors)) {//on prépare la requête pour insérer ensuite le produit dans la BDD avec sa photo
		if(!empty($_FILES['photo']['tmp_name'])){
			$originalName = $_FILES['photo']['name'];
			$extension = substr($originalName, strrpos($originalName, '.'));//s'il existe 1 extension photo indiquée en début (substr découpe la chaine de caracteres) ou fin de chaine de caractères (strpos), on la place en fin de nom de la photo

			//le nom que va avoir le fichier dans le répertoire photo avec le nom de reference du produit
			$nomPhoto = $_POST['reference'] . $extension;
			//si le produit a déjà une photo, alors on supprime l'ancienne du fichier pour y ajouter la nouvelle
			if(!empty($photoActuelle)){
				unlink(PHOTO_DIR . $photoActuelle);
			}

			//on déplace la photo dans le fichier photo et on l'enregistre avec son nom
			move_uploaded_file($_FILES['photo']['tmp_name'], PHOTO_DIR . $nomPhoto);
		} else {
			$nomPhoto = $photoActuelle;

		}

		if (isset($_GET['id'])){//on récupère les infos du produit puis on met à jour dans la bdd
			$query = <<<EOS
				UPDATE produits SET
					nom = :nom,
					reference = :reference,
					description = :description,
					categorie_id = :categorie_id,
					prix = :prix,
					photo = :photo
				WHERE id = :id
EOS;
			$stmt = $pdo->prepare($query);
			$stmt->bindValue(':nom', $_POST['nom']);
			$stmt->bindValue(':reference', $_POST['reference']);
			$stmt->bindValue(':description', $_POST['description']);
			$stmt->bindValue(':categorie_id', $_POST['categorie']);
			$stmt->bindValue(':prix', $_POST['prix']);
			$stmt->bindValue('id', $_GET['id']);
			$stmt->bindValue('photo', $nomPhoto);
			$stmt->execute();
		} else{
		//et si le produit n'existe pas donc on le créé et on l'enregistre dans la bdd
			$query = <<<EOS
			INSERT INTO produits (
				nom,
				reference,
				description,
				categorie_id,
				prix,
				photo
			) VALUES (
				:nom,
				:reference,
				:description,
				:categorie_id,
				:prix,
				:photo
			)

EOS;
			$stmt = $pdo->prepare($query);
			$stmt->bindValue(':nom', $_POST['nom']);
			$stmt->bindValue(':reference', $_POST['reference']);
			$stmt->bindValue(':description', $_POST['description']);
			$stmt->bindValue(':categorie_id', $_POST['categorie']);
			$stmt->bindValue(':prix', $_POST['prix']);
			$stmt->bindValue(':photo', $nomPhoto);
			$stmt->execute();
		}
	
		//on lance la fonction pour afficher le message de succès
		setFlashMessage('Le produit est bien enregistré !');
		//après validation, on fait la redirection vers la page categories.php grâce à la fonction header()
		header('Location: produits.php');
		die; //on arrête l'exécution du script
	}
	/*Adapter la page pr la modif :
		avoir 1 bouton ds la page de liste qui pointe vers cette page en passant l'id du produit dans l'url = bouton modifier
		- si on a un produit dans l'url sans retour de post, faire une requête select pour pré-remplir le formulaire
		- adapter le traitement pour faire un update au lieu d'un insert si on a un id dans l'url
		- adapter la vérification de l'unicité de la référence pour exclure la référence du produit que l'on modifie de la requête */		
		
} elseif(isset($_GET['id'])){
	//en modification, si on n'a pas de retour de formulaire
	//on va chercher les infos du produit en bdd pour pré-remplissage des champs
	$query = 'SELECT * FROM produits WHERE id = ' . $_GET['id'];
	$stmt = $pdo->query($query);
	$produit = $stmt->fetch();
	//1ere methode
	//extract($produit);
	//$categorieId = $produit['categorie_id'];
	//2eme methode
	$nom = $produit['nom'];
	$reference = $produit['reference'];
	$description = $produit['description'];
	$categorieId = $produit['categorie_id'];
	$prix = $produit['prix'];
	$photoActuelle = $produit['photo'];
}

include __DIR__ . '/../layout/top.php';
?>


<h1>Edition de produits</h1>

<?php 
if (!empty($errors)) :
?>
<div class="alert alert-danger"><!-- implode transforme un tableau en chaine de caractères-->
	<h4 class="alert-heading">Le formulaire contient des erreurs</h4>
	<?= implode('<br>', $errors); ?> 
</div>
<?php
endif;
?>

<br />
<!-- l'aattribut enctype est obligatoire pour un formulaire qui contient un téléchargement de fichier -->
<form  method="post" enctype="multipart/form-data">
	<div class="form-group">
		<label>Nom</label>
		<input type="text" name="nom" class="form-control" value="<?= $nom;?>">
	</div>
	<div class="form-group">
		<label>Référence</label>
		<input type="text" name="reference" class="form-control" value="<?= $reference;?>">
	</div>
	<div class="form-group">
		<label>Description</label>
		<textarea name="description" class="form-control"><?= $description;?></textarea>
	</div>
	<div class="form-group">		
		<label>Categorie</label>
		<select name="categorie" class="form-control">
			<option value=""></option>			
			<?php
			foreach ($categories AS $categorie) :
				$selected =($categorie['id'] == $categorieId)
				? 'selected'
				: ''
			;
			?><!-- la catégorie est retenue dans le champ de selection-->
			<option value="<?= $categorie['id']; ?>" <?= $selected; ?>><?= $categorie['nom']; ?></option>
			<?php
			endforeach;
			?>
		</select>
	</div>
	<div class="form-group">
		<label>Prix</label>
		<input type="text" name="prix" class="form-control" value="<?= $prix;?>">
	</div>
	<div class="form-group">
		<label>Photo</label>
		<input type="file" name="photo">
	</div>
	<?php
		if(!empty($photoActuelle)) : 
			echo '<p>Actuellement :<br><img src="'. PHOTO_WEB . $photoActuelle . '" height="150px"></p>';
		endif;
	?>
	<input type="hidden" name="photoActuelle" value="<?= $photoActuelle; ?>"><!-- on garde la valeur (le nom) de la photo actuelle lors de l'update si elle n'est pas modifiée -->
	<br />
	<div class="form-btn-group text-right">
		<button type="submit" class="btn btn-primary">Enregistrer
		</button>
		<a class="btn btn-outline-primary" href="produits.php">Retour</a>

	</div>
</form>
<?php
include __DIR__ . '/../layout/bottom.php';
?>