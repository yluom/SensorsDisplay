<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

if (!$fp = fopen("file.txt","r")) {
  echo "Echec de l'ouverture du fichier";
  exit;
} else {

// test stuff

// end test stuff









 $i = 0;
 $tabvars = null;
 $tabvals = null;
 
 while(!feof($fp)) {
 
 // On récupère une ligne
  $ligne = fgets($fp,1024);
  $ligne = str_replace('  ', ' ', $ligne);  // on supprime les espaces en trop
  $ligne = trim($ligne);
 // echo "<br>Ligne $i : $ligne<br><br>";
   echo "<br> <br>";
  if($ligne == 0) // premiere ligne: on recup les libvariables
  {
      $tabvars = explode(' ', $ligne); 
      $tabvars = array_filter($tabvars);
      $tabvars = array_values($tabvars);
      var_dump($tabvars);
  } else {
   
    $tabvals = explode(' ', $ligne);
    $tabvals = preg_replace('/^0$/', '0.00', $tabvals); // remplace les '0' par '0.00' afin de ne pas etre supprimé par array_filter
    $tabvals = array_filter($tabvals); // Enlève les valeurs vides du tableau ('' et '0')
    $tabvals = array_values($tabvals); // Remet à jour les clés(index) du tableau
    var_dump($tabvals);
   
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
   $tab[$i] = $tabvals;
  
    
  }
    
    
  $i += 1;

 }
 echo "-----------------------------------------------------------------";
 print_r($tab);
 fclose($fp); // On ferme le fichier
}

?>