<?php
	include "bdd.php";
	$idPiece = $_GET['idPiece'];
	$resultats=$connection->query("	SELECT idCapteur, nomType
									FROM capteur, localiser, typecapteur
									WHERE Piece_idPiece = $idPiece
									AND localiser.Capteur_idCapteur = capteur.idCapteur
									AND capteur.TypeCapteur_idTypeCapteur = typecapteur.idTypeCapteur;");
	$resultats->setFetchMode(PDO::FETCH_OBJ);
	while( $resultat = $resultats->fetch() )
	{
			echo '<option value= ' . $resultat->idCapteur .' >' . $resultat->nomType . '</option>';
	}
	$resultats->closeCursor();
?>


