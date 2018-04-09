<?php
require_once __DIR__ . '/../include/initialisation.php';
adminSecurity();//sécurité accès

$errors = [];
$nom = '';


if(!empty($_POST)){//si on a des données venant du formulaire

	sanitizePost(); //on nettoie les données reçues

	//crée des variables à partir d'un tableau (les variables ont les noms des clés dans le tableau)
	extract($_POST);
	//on teste les erreurs de saisie
	if(empty($_POST['nom'])){
		$errors[] = 'Le nom est obligatoire';
	} elseif(strlen($_POST['nom']) > 50) {
		$errors[] = 'Le nom ne doit pas faire plus de 50 caractères';
	}
	//si aucune erreur détectée, on envoie le nom de catégorie dans la BDD
	if(empty($errors)) {//on prépare la requête pour insérer ensuite le nom de la catégorie dans la BDD
		if(isset($_GET['id'])){//modification de la catégorie
			$query = 'UPDATE categorie SET nom = :nom WHERE id = :id';
			$stmt = $pdo->prepare($query);
			$stmt->bindValue(':nom', $_POST['nom']);
			$stmt->bindValue(':id', $_GET['id']);
			$stmt->execute();
		} else { //création pour insertion en BDD
			$query = 'INSERT INTO categorie(nom) VALUES(:nom)';
			$stmt = $pdo->prepare($query);
			$stmt->bindValue(':nom', $_POST['nom']);
			$stmt->execute();
		}
		//on lance la fonction pour afficher le message de succès
		setFlashMessage('La catégorie est bien enregistrée !');
		//après validation, on fait la redirection vers la page categories.php grâce à la fonction header()
		header('Location: categories.php');
		die; //on arrête l'exécution du script
	}
} elseif(isset($_GET['id'])){
	//en modification, si on n'a pas de retour de formulaire
	//on va chercher la catégorie en bdd pour affichage
	$query = 'SELECT * FROM categorie WHERE id = ' . $_GET['id'];
	$stmt = $pdo->query($query);
	$categorie = $stmt->fetch();

	$nom = $categorie['nom'];
}
include __DIR__ . '/../layout/top.php';
?>


<h1>Edition de catégories</h1>

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
<form  method="post">
	<div clas="form-group">
		<label>Nom</label>
		<input type="text" name="nom" class="form-control" value="<?= $nom;?>"><!-- $nom aura le nom de la catégorie saisie par le user ex: $jeans-->
	</div>
	<br />
	<div class="form-btn-group text-right">
		<button type="submit" class="btn btn-primary">Enregistrer
		</button>
		<a class="btn btn-outline-primary" href="categories.php">Retour</a>

	</div>
</form>
<?php
include __DIR__ . '/../layout/bottom.php';
?>