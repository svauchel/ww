<?php 

//ici les parametres pour la connexion
   $host  = "sql.free.fr"; 
   $base  = "sebgege"; 
   $passe = "malibu"; 

//on effectue la connexion
   @mysql_connect("$host","$base","$passe");
 
//Selection de la base de données qui porte le meme nom que votre login
   $select_base=@mysql_selectdb("$base"); 

//Si la connexion echoue
   if (!$select_base) {echo "<font color=\"#CC0000\"><b>Echec de la connexion</b></font><br />";
	echo "Le serveur de l'hébergeur du site (Free) est probablement en panne.<br />";
	echo "Essayez de vous reconnecter plus tard.<br />";
   	exit(-1);
   	}
 
?>
