<?php
	include "bdd.php";
	$idPiece = $_GET['idPiece'];
	$resultats=$connection->query("	SELECT idCapteur, nomType
									FROM Capteur, Localiser, TypeCapteur
									WHERE Piece_idPiece = $idPiece
									AND Localiser.Capteur_idCapteur = Capteur.idCapteur
									AND Capteur.TypeCapteur_idTypeCapteur = TypeCapteur.idTypeCapteur;");
	$resultats->setFetchMode(PDO::FETCH_OBJ);
	while( $resultat = $resultats->fetch() )
	{
			echo '<option value= ' . $resultat->idCapteur .' >' . $resultat->nomType . '</option>';
	}
	$resultats->closeCursor();
?>


