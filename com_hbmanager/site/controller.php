<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HbManager Component Controller
 *
 * @since  2.0.0
 */
class HbmanagerController extends JControllerLegacy
{
	function updateTeamData()
	{
		// Set up the data to be sent in the response.
		// $model = $this->getModel('hbcronjob');
		
		//$teamkey = 'M1';
		$jinput = JFactory::getApplication()->input;
		$teamkey = $jinput->get('teamkey');

		// $model->updateTeam($teamkey);

		// $response = $model->getUpdateDate($teamkey);
		$response = array("teamkey" => "M-1", "date" => date("Y-m-d H:i:s"));

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
