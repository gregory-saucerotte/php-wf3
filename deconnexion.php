<?php

require_once __DIR__ . '/include/initialisation.php';

unset($_SESSION['utilisateur']);

header('Location: index.php');
die;