<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HbManager Component
 *
 * @since  2.0.0
 */
class HbManagerViewReminder extends JViewLegacy
{
	function display($tpl = null)
	{
		
		$jinput = JFactory::getApplication()->input;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($jinput);echo'</pre>';
		
		$viewOption = $jinput->get('viewoption');
		
		if ($viewOption === 'plain') $this->setLayout('plain');

		$model = $this->getModel();

		// $start = microtime(true);

		$model->setDates();
		$this->dates = $model->getDates();
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $this->dates ,1).'</pre>';

		$this->prevgames = $model->getPrevGames();
		$this->reports = $model->getReports();
		// $this->pregames = $model->getPregames();
		$nextAndHomeGames = $model->getNextAndHomeGames();
		$this->nextgames = $nextAndHomeGames->nextgames;
		$this->homegames = $nextAndHomeGames->homegames;

		$this->result = $model->getReport();

		$this->holidays = $model->getCurrentHolidays();
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $this->holidays ,1).'</pre>';
		$this->upcomingHolidays = $model->getUpcomingHolidays();
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $this->upcomingHolidays ,1).'</pre>';

		$this->reminderFlag = $model->getReminderFlag();
		$this->remainingWeeks = $model->getWeeksToNextHoliday();
			
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
		$document->setTitle(JText::_('COM_HBMANAGER_REMINDER_TITLE'));
	}
}
