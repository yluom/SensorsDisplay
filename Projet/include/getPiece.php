<?php
	echo '<option>Test</option>';
	echo '<option>Coucou</option>';
	echo '<option>Hola</option>';
	include "bdd.php";
	$batiment = intval($_GET['batiment']);
	$resultats=$connection->query("SELECT nom FROM Piece, Batiment WHERE idBatiment = Batiment_idBatiment AND Batiment.nom = '$batiment'");
	$resultats->setFetchMode(PDO::FETCH_OBJ);
	while( $resultat = $resultats->fetch() )
	{
			echo '<option>'.$resultat->nom.'</option>';
	}
	$resultats->closeCursor();
?>