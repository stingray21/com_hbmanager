<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');



echo '<h1>Hallenverzeichnis</h1>'."\n\n";

// echo "<ul>\n";
// foreach ($this->mannschaften as $mannschaft) {
// 	echo '<li id="button'.$mannschaft->kuerzel.'" class="hallenButton">'.$mannschaft->mannschaft;
// 	//if (!empty($mannschaft->liga)) echo ' ('.$mannschaft->liga.')';
// 	echo '</li>'."\n";
// }
// echo "</ul>\n";
JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
$form = JForm::getInstance('myform', JPATH_COMPONENT.'/models/forms/teams.xml');
?>
		
<form class="form-validate" action="" method="post" id="updateGyms" name="updateGyms">
	<fieldset class="adminform">
		<?php
		$input = $form->getInput('teamauswahl', 'hbteams');	
		echo $input;								
		//echo '<input class="submit" type="submit" name="submit" value="Mannschaften auswählen" />';
		?>
	</fieldset>
</form>	

<h2 name="hallenAuswahl" id="hallenAuswahl">Alle Hallen im Bezirk</h2>
<?php 
echo '<table id="hallenvztbl" class="hallenvz">'."\n";
echo '<tr><th>Nr</th><th>Name</th><th class="link">Adresse</th><th class="map"></th><th>Telefon</th><th>Haftmittel</th></tr>'."\n";
$rowlabel = 'even';

foreach ($this->gyms as $gym)
{
	$klammer = explode(' ', $gym->kurzname);
	$klammer = $klammer[0];
	
	if ($rowlabel == 'even') $rowlabel = 'odd';
	else $rowlabel = 'even'; 
	
	$start = 'Schloßparkhalle, Schloßplatz, Geislingen, Deutschland';
	$destination = implode(' ',array($gym->plz, $gym->stadt, $gym->strasse));
	$link = 'https://maps.google.com/maps?saddr='.urlencode($start).'&daddr='.urlencode($destination).'&ie=UTF8';
	
	echo '<tr class="'.$rowlabel.'"><td>'.$gym->hallenNummer.'</td><td>'.$gym->name.' ('.$klammer.')</td>';
	echo '<td class="link"><a href="'.$link.'" />';
	echo $gym->plz.' '.$gym->stadt;
	if (!empty($gym->strasse)) echo '<br />'.$gym->strasse;
	echo '</a></td>';
// 	echo '<td class="map"><a href="'.$link.'">'.JHtml::image('com_hbhallenvz/google-maps-standing.png', 'Google maps link', array('height' => 32) , true).'</a></td>';
	echo '<td class="map"><a href="'.$link.'" target="_BLANK"><span class="google-map-icon"></span></a></td>';
	echo '<td>'.$gym->telefon.'</td>';
	echo '<td>'.str_replace('Eingeschränktes Haftmittelverbot: ', '', $gym->haftmittel).'</td>';
	echo "</tr>"."\n";
	
}	
echo '</table>'."\n";

?>