<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.modeladmin');
$test = JTable::addIncludePath(JPATH_COMPONENT . '/tables');
//echo __FILE__.'<pre>';print_r( $test); echo'</pre>';
		

class HbmanagerModelHbteammenus extends JModelLegacy
{	
	
	private $titles = array();
	private $alias = array();
	private $categoryId = array();
	private $componentId = '';
	
	
	function __construct() 
	{
		parent::__construct();
		self::setCategoryIds();
		self::setComponentId();
		self::setTitles();
		self::setAlias();
		//echo __FUNCTION__.'<pre>';print_r($this->categoryuIds); echo'</pre>';
	}
	
	function getTeams()
	{
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		//$query->select('*');
		$query->select('kuerzel, reihenfolge, mannschaft, geschlecht, jugend '
		//	.',name, nameKurz, ligaKuerzel, liga, '
			.', IF(jugend = '.$db->q('aktiv').','.$db->q('aktiv').','
				.$db->q('jugend').' ) AS kategorie'
			.',id, menutype, title, alias'
			);
		$query->from('hb_mannschaft AS t');
		$query->leftJoin($db->qn('#__menu').' AS m ON '
			. '(LOWER(t.kuerzel) = m.alias)');
//		$query->leftJoin($db->qn('#__menu').' AS m ON '
//			. '(LOWER(t.kuerzel) = m.alias AND m.published = 1 )');
		//$query->where($db->qn('hvwLink').' IS NOT NULL');	
		//$query->where($db->qn('menutype').' = '.$db->q('teams'));
		$query->group($db->qn('kuerzel'));
		$query->order('ISNULL('.$db->qn('reihenfolge').'), '.
					$db->qn('reihenfolge').' ASC');		
		
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$teams = $db->loadObjectList();
//		if (empty($teams)) {
//			$teams = self::getEmptyTeam();
//		}
		//echo __FUNCTION__.'<pre>';print_r($teams); echo'</pre>';
		return $teams;
	}
	
	function getMenuId($alias)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('id').', '.$db->qn('alias'));
		$query->from('#__menu');
		$query->where($db->qn('menutype').' = '.$db->q('teams'));
		$query->where($db->qn('alias').' = '.$db->q($alias));
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$id = $db->loadResult();
		//echo __FUNCTION__.'<pre>';print_r($id); echo'</pre>';
		return $id;
	}
	
	function setCategoryIds()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('id').', '.$db->qn('alias'));
		$query->from('#__menu');
		$query->where($db->qn('menutype').' = '.$db->q('teams'));
		$query->where($db->qn('level').' = 1');
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$result = $db->loadAssocList();
		//echo __FUNCTION__.'<pre>';print_r($result); echo'</pre>';
		foreach ($result as $value)
		{
			$id[$value['alias']] = $value['id'];
		}
		return $this->categoryId = $id;
	}
	
	function setComponentId()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('extension_id'));
		$query->from('#__extensions');
		$query->where($db->qn('element').' = '.$db->q('com_hbteam'));
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$id = $db->loadResult();
		//echo __FUNCTION__.'<pre>';print_r($id); echo'</pre>';
		return $this->componentId = $id;
	}
	
	function setTitles()
	{
		$titles['players'] = 'Spieler';
		$titles['goals'] = 'Torschützen';
		$titles['reports'] = 'Spielberichte';
		//echo __FUNCTION__.'<pre>';print_r($titles); echo'</pre>';
		return $this->titles = $titles;
	}
	
	function setAlias()
	{
		$alias['players'] = '-spieler';
		$alias['goals'] = '-tore';
		$alias['reports'] = '-berichte';
		//echo __FUNCTION__.'<pre>';print_r($alias); echo'</pre>';
		return $this->alias = $alias;
	}
	
	function addMenuItems ($teams)
	{
		//echo __FUNCTION__.'<pre>';print_r($teams); echo'</pre>';
		foreach ($teams as $team)
		{
			//echo __FUNCTION__.'<pre>';print_r($team); echo'</pre>';
			if (!empty($team['add'])) {
				$team['parentId'] = $this->categoryId[$team['kategorie']];
				$team['alias'] = strtolower($team['kuerzel']);
				$team['title'] =  $team['mannschaft'];
				$team['id'] = self::addItem($team);
				
				self::addSubmenus($team);
			}
			
		}
	}
	
	protected function addSubmenus($team)
	{
		//echo __FUNCTION__.'<pre>';print_r($team); echo'</pre>';
		foreach ($team['add'] as $key => $item) {
			if ($key != 'team')
			{
				$submenu = array();
				$submenu['title'] =  $this->titles[$key];
				$submenu['alias'] = $team['alias'].$this->alias[$key];
				$submenu['kuerzel'] = $team['kuerzel'];
				$submenu['parentId'] = $team['id'];
				$submenu['id'] = self::getMenuId($submenu['alias']);
				self::addItem($submenu);
			}
		}
	}
	
	function addItem ($team)
	{
		//echo __FUNCTION__.'<pre>';print_r($team); echo'</pre>';
		$table = JTable::getInstance('Teammenu','HbmanagerTable');
		if (!empty($team['id'])) {
			$table->load($team['id']);
		}
		else {
			//echo __FUNCTION__.'<pre>';print_r($submenuId); echo'</pre>';
			$table->setLocation($team['parentId'], 'last-child');
		}
		//echo __FUNCTION__.'<pre>';print_r($table); echo'</pre>';
		$values = self::getValues($team);
		//echo __FUNCTION__.'<pre>';print_r($values); echo'</pre>';
		//$test = $table->save($values);
		$table->bind($values);
		$table->store();
		$table->publish();
		$returnId = $table->id;
		//echo __FUNCTION__.__LINE__.'<pre>';print_r($returnId); echo'</pre>';
		return $returnId;
	}
	
	protected function getValues($team) {
		$value = array();
		$value['menutype'] =  'teams';
		$value['title'] =  $team['title'];
		$value['alias'] =  $team['alias'];
		$value['note'] =  '';
		$value['link'] =  'index.php?option=com_hbteam&view=hbteam';
		$value['type'] =  'component';
		$value['component_id'] =  $this->componentId;
		$value['template_style_id'] =  0;
			$params['teamkey'] = $team['kuerzel'];
			$params['trainer'] = '';
			$params['teamImage'] = '';
			$params['menu-anchor_title'] = '';
			$params['menu-anchor_css'] = '';
			$params['menu_image'] = '';
			$params['menu_text'] = 1;
			$params['page_title'] = '';
			$params['show_page_heading'] = 0;
			$params['page_heading'] = '';
			$params['pageclass_sfx'] = '';
			$params['menu-meta_description'] = '';
			$params['menu-meta_keywords'] = '';
			$params['robots'] = '';
			$params['secure'] = 0;
		$value['params'] =  json_encode($params);
		$value['language'] =  '*';
		$value['client_id'] =  0;
		
		return $value;
	}
	
	function deleteAllTeamMenus()
	{
//		SELECT MAX( `column` ) FROM `table` ;
//		ALTER TABLE `table` AUTO_INCREMENT = number;
	}
	
}