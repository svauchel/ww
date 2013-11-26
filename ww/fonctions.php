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
 
function calcul_points($pro,$glu,$lip) {
	return(round($pro/11 + $glu/9  + $lip/4));
}
function calcul_quantite($pts,$pro,$glu,$lip) {
	return($pts / ($pro/11 + $glu/9  + $lip/4));
}
function verif_login($login,$password) {
	if ($login=="" || $password=="") {return false;}
	$sql="SELECT * FROM WW_USER WHERE login='$login' and password='$password'";
	$result=mysql_query($sql);
	$count=mysql_num_rows($result);
	if($count==1){
		$row = mysql_fetch_array($result);
		$_SESSION['user_id'] = $row[ID];
		$_SESSION['user_login'] = $row[LOGIN];
		$_SESSION['pts_day'] = $row[PTS_DAY];
		$_SESSION['pts_week'] = $row[PTS_WEEK];
		$_SESSION['reset_day'] = $row[RESET_DAY];
		$_SESSION['dayleft'] = 0;
		MAJ_var();
		return true;
	} else {
		return false;
	}
}
function MAJ_var() {
	$_SESSION['pts_day_dispo']  = $_SESSION['pts_day'] - calcul_points_today($_SESSION['user_id']);
	if ($_SESSION['pts_day_dispo']<0) {$_SESSION['pts_day_dispo']=0;}
	$_SESSION['pts_week_dispo'] = $_SESSION['pts_week'] - calcul_points_thisweek($_SESSION['user_id']);
}

function calcul_points_today($user_id) {
	$sql = "SELECT SUM(POINTS) AS PTS FROM `WW_MANGE` WHERE DT=CURDATE() AND USER_ID=$user_id";
	$result=mysql_query($sql);
	$count=mysql_num_rows($result);
	if($count==1){
		$row = mysql_fetch_array($result);
		return($row[PTS]);
	} else {
		return(0);
	}
}
function calcul_points_thisweek($user_id) {
	$cpt=0;
	$today = mktime(0, 0, 0, date("m") , date("d"), date("Y"));
	while(date('N',$today) != $_SESSION['reset_day']) {
		$cpt++;
		$today = mktime(0, 0, 0, date("m") , date("d")-$cpt, date("Y"));
	}
	$cpt++;
	$_SESSION['dayleft'] = 8-$cpt;
	$today = mktime(0, 0, 0, date("m") , date("d")-$cpt, date("Y"));
	$sql = "SELECT DT,SUM(POINTS) AS PTS FROM `WW_MANGE` WHERE DT>'".date('Y-m-d',$today)."' AND USER_ID=$user_id GROUP BY DT";
	$result=mysql_query($sql);
	$count=mysql_num_rows($result);
	if($count>0){
		$pts_more = 0;
		while($row = mysql_fetch_array($result)){
			if ($row[PTS] > $_SESSION['pts_day']) {$pts_more += $row[PTS] - $_SESSION['pts_day'];}
		}
		return($pts_more);
	} else {
		return(0);
	}
}
?>	