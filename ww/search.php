<?php require("connect.php"); ?>
<?php require 'jsonwrapper.php'; ?>
<?php
$query = 'SELECT ID,LIBELLE,UTILISATIONS FROM `WW_ALIM` WHERE LIBELLE LIKE \'%' . str_replace(' ','%',addslashes(utf8_decode($_GET['term']))) . '%\' ORDER BY `UTILISATIONS` DESC,`LIBELLE`';
$result = mysql_query($query) or die('Erreur SQL!'.$query.'<br>'.mysql_error());
while($row = mysql_fetch_array($result)){
	$tab[$row[ID]]['id'] =  $row[ID];
	$tab[$row[ID]]['value'] = utf8_encode($row[LIBELLE]);
}

echo json_encode($tab);
?>