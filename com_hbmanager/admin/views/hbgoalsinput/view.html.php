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
				
		//$teams = $model->getTeams();
		//$this->assignRef('teams', $teams);
		// echo '=> view->teams<br><pre>'; print_r($teams); echo '</pre>';
		
		$confirmation = $model->getConfirmation();
		$this->assignRef('confirmation', $confirmation);
//		echo __FILE__.' ('.__LINE__.')'.'<pre>';print_r($confirmation); echo'</pre>';
		
		$inputData = $model->getInputData();
		$this->assignRef('inputData', $inputData);
//		echo __FILE__.' ('.__LINE__.')'.'<pre>';print_r($inputData); echo'</pre>';
		
		$links = $model->getLinks();
		$this->assignRef('links', $links);
//		echo __FILE__.' ('.__LINE__.')'.'<pre>';print_r($links); echo'</pre>';
		
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