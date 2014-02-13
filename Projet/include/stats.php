<?php
	include "bdd.php";

	$dateDeb = $_GET['dateDeb'];
	$dateFin = $_GET['dateFin'];
	
	$idCapteur1 = $_GET['idCapteur1'];
	$idLibVal1 = $_GET['idLibVal1'];

	$resultats=$connection->query("	SELECT 3*count(valeur) as total
									FROM valeurmesure, mesure 
									WHERE valeurmesure.Mesure_idMesure = Mesure.idMesure 
									AND Capteur_idCapteur = '$idCapteur1' 
									AND LibVal_idLibVal = '$idLibVal1' 
									AND Mesure.date BETWEEN '$dateDeb' AND '$dateFin'");
									
									
	$resultats->setFetchMode(PDO::FETCH_OBJ);
	$res = $resultats->fetch();
	$count = $res->total;
	echo $count;
?>