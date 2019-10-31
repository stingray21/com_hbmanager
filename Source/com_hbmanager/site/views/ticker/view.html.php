<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HbManager Component
 *
 * @since  2.0.0
 */
class HbManagerViewTicker extends JViewLegacy
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
		$this->tz = $this->get('Timezone');
		
		$jinput = JFactory::getApplication()->input;
		$this->token = $jinput->get('token'); // done in JS script

		// $this->gameInfo = $this->get('gameInfo');
		$this->baseUrl = $this->get('baseUrl');
		$this->testMode = $this->get('TestMode');

		$this->ticker = (!empty($this->baseUrl) && !empty($this->token)) ? true : false;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->ticker);echo'</pre>';
		

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
		
		// JHtml::_('jquery.framework');
		// $document->addScript( 'https://d3js.org/d3.v3.min.js' );
		$document->addScript( JUri::root() . 'media/com_hbmanager/js/d3.min.js' );

		$document->addScriptDeclaration( "
		var base_url = '".$this->baseUrl."';
		var testMode = '".$this->testMode."';
		" );

		$document->addScript( JUri::root() . 'media/com_hbmanager/js/ticker.js' );
		$document->addStyleSheet( JUri::root() . 'media/com_hbmanager/css/site.css' );
		$document->setTitle(JText::_('COM_HBMANAGER_TICKER_TITLE'));
	}

}
