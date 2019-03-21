<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('HBmanagerModelHBmanager', JPATH_COMPONENT_SITE . '/models/hbmanager.php');

class HBmanagerModelSponsors extends HBmanagerModelHBmanager
{	
		public function __construct($config = array())
	{		

		parent::__construct($config);
	}

	public function getSponsors ()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('params');
		$query->from($db->qn('#__modules'));
		$query->where($db->qn('module').'='.$db->q('mod_hbsponsor'));
		$db->setQuery($query);
		$result = $db->loadObject();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($result);echo'</pre>';
		$params = new JRegistry($result->params);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($params);echo'</pre>';
		$sponsors = $params->get('ad_json', '[ { "url": "https://www.hkog.de" , "alt": "HKOG" } ]');
		$sponsors = json_decode($sponsors);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($sponsors);echo'</pre>';
		return $sponsors;
	}
	
	// TODO: move sponsor data to DB
	// public function getSponsors ()
	// {
	// 	// getting sponsor information
	// 	$db = JFactory::getDBO();
	// 	$query = $db->getQuery(true);
	// 	$query->select('*');
	// 	$query->from($db->qn($this->table_sponsors));

	// 	$db->setQuery($query);
	// 	$sponsors = $db->loadObjectList ();
	// 	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($sponsors);echo'</pre>';
	// 	return $sponsors;
	// }

}

