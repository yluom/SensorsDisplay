<?php
	// Connection au serveur
	$dns = 'mysql:host=localhost;dbname=projet';
	$utilisateur = 'root';
	$motDePasse = '';
	$connection = new PDO( $dns, $utilisateur, $motDePasse );
?>