<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HbManager Model
 *
 * @since  0.0.1
 */
class HBmanagerModelTeamPages extends JModelAdmin
{
	// private $season;
 	private $table_team = '#__hb_team';

 	private $titles = array();
	private $alias = array();
	private $categoryId = array();
	private $categoryMenutype = array();
	private $componentId = '';
	


	public function __construct($config = array())
	{

		parent::__construct($config);

		self::setCategoryIds();
		self::setCategoryMenutype();
		self::setComponentId();
		self::setTitles();
		self::setAlias();
		
    }

    public function getForm($data = array(), $loadData = true)
	{
		// TODO: implement this method
		
		// // Get the form.
		// $form = $this->loadForm(
		// 	'com_hbmanager.team',
		// 	'team',
		// 	array(
		// 		'control' => 'jform',
		// 		'load_data' => $loadData
		// 	)
		// );

		// if (empty($form))
		// {
		// 	return false;
		// }

		// return $form;
	}

	function getTeams()
	{
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query->select('`teamkey`, `order`, `team`, `sex`, `youth` '
			.', IF(`youth` = '.$db->q('aktiv').', \'grownups\' , \'youth\' ) AS `teamCategory` '
			.',`id`, `menutype`, `title`, `alias`'
			);
		$query->from($db->qn($this->table_team).' AS team');
		$query->leftJoin($db->qn('#__menu').' AS menu ON '
			. '(LOWER(team.teamkey) = menu.alias)');
		$query->group($db->qn('teamkey'));
		$query->order('ISNULL('.$db->qn('order').'), '.
					$db->qn('order').' ASC');		
		
		$db->setQuery($query);
		$teams = $db->loadObjectList();

		$aliases = new stdClass(); //self::getMenuAliases();
		$teams = self::addCheckboxFlags($teams, $aliases);

		return $teams;
	}

	private function addCheckboxFlags($teams, $aliases)
	{
		foreach ($teams as &$team) {
			//echo __FUNCTION__.'<pre>';print_r($team); echo'</pre>';
			$team->menus = null;
			foreach ($aliases as $alias) {
				// TODO 
				if (strpos($alias, $team->alias) !== false ) {
					$team->menus['add][team'] 	 = (strcmp($alias, $team->alias) === 0);
					$team->menus['add][players'] = (strpos($alias, 'spieler') !== false );
					$team->menus['add][reports'] =  (strpos($alias, 'berichte') !== false );
					$team->menus['add][goals'] 	 = (strpos($alias, 'tore') !== false );
				}
			}
		}
		//echo __FUNCTION__.'<pre>';print_r($teams); echo'</pre>';
		return $teams;
	}
	
private function getMenuAliases()
{
	$db = $this->getDbo();
	$query = $db->getQuery(true);
	$query->select($db->qn('alias').', '.$db->qn('id'));
	$query->from('#__menu');
	$query->where($db->qn('parent_id').' IN ('.
			$db->q($this->categoryId['grownups']).','.$db->q($this->categoryId['youth']).')');

	$db->setQuery($query);
	$aliases = $db->loadObjectList();die;
	//$aliases = $db->loadColumn(0); // without sub menu aliases
	// $aliases = self::addSubMenuAliases($db->loadColumn(1));
	echo __FILE__.' ('.__LINE__.'):<pre>';print_r($aliases);echo'</pre>';
	return $aliases;
}

private function addSubMenuAliases($ids)
{
	$ids = array_merge($ids, $this->categoryId);
	$db = $this->getDbo();
	$query = $db->getQuery(true);
	$query->select($db->qn('alias').', '.$db->qn('id'));
	$query->from('#__menu');
	$query->where($db->qn('parent_id').' IN ('.implode(',', $ids).')');
	//echo __FUNCTION__.'<pre>'.$query.'</pre>';
	$db->setQuery($query);
	$aliases = $db->loadColumn();
	//echo __FUNCTION__.'<pre>';print_r($aliases); echo'</pre>';
	return $aliases;
}


	function setCategoryIds()
	{
		$params = JComponentHelper::getParams( 'com_hbmanager' );
		$menuActive = $params->get( 'menuActive' );
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($params);echo'</pre>';
		
		$id['grownups'] = $menuActive;
		
		$menuYouth = $params->get( 'menuYouth' );
		
		if ($menuYouth !== 'ONLY1')  {
			$id['youth'] = $menuYouth;
		} else {
			$id['youth'] = $menuActive;
		}
	
		//echo __FUNCTION__.'<pre>';print_r($id); echo'</pre>';
		return $this->categoryId = $id;
	}
	
	protected function setCategoryMenutype()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('menutype'));
		$query->from('#__menu');
		$query->where($db->qn('id').' = '.$db->q($this->categoryId['grownups']));
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$menutype['grownups'] = $db->loadResult();
		
		$params = JComponentHelper::getParams( 'com_hbmanager' );
		$menuYouth = $params->get( 'menuYouth' );
		if ($menuYouth !== 'ONLY1')  {
			$query = $db->getQuery(true);
			$query->select($db->qn('menutype'));
			$query->from('#__menu');
			$query->where($db->qn('id').' = '.$db->q($this->categoryId['youth']));
			//echo __FUNCTION__.'<pre>'.$query.'</pre>';
			$db->setQuery($query);
			$menutype['youth'] = $db->loadResult();
		} else {
			$menutype['youth'] = $menutype['grownups'];
		}
		
		//echo __FUNCTION__.'<pre>';print_r($alias); echo'</pre>';
		
		return $this->categoryMenutype = $menutype;
	}
	
	
	function setComponentId()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('extension_id'));
		$query->from('#__extensions');
		$query->where($db->qn('element').' = '.$db->q($this->table_team));
		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
		$db->setQuery($query);
		$id = $db->loadResult();
		//echo __FUNCTION__.'<pre>';print_r($id); echo'</pre>';
		return $this->componentId = $id;
	}
	
// 	function getMenuId($alias)
// 	{
// 		$db = $this->getDbo();
// 		$query = $db->getQuery(true);
// 		$query->select($db->qn('id').', '.$db->qn('alias'));
// 		$query->from('#__menu');
// 		//$query->where($db->qn('menutype').' = '.$db->q('teams'));
// 		$query->where($db->qn('alias').' = '.$db->q($alias));
// 		//echo __FUNCTION__.'<pre>'.$query.'</pre>';
// 		$db->setQuery($query);
// 		$id = $db->loadResult();
// 		//echo __FUNCTION__.'<pre>';print_r($id); echo'</pre>';
// 		return $id;
// 	}
	
	
	function setTitles()
	{
		// TODO: language (in backend options)
		$titles['players'] = 'Spieler';
		$titles['goals'] = 'Torschützen';
		$titles['reports'] = 'Spielberichte';
		//echo __FUNCTION__.'<pre>';print_r($titles); echo'</pre>';
		return $this->titles = $titles;
	}
	
	function setAlias()
	{
		// TODO: language (in backend options)
		$alias['players'] = '-spieler';
		$alias['goals'] = '-tore';
		$alias['reports'] = '-berichte';
		//echo __FUNCTION__.'<pre>';print_r($alias); echo'</pre>';
		return $this->alias = $alias;
	}

	// add menus ----------------------------------------------------------------
	
	function addMenuItems ($teams)
	{
		echo __FUNCTION__.'<pre>';print_r($teams[0]); echo'</pre>';
		foreach ($teams as $team)
		{
			//echo __FUNCTION__.'<pre>';print_r($team); echo'</pre>';
			if (isset($team['add']['main'])) {
				$team['menutype'] =  $this->categoryMenutype[$team['teamCategory']];
				$team['parentId'] = $this->categoryId[$team['teamCategory']];
				$team['alias'] = strtolower($team['teamkey']);
				$team['title'] =  $team['team'];
				$team['id'] = self::addItem($team);
				
				//self::addSubmenus($team);
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
				$submenu['menutype'] =  $team['menutype'];
				$submenu['title'] =  $this->titles[$key];
				$submenu['alias'] = $team['alias'].$this->alias[$key];
				$submenu['kuerzel'] = $team['kuerzel'];
				$submenu['parentId'] = $team['id'];
				$submenu['id'] = self::getMenuId($submenu['alias']);
				$submenu['view'] = $key;
				//echo __FUNCTION__.'<pre>';print_r($submenu); echo'</pre>';
				self::addItem($submenu);
			}
		}
	}
	
	function addItem ($team)
	{
		//echo __FUNCTION__.'<pre>';print_r($team); echo'</pre>';
		$table = JTable::getInstance('Menu', 'JTable');
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
		$value['menutype'] =  $team['menutype'];
		$value['title'] =  $team['title'];
		$value['alias'] =  $team['alias'];
		$value['note'] =  '';
		$view = isset($team['view']) ? $team['view'] : '';
		$value['link'] =  'index.php?option=com_hbteam&view=hbteam'.$view;
		$value['type'] =  'component';
		$value['component_id'] =  $this->componentId;
		$value['template_style_id'] =  0;
			$params['teamkey'] = $team['teamkey'];
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

}

	
	