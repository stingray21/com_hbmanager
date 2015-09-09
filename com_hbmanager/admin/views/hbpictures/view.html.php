<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HBmanagerViewHbpictures extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel('hbpictures');
		//echo '=> view->post<br><pre>'; print_r($this); echo '</pre>';
		$this->assignRef('model', $model);
		
		$teams = $model->getTeams();
		$this->assignRef('teams', $teams);
		
		$post = JRequest::get('post');
		//echo '=> view->post<br><pre>'; print_r($post); echo '</pre>';
		$this->assignRef('post', $post);
		
		JToolBarHelper::title(JText::_('COM_HBMANAGER_PICTURES_TITLE'),'hblogo');
		
		// get the stylesheet (with automatic lookup, 
		// possible template overrides, etc.)
		// JHtml::stylesheet('admin.stylesheet.css','media/com_hbmanager/css/');
		 JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}