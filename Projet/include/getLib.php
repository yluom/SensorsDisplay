<?php
	include "bdd.php";
	$idCapteur = $_GET['idCapteur'];
	$resultats=$connection->query("	SELECT idLibVal, libelle
									FROM capteur, typecapteur, libval
									WHERE capteur.idCapteur = $idCapteur
									AND capteur.TypeCapteur_idTypeCapteur = typecapteur.idTypeCapteur
									AND typecapteur.idTypeCapteur = libval.TypeCapteur_idTypeCapteur;");
	$resultats->setFetchMode(PDO::FETCH_OBJ);
	while( $resultat = $resultats->fetch() )
	{
			echo '<option value= ' . $resultat->idLibVal .' >' . $resultat->libelle . '</option>';
	}
	$resultats->closeCursor();
?>


