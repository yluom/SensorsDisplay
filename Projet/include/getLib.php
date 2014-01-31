<?php
	include "bdd.php";
	$idCapteur = $_GET['idCapteur'];
	$resultats=$connection->query("	SELECT idLibVal, libelle
									FROM Capteur, TypeCapteur, LibVal
									WHERE Capteur.idCapteur = $idCapteur
									AND Capteur.TypeCapteur_idTypeCapteur = TypeCapteur.idTypeCapteur
									AND TypeCapteur.idTypeCapteur = LibVal.TypeCapteur_idTypeCapteur;");
	$resultats->setFetchMode(PDO::FETCH_OBJ);
	while( $resultat = $resultats->fetch() )
	{
			echo '<option value= ' . $resultat->idLibVal .' >' . $resultat->libelle . '</option>';
	}
	$resultats->closeCursor();
?>


