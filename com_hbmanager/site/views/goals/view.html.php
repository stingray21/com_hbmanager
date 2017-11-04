<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HbManager Component
 *
 * @since  2.0.0
 */
class HbManagerViewGoals extends JViewLegacy
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
		

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

			return false;
		}

		// Display the view
		parent::display($tpl);
	}


}
