<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/phpimagehelper.php';


class hbmanagerModelHbpictures extends JModelLegacy
{	
	private $teamkey = null;
	public $picFolder = 'images/handball/teams';
	
	
	function __construct() 
	{
		parent::__construct();
		
		self::makeTeamPicFolder();
	}
	
	public function setTeamkey($teamkey) {
		$this->teamkey = $teamkey;
	
	}
	
	function getTeams()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('kuerzel').', '.
			$db->qn('reihenfolge').', '.$db->qn('mannschaft').', '.
			$db->qn('name').', '.$db->qn('nameKurz').', '.
			$db->qn('ligaKuerzel').', '.$db->qn('liga').', '.
			$db->qn('geschlecht').', '.$db->qn('jugend').', '.
			$db->qn('dateiname').', '.$db->qn('saison').', '.
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
		$teams = $db->loadObjectList();	
		$teams = self::getPlayersList($teams);
		
		if ($this->teamkey === null) { return $teams; }
		return $teams[0];
	}
		
	function saveImage ($pic) {
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($pic);echo'</pre>';
		$file = JPATH_SITE.'/'.$pic['dateiname'];
		$path = dirname($file);
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($file);echo'</pre>';
		
		$resolutions = array(200,500,800,1200);
		
		foreach ($resolutions as $res) {
			//echo __FILE__.'('.__LINE__.'):<pre>';print_r($res);echo'</pre>';
			$folder = $path.'/'.$pic['saison'];
			$filename = 'team_'.$pic['kuerzel'].'_'.$pic['saison'].'_'.$res.'px';
			$output = $folder.'/'.$filename.'.png';
			
			// make new folder
			if (!is_dir($folder)) {
				mkdir($folder, 0777, true);
			}
			
			$result = phpimagehelper::smart_resize_image($file,
								'Mannschaftsfoto',	//$string             = null
								$res,				//$width              = 0
								0,					//$height             = 0
								true,				//$proportional       = false
								$output,			//$output             = 'file'
								false,				//$delete_original    = true
								false,				//$use_linux_commands = false
								90					//$quality = 100
							   );
		}
		//unlink($file); //delete file
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($result);echo'</pre>';
		return $result;
	}
	
	protected function formatPlayersList($teams) {
		
		foreach ($teams AS &$team) {
			$i = 1;
			$set = array();
			while (isset($team['titel'.$i]) ) {
				if (!empty($team['titel'.$i]) || !empty($team['namen'.$i])) {
					$set[$i-1]['heading'] = $team['titel'.$i];
					//unset($teams['titel'.$i]);
					$set[$i-1]['list'] = $team['namen'.$i];
					//unset($teams['namen'.$i]);
				}
				$i++;
			}
			$team['list'] = $set;
		}
		return $teams;
	}
	
	protected function getPlayersList($teams) {
		
		foreach ($teams AS &$team) {
			//echo __FILE__.'('.__LINE__.'):<pre>';print_r($team);echo'</pre>';
			//$list = unserialize($team->spielerliste);
			$list = json_decode($team->spielerliste);
			//echo __FILE__.'('.__LINE__.'):<pre>';print_r($list);echo'</pre>';
			$maxLines = (count($list) <= 3 ) ? 3 : count($list);
			for($i = 0 ; $i < $maxLines; $i++) {
//			for($i = 1 ; $i <= count($list); $i++) {
				//echo __FILE__.'('.__LINE__.')'.$i.':<pre>';print_r($list[$i-1]);echo'</pre>';
//				$team->{'untertitel_dt'.$i} = $list[$i-1]['heading'];
//				$team->{'untertitel_dd'.$i} = $list[$i-1]['list'];
				
				$team->liste[$i]['titel'] = (isset($list[$i]->heading )) ? $list[$i]->heading : '';
				$team->liste[$i]['namen'] = (isset($list[$i]->list )) ? $list[$i]->list : '';
			}
		}
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($teams);echo'</pre>';
		return $teams;
	}
			
	function updateDB($teams = null) 
	{
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($teams);echo'</pre>';
		if (empty($teams)) return;
		
		$teams = self::formatPlayersList($teams);
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($teams);echo'</pre>';
		$table = 'hb_mannschaftsfoto';
		$columns = array('kuerzel', 'saison', 'dateiname', 'spielerliste', 'kommentar');
		
		$values = null;
		
		foreach($teams as $row) {
			$values[] = implode(', ', self::formatTeamValues($row));
		}
		//echo __FILE__.' ('.__LINE__.'<pre>';print_r($columns); echo'</pre>';
		//echo __FILE__.' ('.__LINE__.'<pre>';print_r($values); echo'</pre>';
		$db = $this->getDbo();
		
		// Prepare the insert query.
		$query = $db->getQuery(true);
		$query
			->insert($db->qn($table)) 
			->columns($db->qn($columns))
			->values($values);
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$query .= "\nON DUPLICATE KEY UPDATE \n";
		$dublicates = array();
		foreach ($columns as $field) {
			$dublicates[] = $db->qn($field).' = VALUES('.$db->qn($field).')';
		}
		$query .= implode(",\n", $dublicates);
		
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		
		$db->setQuery($query);
		$result = $db->query();
		return $result;
	}

	protected function formatTeamValues($data) {
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($data); echo'</pre>';
		$db = $this->getDbo();
		 
		$value['kuerzel'] = $db->q($data['kuerzel']);
		$value['saison'] = (!empty($data['saison'])) ? $db->q($data['saison']) : 'NULL';
		$value['dateiname'] = (!empty($data['dateiname'])) ? $db->q($data['dateiname']) : 'NULL';
		//$value['spielerliste'] = (!empty($data['list'])) ? $db->q(serialize($data['list'])) : 'NULL';//unserialize()
		$value['spielerliste'] = (!empty($data['list'])) ? $db->q(json_encode($data['list'])) : 'NULL'; //json_decode()
		$value['kommentar'] = (!empty($data['kommentar'])) ? $db->q($data['kommentar']) : 'NULL';

		//echo __FUNCTION__.'<pre>';print_r($value); echo'</pre>';
		return $value;
    }
	
	protected function makeTeamPicFolder () {
		// make new folder
		$folder = JPATH_SITE.'/'.$this->picFolder;
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($folder.' - '.is_dir($folder)); echo'</pre>';
		if (!is_dir($folder)) {
			mkdir($folder, 0777, true);
		}
	}
	
}