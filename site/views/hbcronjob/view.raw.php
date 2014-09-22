<?php // No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HB HVW Manager Component
 */
class HBmanagerViewHbCronJob extends JViewLegacy
{
	function display($tpl = null)
	{
		$model = $this->getModel('HBcronjob');
		
		JHtml::_('jquery.framework');
		$document = JFactory::getDocument();
		
		if (date('N') === '7' && $model->getDailyUpdateStatus()) {
			echo '<p>Alle Mannschaften</p>';
			$document->addScript(JURI::Root().'/media/com_hbmanager/js/hbcronjob_all.js');
		}
		else {
			echo '<p>Aktuell</p>';
			$document->addScript(JURI::Root().'/media/com_hbmanager/js/hbcronjob.js');
		}


		//echo '<p>raw view</p>';
		parent::display($tpl);
	}
}