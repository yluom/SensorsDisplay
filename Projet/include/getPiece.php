<?php
	include "bdd.php";
	$batiment = $_GET['batiment'];
	$resultats=$connection->query("SELECT Piece.nom, Piece.idPiece FROM Piece, Batiment WHERE idBatiment = Batiment_idBatiment AND Batiment.nom = '$batiment'");
	$resultats->setFetchMode(PDO::FETCH_OBJ);
	while( $resultat = $resultats->fetch() )
	{
			echo '<option value= ' . $resultat->idPiece .' >' . $resultat->nom . '</option>';
	}
	$resultats->closeCursor();
?>