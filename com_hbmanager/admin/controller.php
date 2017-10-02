<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');


/**
 * HB Manager Component Controller
 */

class hbmanagerController extends JControllerLegacy
{
	/**
	 * The default view for the display method.
	 *
	 * @var string
	 * @since 12.2
	 */
	// protected $default_view = 'hbmanager';
	protected $default_view = 'teamdata';
	

	function updateTeamData()
	{
		// Set up the data to be sent in the response.
		$model = $this->getModel('teamdata');
		
		//$teamkey = 'M1';
		$jinput = JFactory::getApplication()->input;
		$teamkey = $jinput->get('teamkey');
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($teamkey);echo'</pre>';

		$response = $model->updateTeamData($teamkey);
		// $response = array("teamkey" => $teamkey, "date" => JHTML::_('date', 'now', 'D, d.m.Y - H:i:s', true));

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		//JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');

		// Output the JSON data.
		echo json_encode($response);
		
	}
} 