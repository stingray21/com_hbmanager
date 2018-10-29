<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
JLoader::register('HBmanagerModelHBmanager', JPATH_COMPONENT_SITE . '/models/hbmanager.php');

class HBmanagerModelTicker extends HBmanagerModelHBmanager
{	

	public $url = null;
	public $test = true;

	public function __construct($config = array())
	{		
		self::setParams();

		parent::__construct($config);
	}

	private function setParams() 
	{
		$params = JComponentHelper::getParams( 'com_hbmanager' ); // global config parameter
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($params);echo'</pre>';
			
		$this->url = $params->get('hvwurl-ticker');
		
		$this->test = false;

		$menuitemid = JRequest::getInt('Itemid');		
		if ($menuitemid)
		{
			$menu = JFactory::getApplication()->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($menuparams);echo'</pre>';
			$this->test = $menuparams->get('test');
		}
		
	}

	public function getBaseUrl()
	{
		return $this->url;
	}

	public function getTestMode()
	{
		return $this->test;
	}

	// public function getGameInfo()
	// {
	// 	$db = JFactory::getDBO();
	// 	$query = $db->getQuery(true);

	// 	$query->select('`gymId`, `season`, `teamkey`, `leagueKey`, `gameIdHvw`, `dateTime`, `home`, `away`, `goalsHome`, `goalsAway`, `goalsHome1`, `goalsAway1`, `comment`, `gymName`, `town`');

	// 	$query->from($this->table_game.' AS game');
	// 	$query->leftJoin($db->qn($this->table_gym).' USING ('.$db->qn('gymId').')');
	// 	$query->where($db->qn('season').' = '.$db->q($this->season));
	// 	$query->where($db->qn('gameIdHvw').' = '.$db->q($this->selectedGameId));
	// 	// echo __FILE__.' - line '.__LINE__.'<pre>'.$query.'</pre'; 

	// 	$db->setQuery($query);
	// 	$infos = $db->loadObject();
	// 	// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($infos);echo '</pre';die;
	// 	$infos = self::formatGameInfos($infos);

	// 	return $infos;
	// }

	// private function formatGameInfos($data)
	// {		
	// 	// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($data);echo '</pre>';
	// 	$infos = null;
		
	// 	$infos['season'] = $data->season;
	// 	$infos['teamkey'] = $data->teamkey;
	// 	$infos['leagueKey'] = $data->leagueKey;
	// 	$infos['gameIdHvw'] = intval($data->gameIdHvw);
	// 	$infos['dateTime'] = JHtml::_('date', $data->dateTime, 'Y-m-d H:i:s', $this->tz);
	// 	$infos['date'] = JHtml::_('date', $data->dateTime, 'd.m.y', $this->tz);
	// 	$infos['time'] = JHtml::_('date', $data->dateTime, 'H:i', $this->tz);
	// 	$infos['home'] = $data->home;
	// 	$infos['away'] = $data->away;
	// 	$infos['goalsHome'] = intval($data->goalsHome);
	// 	$infos['goalsAway'] = intval($data->goalsAway);
	// 	$infos['goalsHome1'] = intval($data->goalsHome1);
	// 	$infos['goalsAway1'] = intval($data->goalsAway1);
	// 	$infos['comment'] = $data->comment;
	// 	$infos['gymId'] = intval($data->gymId);
	// 	$infos['gymName'] = $data->gymName;
	// 	$infos['town'] = $data->town;

	// 	// echo __FILE__.' - line '.__LINE__.'<pre>';print_r($infos);echo '</pre>';
	// 	return $infos;
	// }

}

