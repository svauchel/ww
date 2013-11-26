<?php session_start (); ?>
<?php require_once("fonctions.php"); ?>
<!DOCTYPE html>
<html>
    <head>
		<meta charset="UTF-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="initial-scale=1.0">
		<title>WW</title>
        <link rel="stylesheet" type="text/css" href="jquery-ui-git.css" />
        <link rel="stylesheet" type="text/css" href="style.css" />
        <!-- Internet Explorer HTML5 enabling code: -->
        <!--[if IE]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <style type="text/css">
        .clear {
          zoom: 1;
          display: block;
        }
        </style>
        <![endif]-->
		<script type="text/javascript" src="jquery.min.js"></script>
		<script src="jquery-ui.min.js"></script>
		<script>
		$(function() {
			$( "#log" ).hide();
			$( "#log" ).val("");

			function log( message ) {
				$( "#log" ).html(message);
				$( "#log" ).show();
			}

			$( "#aliment" ).autocomplete({
				source: "search.php",
				minLength: 3,
				select: function( event, ui ) {
					$( "#id_aliment" ).val(ui.item.id);
			}
			});

			$("#recherche_aliment").bind("submit", function(){
				var bReturn = true;
				if ( jQuery.trim($("#aliment").val()).length < 3 ) {
					bReturn = false;
					$("#aliment").css({border: "2px solid red"});
					log('Vous devez saisir au moins 3 caractÃ¨res pour lancer la recherche');
				}
				if ( $("#id_aliment").val()=="" && bReturn) {
					bReturn = false;
					$("#aliment").css({border: "2px solid red"});
					log('Vous devez s&eacute;lectionner un aliment pour continuer');
				}
				return bReturn;
			});
			
			$('#quantite').change(function() {
				var aliment_id = $("#aliment_id").val(); 
				var quantite = $("#quantite").val(); 
				var request = $.ajax({
					url: "calcul.php",
					type: "GET",
					data: "id="+aliment_id+"&quantite="+quantite
				});
				request.done(function(msg) {
					var tab_retour = msg.split('/');
					$("#points").val(parseInt(tab_retour[0]));
					$("#calories").html("("+tab_retour[1]+" calories)");
					$("#calories_num").val(tab_retour[1]);
				});
			});
			
			$('#points').change(function() {
				var aliment_id = $("#aliment_id").val(); 
				var points = $("#points").val(); 
				var request = $.ajax({
					url: "calcul.php",
					type: "GET",
					data: "id="+aliment_id+"&points_dispo="+points
				});
				request.done(function(msg) {
					var tab_retour = msg.split('/');
					$("#quantite").val(parseInt(tab_retour[0]));
					$("#calories").html("("+tab_retour[1]+" calories)");
					$("#calories_num").val(tab_retour[1]);
				});
			});			

			$("#saisie_aliment").bind("submit", function(){
				var bReturn = true;
				$('input[name^="u_"]').css({border:""});
				if ( $("#u_libelle").val() == "" ) {
					bReturn = false;
					$("#u_libelle").css({border: "2px solid red"});
					log('Tous les champs sont obligatoires');
				}
				if ( $("#u_calories").val() == "" ) {
					bReturn = false;
					$("#u_calories").css({border: "2px solid red"});
					log('Tous les champs sont obligatoires');
				}
				if ( $("#u_proteines").val() == "" ) {
					bReturn = false;
					$("#u_proteines").css({border: "2px solid red"});
					log('Tous les champs sont obligatoires');
				}				
				if ( $("#u_glucides").val() == "" ) {
					bReturn = false;
					$("#u_glucides").css({border: "2px solid red"});
					log('Tous les champs sont obligatoires');
				}
				if ( $("#u_lipides").val() == "" ) {
					bReturn = false;
					$("#u_lipides").css({border: "2px solid red"});
					log('Tous les champs sont obligatoires');
				}
				if ( $("#u_points").val() == "" ) {
					bReturn = false;
					$("#u_points").css({border: "2px solid red"});
					log('Tous les champs sont obligatoires');
				}
				return bReturn;
			});

			$('input[name^="u_"]').change(function() {
				var val_pro = $("#u_proteines").val();
				var val_glu = $("#u_glucides").val();
				var val_lip = $("#u_lipides").val();
				var val_points = val_pro/11 + val_glu/9 + val_lip/4;
				$("#uu_points").val(Math.round(val_points));
			});			

			$('#tab_hier').hide();
			$('#aff_hier').click(function() {
				$('#tab_hier').toggle();
				return false;
			});

			$('#aff_today').click(function() {
				$('#tab_today').toggle();
				return false;
			});

			$('#tab_demain').hide();
			$('#aff_demain').click(function() {
				$('#tab_demain').toggle();
				return false;
			});
		});
		</script>
	</head>
	<body>
		<div id="log"></div>
		<div class="ui-widget">
		<?php $task = $_POST['task']; ?>
		<?php if ($task=="") {$task=$_GET['task'];} ?>

		<!-------------------------------------------------------------------------------------------------------------------------->
		<?php if ($task=="deconnexion") {
			session_unset();
			session_destroy();
			$task="";
		}
		?>

		<!-------------------------------------------------------------------------------------------------------------------------->
		<?php if ($task=="connexion") {
			if (!verif_login($_POST['login'],$_POST['password'])) {echo 'Erreur de connexion';}
			$task="";
		}
		?>

		<!-------------------------------------------------------------------------------------------------------------------------->
		<?php if ($task=="courbe_poids") { 
		
			// construction du tableau de données
			//	[Date.UTC(2013,  3, 5),  63.5],
			//  [Date.UTC(2013,  3, 12), 61.0]
			$tab_poids ="";
			$sql = "SELECT DT,POIDS FROM `WW_PESE` WHERE USER_ID=".$_SESSION['user_id']." ORDER BY DT";
			$result=mysql_query($sql);
			$count=mysql_num_rows($result);
			if($count>0){
				while($row = mysql_fetch_array($result)){
					if ($tab_poids!="") {$tab_poids.=", ";}
					$tab_poids .= '[Date.UTC(';
					$tab_poids .=  date("Y",strtotime($row[DT]));
					$tab_poids .= ", ";
					$tab_poids .= date("n",strtotime($row[DT])) - 1;
					$tab_poids .= ", ";
					$tab_poids .= date("j",strtotime($row[DT]));
					$tab_poids .= "), " . $row[POIDS] . "]";
				}
			}
		?>
			<script src="http://code.highcharts.com/highcharts.js"></script>
			<div class="fond">
			<div id="container" style="min-width: 400px; height: 400px; width:90%;border:1px solid #ccc;margin: 0 auto"></div>		
		<script>
		$(function () {
        $('#container').highcharts({
            chart: {
                type: 'line'
            },
            title: {
                text: 'Courbe de poids'
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: { // don't display the dummy year
                    month: '%e. %b',
                    year: '%b'
                }
            },
            yAxis: {
                title: {
                    text: 'poids'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: [{
                name: '<?php echo $_SESSION['user_login']; ?>',
	            data: [
					<?php echo $tab_poids; ?>
				]
            }]
        });
    });
		</script>
		</div>
		<p style="text-align:center;width:100%;"><a href="http://sebgege.free.fr/ww"><< Retour Ã  l'accueil</a></p></div></body></html>
		<?php return; } ?>
		
		<!-------------------------------------------------------------------------------------------------------------------------->
		<?php if ($task=="mange_aliment") {
			if ($_POST['demain']) {
				// insertion en base : WW_MANGE
				$query = "INSERT INTO `WW_MANGE` (USER_ID, LIBELLE, QUANTITE, POINTS, CALORIES, DT) SELECT ".$_SESSION['user_id'].",WW_ALIM.LIBELLE,'".$_POST['quantite']."',".$_POST['points'].",".$_POST['calories_num'].",ADDDATE(CURDATE(), INTERVAL 1 DAY) FROM WW_ALIM WHERE ID=".$_POST['aliment_id'];
				$result = mysql_query($query) or die('Erreur SQL!'.$query.'<br>'.mysql_error());
			} else {
				// insertion en base : WW_MANGE
				$query = "INSERT INTO `WW_MANGE` (USER_ID, LIBELLE, QUANTITE, POINTS, CALORIES, DT) SELECT ".$_SESSION['user_id'].",WW_ALIM.LIBELLE,'".$_POST['quantite']."',".$_POST['points'].",".$_POST['calories_num'].",CURDATE() FROM WW_ALIM WHERE ID=".$_POST['aliment_id'];
				$result = mysql_query($query) or die('Erreur SQL!'.$query.'<br>'.mysql_error());
			}
			MAJ_var();
			
			// MAJ de l'aliment mangé (compteur)
			$query = "UPDATE `WW_ALIM` SET UTILISATIONS=UTILISATIONS+1 WHERE ID=".$_POST['aliment_id'];
			$result = mysql_query($query) or die('Erreur SQL!'.$query.'<br>'.mysql_error());
			
			$task="";
		}

		if ($task=="supp_mange") {
				$sql = "DELETE FROM `WW_MANGE` WHERE ID=".$_POST['supp_id'];
				$result=mysql_query($sql);

				// MAJ des variables de session
				MAJ_var();
				$task="";
			}
		?>

		<!-------------------------------------------------------------------------------------------------------------------------->
		<?php if ($task=="pesee") {
			$sql = "DELETE FROM `WW_PESE` WHERE DT=CURDATE() AND USER_ID=".$_SESSION['user_id'];
			$result=mysql_query($sql);

			$query = "INSERT INTO `WW_PESE` (USER_ID, DT, POIDS) VALUES (".$_SESSION['user_id'].",CURDATE(),".$_POST['poids'].")";
			$result = mysql_query($query) or die('Erreur SQL!'.$query.'<br>'.mysql_error());
			$task="";
		} ?>
		
		<!-------------------------------------------------------------------------------------------------------------------------->
		<?php if ($task=="") { ?>
			<h1>Accueil</h1>
			<div class="fond">
			<form action="#" name="recherche_aliment" id="recherche_aliment" METHOD="POST">
			<label for="aliment">Aliment : </label>
			<input type="text" size="30" id="aliment" name="aliment" autocorrect="off" autocapitalize="off" autocomplete="off" />
			<input type="hidden" id="id_aliment" name="id_aliment" />
			<input type="hidden" id="task" name="task" value="cherche_infos" />
			<input type="submit" value="OK" />
			</form>
			</div>
			<p><center><a href="index.php?task=saisie_aliment">Ajouter un aliment</a></center></p>
			<p><center><a href="index.php?task=calculatrice">Calculatrice</a></center></p>
		<?php } ?>
		
		<!-------------------------------------------------------------------------------------------------------------------------->
		<?php if ($task=="cherche_infos") { ?>
			<?php
				$id = $_POST['id_aliment'];
				if ($id=="") {$id = $_GET['id_aliment'];}
				$query = 'SELECT LIBELLE,QUANTITE,CALORIES,GLUCIDES,LIPIDES,PROTEINES,POINTS FROM `WW_ALIM` WHERE ID = '.$id;
				$result = mysql_query($query) or die('Erreur SQL!'.$query.'<br>'.mysql_error());
				while($row = mysql_fetch_array($result)){
					$libelle   = $row[LIBELLE];
					$unite     = $row[QUANTITE];
					$calories  = $row[CALORIES];
					$proteines = $row[PROTEINES];
					$glucides  = $row[GLUCIDES];
					$lipides   = $row[LIPIDES];
					$points    = $row[POINTS];
				}
			?>
			<?php echo '<h1>'.utf8_encode($libelle).'</h1>'; ?>
			<div class="fond">
			<?php echo '<form action="#" METHOD="POST"><input type="hidden" name="aliment_id" id="aliment_id" value="'.$id.'" /><input type="hidden" name="libelle" id="libelle" value="'.utf8_encode($libelle).'" />'; ?>
			<table>
				<tr><th>Unit&eacute;</th><th>Calories</th><th>Prot&eacute;ines</th><th>Glucides</th><th>Lipides</th><th>Points</th></tr>
				<tr><td><?php echo $unite; ?></td><td><?php echo $calories; ?></td><td><?php echo $proteines; ?></td><td><?php echo $glucides; ?></td><td><?php echo $lipides; ?></td><td><b><?php echo $points; ?></b></td></tr>
			</table>
			<p><center><a href="index.php?task=saisie_aliment&id_aliment=<?php echo $id; ?>">Modifier cet aliment</a></center></p>
				<label for="quantite">Quantit&eacute; : </label>
				<input type="text" size="4" id="quantite" name="quantite" />
				<?php
					switch($unite) {
					case "100 g":
						echo 'g';
						break;
					case "1":
						echo 'unit&eacute;(s)';
						break;
					case "100 ml":
						echo 'ml';
						break;
					case "c.c.":
						echo 'c.c.';
						break;
					default:
						echo '?';
					}
				?>
				<br/><img src="fleche.png" alt="->" /><br/>
				<label for="points">Points : </label>
				<input type="text" size="4" class="aff_result" id="points" name="points" />
				<br/>
				<p id="calories" name="calories"></p>
				<input type="hidden" id="calories_num" name="calories_num" />
				<?php if ($_SESSION['user_id']!="") { ?>
					<input type="hidden" id="task" name="task" value="mange_aliment" />
					<label for="demain">A comptabiliser pour demain </label><input type="checkbox" id="demain" name="demain" /><br/>
					<input type="submit" value="C'est mang&eacute; !" />
				<?php } ?>
				</form>
				</div>
		<?php } ?>

		<!-------------------------------------------------------------------------------------------------------------------------->
		<?php if ($task=="saisie_aliment") { ?>
			<h1>Saisie d'un aliment</h1>
			<div class="fond">
			<?php
				$modif=0;
				$id = $_GET['id_aliment'];
				if ($id!="") {
					$modif=1;
					$query = 'SELECT LIBELLE,QUANTITE,CALORIES,GLUCIDES,LIPIDES,PROTEINES,POINTS FROM `WW_ALIM` WHERE ID = '.$id;
					$result = mysql_query($query) or die('Erreur SQL!'.$query.'<br>'.mysql_error());
					while($row = mysql_fetch_array($result)){
						$libelle   = $row[LIBELLE];
						$unite     = $row[QUANTITE];
						$calories  = $row[CALORIES];
						$proteines = $row[PROTEINES];
						$glucides  = $row[GLUCIDES];
						$lipides   = $row[LIPIDES];
						$points    = $row[POINTS];
					}
				}
			?>
			<form action="#" id="saisie_aliment" name="saisie_aliment" METHOD="POST">
			<?php echo '<input type="hidden" name="u_id" id="u_id" value="'.$id.'" />'; ?>
			<?php echo '<input type="hidden" name="u_modif" id="u_modif" value="'.$modif.'" />'; ?>
			Libell&eacute; : <input type="text" id="u_libelle" name="u_libelle" size="24" value="<?php echo utf8_encode($libelle); ?>" /><br/>
			Unit&eacute; : <select name="u_unite" id="u_unite">
						<option value="100 g" <?php if ($unite=="100 g") {echo ' selected';} ?>>100 g</option>
						<option value="100 ml" <?php if ($unite=="100 ml") {echo ' selected';} ?>>100 ml</option>
						<option value="1" <?php if ($unite=="1") {echo ' selected';} ?>>1</option>
						<option value="c.c." <?php if ($unite=="c.c.") {echo ' selected';} ?>>c.c.</option>
					</select><br/>
			Calories : <input type="text" id="u_calories" name="u_calories" size="5" value="<?php echo $calories; ?>" /><br/>
			Prot&eacute;ines : <input type="text" id="u_proteines" name="u_proteines" size="5" value="<?php echo $proteines; ?>" /><br/>
			Glucides : <input type="text" id="u_glucides" name="u_glucides" size="5" value="<?php echo $glucides; ?>" /><br/>
			Lipides : <input type="text" id="u_lipides" name="u_lipides" size="5" value="<?php echo $lipides; ?>" /><br/><br/>
			Points : <input type="text" id="uu_points" name="uu_points" size="5" value="<?php echo $points; ?>" /><br/>
			<input type="hidden" id="task" name="task" value="save_aliment" />
			<input type="submit" value="Enregistrer" />
			</form>
			</div>
		<?php } ?>

		<!-------------------------------------------------------------------------------------------------------------------------->
		<?php if ($task=="calculatrice") { ?>
			<h1>Calculatrice</h1>
			<div class="fond">
			<form action="#" id="saisie_aliment" name="saisie_aliment" METHOD="POST">
			Prot&eacute;ines : <input type="text" id="u_proteines" name="u_proteines" size="5" value="<?php echo $proteines; ?>" /><br/>
			Glucides : <input type="text" id="u_glucides" name="u_glucides" size="5" value="<?php echo $glucides; ?>" /><br/>
			Lipides : <input type="text" id="u_lipides" name="u_lipides" size="5" value="<?php echo $lipides; ?>" /><br/><br/>
			Points : <input type="text" id="uu_points" name="uu_points" size="5" value="<?php echo $points; ?>" /><br/>
			</form>
			</div>
		<?php } ?>

		<!-------------------------------------------------------------------------------------------------------------------------->
		<?php if ($task=="save_aliment") { ?>
			<h1>Sauvegarde en cours...</h1>
			<?php
				$id = $_POST['u_id'];
				$modif = $_POST['u_modif'];
				$libelle = utf8_decode($_POST['u_libelle']);
				$unite = $_POST['u_unite'];
				$calories = $_POST['u_calories'];
				$proteines = $_POST['u_proteines'];
				$glucides = $_POST['u_glucides'];
				$lipides= $_POST['u_lipides'];
				$points= $_POST['uu_points'];

				if ($modif==1) {
					$query = "UPDATE `WW_ALIM` SET LIBELLE='$libelle', QUANTITE='$unite', CALORIES=$calories, PROTEINES=$proteines, GLUCIDES=$glucides, LIPIDES=$lipides, POINTS=$points  WHERE ID=$id";
					$result = mysql_query($query) or die('Erreur SQL!'.$query.'<br>'.mysql_error());
				} else {
					$query = "INSERT INTO `WW_ALIM` (LIBELLE, QUANTITE, CALORIES, PROTEINES, GLUCIDES, LIPIDES, POINTS, UTILISATIONS) values ('$libelle','$unite',$calories,$proteines,$glucides,$lipides,$points,0)";
					$result = mysql_query($query) or die('Erreur SQL!'.$query.'<br>'.mysql_error());
					$id = mysql_insert_id();
				}
			?>
			<div class="fond">
				<p style="text-align:center;width:100%;">
					Aliment enregistr&eacute;<br/>
					<a href="index.php?task=cherche_infos&id_aliment=<?php echo $id; ?>">Acc&eacute;der &agrave; cet aliment</a>			
				</p>
			</div>
		<?php } ?>

		<!-------------------------------------------------------------------------------------------------------------------------->
		<?php if ($_SESSION['user_id']!="") { ?>
			<div id="user">
			<?php if (date('N')==$_SESSION['reset_day']) { ?>
				<form action="#" METHOD="POST">
				<label for="poids">Pes&eacute;e : </label>
				<input type="text" size="4" class="aff_result" id="poids" name="poids" /> Kg
				<input type="hidden" id="task" name="task" value="pesee" />
				<input type="submit" value="OK" />
				</form><br/>
			<?php } ?>
			<?php 
				if ($_SESSION['pts_day_dispo'] < 0) {$couleur="color:red;";}
				echo '<p style="'.$couleur.'">' . $_SESSION['user_login'].' : il te reste ' . $_SESSION['pts_day_dispo'] . ' pts ce jour et ' . $_SESSION['pts_week_dispo'] . ' pts semaine (pour '.$_SESSION['dayleft'].' jours)</p>';
			?>

		<?php
			$sql = "SELECT ID, LIBELLE, QUANTITE, POINTS FROM `WW_MANGE` WHERE DT=ADDDATE(CURDATE(), INTERVAL -1 DAY) AND USER_ID=".$_SESSION['user_id'];
			$result=mysql_query($sql);
			$count=mysql_num_rows($result);
			if($count>0){
				echo "<br/><p><a id=\"aff_hier\" href=\"#\">Hier</a></p><table id=\"tab_hier\">";
				echo "<tr><th width=\"60%\">Aliment</th><th>Quantit&eacute;</th><th>Pts</th><th style=\"background-color:#f9f9f9;border:0px !important;\"></th></tr>";
				while($row = mysql_fetch_array($result)){
					$supp = "<form action=\"#\" METHOD=\"POST\"><input type=\"hidden\" id=\"supp_id\" name=\"supp_id\" value=\"".$row[ID]."\" /><input type=\"hidden\" id=\"task\" name=\"task\" value=\"supp_mange\" /><input style=\"color:red;font-weight:bold;\" type=\"submit\" value=\"X\" /></form>";
					echo "<tr><td>".utf8_encode($row[LIBELLE])."</td><td>$row[QUANTITE]</td><td>$row[POINTS]</td><td>$supp</td></tr>";
				}
				echo "</table>";
			}

			$sql = "SELECT ID, LIBELLE, QUANTITE, POINTS FROM `WW_MANGE` WHERE DT=CURDATE() AND USER_ID=".$_SESSION['user_id'];
			$result=mysql_query($sql);
			$count=mysql_num_rows($result);
			if($count>0){
				echo "<br/><p><a id=\"aff_today\" href=\"#\">Aujourd'hui</a></p><table id=\"tab_today\">";
				echo "<tr><th width=\"60%\">Aliment</th><th>Quantit&eacute;</th><th>Pts</th><th style=\"background-color:#f9f9f9;border:0px !important;\"></th></tr>";
				while($row = mysql_fetch_array($result)){
					$supp = "<form action=\"#\" METHOD=\"POST\"><input type=\"hidden\" id=\"supp_id\" name=\"supp_id\" value=\"".$row[ID]."\" /><input type=\"hidden\" id=\"task\" name=\"task\" value=\"supp_mange\" /><input style=\"color:red;font-weight:bold;\" type=\"submit\" value=\"X\" /></form>";
					echo "<tr><td>".utf8_encode($row[LIBELLE])."</td><td>$row[QUANTITE]</td><td>$row[POINTS]</td><td>$supp</td></tr>";
				}
				echo "</table>";
			}

			$sql = "SELECT ID, LIBELLE, QUANTITE, POINTS FROM `WW_MANGE` WHERE DT=ADDDATE(CURDATE(), INTERVAL 1 DAY) AND USER_ID=".$_SESSION['user_id'];
			$result=mysql_query($sql);
			$count=mysql_num_rows($result);
			if($count>0){
				echo "<br/><p><a id=\"aff_demain\" href=\"#\">Demain</a></p><table id=\"tab_demain\">";
				echo "<tr><th width=\"60%\">Aliment</th><th>Quantit&eacute;</th><th>Pts</th><th style=\"background-color:#f9f9f9;border:0px !important;\"></th></tr>";
				while($row = mysql_fetch_array($result)){
					$supp = "<form action=\"#\" METHOD=\"POST\"><input type=\"hidden\" id=\"supp_id\" name=\"supp_id\" value=\"".$row[ID]."\" /><input type=\"hidden\" id=\"task\" name=\"task\" value=\"supp_mange\" /><input style=\"color:red;font-weight:bold;\" type=\"submit\" value=\"X\" /></form>";
					echo "<tr><td>".utf8_encode($row[LIBELLE])."</td><td>$row[QUANTITE]</td><td>$row[POINTS]</td><td>$supp</td></tr>";
				}
				echo "</table>";
			}

		?>		
			<br/>
			<p style="text-align:center;width:100%;"><a href="index.php?task=courbe_poids">Voir ma courbe de poids</a></p>
			<br/>

			<form action="#" name="pave_connexion" id="pave_connexion" METHOD="POST">
			<input type="hidden" id="task" name="task" value="deconnexion" />
			<input type="submit" value="Se d&eacute;connecter" />
			</form>
		</div>
		<?php } else { if ($task=="") {?>
			<div id="user">
			<form action="#" name="pave_connexion" id="pave_connexion" METHOD="POST">
				<label for="login">Login : </label>
				<input type="text" size="9" class="aff_result" id="login" name="login" />
				<br/>
				<label for="password">Password : </label>
				<input type="password" size="9" id="password" name="password" />
				<br/>
				<input type="hidden" id="task" name="task" value="connexion" />
				<input type="submit" value="Se connecter" />
			</form>
			</div>
		<?php } } ?>
		
		<!-------------------------------------------------------------------------------------------------------------------------->
		<div id="footer">
			<br/>
			<?php if ($task!="") { ?><a href="http://sebgege.free.fr/ww"><< Retour Ã  l'accueil</a><?php } ?>
		</div>

		</div>
	</body>
</html>
