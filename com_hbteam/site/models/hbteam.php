<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HB Team Home Model
 */
class hbteamModelhbteam extends JModelLegacy
{
	/**
	 * @var array messages
	 */
	protected $messages;
	protected $teamkey;
	protected $team;
	protected $pictureInfo;
	
	function __construct() 
	{
		parent::__construct();
		
		//request the selected teamkey
		$menuitemid = JRequest::getInt('Itemid');
		if ($menuitemid)
		{
			$menu = JFactory::getApplication()->getMenu();
			$menuparams = $menu->getParams($menuitemid);
		}
		$this->teamkey = $menuparams->get('teamkey');
		//$this->team = self::getTeam($this->teamkey);
		$this->pictureInfo = self::getPictureInfo();
	}
	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param       type    The table type to instantiate
	 * @param       string  A prefix for the table class name. Optional.
	 * @param       array   Configuration array for model. Optional.
	 * @return      JTable  A database object
	 * @since       2.5
	 */
	public function getTable($type = 'hbteam', $prefix = 'hbteamTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Get the message
	 * @param  string The corresponding id of the message to be retrieved
	 * @return string The message to be displayed to the user
	 */
	public function getMsg($teamkey = "noteam")
	{
		if (!is_array($this->messages))
		{
			$this->messages = array();
		}
		
		if (!isset($this->messages[$teamkey]))
		{
			//request the selected teamkey
			$menuitemid = JRequest::getInt('Itemid');
			if ($menuitemid)
			{
				$menu = JFactory::getApplication()->getMenu();
				$menuparams = $menu->getParams($menuitemid);
			}
			$teamkey = $menuparams->get('teamkey');
			
			// Get a Tablehbteam instance
			$table = $this->getTable();

			// Load the message
			$table->load($teamkey);

			// Assign the message
			$this->messages[$teamkey] = $table->mannschaft;
		}

		return $this->messages[$teamkey];
	}
	
	function getTeam($teamkey = "non")
	{
		if ($teamkey === "non"){
			$teamkey = $this->teamkey;
		}
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('kuerzel').', '.
			$db->qn('reihenfolge').', '.$db->qn('mannschaft').', '.
			$db->qn('name').', '.$db->qn('nameKurz').', '.
			$db->qn('ligaKuerzel').', '.$db->qn('liga').', '.
			$db->qn('geschlecht').', '.$db->qn('jugend').', '.
			$db->qn('saison').', '.
			$db->qn('spielerliste').', '.$db->qn('kommentar') );
		$query->from('hb_mannschaft');
		$query->leftJoin($db->qn('hb_mannschaftsfoto').' USING ('.
				$db->qn('kuerzel').')');
		if ($this->teamkey !== null) {
			$query->where($db->qn('kuerzel').' = '.$db->q($this->teamkey));
		}
		$query->order('ISNULL('.$db->qn('reihenfolge').'), '.
					$db->qn('reihenfolge').' ASC');
		// Zur Kontrolle
//		echo __FILE__.' ('.__LINE__.'<pre>'; echo $query; echo "</pre>";
		$db->setQuery($query);
		$team = $db->loadObject();	
		$team = self::getPlayersList($team);
		
		if (empty($team)){
			$team = new stdClass();
			$team->mannschaft = 'Mannschaft';
			$team->liga = 'Liga';
			$team->nameKurz = '';
		}

		//echo __FILE__.' ('.__LINE__.')<pre>'; print_r($team); echo '</pre>';
		
		return $team;
	}
	
	
	function getPictureInfo()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaftsfoto');
		$query->where($db->qn('kuerzel').' = '.$db->q($this->teamkey));
		// Zur Kontrolle
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$pictureInfo = $db->loadObject();
		return $pictureInfo;
	}
	
	function getPicture()
	{
		if (empty($this->pictureInfo)) return null;
		//echo '=> model->pictureInfo<br><pre>'; print_r($pictureInfo); echo '</pre>';
		$pic = new stdClass();
		$pic->saison = $this->pictureInfo->saison;
		$pic->comment = $this->pictureInfo->kommentar;
		return $pic;
	}
	
	public function getImage($res)
	{
		if (empty($this->pictureInfo)) return null;
		$season = self::getCurrentSeason();
		//echo '=> model->pictureInfo<br><pre>'; print_r($pictureInfo); echo '</pre>';
		$path = './images/handball/teams/'.$season.'/team_'.$this->pictureInfo->kuerzel.'_'
				.$season.'_'.$res.'px.png';
		return $path;
	}
	
	private function getCurrentSeason() 
	{
		$year = strftime('%Y');
		if (strftime('%m') < 8) {
			$year = $year-1;
		}
		return $currentSeason = $year.'-'.($year+1);
	}

	
	protected function getPlayersList($team) {
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($team);echo'</pre>';
		//$list = unserialize($team->spielerliste);
		$list = json_decode($team->spielerliste);
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($list);echo'</pre>';
		for($i = 0 ; $i < count($list); $i++) {
	//			for($i = 1 ; $i <= count($list); $i++) {
			//echo __FILE__.'('.__LINE__.')'.$i.':<pre>';print_r($list[$i-1]);echo'</pre>';
	//				$team->{'untertitel_dt'.$i} = $list[$i-1]['heading'];
	//				$team->{'untertitel_dd'.$i} = $list[$i-1]['list'];

			$team->liste[$i]['titel'] = (isset($list[$i]->heading )) ? $list[$i]->heading : '';
			$team->liste[$i]['namen'] = (isset($list[$i]->list )) ? $list[$i]->list : '';
		}
		
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($team);echo'</pre>';
		return $team;
	}
	
}