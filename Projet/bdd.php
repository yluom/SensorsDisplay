<?php
	// Connection au serveur
	$dns = 'mysql:host=********;dbname=********';
	$utilisateur = '********';
	$motDePasse = '********';
	$connection = new PDO( $dns, $utilisateur, $motDePasse );
?>