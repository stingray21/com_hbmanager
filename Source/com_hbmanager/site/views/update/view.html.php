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
	/**
	 * Display the HB Manager view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// Get application
		$app = JFactory::getApplication();
		// $context = "hbmanager.list.admin.teams";

		// Get data from the model
		$this->items			= $this->get('Items');
		$this->pagination		= $this->get('Pagination');
		// $this->state			= $this->get('State');
		// $this->filter_order 	= $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'order', 'cmd');
		// $this->filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd');
		// $this->filterForm    	= $this->get('FilterForm');
		// $this->activeFilters 	= $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Assign data to the view
		$this->dateFormat = $this->get('DateFormat');
		$this->dateFormatMobile = $this->get('DateFormatMobile');
		$this->teamList = $this->get('TeamList');

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
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
		$document->addStyleSheet( JUri::root() . 'media/com_hbmanager/css/site.css' );
		$document->setTitle(JText::_('COM_HBMANAGER_UPDATE_TITLE'));
	}
}
