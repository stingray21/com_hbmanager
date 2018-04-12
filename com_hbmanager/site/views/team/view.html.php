<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HbManager Component
 *
 * @since  2.0.0
 */
class HbManagerViewTeam extends JViewLegacy
{
	/**
	 * Display the HB Manager view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// Assign data to the view
		$this->tz = $this->get('Timezone');
		$this->team = $this->get('Team');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->team);echo'</pre>';
		$this->show = $this->get('ShowParams');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->show);echo'</pre>';
		$this->trainings = $this->get('Training');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->trainings);echo'</pre>';
		$this->coaches = $this->get('Coaches');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->coaches);echo'</pre>';
		$this->schedule = $this->get('Schedule');
		// // echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->schedule);echo'</pre>';
		$this->standings = $this->get('Standings');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->standings);echo'</pre>';

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

			return false;
		}

		// Display the view
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		
		$document->addStyleSheet( JUri::root() . 'media/com_hbmanager/css/site.css' );
		// $document->setTitle(JText::_('COM_HBMANAGER_GOALS_TITLE'));
	}

}
