<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


class hbmanagerViewEmails extends JViewLegacy
{

	function display($tpl = null)
	{
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		$model = $this->getModel();


		$this->bodytemplate = $model->getEmailTemplate();
		$this->teams = $model->getTeams();
		$this->excelLink = -1;


		// Set the submenu
		HbmanagerHelper::addSubmenu('emails');

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
		$title = JText::_('COM_HBMANAGER_EMAILS_TITLE');

		JToolBarHelper::title($title, 'hb-ball');
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::root() . 'media/com_hbmanager/css/admin.css');
		$document->setTitle(JText::_('COM_HBMANAGER_EMAILS_TITLE'));
	}
}