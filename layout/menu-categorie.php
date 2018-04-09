<?php
$query = 'SELECT * FROM categorie';
$stmt = $pdo->query($query);
$categoriesMenu = $stmt->fetchAll();
?>
<!-- pour créer la barre de la nav on va chercher les catégories de produits dans la BDD, ce qui permet une màj auto lors de modif/cxl/new catégorie -->
<div class="navbar-collapse">	
    <ul class="navbar-nav">
	    <?php
	    foreach($categoriesMenu as $categorieMenu) :
	    ?>		
        <li class="nav-item">
            <a class="nav-link" href="<?= RACINE_WEB; ?>categorie.php?id=<?= $categorieMenu['id']; ?>">
            	<?= $categorieMenu['nom']; ?>
            </a>
        </li>
    	<?php
    	endforeach;
	    ?>	
	</ul>
</div>	