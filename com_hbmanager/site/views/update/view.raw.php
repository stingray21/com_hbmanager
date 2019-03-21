<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HbManager Component
 *
 * @since  2.0.0
 */
class HbManagerViewUpdate extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$jinput = JFactory::getApplication()->input;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($jinput);echo'</pre>';
		// $teamkey = $jinput->get('teamkey');
		// if (empty($teamkey)) $teamkey = 'all';
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($teamkey);echo'</pre>';
		
		$viewOption = $jinput->get('viewoption');
		
		if ($viewOption === 'plain') $this->setLayout('plain');

		$model = $this->getModel();

		// $start = microtime(true);
		
		$this->result = $model->updateMultipleTeams();
			
		// $time_elapsed = microtime(true) - $start;
		// $this->assignRef('time_elapsed', $time_elapsed);
		// echo '<p>'.$time_elapsed.' µs</p>';			

		
		//echo '<p>raw view</p>';
		parent::display($tpl);

		// Set the document
		// $this->setDocument();
	}

		/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->addScript( JUri::root() . 'media/com_hbmanager/js/teamdata.js' );
		$document->setTitle(JText::_('COM_HBMANAGER_CRONJOB_TITLE'));
	}
}
