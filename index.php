<?php
//on appelle tous les traitements (connexion pdo, le haut du site...), on peut aussi créer des fonctions qui seront appelées dans le layout
require_once __DIR__ . '/include/initialisation.php';

include __DIR__ . '/layout/top.php';
?>
<h1>Page d'accueil</h1>
<?php
include __DIR__ . '/layout/bottom.php';
?>