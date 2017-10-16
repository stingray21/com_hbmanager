<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


class hbmanagerViewGamesPrev extends JViewLegacy
{

	function display($tpl = null)
	{

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		$model = $this->getModel();

		$post = JRequest::get('post');
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($post);echo'</pre>';

		$dates = (isset($post['gameDates'])) ? $post['gameDates'] : [];
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($dates);echo'</pre>';
		// $dates['prevStart'] = '2017-10-14';
		// $dates['prevEnd'] 	= '2017-10-15';

		$model->setDates($dates);
		$this->dates = $model->getDates();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->dates);echo'</pre>';
		$this->games = $model->getPrevGames();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->games);echo'</pre>';

		$config = new JConfig();
		$this->user = JFactory::getUser();
		// $userid = $user->id;

		// Set the submenu
		HbmanagerHelper::addSubmenu('gamesprev');

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
		$title = JText::_('COM_HBMANAGER_GAMES_PREV_TITLE');

		JToolBarHelper::title($title, 'teams');
		JToolBarHelper::custom('games.saveReport', 'save', 'save',  JText::_('COM_HBMANAGER_GAMES_TOOLBAR_SAVE'), false);
		JToolBarHelper::custom('games.publishReport', 'out', 'out',  JText::_('COM_HBMANAGER_GAMES_TOOLBAR_PUBLISH'), false);
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
		$document->setTitle(JText::_('COM_HBMANAGER_GAMES_PREV_TITLE'));
	}
}