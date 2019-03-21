<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('HBmanagerModelHBmanager', JPATH_COMPONENT_SITE . '/models/hbmanager.php');

class HBmanagerModelGyms extends HBmanagerModelHBmanager
{	

	public function __construct($config = array())
	{		
		
		parent::__construct($config);
	}

	// public function getTable($type = 'HBhallenvz', $prefix = 'HBhallenvzTable', $config = array())
	// {
	// 	return JTable::getInstance($type, $prefix, $config);
	// }

	// function getTeams($category = '')
	// {
	// 	$db = $this->getDbo();		
	// 	$query = $db->getQuery(true);
	// 	$query->select('*');
	// 	$query->from('hb_mannschaft');
	// 	switch ($category) {
	// 		case 'Aktiv':
	// 			$query->where($db->qn('jugend').' = 0');
	// 			break;
	// 		case 'JugendM':
	// 			$query->where($db->qn('jugend').' = 1 AND geschlecht = '.$db->q('m'));
	// 			break;
	// 		case 'JugendW':
	// 			$query->where($db->qn('jugend').' = 1 AND geschlecht = '.$db->q('w'));
	// 			break;
	// 		case 'JugendG':
	// 			$query->where($db->qn('jugend').' = 1 AND geschlecht = '.$db->q('g'));
	// 			break;
	// 		default:
	// 			break;
	// 	}
	// 	//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
	// 	$db->setQuery($query);
	// 	return $teams = $db->loadObjectList();
	// }
	
	// function getGyms($teamkey = 'allGyms')
	// {
	// 	$db = $this->getDbo();
	// 	$query = $db->getQuery(true);
	// 	$query->select('DISTINCT '.$db->qn('hallenNr').', '.
	// 			$db->qn('kurzname').', '.$db->qn('hallenName').', '.
	// 			$db->qn('plz').', '.$db->qn('stadt').', '.$db->qn('strasse').', '.
	// 			$db->qn('telefon').', '.$db->qn('haftmittel'));
	// 	if ($teamkey == 'allGyms') $query->from('hb_halle') ;
	// 	else {
	// 		$query->from('hb_spiel');
	// 		if ($teamkey != 'all') $query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
	// 		$query->join('INNER',$db->qn('hb_halle').' USING ('.$db->qn('hallenNr').')');
	// 	}
	// 	$query->order($db->qn('hallenNr'));
	// 	//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
	// 	$db->setQuery($query);
	// 	$gyms = $db->loadObjectList();
	// 	$gyms = self::formatGyms($gyms);
	// 	return $gyms;
	// }
	
	// protected function formatGyms($gyms) {
	// 	foreach ($gyms as $gym) {
	// 		$gym->haftmittel = str_replace('Eingeschränktes Haftmittelverbot: ', '', $gym->haftmittel);
	// 	}
	// 	//echo '<p>'.__FUNCTION__.'</p><pre>'; print_r($gyms); echo "</pre>";
	// 	return $gyms;
	// }
			
	// function updateGyms($teamkey = 'all') 
	// {
	// 	$db = JFactory::getDbo();
	// 	$query = $db->getQuery(true);
	// 	$query->select('DISTINCT '.$db->qn('hallenNr').', '.
	// 			$db->qn('kurzname').', '.$db->qn('hallenName').', '.
	// 			$db->qn('plz').', '.$db->qn('stadt').', '.$db->qn('strasse').', '.
	// 			$db->qn('telefon').', '.$db->qn('haftmittel'));
	// 	if ($teamkey == 'allGyms') $query->from('hb_halle') ;
	// 	else {
	// 		$query->from('hb_spiel');
	// 		if ($teamkey != 'all') $query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
	// 		$query->join('INNER',$db->qn('hb_halle').' USING ('.$db->qn('hallenNr').')');
	// 	}
	// 	$query->order($db->qn('hallenNr'));

	// 	//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
	// 	$db->setQuery($query);
	// 	$gyms = $db->loadObjectList();

	// 	//echo "<pre>"; print_r($gyms); echo "</pre>";
	// 	return $gyms;
	// }

}

