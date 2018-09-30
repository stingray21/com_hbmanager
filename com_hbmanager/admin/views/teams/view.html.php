<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


class hbmanagerViewTeams extends JViewLegacy
{

	function display($tpl = null)
	{
		// Get application
		$app = JFactory::getApplication();
		$context = "hbmanager.list.admin.teams";
		// Get data from the model
		$this->items			= $this->get('Items');
		$this->pagination		= $this->get('Pagination');
		$this->state			= $this->get('State');
		$this->filter_order 	= $app->getUserStateFromRequest($context.'filter_order', 'filter_order', 'order', 'cmd');
		$this->filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd');
		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Set the submenu
		HbmanagerHelper::addSubmenu('teams');

		// Set the toolbar and number of found items
		$this->addToolBar();

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
		$title = JText::_('COM_HBMANAGER_TEAMS_TITLE');

		if ($this->pagination->total)
		{
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'> (" . $this->pagination->total . ")</span>";
		}

		JToolBarHelper::title($title, 'hb-ball');
		JToolBarHelper::addNew('team.add');
		JToolBarHelper::editList('team.edit');
		JToolBarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'teams.delete');
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet( JUri::root() . 'media/com_hbmanager/css/admin.css' );
		$document->setTitle(JText::_('COM_HBMANAGER_TEAMS_TITLE'));
	}
}