<script>
	var nbErrors = 0;
	function finParser(tps, nbLine, totalLine){
		setTimeout(function () {
									document.getElementById('done').innerHTML = "<h1 style='color : #2a6496;'>Done !</h1><br>";
								
									document.getElementById('tpsExec').innerHTML = "Execution time : " + tps + " sec";
									document.getElementById('nbLine').innerHTML = "Lines imported : "+ nbLine + "/" + totalLine + ".";
									document.getElementById('infos').style.visibility = "visible";
									
									if(nbErrors > 0){
										if (nbErrors == 1)
											document.getElementById('nbErrors').innerHTML = nbErrors + " Error";
										else
											document.getElementById('nbErrors').innerHTML = nbErrors + " Errors/Warnings";
										document.getElementById('errors').style.visibility = "visible";
									}
								}, 700);
	}
</script>


<div class="row">
  <div class="col-lg-12">
	<h1>Rapport <small>Import report</small></h1>
	<ol class="breadcrumb">
	  <li><a href="index.php?p=dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
	  <li class="active"><i class="fa fa-edit"></i> Importation rapport</li>
	</ol>
  </div>
</div><!-- /.row -->

<div class="row">
	<div class="col-lg-12" id="done">
			<div class="form-group">
				<label>Avancement</label>
				<div class="progress progress-striped active" style="width: 100%">
					<div class="progress-bar" id="avancement" style="width: 0%"></div>
				</div>
			</div>
	</div>
</div>

<div class='row' id="infos" style="visibility: hidden;">
	<div class='col-lg-6'>
		<div class='alert alert-dismissable alert-success'>
			<button type='button' class='close' data-dismiss='alert'>×</button>
			<span id="tpsExec">Temps d'exec Total = " . $page_load_timeTotal . " sec</span>
		</div>
	</div>
	<div class='col-lg-6'>
		<div class='alert alert-dismissable alert-info'>
			<button type='button' class='close' data-dismiss='alert'>×</button>
			<span id="nbLine">$cptLignesImportees/$nbTotalLigne lignes importees !</span>
		</div>
	</div>
</div>

<div class="row" id="errors" style="visibility: hidden;">
	<div class="col-lg-12">
		<div class="panel-group" id="accordion">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" id="nbErrors">
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
	</div>
</div>

<?php
include "bdd.php";
$nbCheck= 0;
$sommeIteration = 0;
global $cptCheck;
$cptCheck = 0;

function addError ($str) // TODO Discerner erreurs/warnings
{
	echo '<script>nbErrors++;document.getElementById("erreurs").innerHTML = document.getElementById("erreurs").innerHTML + "<div class=\"alert alert-dismissable alert-danger\">' . $str . '</div>";</script>';
}

function checkSiMesureEstUnDoublon ($date, $idCapteur, $connection)
{
	/*global $cptIteration;
	$cptIteration = 0;
	$count = count($datesMesuresEffectuees);
	for ($i = 0; $i < $count; $i++)
	{
		$cptIteration++;
		if($datesMesuresEffectuees[$i][0] == $date && in_array($idCapteur, $datesMesuresEffectuees[$i][1]))
			return true;
	}
	return false;*/
	$resultats=$connection->query("
							SELECT idMesure
							FROM mesure
							WHERE date = '$date'
							AND Capteur_idCapteur = $idCapteur;
							");
	$resultats->setFetchMode(PDO::FETCH_OBJ);

	if ($resultats->fetch() == false)
	{
		return false;
	} else {
		return true;
	}
}

$timestartTotal=microtime(true);
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

$readyForParsing = true; // Variable utilisée pour NE PAS parser le fichier, si aucun libellé n'est trouvé dans la bdd

// S'il manque des params 
if (!isset($_FILES['data'])) {
	addError("Error: Paramètre data manquant !!");
} else if ($_FILES['data']['error']) {  // S'il y a eu une erreur lors du transfert du fichier
	switch ($_FILES['data']['error']) 
	{
		case 1:	//UPLOAD_ERR_INI_SIZE
			addError("Error: Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !");     
			break;     
		case 2:	//UPLOAD_ERR_FORM_SIZE
			addError( "Error: Le fichier dépasse la limite autorisée dans le formulaire HTML !"); 
			break;     
		case 3:	//UPLOAD_ERR_PARTIAL
			addError( "Error: L'envoi du fichier a été interrompu pendant le transfert !");     
			break;     
		case 4:	//UPLOAD_ERR_NO_FILE
			addError( "Error: Le fichier que vous avez envoyé a une taille nulle !"); 
			break;     
	}
} else if ($_FILES['data']['type'] != "text/plain" ) {
	$format = $_FILES['data']['type'];
	addError("Error: Le fichier importé est au format $format. Seul le format texte est accepté !");
} else { // cas normal

	date_default_timezone_set('Europe/Paris');


	// On va laisser le temps (999s) à php de turbiner son fichier
	ini_set('max_execution_time', 999);

	// On récupère l'id de la piece ainsi que le fichier
	//$idPiece = $_POST['idpiece']; 
	$file = $_FILES['data']['tmp_name'];
	
	// variables utilisées pour les stats (timing)
	$sommeReq = 0; // somme des temps de l'execution de chaque requete
	$nbReq = 0; // nombre de requetes executées

	$cptDoublon = 0;	//Compte le nombre de mesures qui existent déjà dans la BDD ( à cette date )

	if (!$fp = fopen($file,"r")) {
		addError( "Error: Echec de l'ouverture du fichier");
		$readyForParsing = false;
	} else // Fichier ouvert avec succès 
	{ 
		// on veut le nombre de lignes, donc on doit malheureusement (re-)lire tout le fichier
		$contenu_fichier = fread( $fp, filesize( $file )); 
		$nbTotalLigne = substr_count($contenu_fichier, "\n"); // On compte le nombre de lignes
		$fp = fopen($file,"r"); // On réouvre le fichier (replace le curseur de lecture au debut)

		if($nbTotalLigne < 2) // Il faut au moins 2 lignes dans le fichier.txt : une pour les libellevariables et une pour les variables (au minimum)
		{
			addError( "Error: Le fichier importé contient seulement ". $nbTotalLigne . " lignes,<br> veuillez importer un fichier qui fais minimum 2 lignes (1 ligne pour les libelles de variables, une ligne pour les valeurs des variables.");
		} else // S'il y a au moins 2 lignes, on parse le fichier
		{ 
		
			// Recherche des dates debut et fin du fichier
			$leFichierEnEntier = file($file); // file($file);
			$premiere_ligne = $leFichierEnEntier[1];
			$derniere_ligne = $leFichierEnEntier[count($leFichierEnEntier)-1];
			$backcheck = 2;
			while(empty($derniere_ligne))
			{
				$derniere_ligne = $leFichierEnEntier[count($leFichierEnEntier)-$backcheck];
				$backcheck++;
			}

			
			$premiere_date = substr($premiere_ligne, 0, 17);
			$derniere_date = substr($derniere_ligne, 0, 17);
			
			$datetimeBegin = date_create_from_format('d/m/y H:i:s', $premiere_date); // crée un datetime pour l'entrer dans la bdd
			$datetimeEnd = date_create_from_format('d/m/y H:i:s', $derniere_date); // crée un datetime pour l'entrer dans la bdd
			
			
			$premiere_date = $datetimeBegin->format('Y-m-d H:i:s');
			$derniere_date = $datetimeEnd->format('Y-m-d H:i:s');
			
			include "bdd.php"; // Connexion à la bdd
			// ----- Bloc recherche des dates des mesures déjà entrées dans la bdd TODO TODO TODO  
			/*$resultats=$connection->query("
							SELECT date
							FROM mesure
							WHERE date BETWEEN '$premiere_date' AND '$derniere_date'
							GROUP BY date;
							");
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$indexDates = 0;
			unset($datesMesuresEffectuees);
			while( $resultat = $resultats->fetch())
			{
				$datesMesuresEffectuees[$indexDates][0] = $resultat->date;
				$dateTemp = $resultat->date;
				
				$resulBis=$connection->query("
							SELECT Capteur_idCapteur
							FROM mesure
							WHERE date = '$dateTemp'
					");
					
				$indexIdCapteur = 0;
				unset($tableauIdCapteur);
				$resulBis->setFetchMode(PDO::FETCH_OBJ);
				while( $resultatBis = $resulBis->fetch())
				{
					$tableauIdCapteur[$indexIdCapteur] = $resultatBis->Capteur_idCapteur;
					$indexIdCapteur++;
				}
				
				$datesMesuresEffectuees[$indexDates][1] = $tableauIdCapteur;
				$indexDates++;
			}*/
			
			// ----- Bloc recherche idMesureMax
			// on va chercher l'idmesure maximal, afin de rentrer les prochaines mesures avec un bon idmesure
			$resultats=$connection->query("SELECT max(idMesure) AS maximum FROM mesure"); 
			$resultats->setFetchMode(PDO::FETCH_OBJ);
			$result = $resultats->fetch();
			$idMesureCourant = $result->maximum; // On recupère le maximum, qui nous servira à remplir la table mesure
			if(empty($idMesureCourant)) // Concrètement, si idMesureCourant est vide, cela signifie que la table mesure est vide
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
					
					// On commence à construire le début nos deux grosses requetes
					$strInsertValMesure = "INSERT INTO valeurmesure (Mesure_idMesure, valeur, libval_idlibval) VALUES "; 
					$strInsertMesure = "INSERT INTO mesure(date, idMesure, capteur_idcapteur) VALUES ";

					$i = 0; // On init i à 0
					
					foreach ($tabLibelleValeurs as &$nomCaptEtLib) { // pour chaque entete de colonne
					
						$nomCaptEtLibArray = explode("/", $nomCaptEtLib); // On separe en 2 le nomCapteur et libelleVal
						
						if(count($nomCaptEtLibArray) != 2) // dafuq ? Capteur/libelle
						{
							addError("Warning: l'entete de colonne $nomCaptEtLib est mal formée !");
						} else {
						
							$requete2ouf = "
									SELECT  idCapteur, idLibVal
									FROM capteur, libval, typecapteur
									WHERE capteur.TypeCapteur_idTypeCapteur = libval.TypeCapteur_idTypeCapteur
									AND nomCapteur = '$nomCaptEtLibArray[0]'
									AND libelle = '$nomCaptEtLibArray[1]';
									";
							//echo "<br> Req: <br> ". $requete2ouf;
	
							$resultats=$connection->query($requete2ouf);
							$resultats->setFetchMode(PDO::FETCH_OBJ);
							
							if($resultats->rowCount() == 0)
							{
								addError("Warning: Dans la bdd, il n'existe aucune correspondance entre le capteur $nomCaptEtLibArray[0] et le libelle $nomCaptEtLibArray[1] !");
							} else {
								$resultat = $resultats->fetch();
								$indice = array_search($nomCaptEtLib, $tabLibelleValeurs); // Retourne d'indice si le libelle existe dans le fichier

								// on stoque l'association indexDansLeFichier/idLibVal/idCapteur
								$tabIndiceColonne[$i][0] = $indice;
								$tabIndiceColonne[$i][1] = $resultat->idLibVal;
								$tabIndiceColonne[$i][2] = $resultat->idCapteur;
								$i += 1;
							}
							$resultats->closeCursor();
						}
					}

					// TRIAGE du tableau en fonction de l'idCapteur (necessaire pour minimiser les requetes d'insertion dans mesure)
					foreach ($tabIndiceColonne as $array) {
						$idCapt[] = $array[2];
					}
					array_multisort($idCapt,SORT_NUMERIC,$tabIndiceColonne);
					
				} else { // Toutes les lignes sauf la premiere
					if($readyForParsing) {
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
							addError('Warning: line ' . ($numLigneCourante + 1) . ' not imported because of a wrong date format :(...<br> Correct the line and import the file again !');
							$dateErronee = true;
						} else {
							$dateFormat = $datetime->format('Y-m-d H:i:s');
						}
						
						if (!$dateErronee) { // pas d'erreur sur la date 
							// ----- Bloc Remplissage de la bdd !
							$idCapteurCourant = -10;
							$total = count($tabIndiceColonne);
							for ( $i = 0; $i < $total; $i++) 
							{
								if(checkSiMesureEstUnDoublon($dateFormat, $tabIndiceColonne[$i][2], $connection))
								{			
									$cptDoublon++;
								} else {
									if($tabIndiceColonne[$i][2] != $idCapteurCourant) // Si l'on "change" de capteur (et donc de type capteur), on créée une nouvelle mesure dans la table mesure (donc ajout d'une ligne dans la string de requete d'insertion dans mesure !
									{
										$idCapteurCourant = $tabIndiceColonne[$i][2];
										$strInsertMesure = $strInsertMesure . "('$dateFormat', $idMesureCourant, $idCapteurCourant),";
										$idMesureCourant++;
									}
									$strInsertValMesure = $strInsertValMesure . "(" .  ($idMesureCourant - 1) . "," . $tabValeurs[$tabIndiceColonne[$i][0]] . "," . $tabIndiceColonne[$i][1] . "),";
									$cptLignesDansRequete++; // TODO
								}
/*$sommeIteration += $cptIteration;
$nbCheck++;
$moyit = $sommeIteration/$nbCheck;
echo "Moyenne d'ité : $moyit ($nbCheck checks)<br>";*/
							}
							
							if($cptLignesDansRequete > 800 || $numLigneCourante == $nbTotalLigne)
							{
									// On ferme la requete avec un "" si on est à la fin du fichier ou si on à traité les 25 lignes (par defaut) 
									$strInsertValMesure[strlen($strInsertValMesure)-1] = ";";
									$strInsertMesure[strlen($strInsertMesure)-1] = ";";
								
									//echo " Req de mesure = $strInsertMesure <br>";
									//echo " Req de valmesure = $strInsertValMesure <br>";
										$timestart=microtime(true);
									if($strInsertMesure[strlen($strInsertMesure)-2] == ')'){ 
										$connection->query($strInsertMesure) or die(print_r($connection->errorInfo()));
										$connection->query($strInsertValMesure) or die(print_r($connection->errorInfo()));
									}
										//Affichage du chrono
										$timeend=microtime(true);
										$time=$timeend-$timestart;
										//Afficher le temps d'éxecution
										$page_load_time = number_format($time, 3);
										//echo "Requete #" . $numLigneCourante/25 ." executee en " . $page_load_time . " sec<br>";
										$sommeReq += $page_load_time;
										$nbReq++;
									$strInsertValMesure = "INSERT INTO valeurmesure (Mesure_idMesure, valeur, libval_idlibval) VALUES ";
									$strInsertMesure = "INSERT INTO mesure(date, idMesure, capteur_idcapteur) VALUES ";
									$cptLignesDansRequete = 0;
							}

 							$cptLignesImportees++;
						}
						// Affiche le pourcentage d'avancement (tout les 1%)
						$div = intval($nbTotalLigne/1000);
						if ($div == 0)
							$div = 1;
						if($numLigneCourante % $div == 0)
						{
							$avancement = $numLigneCourante/$nbTotalLigne*100;
							$avancement = number_format($avancement, 2); // On garde 2 décimales 
							echo '<script>
								document.getElementById("avancement").style.width = "' . $avancement . '%";
							</script>';
							//ob_flush();
							flush();
						}
					}
				}	
			}
			
			if($cptDoublon > 0)
				addError( "Warning: Nombre de lignes non importées (doublon) : $cptDoublon (sur $nbTotalLigne lignes)");
			
			
			//echo $strInsertValMesure;
			//Affichage du chrono
			$timeendTotal=microtime(true);
			$timeTotal=$timeendTotal-$timestartTotal;
			//Afficher le temps d'éxecution
			$page_load_timeTotal = number_format($timeTotal, 3);
			//echo "AVG par Req = " . $sommeReq/$nbReq . " sec<br>";
		
		}
		fclose($fp); // On ferme le fichier
	}
}
	if($readyForParsing) // Si le fichier a été parsé
		echo "<script>finParser($page_load_timeTotal,$cptLignesImportees,$nbTotalLigne);</script>";
	else // s'il n'as pas été parsé pour quelque raison que ce soit
		echo "<script>finParser(0,0,0);</script>";
?>


