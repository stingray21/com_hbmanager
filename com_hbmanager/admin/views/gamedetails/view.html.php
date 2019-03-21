<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


class hbmanagerViewGamedetails extends JViewLegacy
{

	function display($tpl = null)
	{

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}
		JHtml::_('bootstrap.modal');

		$model = $this->getModel();

		// $post = JRequest::get('post');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($post);echo'</pre>';

		// $dates = (isset($post['gameDates'])) ? $post['gameDates'] : [];
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';

		$this->games = $model->getGames();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->games);echo'</pre>';

		// Set the submenu
		HbmanagerHelper::addSubmenu('gamedetails');

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
		$title = JText::_('COM_HBMANAGER_GAMEDETAILS_TITLE');

		JToolBarHelper::title($title, 'hb-ball');
		JToolBarHelper::custom('showAll', 'list', 'list',  JText::_('COM_HBMANAGER_GAMEDETAILS_TOOLBAR_SHOW_ALL'), false);
		// JToolBarHelper::custom('importAll', 'signup', 'signup',  JText::_('COM_HBMANAGER_GAMEDETAILS_TOOLBAR_IMPORT_ALL'), false);
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->addScript( JUri::root() . 'media/com_hbmanager/js/gamedetails.js' );
		$document->addStyleSheet( JUri::root() . 'media/com_hbmanager/css/admin.css' );
		$document->setTitle(JText::_('COM_HBMANAGER_GAMEDETAILS_TITLE'));
	}
}