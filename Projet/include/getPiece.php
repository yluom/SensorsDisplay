<?php
	include "bdd.php";
	$batiment = $_GET['batiment'];
	$resultats=$connection->query("SELECT piece.nom, piece.idPiece FROM piece, batiment WHERE idBatiment = Batiment_idBatiment AND batiment.nom = '$batiment'");
	$resultats->setFetchMode(PDO::FETCH_OBJ);
	while( $resultat = $resultats->fetch() )
	{
			echo '<option value= ' . $resultat->idPiece .' >' . $resultat->nom . '</option>';
	}
	$resultats->closeCursor();
?>