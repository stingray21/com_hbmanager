<?php
define( '_JEXEC', 1 );
define( 'DS', '/' );

define( 'JPATH_BASE', '..'.DS.'..'.DS.'..'.DS );

//print_r( JPATH_BASE.'includes'.DS.'defines.php' );
require_once( JPATH_BASE.'includes'.DS.'defines.php' );
require_once( JPATH_BASE.'includes'.DS.'framework.php' );
require_once( JPATH_BASE.'libraries'.DS.'joomla'.DS.'factory.php' );


$mainframe = JFactory::getApplication('site');


$jinput = JFactory::getApplication()->input;
$teamkey = $jinput->get('teamkey', 'all');

$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('DISTINCT '.$db->qn('hallenNummer').', '.$db->qn('halleID').', '.
		$db->qn('kurzname').', '.$db->qn('name').', '.
		$db->qn('plz').', '.$db->qn('stadt').', '.$db->qn('strasse').', '.
		$db->qn('telefon').', '.$db->qn('haftmittel'));
if ($teamkey == 'allGyms') $query->from('hb_halle') ;
else {
	$query->from('hb_spiel');
	if ($teamkey != 'all') $query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
	$query->join('INNER',$db->qn('hb_halle').' USING ('.$db->qn('hallenNummer').')');
}
$query->order($db->qn('hallenNummer'));

//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
$db->setQuery($query);
$gyms = $db->loadObjectList();

//echo "<pre>"; print_r($gyms); echo "</pre>";
echo json_encode($gyms);

?>
