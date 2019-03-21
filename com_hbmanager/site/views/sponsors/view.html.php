<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HbManager Component
 *
 * @since  2.0.0
 */
class HbManagerViewSponsors extends JViewLegacy
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
		$this->sponsors = $this->get('Sponsors');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->sponsors);echo'</pre>';
		// $this->season = $this->get('Season');

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
