<?php


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


class hbmanagerViewPrintNews extends JViewLegacy
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

		$model->setDates($dates);
		$this->dates = $model->getDates();
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->dates);echo'</pre>';
		$this->prevgames = $model->getPrevGames();
		$this->reports = $model->getReports();
		// $this->pregames = $model->getPregames();
		$nextAndHomeGames = $model->getNextAndHomeGames();
		$this->nextgames = $nextAndHomeGames->nextgames;
		$this->homegames = $nextAndHomeGames->homegames;

		// Set the submenu
		HbmanagerHelper::addSubmenu('printnews');

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
		$title = JText::_('COM_HBMANAGER_GAMES_PRINTNEWS_TITLE');

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
		$document->addStyleSheet( JUri::root() . 'media/com_hbmanager/css/admin.css' );
		$document->setTitle(JText::_('COM_HBMANAGER_GAMES_PRINTNEWS_TITLE'));
	}
}