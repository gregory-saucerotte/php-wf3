<?php

require_once __DIR__ . '/include/initialisation.php';

$email = '';

if(!empty($_POST)){
	sanitizePost();
	extract($_POST);

	if(empty($_POST['email'])){
		$errors[] = "L'email est obligatoire";

	}
	if(empty($_POST['mdp'])){
		$errors[] = 'Le mot de passe est obligatoire';
	}
//on va chercher l'email de l'utilisateur dans la BDD
	if(empty($errors)){
		$query = 'SELECT * FROM utilisateur WHERE email = :email ';
		$stmt = $pdo->prepare($query);
		$stmt->bindValue(':email', $_POST['email']);
		$stmt->execute();
		//on vérifie la présence du mail de l'utilisateur avec fetch()
		$utilisateur = $stmt->fetch();
		//on vérifie le mdp associé au mail présent dans la bdd
		if(!empty($utilisateur)){//si le mdp match avec l'email
			if(password_verify($_POST['mdp'], $utilisateur['mdp'])){
				//connecter 1 user, c'est l'enregistrer en session
				$_SESSION['utilisateur'] = $utilisateur;
				//on redirige vers l'accueil quand connecté
				header('Location: index.php');
				die;
			}
		}

		$errors[] = 'Identifiant ou mot de passe incorrect';
	}
}
 
if (!empty($errors)) :
?>
<div class="alert alert-danger"><!-- implode transforme un tableau en chaine de caractères-->
	<h4 class="alert-heading">Le formulaire contient des erreurs</h4>
	<?= implode('<br>', $errors); ?> 
</div>
<?php
endif;

include __DIR__ . '/layout/top.php';
?>
<h1>Connexion</h1>
<form  method="post">
	<div class="form-group">
		<label>Email</label>
		<input type="text" name="email" value="<?= $email; ?>" class="form-control">
	</div>
	<div class="form-group">
		<label>Mot de passe</label>
		<input type="password" name="mdp" class="form-control">
	</div>
	<div class="form-btn-group text-right">
		<button type="submit" class="btn btn-primary">Se connecter</button>
	</div>
</form>


<?php
include __DIR__ . '/layout/bottom.php';
?>