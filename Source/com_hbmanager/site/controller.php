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
		$model = $this->getModel('update');
		
		//$teamkey = 'M1';
		$jinput = JFactory::getApplication()->input;
		$teamkey = $jinput->get('teamkey');
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($teamkey);echo'</pre>';

		$response = $model->updateTeamData($teamkey);
		// $response = array("teamkey" => $teamkey, "date" => JHTML::_('date', 'now', 'D, d.m.Y - H:i:s', true));

		// Get the document object.
		$document = JFactory::getDocument();

		// Change the suggested filename 
		// -> returns result.json file instead of being displayed in the browser
		// JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Output the JSON data.
		$response = json_encode($response);
		echo $response;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($response);echo'</pre>';
	}


	function update()
	{
		$jinput = JFactory::getApplication()->input;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($jinput);echo'</pre>';
		$teamkey = $jinput->get('teamkey');
		if (empty($teamkey)) $teamkey = 'all';
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($teamkey);echo'</pre>';
		
		// Get the model
		// $model = $this->getModel('update');
		// echo __FILE__.' ('.__LINE__.'):<pre>'.$model.'</pre>';


		// http://localhost/handball/hb_joomla3/index.php?option=com_hbmanager&task=update

		// Redirect 
		$this->setRedirect(JRoute::_('index.php?option=com_hbmanager&view=update', false));
	}

	function updateCronjob()
	{
		$jinput = JFactory::getApplication()->input;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($jinput);echo'</pre>';
		$teamkey = $jinput->get('teamkey');
		if (empty($teamkey)) $teamkey = 'all';
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($teamkey);echo'</pre>';
		
		// Get the model
		// $model = $this->getModel('update');
		// echo __FILE__.' ('.__LINE__.'):<pre>'.$model.'</pre>';

		// http://localhost/handball/hb_joomla3/index.php?option=com_hbmanager&task=updateCronjob

		// Redirect 
		$this->setRedirect(JRoute::_('index.php?option=com_hbmanager&view=update&format=raw&viewoption=plain', false));
	}

	function sendReminder()
	{
		$jinput = JFactory::getApplication()->input;
		echo __FILE__.' ('.__LINE__.'):<pre>';print_r($jinput);echo'</pre>';
		
		// Get the model
		// $model = $this->getModel('update');
		// echo __FILE__.' ('.__LINE__.'):<pre>'.$model.'</pre>';

		// http://hkog.local/index.php?option=com_hbmanager&task=sendReminder

		// Redirect 
		$this->setRedirect(JRoute::_('index.php?option=com_hbmanager&view=reminder&format=raw&viewoption=plain', false));
	}

	function getGameData()
	{
		// Set up the data to be sent in the response.
		$model = $this->getModel('gamereports');
		
		//$teamkey = 'M1';
		$jinput = JFactory::getApplication()->input;
		$teamkey = $jinput->get('teamkey');
		$gameId = $jinput->get('gameId');
		// echo __FILE__.'('.__LINE__.'):<pre>';print_r($gameId);echo'</pre>';

		// $response = $model->getGameData($teamkey, $gameId);
		$response = $model->getGameDataAll($teamkey);
		// $response = array("teamkey" => $gameId);

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename 
		// -> returns result.json file instead of being displayed in the browser
		// JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');

		// http://localhost/handball/hb_joomla3/index.php?option=com_hbmanager&task=getGameData&format=raw&teamkey=M-1&gameId=70026

		// Output the JSON data.
		echo json_encode($response);
		
	}

	function getGamePdfList()
	{
		// Set up the data to be sent in the response.
		$model = $this->getModel('gamedetailsupdate');
		
		$response = $model->getGamePdfList();

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Output the JSON data.
		echo json_encode($response);		

		// http://handball.local/index.php?option=com_hbmanager&task=getGamePdfList&format=raw
	}

	function importAllGameDetails()
	{
		// Set up the data to be sent in the response.
		$model = $this->getModel('gamedetailsupdate');
		
		$response = $model->getGameImportList();

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Output the JSON data.
		echo json_encode($response);		

		// http://handball.local/index.php?option=com_hbmanager&task=importAllGameDetails&format=raw
	}

	function getAdditionalGameInfo()
	{
		// Set up the data to be sent in the response.
		$model = $this->getModel('ticker');
		
		//$teamkey = 'M1';
		$jinput = JFactory::getApplication()->input;
		$gameId = $jinput->get('gameId');
		// echo __FILE__.'('.__LINE__.'):<pre>';print_r($gameId);echo'</pre>';

		// $response = $model->getGameData($teamkey, $gameId);
		$response = $model->getAdditionalGameInfo($gameId);
		// $response = array("teamkey" => $gameId);

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename 
		// -> returns result.json file instead of being displayed in the browser
		// JResponse::setHeader('Content-Disposition','attachment;filename="result.json"');

		// http://handball.local/hkog/index.php?option=com_hbmanager&task=getAdditionalGameInfo&format=raw&gameId=70026

		// Output the JSON data.
		echo json_encode($response);
		
	}
}
