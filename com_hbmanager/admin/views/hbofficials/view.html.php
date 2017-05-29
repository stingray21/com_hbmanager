<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HBofficialsViewHBofficials extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$model = $this->getModel('HBofficials');
		$this->assignRef('model', $model);
		
		
		JToolBarHelper::title(JText::_('COM_HBOFFICIALS_HOME_TITLE'), 'hblogo');
		
		
		// get the stylesheet (with automatic lookup, 
		// possible template overrides, etc.)
		//JHtml::stylesheet('admin.stylesheet.css','media/com_hbmanager/css/');
		JHtml::stylesheet('com_hbofficials/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}