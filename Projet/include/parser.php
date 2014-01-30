<?php
$timestartTotal=microtime(true);
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

// variables utilisées pour les stats (timing)
$sommeReq = 0; // somme des temps de l'execution de chaque requete
$nbReq = 0; // nombre de requetes executées

//$idPiece = $_POST['idpiece']; TODO

// On va laisser le temps à php de turbiner 
ini_set('max_execution_time', 999);

if (!$fp = fopen("file.txt","r")) {
	echo "Echec de l'ouverture du fichier";
	exit;
} else { // Fichier ouvert avec succès 

	include "bdd.php"; // Connexion à la bdd
	
	// on va chercher l'idmesure maximal, afin de rentrer les prochaines mesures avec un bon idmesure
	$resultats=$connection->query("SELECT max(idMesure) AS maximum FROM Mesure"); 
	$resultats->setFetchMode(PDO::FETCH_OBJ);
	$result = $resultats->fetch();
	$idValMesureCourant = $result->maximum; // On recupère le maximum
	
	if(empty($idValMesureCourant)) // Concrètement, si idValMesureCourant est vide, cela signifie que la base de données est vide
		$idValMesureCourant = 1; // on le met donc à 1
	else
		$idValMesureCourant++; // Sinon on incrémente, pour stoquer la prochaine mesure
		
	$resultats->closeCursor(); // On ferme la requete
	
	// on veut le nombre de lignes, donc on doit malheureusement lire tout le fichier
	$contenu_fichier = fread( $fp, filesize( "file.txt" )); 
	$nbTotalLigne = substr_count($contenu_fichier, "\n"); // On compte le nombre de lignes
	$fp = fopen("file.txt","r"); // On réouvre le fichier (replace le curseur de lecture au debut)

	
	//echo "Nombre de lignes = $nbTotalLigne"; // Debug
	
	$cptGlobal = 0;


	 $numLigneCourante = -1; // on commence à -1 
	 
	 // On initialise les deux gros tableaux
	 $tabLibelleValeurs = null; // celui ci va contenir tous les libellés des variables présentes dans le fichier
	 $tabValeurs = null; // celui ci va contenir toutes les valeurs correspondant au libellés ci-dessus trouvés dans le fichier
	 
	 while(!feof($fp)) { // Tant qu'on n'est pas à la fin du fichier 

		
		$ligne = fgets($fp,1024); // On récupère une ligne de 1024 octets 
		$ligne = str_replace('  ', ' ', $ligne);  // on supprime les multiples espaces en trop
		$ligne = trim($ligne); // on supprime les espaces en debut et fin de chaine au cas ou (car on va splitter sur les espaces)
		$numLigneCourante++; // on passe à la ligne 0: la première

		if($ligne == 0) // premiere ligne: on recupère les libvariables
		{
			$tabLibelleValeurs = explode(' ', $ligne); // on split la ligne en blocs séparés par des espaces
			$tabLibelleValeurs = array_filter($tabLibelleValeurs); // Enlève les valeurs vides du tableau ('' et '0')
			$tabLibelleValeurs = array_values($tabLibelleValeurs); // Remet à jour les clés(index) du tableau
			//var_dump($tabLibelleValeurs); // Debug: affichage du tableau

			
			$i = 0; // On init i à 0

			// On commence à construire le début nos deux grosses requetes
			$strInsertValMesure = "INSERT INTO ValeurMesure (Mesure_idMesure, valeur, libval_idlibval) VALUES "; 
			$strInsertMesure = "INSERT INTO Mesure(date, idMesure, capteur_idcapteur) VALUES ";
			
			
			// Recuperation de l'association libelléVal - idLibval - idtypeCapteur pour une piece donnée
			// TODO add WHERE idPiece = POST[idpiece]
			$resultats=$connection->query("
							SELECT libelle, idLibVal, Capteur.idCapteur FROM Capteur, Localiser, LibVal
							WHERE Piece_idPiece = 1
							AND Capteur.idCapteur = Localiser.Capteur_idCapteur
							AND Capteur.TypeCapteur_idTypeCapteur = LibVal.TypeCapteur_idTypeCapteur
							ORDER BY Capteur.idCapteur;"
							);
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			

			// On verifie que les libellés de la bdd existent bien dans le fichier
			while( $resultat = $resultats->fetch())
			{	
				$indice = array_search($resultat->libelle, $tabLibelleValeurs); // Retourne d'indice si le libelle existe dans le fichier
				if($indice !== FALSE) // Il a trouvé un indice
				{
					$tabIndiceColonne[$i][0] = $indice;
					$tabIndiceColonne[$i][1] = $resultat->idLibVal;
					$tabIndiceColonne[$i][2] = $resultat->idCapteur;
					$i += 1;
				} else { // indice == FALSE
					echo "Variable ". $resultat->libelle . " de la bdd entre n'existe pas dans le ficher.<br>";
				}
			}
			$resultats->closeCursor();
		} else { // Toutes les lignes sauf la premiere
		
			// si ya deja une donnee à une datetime et pour ce capteur
			$tabValeurs = explode(' ', $ligne); // On split sur les espaces
			$tabValeurs = preg_replace('/^0$/', '0.00', $tabValeurs); // remplace les '0' par '0.00' afin de ne pas etre supprimé par array_filter
			$tabValeurs = array_filter($tabValeurs); // Enlève les valeurs vides du tableau ('' et '0')
			$tabValeurs = array_values($tabValeurs); // Remet à jour les clés(index) du tableau

			// cette boucle transforme les xxE-xx en float (#useless ?)
			foreach ($tabValeurs as &$var) {
				if(strpos($var, 'E') !== FALSE) // exposant "E" found !
				{
					$exp = strstr($var, 'E');
					$exp = intval(substr($exp, -3)); // on recup les 3 derniers caractères "-XX" + transformation en int
					$val = (float) strstr($var, 'E', true); // On recupere la valeur sans exposant
					$res = $val * pow(10,$exp); 
					$var = $res;
				}    
			}
			
			// Recuperation de la date
			$date = $tabValeurs[0]; // premiere valeur: la date
			$heure = $tabValeurs[1]; // deuxième valeur: l'heure
			$datetime = date_create_from_format('d/m/y H:i:s.u', $date." ".$heure); // crée un datetime pour l'entrer dans la bdd
			
			if ($datetime == false) { // Si date_create_from_format renvoi false, ça veut dire que le format de la date n'as pas été respecté: ERREUR !
				echo 'Error : wrong date format at line ' . ($numLigneCourante + 1) . ' in your file :(...<br> Correct it and import the file again !';
			}
			
			$dateFormat = $datetime->format('Y-m-d H:i');			

			// c'est ici que l'on va remplir la bdd.
			$idCapteurCourant = -10;
			$total = count($tabIndiceColonne);
			for ( $i = 0; $i < $total; $i++) 
			{
				if($tabIndiceColonne[$i][2] != $idCapteurCourant)
				{
					$idCapteurCourant = $tabIndiceColonne[$i][2];
					$strInsertMesure = $strInsertMesure . "('$dateFormat', $idValMesureCourant, $idCapteurCourant),";
					$idValMesureCourant++;
				}
				$strInsertValMesure = $strInsertValMesure . "(" .  ($idValMesureCourant - 1) . "," . $tabValeurs[$tabIndiceColonne[$i][0]] . "," . $tabIndiceColonne[$i][1] . "),";
			}
			if($cptGlobal > 25 || $numLigneCourante == $nbTotalLigne){
					$strInsertValMesure[strlen($strInsertValMesure)-1] = ";";
					$strInsertMesure[strlen($strInsertMesure)-1] = ";";
				
					//echo " Req de mesure = $strInsertMesure <br>";
					//echo " Req de valmesure = $strInsertValMesure <br>";
						$timestart=microtime(true);
					$connection->query($strInsertMesure) or die(print_r($connection->errorInfo()));
					$connection->query($strInsertValMesure) or die(print_r($connection->errorInfo()));
					
						//Affichage du chrono
						$timeend=microtime(true);
						$time=$timeend-$timestart;
						//Afficher le temps d'éxecution
						$page_load_time = number_format($time, 3);
						//echo "Requete #" . $numLigneCourante/25 ." executee en " . $page_load_time . " sec<br>";
						$sommeReq += $page_load_time;
						$nbReq++;
					$strInsertValMesure = "INSERT INTO ValeurMesure (Mesure_idMesure, valeur, libval_idlibval) VALUES ";
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
	//echo "AVG par Req = " . $sommeReq/$nbReq . " sec<br>";
	echo "Temps d'exec Total = " . $page_load_timeTotal . " sec<br>";
	
	fclose($fp); // On ferme le fichier
}

?>