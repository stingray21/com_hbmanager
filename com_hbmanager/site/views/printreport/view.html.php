<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HbManager Component
 *
 * @since  2.0.0
 */
class HbManagerViewPrintreport extends JViewLegacy
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

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Assign data to the view
		$this->dateFormat = $this->get('DateFormat');
		$this->dateFormatMobile = $this->get('DateFormatMobile');

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
		$document->setTitle(JText::_('COM_HBMANAGER_PRINTREPORT_TITLE'));
	}
}
