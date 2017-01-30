<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB Manager Component
 */
class HbmanagerViewHbreportinput extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
			
		$model = $this->getModel('hbreportinput');
		$this->assignRef('model', $model);
		
		$links = $model->getLinks();
		$this->assignRef('links', $links);
		// echo __FILE__.' ('.__LINE__.')'.'<pre>';print_r($links); echo'</pre>';
		
		JToolBarHelper::title(JText::_('COM_HBMANAGER_GOALSINPUT_TITLE'),'hblogo');
		
		JHtml::stylesheet('com_hbmanager/admin.stylesheet.css', array(), true);
		
		// Display the view
		parent::display($tpl);
	}
}