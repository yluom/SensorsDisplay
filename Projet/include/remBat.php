<?php
	include "bdd.php";
	
	$idBatiment = $_GET['idBatiment'];
	//echo "Je suis le PHP et tu veux que je vire le batiment $idBatiment mais je ne le fait pas car je test.";
	$resultats=$connection->query("DELETE FROM Batiment WHERE idBatiment = $idBatiment");
	
	//Traitement des erreurs ---> A REVOIR !!!! (certainement pas -1...)
	/*if($resultats == -1){
		echo "KO";
	} else {
		echo "OK";
	}*/
	include "listBat.php";
	$resultats->closeCursor();
?>