<?php
	include "bdd.php";

	if(!empty($_GET['dateDeb']) && !empty($_GET['dateFin']) && !empty($_GET['idCapteur1']) && !empty($_GET['idLibVal1']) && !empty($_GET['idCapteur2']) && !empty($_GET['idLibVal2']) && !empty($_GET['idCapteur3']) && !empty($_GET['idLibVal3']) && !empty($_GET['groupBy'])){
		$dateDeb = $_GET['dateDeb'];
		$dateFin = $_GET['dateFin'];
		
		$idCapteur1 = $_GET['idCapteur1'];
		$idLibVal1 = $_GET['idLibVal1'];
		
		$idCapteur2 = $_GET['idCapteur2'];
		$idLibVal2 = $_GET['idLibVal2'];
		
		$idCapteur3 = $_GET['idCapteur3'];
		$idLibVal3 = $_GET['idLibVal3'];
		
		$groupBy = $_GET['groupBy'];
	} else {
		$dateDeb = '2010-12-01';
		$dateFin = '2010-12-02';
		
		$idCapteur1 = 5;
		$idLibVal1 = 17;
		
		$idCapteur2 = 2;
		$idLibVal2 = 6;
		
		$idCapteur3 = 8;
		$idLibVal3 = 25;
		
		$groupBy = "";
	}
	
	$grbStr = "";
	
	switch($groupBy){
		case "YEAR" :	$grbStr = " GROUP BY YEAR(Tab1.date) ";
			break;
		case "MONTH" :	$grbStr = " GROUP BY YEAR(Tab1.date), MONTH(Tab1.date) ";
			break;
		case "WEEK" :	$grbStr = " GROUP BY YEAR(Tab1.date), MONTH(Tab1.date), WEEK(Tab1.date) ";
			break;
		case "DAY" :	$grbStr = " GROUP BY YEAR(Tab1.date), MONTH(Tab1.date), DAY(Tab1.date) ";
			break;
		case "HOUR" :	$grbStr = " GROUP BY YEAR(Tab1.date), MONTH(Tab1.date), DAY(Tab1.date), HOUR(Tab1.date) ";
			break;
	}
					
		$resultats=$connection->query("	SELECT Tab1.x, Tab2.y, Tab3.value
									FROM 	(SELECT valeur as x, mesure.date 		FROM valeurmesure, mesure WHERE valeurmesure.Mesure_idMesure = mesure.idMesure AND capteur_idCapteur = '$idCapteur1' AND LibVal_idLibVal = '$idLibVal1' AND mesure.date BETWEEN '$dateDeb%' AND '$dateFin%' ) Tab1,
											(SELECT valeur as y, mesure.date 		FROM valeurmesure, mesure WHERE valeurmesure.Mesure_idMesure = mesure.idMesure AND capteur_idCapteur = '$idCapteur2' AND LibVal_idLibVal = '$idLibVal2' AND mesure.date BETWEEN '$dateDeb%' AND '$dateFin%') Tab2,
											(SELECT valeur as value, mesure.date 	FROM valeurmesure, mesure WHERE valeurmesure.Mesure_idMesure = mesure.idMesure AND capteur_idCapteur = '$idCapteur3' AND LibVal_idLibVal = '$idLibVal3' AND mesure.date BETWEEN '$dateDeb%' AND '$dateFin%') Tab3
									WHERE Tab1.date = Tab2.date
									AND Tab2.date = Tab3.date
									$grbStr
									ORDER BY Tab3.value DESC;");
									
									
		$resultats->setFetchMode(PDO::FETCH_OBJ);
		
		$test = true;
		while( $resultat = $resultats->fetch() )
		{
			if($test){
				echo	'	{
								"y" : ' . $resultat->y .',
								"x" : ' . $resultat->x .',
								"value" : ' . $resultat->value .'
				
							}';
				$test = false;
			} else {
				echo	'	,{
								"y" : ' . $resultat->y .',
								"x" : ' . $resultat->x .',
								"value" : ' . $resultat->value .'
				
							}';
			}
		}
		
		$resultats->closeCursor(); 
	
		echo "END";
		
		$grbStr = str_replace("Tab1.date", "dateMesure", $grbStr);
		
		$resultats=$connection->query("	SELECT LEFT(Tab1.date,13) as dateMesure, ROUND(AVG(Tab1.x),2) as x, ROUND(AVG(Tab2.y),2) as y, ROUND(AVG(Tab3.value),2) as value
										FROM (	SELECT valeur as x, mesure.date FROM valeurmesure, mesure 
												WHERE valeurmesure.Mesure_idMesure = mesure.idMesure 
												AND Capteur_idCapteur = '$idCapteur1' 
												AND LibVal_idLibVal = '$idLibVal1' 
												AND mesure.date BETWEEN '$dateDeb%' AND '$dateFin%' ) Tab1,
										(		SELECT valeur as y, mesure.date 
												FROM valeurmesure, mesure 
												WHERE valeurmesure.Mesure_idMesure = mesure.idMesure 
												AND Capteur_idCapteur = '$idCapteur2' 
												AND LibVal_idLibVal = '$idLibVal2' 
												AND mesure.date BETWEEN '$dateDeb%' AND '$dateFin%') Tab2, 
										(		SELECT valeur as value, mesure.date 
												FROM valeurmesure, mesure 
												WHERE valeurmesure.Mesure_idMesure = mesure.idMesure 
												AND Capteur_idCapteur = '$idCapteur3' 
												AND LibVal_idLibVal = '$idLibVal3' 
												AND mesure.date BETWEEN '$dateDeb%' AND '$dateFin%') Tab3 
										WHERE Tab1.date = Tab2.date 
										AND Tab2.date = Tab3.date 
										$grbStr ;");
										
										

		$resultats->setFetchMode(PDO::FETCH_OBJ);
		
		$test = true;
		while( $resultat = $resultats->fetch() )
		{
			if($test){
				echo	'	{
								"date" : "' . $resultat->dateMesure .'",
								"y" : ' . $resultat->y .',
								"x" : ' . $resultat->x .',
								"value" : ' . $resultat->value .'
							}';
				$test = false;
			} else {
				echo	'	,{
								"date" : "' . $resultat->dateMesure .'", 
								"y" : ' . $resultat->y .',
								"x" : ' . $resultat->x .',
								"value" : ' . $resultat->value .'
							}';
			}
		}
		
		$resultats->closeCursor(); 
					
	
?>