<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HbManager Component
 *
 * @since  2.0.0
 */
class HbManagerViewGamereports extends JViewLegacy
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
		$this->season = $this->get('Season');
		$this->games = $this->get('Games');
		$this->selectedGameId = $this->get('selectedGame');
		$this->gameGraph = true;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->games);echo'</pre>';
		

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
		
		JHtml::_('jquery.framework');
		$document->addScript( 'https://d3js.org/d3.v3.min.js' );

		$document->addScript( JUri::root() . 'media/com_hbmanager/js/gamereport.js' );
		$document->addStyleSheet( JUri::root() . 'media/com_hbmanager/css/site.css' );
		$document->setTitle(JText::_('COM_HBMANAGER_GAMEREPORT_TITLE'));
	}

}
