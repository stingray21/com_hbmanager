<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


class hbmanagerViewTeamPages extends JViewLegacy
{
	/**
	 * The sidebar markup
	 *
	 * @var  string
	 */
	protected $sidebar;

	function display($tpl = null)
	{

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Assign data to the view
		$this->teams = $this->get('Teams');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->teams);echo'</pre>';

		// Set the submenu
		HbmanagerHelper::addSubmenu('teampages');

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
		$title = JText::_('COM_HBMANAGER_TEAMPAGES_TITLE');

		JToolBarHelper::title($title, 'hb-ball');
		JToolBarHelper::custom('teampages.save', 'save', 'save',  JText::_('COM_HBMANAGER_TEAMPAGES_TOOLBAR_SAVE'), false);
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
		$document->setTitle(JText::_('COM_HBMANAGER_TEAMPAGES_TITLE'));
	}
}