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
		
		
		if (isset($this->javaScript) && $this->javaScript) {
			//echo '<p>JavaScript</p>';
			$document->addScript(JURI::Root().'/media/com_hbmanager/js/hbcronjob.js');
		}
		else {
			//echo '<p>without JavaScript</p>';
			
			// continue the script execution after disconnection (cron-job.org)
			// ignore_user_abort(true);
			
			$start = microtime(true);
			
			$result = $model->updateHvwDataCronjob();
			//echo '<pre> ->$result ';print_r($result); echo '</pre>';
			$this->assignRef('result', $result);
			
			$time_elapsed = microtime(true) - $start;
			$this->assignRef('time_elapsed', $time_elapsed);
			//echo '<p>'.$time_elapsed_us.' µs</p>';
		}

		
		
		//echo '<p>raw view</p>';
		parent::display($tpl);
	}
}