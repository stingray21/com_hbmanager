<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HB Team Home Model
 */
class HBteamHomeModelHBteamHome extends JModelItem
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
	public function getTable($type = 'HBteamHome', $prefix = 'HBteamHomeTable', $config = array())
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
			
			// Get a TableHBteamHome instance
			$table = $this->getTable();

			// Load the message
			$table->load($teamkey);

			// Assign the message
			$this->messages[$teamkey] = $table->mannschaft;
		}

		return $this->messages[$teamkey];
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
		//echo '=> model->pictureInfo<br><pre>'; print_r($pictureInfo); echo '</pre>';
		$pic = new stdClass();
		$pic->filename = $pictureInfo->dateiname;
		$pic->saison = $pictureInfo->saison;
		$pic->comment = $pictureInfo->kommentar;
		$pic->caption = self::buildCaption($pictureInfo);
		return $pic;
	}
	
	function buildCaption($pic)
	{	
		$pic = (array) $pic;
		$caption = '';
		for ($i = 1; $i <= 4; $i++) { 
			if (!empty($pic['untertitel_dt'.$i])) {
				$caption .= '<dt>'.$pic['untertitel_dt'.$i].'</dt>'."\n";
			}
			if (!empty($pic['untertitel_dd'.$i])) {
				$caption .= '<dd>'.$pic['untertitel_dd'.$i].'</dd>'."\n";
			}
		}
		if (!empty($caption)) {
			$caption = '<dl class="teampic_caption">'."\n".$caption;
			$caption = $caption."\n".'</dl>'."\n";
			return $caption;
		}
		return null;
	}
}