<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HB HallenVZ Model
 */
class HBhallenvzModelHBhallenvz extends JModelItem
{
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param       type    The table type to instantiate
	 * @param       string  A prefix for the table class name. Optional.
	 * @param       array   Configuration array for model. Optional.
	 * @return      JTable  A database object
	 * @since       2.5
	 */
	public function getTable($type = 'HBhallenvz', $prefix = 'HBhallenvzTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	function getMannschaften($category = '')
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('aaa_mannschaft');
		switch ($category) {
			case 'Aktiv':
				$query->where($db->qn('jugend').' = 0');
				break;
			case 'JugendM':
				$query->where($db->qn('jugend').' = 1 AND geschlecht = '.$db->q('m'));
				break;
			case 'JugendW':
				$query->where($db->qn('jugend').' = 1 AND geschlecht = '.$db->q('w'));
				break;
			case 'JugendG':
				$query->where($db->qn('jugend').' = 1 AND geschlecht = '.$db->q('g'));
				break;
			default:
				break;
		}
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$mannschaften = $db->loadObjectList();
		return $mannschaften;
	}
	
	function getHallen($teamkey = 'allGyms')
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT '.$db->qn('hallenNummer').', '.$db->qn('halleID').', '.
				$db->qn('kurzname').', '.$db->qn('name').', '.
				$db->qn('plz').', '.$db->qn('stadt').', '.$db->qn('strasse').', '.
				$db->qn('telefon').', '.$db->qn('haftmittel'));
		if ($teamkey == 'allGyms') $query->from('aaa_halle') ;
		else {
			$query->from('aaa_spiel');
			if ($teamkey != 'all') $query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
			$query->join('INNER',$db->qn('aaa_halle').' USING ('.$db->qn('hallenNummer').')');
		}
		$query->order($db->qn('hallenNummer'));
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$hallen = $db->loadObjectList();
		
		return $hallen;
	}
}