<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * HB Hallenverzeichnis Component Controller
 */
class HBGymsController extends JControllerLegacy
{
	
	function updateGyms()
	{
		// Set up the data to be sent in the response.
		$model = $this->getModel('hbdata');
		
		$jinput = JFactory::getApplication()->input;
		$teamkey = $jinput->get('teamkey', 'all');

		$response = $model->updateGyms();;
		//$response = array("success" => true);

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');

		// Output the JSON data.
		echo json_encode($response);
	}
}