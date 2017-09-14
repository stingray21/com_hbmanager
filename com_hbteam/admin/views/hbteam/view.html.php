<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HBteamViewHBteam extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
				
//		$model = $this->getModel('hbteam');
//		$this->assignRef('model', $model);
		
//		$post = JRequest::get('post');
//		//echo '=> view->post<br><pre>'; print_r($post); echo '</pre>';
//		$this->assignRef('post', $post);
		
		JToolBarHelper::title(JText::_('COM_HBTEAM'),'hblogo');
		
//		JHtml::stylesheet('com_hbmanager/hbteam.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}