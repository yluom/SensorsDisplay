<?php
	include "bdd.php";

	$dateDeb = $_GET['dateDeb'];
	$dateFin = $_GET['dateFin'];
	
	$idCapteur1 = $_GET['idCapteur1'];
	$idLibVal1 = $_GET['idLibVal1'];

	$resultats=$connection->query("	SELECT count(valeur) as total
									FROM valeurmesure, mesure 
									WHERE valeurmesure.Mesure_idMesure = mesure.idMesure 
									AND Capteur_idCapteur = '$idCapteur1' 
									AND LibVal_idLibVal = '$idLibVal1' 
									AND mesure.date BETWEEN '$dateDeb' AND '$dateFin'");
									
									
	$resultats->setFetchMode(PDO::FETCH_OBJ);
	$res = $resultats->fetch();
	$stats = "	Hour : ". ceil(($res->total)/2) ." <br>
				Day : ". ceil(($res->total)/2/24) ." <br>
				Week : ". ceil(($res->total)/2/24/7) ." <br>
				Month : ". ceil(($res->total)/2/24/30) ." <br>
				Year : ". ceil(($res->total)/2/24/30/12) ;
	echo $stats;
?>