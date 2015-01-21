<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HbmanagerViewHbgoalsinput extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
			
		$model = $this->getModel('hbgoalsinput');
		$this->assignRef('model', $model);
		
//		$post = JRequest::get('post');
//		//echo '=> view->post<br><pre>'; print_r($post); echo '</pre>';
//		$this->assignRef('post', $post);
		
		//$teams = $model->getTeams();
		//$this->assignRef('teams', $teams);
		// echo '=> view->teams<br><pre>'; print_r($teams); echo '</pre>';
		
//		$jinput = JFactory::getApplication()->input;
//		$task = $jinput->get('task');
//		if ($task === 'addTeams') {
//			$leagues = $model->getLeagues();
//		}		
//		$this->assignRef('leagues', $leagues);
		
		JToolBarHelper::title(JText::_('COM_HBMANAGER_GOALSINPUT_TITLE'),'hblogo');
		
		// get the stylesheet (with automatic lookup, possible template overrides, etc.)
		//JHtml::stylesheet('admin.stylesheet.css','media/com_hbhvwmanager/css/');
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		//JHtml::stylesheet('com_hbmanager/hbteammenus.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}