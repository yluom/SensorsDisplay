<?php
	// Connection au serveur
	$dns = 'mysql:host=db515321521.db.1and1.com;dbname=db515321521';
	$utilisateur = 'dbo515321521';
	$motDePasse = 'onlyforme';
	$connection = new PDO( $dns, $utilisateur, $motDePasse );
?>