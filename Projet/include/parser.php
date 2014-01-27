<?php
$timestartTotal=microtime(true);
ini_set('display_errors', 1); 
error_reporting(E_ALL); 


$sommeReq = 0;
$nbReq = 0;

ini_set('max_execution_time', 999);

if (!$fp = fopen("file.txt","r")) {
	echo "Echec de l'ouverture du fichier";
	exit;
} else {
	include "bdd.php";
	$resultats=$connection->query("SELECT max(idMesure) AS maximum FROM Mesure");
	$resultats->setFetchMode(PDO::FETCH_OBJ);
	$result = $resultats->fetch();
	$idValMesureCourant = $result->maximum; // pour l'instant le maximum
	if(empty($idValMesureCourant))
		$idValMesureCourant = 1;
	else
		$idValMesureCourant++;
		
	$resultats->closeCursor();
	

	$contenu_fichier = fread( $fp, filesize( "file.txt" ));
	$nbTotalLigne = substr_count($contenu_fichier, "\n");
	$fp = fopen("file.txt","r");

	
	echo "Nombre de ligne = $nbTotalLigne";
	
	$cptGlobal = 0;



	 $y = -1;
	 $tabvars = null;
	 $tabvals = null;
	 
	 while(!feof($fp)) {

		// On récupère une ligne
		$ligne = fgets($fp,1024);
		$ligne = str_replace('  ', ' ', $ligne);  // on supprime les espaces en trop
		$ligne = trim($ligne);
		// echo "<br>Ligne $y : $ligne<br><br>";
		$y++;
		//   echo "<br> <br>";

		if($ligne == 0) // premiere ligne: on recup les libvariables
		{
			$tabvars = explode(' ', $ligne); 
			$tabvars = array_filter($tabvars);
			$tabvars = array_values($tabvars);
			var_dump($tabvars);
			//  echo "<br> <br>";

			// Recuperation de l'association libelléVal - idLibval - idtypeCapteur
			// TODO add WHERE idPiece = POST[idpiece]
			$resultats=$connection->query("	SELECT libelle, idLibVal, Capteur.idCapteur FROM Capteur, Localiser, LibVal
											WHERE Piece_idPiece = 1
											AND Capteur.idCapteur = Localiser.Capteur_idCapteur
											AND Capteur.TypeCapteur_idTypeCapteur = LibVal.TypeCapteur_idTypeCapteur
											ORDER BY Capteur.idCapteur;");
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$i = 0;

			$strInsertValMesure = "INSERT INTO valeurmesure (Mesure_idMesure, valeur, libval_idlibval) VALUES ";
			$strInsertMesure = "INSERT INTO Mesure(date, idMesure, capteur_idcapteur) VALUES ";
			while( $resultat = $resultats->fetch())
			{	
				$indice = array_search($resultat->libelle, $tabvars);
				if($indice !== FALSE) // Il à trouvé un indice
				{
					$tabIndiceColonne[$i][0] = $indice;
					$tabIndiceColonne[$i][1] = $resultat->idLibVal;
					$tabIndiceColonne[$i][2] = $resultat->idCapteur;
					$i += 1;
				} else {
					echo "Variable ". $resultat->libelle . " du bdd entre n'existe pas dans le ficher.<br>";
				}
			}
			$resultats->closeCursor();
		} else {
			
			$tabvals = explode(' ', $ligne);
			$tabvals = preg_replace('/^0$/', '0.00', $tabvals); // remplace les '0' par '0.00' afin de ne pas etre supprimé par array_filter
			$tabvals = array_filter($tabvals); // Enlève les valeurs vides du tableau ('' et '0')
			$tabvals = array_values($tabvals); // Remet à jour les clés(index) du tableau
		  

			// cette boucle transforme les xxE-xx en float
			foreach ($tabvals as &$var) {
				if(strpos($var, 'E') !== FALSE) // "E" found !
				{
					$exp = strstr($var, 'E');
					$exp = intval(substr($exp, -3)); // on recup les 3 derniers caractères "-XX" + transformation en int
					$val = (float) strstr($var, 'E', true); // On recupere la valeur sans exposant
					$res = $val * pow(10,$exp); 
					$var = $res;
				}    
			}
			
			//Recuperation de la date
			$date = $tabvals[0];
			$heure = $tabvals[1];
			$datetime = date_create_from_format('d/m/y H:i:s.u', $date." ".$heure);	
			if($datetime==false){
				echo 'Error : wrong date format at line ' . ($y + 1) . ' in your file';
			}
			$dateFormat = $datetime->format('Y-m-d H:i');			

			// c'est ici que l'on va remplir la bdd.
			$idCapteurCourant = -10;
			$total = count($tabIndiceColonne);
			for( $i = 0; $i < $total; $i++) 
			{
				if($tabIndiceColonne[$i][2] != $idCapteurCourant)
				{
					$idCapteurCourant = $tabIndiceColonne[$i][2];

					$strInsertMesure = $strInsertMesure . "('$dateFormat', $idValMesureCourant, $idCapteurCourant),";
					
					$idValMesureCourant++;
				}
				$strInsertValMesure = $strInsertValMesure . "(" .  ($idValMesureCourant - 1) . "," . $tabvals[$tabIndiceColonne[$i][0]] . "," . $tabIndiceColonne[$i][1] . "),";
			}
			if($cptGlobal > 25 || $y == $nbTotalLigne){
					$strInsertValMesure[strlen($strInsertValMesure)-1] = ";";
					$strInsertMesure[strlen($strInsertMesure)-1] = ";";
				
					echo " Req de mesure = $strInsertMesure <br>";
					echo " Req de valmesure = $strInsertValMesure <br>";
						$timestart=microtime(true);
					$connection->query($strInsertMesure) or die(print_r($connection->errorInfo()));
					$connection->query($strInsertValMesure) or die(print_r($connection->errorInfo()));
					
						//Affichage du chrono
						$timeend=microtime(true);
						$time=$timeend-$timestart;
						//Afficher le temps d'éxecution
						$page_load_time = number_format($time, 3);
						echo "Requete #" . $y/25 ." executé en " . $page_load_time . " sec<br>";
						$sommeReq += $page_load_time;
						$nbReq++;
					$strInsertValMesure = "INSERT INTO valeurmesure (Mesure_idMesure, valeur, libval_idlibval) VALUES ";
					$strInsertMesure = "INSERT INTO Mesure(date, idMesure, capteur_idcapteur) VALUES ";
					$cptGlobal = 0;
			}
			$cptGlobal++;
			//Fin du code PHP
		}
		
	}

	//Affichage du chrono
	$timeendTotal=microtime(true);
	$timeTotal=$timeendTotal-$timestartTotal;
	//Afficher le temps d'éxecution
	$page_load_timeTotal = number_format($timeTotal, 3);
	echo "AVG par Req = " . $sommeReq/$nbReq . " sec<br>";
	echo "Temps d'exec Total = " . $page_load_timeTotal . " sec<br>";
	
	fclose($fp); // On ferme le fichier
}

?>