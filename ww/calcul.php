<?php session_start (); ?>
<?php require_once("connect.php"); ?>
<?php require_once("fonctions.php"); ?>
<?php
if ($_GET['id'] != $_SESSION['id']) {
	$id = $_GET['id'];
	$query = 'SELECT QUANTITE,CALORIES,PROTEINES,GLUCIDES,LIPIDES,POINTS FROM `WW_ALIM` WHERE ID = '.$id;
	$result = mysql_query($query) or die('Erreur SQL!'.$query.'<br>'.mysql_error());
	while($row = mysql_fetch_array($result)){
		$unite     = $row[QUANTITE];
		$calories  = $row[CALORIES];
		$proteines = $row[PROTEINES];
		$glucides  = $row[GLUCIDES];
		$lipides   = $row[LIPIDES];
		$points    = $row[POINTS];
		$_SESSION['id'] = $id;
		$_SESSION['unite'] = $unite;
		$_SESSION['calories'] = $calories;
		$_SESSION['proteines'] = $proteines;
		$_SESSION['glucides'] = $glucides;
		$_SESSION['lipides'] = $lipides;
		$_SESSION['points'] = $points;
	}
} else {
	$id = $_SESSION['id'];
	$unite = $_SESSION['unite'];
	$calories = $_SESSION['calories'];
	$proteines = $_SESSION['proteines'];
	$glucides = $_SESSION['glucides'];
	$lipides = $_SESSION['lipides'];
	$points = $_SESSION['points'];
}

$quantite = $_GET['quantite'];
if ($quantite!="") {
	if ($unite=="100 g" || $unite=="100 ml") {
		if ($proteines+$glucides+$lipides == 0) {
			echo ($points * $quantite / 100) ."/".round($quantite*$calories/100);
		} else {
			echo calcul_points($quantite*$proteines/100,$quantite*$glucides/100,$quantite*$lipides/100)."/".round($quantite*$calories/100);
		}
	} else {
		if ($proteines+$glucides+$lipides == 0) {
			echo ($points * $quantite) . "/".round($quantite*$calories);
		} else {
			echo calcul_points($quantite*$proteines,$quantite*$glucides,$quantite*$lipides)."/".round($quantite*$calories);
		}
	}
	return;
}

$points_dispo = $_GET['points_dispo'];
if ($points_dispo!="") {
	if ($unite=="100 g" || $unite=="100 ml") {
		$quantite = calcul_quantite($points_dispo,$proteines,$glucides,$lipides)*100;
		$quantite=round($quantite);
		while(calcul_points($quantite*$proteines/100,$quantite*$glucides/100,$quantite*$lipides/100)<=$points_dispo) {
			$quantite++;
		}
		$quantite--;
		echo $quantite."/".round($quantite*$calories/100);
	} else {
		$quantite = calcul_quantite($points_dispo,$proteines,$glucides,$lipides);
		$quantite=round($quantite);
		while(calcul_points($quantite*$proteines,$quantite*$glucides,$quantite*$lipides)<=$points_dispo) {
			$quantite++;
		}
		$quantite--;
		echo $quantite."/".round($quantite*$calories);
	}
	return;
}
?>
