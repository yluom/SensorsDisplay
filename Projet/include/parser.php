<!-- Bootstrap core CSS/JS -->
<link href="../css/bootstrap.css" rel="stylesheet">
<script src="../js/jquery-1.10.2.js"></script>
<script src="../js/bootstrap.js"></script>


<div class="progress progress-striped active" style="width: 100%">
	<div class="progress-bar" id="avancement" style="width: 0%"></div>
</div>



<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
          Errors
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse">
      <div class="panel-body" id="erreurs">
		
	  </div>
    </div>
  </div>
</div>




<?php
function addError($str){
	echo '<script>document.getElementById("erreurs").innerHTML = document.getElementById("erreurs").innerHTML + "' . $str . '<br>";</script>';
}


$timestartTotal=microtime(true);
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

// S'il manque des params 
if (!isset($_POST['idpiece']))
{
	addError("E: Paramètre optionpiece manquant !!");
	exit;
} else if (!isset($_FILES['data'])) {
	addError("E: Paramètre data manquant !!");
	exit;
} else if ($_FILES['data']['error']) {  // S'il y a eu une erreur lors du transfert du fichier
          switch ($_FILES['data']['error']) {
		case 1:	//UPLOAD_ERR_INI_SIZE
			addError("E: Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !");     
			break;     
		case 2:	//UPLOAD_ERR_FORM_SIZE
			addError( "E: Le fichier dépasse la limite autorisée dans le formulaire HTML !"); 
			break;     
		case 3:	//UPLOAD_ERR_PARTIAL
			addError( "E: L'envoi du fichier a été interrompu pendant le transfert !");     
			break;     
		case 4:	//UPLOAD_ERR_NO_FILE
			addError( "E: Le fichier que vous avez envoyé a une taille nulle !"); 
			break;     
          }
          exit;
}
else { // cas normal


	// On va laisser le temps (999s) à php de turbiner son fichier
	ini_set('max_execution_time', 999);

	// On récupère l'id de la piece ainsi que le fichier
	$idPiece = $_POST['idpiece']; 
	$file = $_FILES['data']['tmp_name'];
	
	// variables utilisées pour les stats (timing)
	$sommeReq = 0; // somme des temps de l'execution de chaque requete
	$nbReq = 0; // nombre de requetes executées

	$cptDoublon = 0;	//Compte le nombre de mesures qui existent déjà dans la BDD

	if (!$fp = fopen($file,"r")) {
		addError( "E: Echec de l'ouverture du fichier");
		exit;
	} else // Fichier ouvert avec succès 
	{ 
		// on veut le nombre de lignes, donc on doit malheureusement (re-)lire tout le fichier
		$contenu_fichier = fread( $fp, filesize( $file )); 
		$nbTotalLigne = substr_count($contenu_fichier, "\n"); // On compte le nombre de lignes
		$fp = fopen($file,"r"); // On réouvre le fichier (replace le curseur de lecture au debut)

		if($nbTotalLigne < 2) // Il faut au moins 2 lignes dans le fichier.txt : une pour les libellevariables et une pour les variables (au minimum)
		{
			addError( "E: Le fichier importé contient seulement ". $nbTotalLigne . " lignes,<br> veuillez importer un fichier qui fais minimum 2 lignes (1 ligne pour les libelles de variables, une ligne pour les valeurs des variables.");
		} else // S'il y a au moins 2 lignes, on parse le fichier
		{ 
			include "bdd.php"; // Connexion à la bdd
			// ----- Bloc recherche des dates des mesures déjà entrées dans la bdd 
			$resultats=$connection->query("
						SELECT date
						FROM Mesure, Capteur, Localiser
						WHERE Capteur.idCapteur = Localiser.Capteur_idCapteur
						AND Localiser.Piece_idPiece = ".$idPiece."
						GROUP BY DATE
					");
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$indexDates = 0;
			$datesMesuresEffectuees = null;
			while( $resultat = $resultats->fetch())
			{
			    $datesMesuresEffectuees[$indexDates] = $resultat->date;
			    //echo "$datesMesuresEffectuees[$indexDates] <br>";
			    $indexDates++;
			}
			
			
			// ----- Bloc recherche idMesureMax
			// on va chercher l'idmesure maximal, afin de rentrer les prochaines mesures avec un bon idmesure
			$resultats=$connection->query("SELECT max(idMesure) AS maximum FROM Mesure"); 
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$result = $resultats->fetch();
			$idMesureCourant = $result->maximum; // On recupère le maximum, qui nous servira à remplir la table Mesure
			if(empty($idMesureCourant)) // Concrètement, si idMesureCourant est vide, cela signifie que la table Mesure est vide
				$idMesureCourant = 1; // on le met donc à 1
			else
				$idMesureCourant++; // Sinon on incrémente, pour stoquer la prochaine mesure
			$resultats->closeCursor(); // On ferme la requete
			
			$cptLignesImportees = 0;
			$cptLignesDansRequete = 0; // Compte le nombre de lignes entrées dans la bdd
			$numLigneCourante = -1; // on commence à -1 
			
			// On initialise les deux gros tableaux
			$tabLibelleValeurs = null; // celui ci va contenir tous les libellés des variables présentes dans le fichier
			$tabValeurs = null; // celui ci va contenir toutes les valeurs correspondant au libellés ci-dessus trouvés dans le fichier
			
			
			// ----- Bloc parcours ligne par ligne
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
							addError( "E: Variable ". $resultat->libelle . " de la bdd entre n'existe pas dans le ficher.");
						}
					}
					$resultats->closeCursor();
				} else { // Toutes les lignes sauf la premiere
				
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
					
					// Recuperation de la date & formattage pour bdd
					$date = $tabValeurs[0]; // premiere valeur: la date
					$heure = $tabValeurs[1]; // deuxième valeur: l'heure
					$datetime = date_create_from_format('d/m/y H:i:s.u', $date." ".$heure); // crée un datetime pour l'entrer dans la bdd
					

					$dateErronee = false;
					if ($datetime == false) // Si date_create_from_format renvoi false, ça veut dire que le format de la date n'as pas été respecté: ERREUR !
					{ 
						addError('E: wrong date format at line ' . ($numLigneCourante + 1) . ' in your file :(...<br> Correct the line and import the file again !');
						$dateErronee = true;
					} else {
						$dateFormat = $datetime->format('Y-m-d H:i:s');
					}
					
					if( !$dateErronee && !is_null($datesMesuresEffectuees) && in_array($dateFormat, $datesMesuresEffectuees) == TRUE) 
					{
						//addError( "E: la mesure a la date ". date_format($datetime, "Y-m-d H:i:s"). " existe deja dans la bdd ! (ligne non importee dans la bdd !)");
						$cptDoublon++;
					} else if (!$dateErronee) { // pas d'erreur sur la date & mesure inexistante dans la bdd: on rempli !
						// ----- Bloc Remplissage de la bdd !
						$idCapteurCourant = -10;
						$total = count($tabIndiceColonne);
						for ( $i = 0; $i < $total; $i++) 
						{
							if($tabIndiceColonne[$i][2] != $idCapteurCourant) // Si l'on "change" de capteur (et donc de type capteur), on créée une nouvelle mesure dans la table Mesure (donc ajout d'une ligne dans la string de requete d'insertion dans mesure !
							{
								$idCapteurCourant = $tabIndiceColonne[$i][2];
								$strInsertMesure = $strInsertMesure . "('$dateFormat', $idMesureCourant, $idCapteurCourant),";
								$idMesureCourant++;
							}
							$strInsertValMesure = $strInsertValMesure . "(" .  ($idMesureCourant - 1) . "," . $tabValeurs[$tabIndiceColonne[$i][0]] . "," . $tabIndiceColonne[$i][1] . "),";
						}
						
						if($cptLignesDansRequete > 25 || $numLigneCourante == $nbTotalLigne)
						{
								// On ferme la requete avec un "" si on est à la fin du fichier ou si on à traité les 25 lignes (par defaut) 
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
								$cptLignesDansRequete = 0;
						}
						$cptLignesDansRequete++;
						$cptLignesImportees++;
					}
					// Affiche le pourcentage d'avancement (tout les 1%)
					if($numLigneCourante % intval($nbTotalLigne/1000) == 0)
					{
						$avancement = $numLigneCourante/$nbTotalLigne*100;
						$avancement = number_format($avancement, 2); // On garde 2 décimales 
						echo '
						<script>
							document.getElementById("avancement").style.width = "' . $avancement . '%";
						</script>';
						ob_flush();
						flush();
					} else {
						//echo "intval = ". intval($nbTotalLigne % $numLigneCourante) . "  /// ".($nbTotalLigne/100)."<br>";
						//flush();
					}
				}	
			}
			
			if($cptDoublon > 0)
				addError( "E: Nombre de mesure non importées (doublon) : $cptDoublon");
			
			//Affichage du chrono
			$timeendTotal=microtime(true);
			$timeTotal=$timeendTotal-$timestartTotal;
			//Afficher le temps d'éxecution
			$page_load_timeTotal = number_format($timeTotal, 3);
			//echo "AVG par Req = " . $sommeReq/$nbReq . " sec<br>";
			echo "Temps d'exec Total = " . $page_load_timeTotal . " sec<br>";
			echo "$cptLignesImportees/$nbTotalLigne lignes importees !";
		
		}
		fclose($fp); // On ferme le fichier
	}
}
?>