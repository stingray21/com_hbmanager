<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB HVW Manager Component
 */
class HBmanagerViewHbdata extends JViewLegacy
{
	function display($tpl = null)
	{
		$model = $this->getModel('HBdata');
		$this->assignRef('model', $model);
		
		parent::display($tpl);
	}
}