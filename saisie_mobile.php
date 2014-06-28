<?php

require_once($_SERVER['DOCUMENT_ROOT'] .'/dev_revoltesj/_conf/DEV_J.php');
echo $control;
/* LISTE DES GARES */

// Liste des gares
$liste_gare = $connexion->prepare('
		SELECT DISTINCT(libelle_point_arret) as libelle_point_arret, left(code_uic,7) as code_uic
		FROM s_gares_officiel
		WHERE j = 1
		ORDER BY libelle_point_arret ASC
		
	');

$liste_gare->execute();
	while ($enregistrement = $liste_gare->fetch())
		{
			$liste[] = "<option value='".$enregistrement["code_uic"]."'>".$enregistrement["libelle_point_arret"]."</option>\n";
		}
	//print_r($liste_gare->errorInfo());
	$liste_gare->closeCursor();
	
/* Validation du formulaire */
/*
foreach($_POST as $key => $val) 
	{
		echo $key .'=>'. $val .'<br>';
	}		
*/
// Rechercher les trains correspondant
$l = explode('/',$_POST['date']);
$date = $l[0].$l[1].$l[2];
//$date = $l[2].$l[1].$l[0];
//echo 'date : '.$date;
$heure = $_POST['h_debut'];
//echo 'heure depart :'.$heure;
$heure1 = $_POST['h_fin'];
//echo 'heure fin :'.$heure1;
$control =0;
$j = date('w',strtotime($l[2].'-'.$l[1].'-'.$l[0]));
$gare_depart = 'StopPoint:DUA'.substr($_POST['gare_depart'],0,7);
//echo "gare_depart : ".$gare_depart;
switch($j)
	{
		case 0:
			$rj = 'AND s_calendar.sunday = 1';
			break;
		case 1:
			$rj = 'AND s_calendar.monday = 1';
			break;
		case 2:
			$rj = 'AND s_calendar.tuesday = 1';
			break;
		case 3:
			$rj = 'AND s_calendar.wednesday = 1';
			break;
		case 4:
			$rj = 'AND s_calendar.thursday= 1';
			break;
		case 5:
			$rj = 'AND s_calendar.friday= 1';
			break;
		case 6:
			$rj = 'AND s_calendar.saturday= 1';
			break;
	
	
	}
if($_POST['gare_depart'] != '')
	{
	$control =1;
	$sql_searchTrains = $connexion->prepare('
	
			SELECT *
			FROM s_stop_times
			INNER JOIN s_trips ON s_trips.trip_id = s_stop_times.trip_id
			INNER JOIN s_routes ON s_routes.route_id = s_trips.route_id 
			INNER JOIN s_calendar ON s_calendar.service_id = s_trips.service_id
			INNER JOIN s_stops ON s_stop_times.stop_id = s_stops.stop_id
			WHERE s_stop_times.stop_id = :depart
			AND departure_time >= :h_dep
			AND departure_time <= :h_fin
			AND s_calendar.start_date >= :date
			AND s_calendar.end_date >= :date
			
			'.$rj.'			
			#GROUP BY s_stop_times.departure_time		
			ORDER BY departure_time ASC

		');

		$sql_searchTrains ->execute(array(
								':depart' => $gare_depart,
								':h_dep' => $heure,
								':h_fin' => $heure1,
								':date' => $date,
								
								
								
								)
						);

		while ($enregistrement = $sql_searchTrains ->fetch())
				{
				
					$tab_horaires[] = $enregistrement;
				}
		//print_r($sql_searchTrains ->errorInfo());
		//echo $sql_searchTrains->rowCount();
		$sql_searchTrains ->closeCursor();

	//controler si le train passe par un point ou non
	
	foreach($tab_horaires as $t)
		{
			if($_POST['gare_arrivee'] != '')
				{
					$gare_arrived = 'StopPoint:DUA'.substr($_POST['gare_arrivee'],0,7);
					$cpp = $connexion->prepare('
						SELECT * 
						FROM s_stop_times
						WHERE trip_id = :trip
						AND stop_id = :stop_end
						AND departure_time >= :departure_time
					');
				
					$cpp->execute(array(':trip' => $t['trip_id'] ,':stop_end' => $gare_arrived, ':departure_time' => $t['departure_time']));
				
					
					
					if($cpp->rowCount() !=0)
						{
							//echo $t['trip_id'].'=====>';
							$tab_horaires2[] = $t;
						}
					$cpp->closeCursor();
				} else {
					$tab_horaires2[] = $t;
				}
		}
	
	

	}
	
/* */
if($_GET['control'] == 2)	{ $control =2; }

if($_POST['send'] == "Signaler")
	{
		
		$send = $connexion->prepare('
	
	
	INSERT INTO `lignej_blog`.`s_incidents` (
		`annule` ,
		`jaime` ,
		`jaimepas`,
		mission,
		heure_train,
		stationdep,
		date_train
		)
		VALUES (:annule, :aime, :aimepas, :mission, :heuretrain, :stationdep, :date_train)
		
		');	
		if($_POST['train_supprime'] == '') { $ts = 0; } else { $ts =1;}
	$send->bindValue(':annule', $ts, PDO::PARAM_INT);
	$send->bindValue(':aime', $_POST['jaime'], PDO::PARAM_STR);
	$send->bindValue(':aimepas', $_POST['jaimepas'], PDO::PARAM_STR);
	$send->bindValue(':mission', $_POST['mission'], PDO::PARAM_STR);
	$send->bindValue(':heuretrain', $_POST['heuretrain'], PDO::PARAM_STR);
	$send->bindValue(':stationdep', $_POST['stationdep'], PDO::PARAM_STR);
	$send->bindValue(':date_train', $_POST['date_train'], PDO::PARAM_STR);
	$send->execute();
	$send->closeCursor();
	$control =3;
	
	}
	

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="escargot_detoure48px.ico" rel="shortcut icon" />

<!-- feuille de style pour petit ecran -->
<!--<link rel="stylesheet" media="screen and (max-width:800px)" href="revoltes_smartphone.css" type="text/css" />-->
<!-- feuille de style pour ecran -->
<link rel="stylesheet" media="screen" href="revoltes_saisie.css" type="text/css" />
<script src="revoltes_script.js" type="text/javascript" ></script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
select,
ol,
label {
font-size:40px;
}
</style>
<title>
Signalement d'un incident
</title>
</head>

<body>
<div class="conteneur">
<a href="http://www.revoltes-lignej.fr/"><img src="http://revoltes-lignej.fr/wp-content/uploads/2014/05/cropped-cropped-Nouveau_visueljournalGroupe.jpg" class="header-image" width="80%" alt="l'escargot des révoltés!" /></a>
<h2>Signaler un retard ou une suppression de train.</h2>
<p> Mode d'emploi :
<ol>

<li id="control0" class="<?php if($control == 0) {  echo "en_cours"; } else { echo "inactif"; } ?>" >Sélectionnez votre trajet, toutes les informations sont obligatoires
</li>
<li id="control1" class="<?php if($control == 1) {  echo "en_cours"; } else { echo "inactif"; } ?>">Complétez le signalement et témoignez de l'incident
</li>
<!--<li id="contrlol2" class="<?php //if($control == 2) {  echo "en_cours"; } else { echo "inactif"; } ?>">Dans le dernier formulaire, renseignez s'il s'agit d'une suppression, les points négatifs et positifs
</li>-->
</ol>
</p>


<!--<div name="" class="gare">
Quelle ligne&nbsp;?&nbsp;
<select name="ligne" size="1">
<option value="A">A</option>
<option value="J">J</option>
<option value="L">L</option>
</select>
</div>-->

<?php if($control ==0) { ?>


<div name="recherche" class="bloc_recherche">
 <form action="saisie_mobile.php" method="POST">
 <div id="rech_date_heure" class="recherche">
  <div name="rech_date" class="recherche_ligne">
   <label for="date" >Date</label><br />
   <select name="date" id="date">
<!-- affichage des 15 derniers jours -->
   <?php
    $cpt=0;
    while ($cpt > -15)
		{
		$dateprecedente = date("Y/m/d",strtotime("+$cpt days"));
		echo "<option value=\"".$dateprecedente."\">".$dateprecedente."</option>\n";
		$cpt--;
		}
   ?>
   </select>
  </div>

 <div name="rech_heure" class="recherche_ligne">
   <label for="plage_horaire">Plage horaire</label><br />
   <select name="plage_horaire" id="plage_horaire" onchange="javascript:calcule_heures();">
    <option value="00">00:00 - 05:00</option>
    <option value="05">05:01 - 06:00</option>
    <option value="06">06:01 - 07:00</option>
    <option value="07">07:01 - 08:00</option>
    <option value="08">08:01 - 09:00</option>
    <option value="09">09:01 - 10:00</option>
    <option value="10">10:01 - 11:00</option>
    <option value="11">11:01 - 12:00</option>
    <option value="12">12:01 - 13:00</option>
    <option value="13">13:01 - 14:00</option>
    <option value="14">14:01 - 15:00</option>
    <option value="15">15:01 - 16:00</option>
    <option value="16">16:01 - 17:00</option>
    <option value="17">17:01 - 18:00</option>
    <option value="18">18:01 - 19:00</option>
    <option value="19">19:01 - 20:00</option>
    <option value="20">20:01 - 21:00</option>
    <option value="21">21:01 - 23:59</option>
   </select>
<input type="hidden" name="h_debut" id="h_debut" />
<input type="hidden" name="h_fin" id="h_fin" />

  </div>
  </div>
  <div name="rech_gare_dep" class="recherche">
   <label for="gare_depart">Gare de départ</label>&nbsp;
   <SELECT name="gare_depart" id="gare_depart">
    <option value="" selected>choisissez votre gare de départ</option>
    <?php 
      foreach($liste as $l)
	 {
	 	echo $l;
	 }	
    ?>
   </SELECT>
  </div>
  <div name="rech_gare_arr" class="recherche">
  <label for="gare_arrivee">Gare d'arrivée</label>&nbsp;
   <SELECT name="gare_arrivee" id="gare_arrivee">
    <option value="" selected>choisissez votre gare d'arrivée</option>
    <?php 
     foreach($liste as $l)
	  {
		echo $l;
	  }	
    ?>
   </SELECT>
  </div>
  <div name="rech_submit" class="recherche" style="margin-top:20px">
  <input type="submit" value="Rechercher mon train" class="bouton" >
  </div>
 </form>
</div>

<?php } else if($control == 1) { ?>


<table align="center" class="restit">
<tr>
	<td>Mission</td>
	<td>Heure Passage</td>
	<td>Destination</td>
	<td>Actions</td>
</tr>
<?php
foreach($tab_horaires2 as $time)
	{
		echo  '<tr>';
		echo  '<td>'.$time['trip_headsign'].'</td>';
		echo  '<td>'.$time['departure_time'].'</td>';
		
		switch(substr($time['trip_headsign'], 0, 1))
			{
				case 'P':
					$destination = 'Paris';
					break;					
				case 'A' :
					$destination = 'Argenteuil';
					break;
				case 'C' :
					$destination = 'Conflans-Sainte-Honorine';
					break;
				case 'E' :
					$destination = 'Ermont - Eaubonne';
					break;
				case 'G' : 
					$destination = 'Gisors';
					break;
				case 'K' : 
					$destination = 'Cormeilles-en-Parisis';
					break;
				case 'L' : 
					$destination = 'Les Mureaux';
					break;
				case 'M' : 
					$destination = 'Mantes-la-Jolie';
					break;
				case 'T' : 
					$destination = 'Pontoise';
					break;
				case 'V' : 
					$destination = 'Vernon';
					break;
				case 'Y' : 
					$destination = 'Boissy-l\'Aillerie';
					break;
				Default:
					$destination = 'Banlieue';
					break;
			}
				
		echo  '<td>'.$destination.'</td>';
		echo  '<td><form action="saisie_mobile.php" method="post">suppression<input type="checkbox" name="train_supprime" value="1"/>&nbsp;';
		echo "\n";
		echo  '<textarea name="jaime"  maxlength="2000"  rows="1" style="border : 2px solid green">j aime</textarea>&nbsp;';
		echo "\n";
		echo  '<textarea name="jaimepas"  maxlength="2000"  rows="1" style="border : 2px solid red">je n aime pas</textarea>&nbsp;';
		echo "\n";
		echo  '<input type="hidden" name="date_train" value="'.$date.'"/>';
		echo "\n";
        echo  '<input type="hidden" name="heuretrain" value="'.$time['departure_time'].'"/>';
		echo "\n";
        echo  '<input type="hidden" name="mission" value="'.$time['trip_headsign'].'"/>';
		echo "\n";
        echo  '<input type="hidden" name="stationdep" value="'.$gare_depart.'"/>';
		echo "\n";
		echo  '<input type="submit" name="send" value="Signaler"></form></td>';
		echo  '</tr>';
		echo "\n";
	
	
	}
?>
</table>
<?php //} else if($control ==2) { ?>
<!--  
<form action="saisie.php" method="POST">
Cochez si le train a été supprimé&nbsp;<input type="checkbox" name="train_supprime" value="1"></br>
<table align="center">
<tr>
<td>	
J'ai aimé<br />
<textarea name="jaime"  maxlength="2000"  rows="5" style="border : 1px solid green">
état de propreté...
</textarea>
</td>
<td>
Je n'ai pas aimé<br />
<textarea name="jaimepas"  maxlength="2000"  rows="5" style="border : 1px solid red">
pas d'annonce pour le retard ou la suppression...
</textarea></br>
</td>
</tr>
</table>
</div>
<table align="center" >
<td>
<input type="hidden" name="date_train" value="<?php //echo $_GET['date']; ?>">
<input type="hidden" name="heuretrain" value="<?php //echo $_GET['deptime']; ?>">
<input type="hidden" name="mission" value="<?php //echo $_GET['mission']; ?>">
<input type="hidden" name="stationdep" value="<?php //echo $_GET['stationdep']; ?>">
<input type="submit" name="send" value="Signaler">
</td>
</table>
</form>
-->
<?php } else if ($control == 3) { ?>

	MERCI POUR VOTRE PARTICIPATION !<br />
	<a href="http://www.revoltes-lignej.fr/incidents-forms/saisie_mobile.php?control=0" >Retour au formulaire.</a><br />
	<a href="http://www.revoltes-lignej.fr" >Retour à l'acceuil.</a>
	
<?php } ?>



<!--
<div name="bloc_regul" style="clear:both">
<h2>La régularité en temps réel, par branche, issue de <u>vos</u> contributions.</h2>

<p>Définition : la régularité est le taux des trains arrivés à l'heure <b>à quai</b> par rapport à l'offre théorique.</br> 
La régularité quotidienne ne prend en compte que les contributions du jour.</br>
La régularité absolue est calculée avec l'ensemble des contributions depuis la mise en place du service.</p>
<p>
La couleur verte signifie que la valeur est <font id="vert">conforme au plan quinquennal</font> SNCF-STIF (au moins 94% de ponctualité).</br>
La couleur rouge signifie que la valeur est <font id="rouge">inférieure à l'objectif</font>.</p>

<table align="center" style="border : 1px solid lightgray">
<tr><td>Axe</td><td>quotidienne</td><td>absolue</td>
</tr>
<tr><td rowspan="2">Ermont</td><td class="regul"><font class="regul">xx%</font></td><td><font class="regul">xx%</font></td>
</tr>
<tr><td class="contrib">n contribution(s)</td><td class="contrib">C contribution(s)</td>
</tr>
<tr><td rowspan="2">Mantes la Jolie via Conflans</td><td class="regul"><font class="regul">xx%</font></td><td><font class="regul">xx%</font></td>
</tr>
<tr><td class="contrib">n contribution(s)</td><td class="contrib">C contribution(s)</td>
</tr>
<tr><td rowspan="2">Mantes la jolie via Poissy</td><td class="regul"><font class="regul">xx%</font></td><td><font class="regul">xx%</font></td>
</tr>
<tr><td class="contrib">n contribution(s)</td><td class="contrib">C contribution(s)</td>
</tr>
<tr><td rowspan="2">Pontoise</td><td class="regul"><font class="regul"  id="vert">xx%</font></td><td><font class="regul"  id="rouge">xx%</font></td>
</tr>
<tr><td class="contrib">n contribution(s)</td><td class="contrib">C contribution(s)</td>
</tr>
<tr><td rowspan="2">Les Mureaux</td><td class="regul"><font class="regul"  id="vert">xx%</font></td><td><font class="regul"  id="rouge">xx%</font></td>
</tr>
<tr><td class="contrib">n contribution(s)</td><td class="contrib">C contribution(s)</td>
</tr>
</table>
<div name="stat_1" class="stat">
<h3>Ermont</h3>
quotidienne&nbsp;&nbsp;</br>
absolue&nbsp;&nbsp;
</div>
<div name="stat_2" class="stat">
<h3>Mantes la jolie via Conflans</h3>
quotidienne&nbsp;<font class="regul">xx%</font></br>
absolue&nbsp;<font class="regul">xx%</font>
</div>
<div name="stat_3" class="stat">
<h3>Mantes la jolie via Poissy</h3>
quotidienne&nbsp;<font class="regul">xx%</font></br>
absolue&nbsp;<font class="regul">xx%</font>
</div>
<div name="stat_4" class="stat">
<h3>Les Mureaux</h3>
quotidienne&nbsp;<font class="regul">xx%</font></br>
absolue&nbsp;<font class="regul">xx%</font>
</div>
<div name="stat_5" class="stat">
<h3>Gisors</h3>
quotidienne&nbsp;<font class="regul" id="vert">xx%</font></br>
absolue&nbsp;<font class="regul" id="vert">xx%</font>
</div>
<div name="stat_6" class="stat">
<h3>Pontoise</h3>
quotidienne&nbsp;<font class="regul" id="rouge">xx%</font></br>
absolue&nbsp;<font class="regul" id="rouge">xx%</font>
</div>
</div>-->

</div>

</body>
