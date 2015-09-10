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
		$query->select('*');
		$query->from('hb_mannschaft');
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
		//echo __FILE__.__LINE__.'=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		$team = $db->loadObject();
		if (empty($team)){
			$team = new stdClass();
			$team->mannschaft = 'Mannschaft';
			$team->liga = 'Liga';
			$team->nameKurz = '';
		}

		//echo __FILE__.__LINE__.'<pre>'; print_r($team); echo '</pre>';
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
		$pictureInfo = self::getPictureInfo();
		if (empty($pictureInfo)) return null;
		//echo '=> model->pictureInfo<br><pre>'; print_r($pictureInfo); echo '</pre>';
		$pic = new stdClass();
		$pic->filename = $pictureInfo->dateiname;
		$pic->saison = $pictureInfo->saison;
		$pic->comment = $pictureInfo->kommentar;
		$pic->caption = self::getCaption($pictureInfo);
		return $pic;
	}
	
	function getCaption($pictureInfo)
	{	
		//echo __FILE__.__LINE__.'<pre>'; print_r($pictureInfo); echo '</pre>';
		$captionData = (array) $pictureInfo;
		$caption = null;
		for ($i = 1; $i <= 4; $i++) { 
			if (!empty($captionData['untertitel_dd'.$i]) AND 
					!empty($captionData['untertitel_dd'.$i])) {
				$caption[$i] = new stdClass();
				$caption[$i]->headline = $captionData['untertitel_dt'.$i];
				$caption[$i]->content = $captionData['untertitel_dd'.$i];
			}
		}
		//echo __FILE__.__LINE__.'<pre>'; print_r($caption); echo '</pre>';
		return $caption;
	}
	
}