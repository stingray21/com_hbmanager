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
		//echo '=> model->$query <br><pre>"; print_r($query); echo "</pre>';
		$db->setQuery($query);
		$team = $db->loadObject();
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
		$pic->caption = self::buildCaption($pictureInfo);
		return $pic;
	}
	
	function buildCaption($pic)
	{	
		$pic = (array) $pic;
		$caption = '';
		for ($i = 1; $i <= 4; $i++) { 
			if (!empty($pic['untertitel_dt'.$i]) AND 
					!empty($pic['untertitel_dd'.$i])) {
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
	function getRanking()
	{
		$db = $this->getDbo();
		$query = "SELECT 
			mannschaft,

			COUNT(IF(s.hTore IS NOT NULL, s.hTore, 0)) spiele, 
			SUM(IF(w='H', 1, 0)) spieleHeim, 
			SUM(IF(w='A', 1, 0)) spieleGast, 

			SUM( 
			CASE 
			WHEN s.hTore > s.gTore THEN 2 
			WHEN s.hTore = s.gTore THEN 1 
			ELSE 0 
			END) punkte, 

			SUM( 
			CASE 
			WHEN w = 'H' AND s.hTore > s.gTore THEN 2 
			WHEN w = 'H' AND s.hTore = s.gTore THEN 1 
			ELSE 0 
			END) punkteHeim, 

			SUM( 
			CASE 
			WHEN w = 'A' AND s.hTore > s.gTore THEN 2 
			WHEN w = 'A' AND s.hTore = s.gTore THEN 1 
			ELSE 0 
			END) punkteGast, 

			SUM( 
			CASE 
			WHEN s.hTore < s.gTore THEN 2 
			WHEN s.hTore = s.gTore THEN 1 
			ELSE 0 
			END) nPunkte, 

			SUM( 
			CASE 
			WHEN w = 'H' AND s.hTore < s.gTore THEN 2 
			WHEN w = 'H' AND s.hTore = s.gTore THEN 1 
			ELSE 0  
			END) nPunkteHeim, 

			SUM( 
			CASE 
			WHEN w = 'A' AND s.hTore < s.gTore THEN 2 
			WHEN w = 'A' AND s.hTore = s.gTore THEN 1 
			ELSE 0  
			END) nPunkteGast,

			SUM(IF(s.hTore > s.gTore, 1, 0)) s, 
			SUM(IF(w = 'H' AND s.hTore > s.gTore, 1, 0)) sHeim, 
			SUM(IF(w = 'A' AND s.hTore > s.gTore, 1, 0)) sGast, 


			SUM(IF(s.hTore = s.gTore, 1, 0)) u, 
			SUM(IF(w = 'H' AND s.hTore = s.gTore, 1, 0)) uHeim, 
			SUM(IF(w = 'A' AND s.hTore = s.gTore, 1, 0)) uGast, 


			SUM(IF(s.hTore < s.gTore, 1, 0)) n, 
			SUM(IF(w = 'H' AND s.hTore < s.gTore, 1, 0)) nHeim, 
			SUM(IF(w = 'A' AND s.hTore < s.gTore, 1, 0)) nGast, 


			SUM(IF(s.hTore IS NOT NULL, s.hTore, 0)) AS tore, 
			SUM(IF(w = 'H', s.hTore, 0)) AS toreHeim, 
			SUM(IF(w = 'A', s.hTore, 0)) AS toreGast, 

			SUM(IF(s.hTore IS NOT NULL, s.gTore, 0)) AS gegenTore, 
			SUM(IF(w = 'H', s.gTore, 0)) AS gegenToreHeim, 
			SUM(IF(w = 'A', s.gTore, 0)) AS gegenToreGast,	

			SUM(IF(s.hTore IS NOT NULL, s.hTore-s.gTore, 0)) AS diff,
			SUM(IF(w = 'H', s.hTore-s.gTore, 0)) AS diffHeim,
			SUM(IF(w = 'A', s.hTore-s.gTore, 0)) AS diffGast

			FROM ( 
				SELECT heim as mannschaft 
				FROM hbdata_m1_spielplan 
				GROUP BY mannschaft
				) AS m
			LEFT JOIN
			(SELECT 
			'H' w, 
			s1.datum datum,
			s1.heim mannschaft, 
			s1.gast gegner, 
			s1.toreHeim hTore, 
			s1.toreGast gTore
			FROM hbdata_m1_spielplan s1 
			WHERE s1.toreHeim IS NOT NULL

			UNION 

			SELECT 
			'A' w,
			s2.datum datum,
			s2.gast mannschaft, 
			s2.heim gegner, 
			s2.toreGast hTore, 
			s2.toreHeim gTore 
			FROM hbdata_m1_spielplan s2 
			WHERE s2.toreHeim IS NOT NULL
			) AS s USING (mannschaft)

			GROUP BY mannschaft 
			ORDER BY punkte DESC, s DESC, diff DESC";
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		echo '<pre>';print_r($result);echo'</pre>';
		return $result;
	}
	function getHead2Head($team, $opponent, $table)
	{
		$db = $this->getDbo();
		$query = "SELECT 
			mannschaft, gegner, 
			SUM(tore - gtore) as diff, 
			SUM(IF(w = 'A', tore, 0)) - SUM(IF(w = 'H', gtore, 0)) AS ausTorDiff
			FROM 
			(SELECT 
			'H' w, 
			s1.datum datum,
			s1.heim mannschaft, 
			s1.gast gegner, 
			s1.toreHeim tore, 
			s1.toreGast gtore
			FROM hbdata_m1_spielplan s1 
			WHERE heim='TSV Geislingen' and gast='SG Tail/Trucht'

			UNION 

			SELECT 
			'A' w,
			s2.datum datum,
			s2.gast mannschaft, 
			s2.heim gegner, 
			s2.toreGast tore, 
			s2.toreHeim gTore 
			FROM hbdata_m1_spielplan s2 
			WHERE gast='TSV Geislingen' and heim='SG Tail/Trucht'
			) AS s 

			GROUP BY mannschaft";
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$result = $db->loadObject();
		echo '<pre>';print_r($result);echo'</pre>';
		return $result;
	}
}