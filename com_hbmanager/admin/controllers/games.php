<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class HbmanagerControllerGames extends JControllerAdmin
{

	// public function getModel($name = 'GamesPrev', $prefix = 'HbmanagerModel', $config = array('ignore_request' => true))
	// {
	// 	$model = parent::getModel($name, $prefix, $config);
	// 	echo "TESTING";
	// 	return $model;
	// }

	// public function selectDates($name = 'gamesprev', $prefix = 'HbmanagerModel', $config = array('ignore_request' => true))
	// {
	// 	$post = JRequest::get('post');
	// 	// $gamesPrev = (isset($post['gamesprev'])) ? $post['gamesprev'] : null;
	// 	echo __FILE__.' ('.__LINE__.'):<pre>';print_r($post);echo'</pre>';

	// 	// $model = $this->getModel('gamesprev'); 
	// 	// $model->updateReportsInDB($gamesPrev);

	// 	$this->setRedirect(JRoute::_('index.php?option=com_hbmanager&view=gamesprev', false));
	// }

	public function saveReport($name = 'gamesprev', $prefix = 'HbmanagerModel', $config = array('ignore_request' => true))
	{
		$post = JRequest::get('post');
		$gamesPrev = (isset($post['gamesprev'])) ? $post['gamesprev'] : null;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($post);echo'</pre>';

		$model = $this->getModel('gamesprev'); 
		$model->updateReportsInDB($gamesPrev);

		$this->setRedirect(JRoute::_('index.php?option=com_hbmanager&view=gamesprev&prevStart='.$post['gameDates']['prevStart'].'&prevEnd='.$post['gameDates']['prevEnd'], false));
	}	

	public function publishReport($name = 'gamesprev', $prefix = 'HbmanagerModel', $config = array('ignore_request' => true))
	{
		$post = JRequest::get('post');
		$gamesPrev = (isset($post['gamesprev'])) ? $post['gamesprev'] : null;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($post);echo'</pre>';

		$model = $this->getModel('gamesprev'); 
		$model->updateReportsInDB($gamesPrev);

		$checkedGames = $model->getIncludedGames($gamesPrev);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($checkedGames);echo'</pre>';
		if (array_sum($checkedGames) > 0) {
			$model->writeNews($checkedGames);
		} else {
			echo __FILE__.' ('.__LINE__.'):<pre>COM_HBMANAGER_GAMES_NO_GAMES_SELECTED</pre>';
			JFactory::getApplication()->enqueueMessage(JText::_('COM_HBMANAGER_GAMES_NO_GAMES_SELECTED'), 'warning');
		}

		$this->setRedirect(JRoute::_('index.php?option=com_hbmanager&view=gamesprev&prevStart='.$post['gameDates']['prevStart'].'&prevEnd='.$post['gameDates']['prevEnd'], false));
	}

	public function publishAllReport($name = 'gamesprev', $prefix = 'HbmanagerModel', $config = array('ignore_request' => true))
	{
		$post = JRequest::get('post');
		$gamesPrev = (isset($post['gamesprev'])) ? $post['gamesprev'] : null;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($post);echo'</pre>';

		$model = $this->getModel('gamesprev'); 
		$model->updateReportsInDB($gamesPrev);

		foreach ($gamesPrev as $key => $value) {
			$allGames[$value['gameIdHvw']] = true;
		}
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($allGames);echo'</pre>';
		if (array_sum($allGames) > 0) {
			$model->writeNews($allGames);
		} 

		$this->setRedirect(JRoute::_('index.php?option=com_hbmanager&view=gamesprev&prevStart='.$post['gameDates']['prevStart'].'&prevEnd='.$post['gameDates']['prevEnd'], false));
	}

	
	public function savePregame($name = 'gamesnext', $prefix = 'HbmanagerModel', $config = array('ignore_request' => true))
	{
		$post = JRequest::get('post');
		$gamesNext = (isset($post['gamesnext'])) ? $post['gamesnext'] : null;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($post);echo'</pre>';

		$model = $this->getModel('gamesnext'); 
		$model->updatePregamesInDB($gamesNext);

		$this->setRedirect(JRoute::_('index.php?option=com_hbmanager&view=gamesnext&nextStart='.$post['gameDates']['nextStart'].'&nextEnd='.$post['gameDates']['nextEnd'], false));
	}

	public function publishPregame($name = 'gamesnext', $prefix = 'HbmanagerModel', $config = array('ignore_request' => true))
	{
		$post = JRequest::get('post');
		$gamesNext = (isset($post['gamesnext'])) ? $post['gamesnext'] : null;
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($post);echo'</pre>';

		$model = $this->getModel('gamesnext'); 
		$model->updatePregamesInDB($gamesNext);
		
		$checkedGames = $model->getIncludedGames($gamesNext);
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($checkedGames);echo'</pre>';
		if (array_sum($checkedGames) > 0) {
			$model->writeNews($checkedGames);
		} else {
			echo __FILE__.' ('.__LINE__.'):<pre>COM_HBMANAGER_GAMES_NO_GAMES_SELECTED</pre>';
			JFactory::getApplication()->enqueueMessage(JText::_('COM_HBMANAGER_GAMES_NO_GAMES_SELECTED'), 'warning');
		}

		$this->setRedirect(JRoute::_('index.php?option=com_hbmanager&view=gamesnext&nextStart='.$post['gameDates']['nextStart'].'&nextEnd='.$post['gameDates']['nextEnd'], false));
	}

	public function publishAllPregame($name = 'gamesnext', $prefix = 'HbmanagerModel', $config = array('ignore_request' => true))
	{
		$post = JRequest::get('post');
		$gamesNext = (isset($post['gamesnext'])) ? $post['gamesnext'] : null;
		echo __FILE__.' ('.__LINE__.'):<pre>';print_r($post);echo'</pre>';

		$model = $this->getModel('gamesnext'); 
		$model->updatePregamesInDB($gamesNext);

		foreach ($gamesNext as $key => $value) {
			$allGames[$value['gameIdHvw']] = true;
		}
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($allGames);echo'</pre>';
		if (array_sum($allGames) > 0) {
			$model->writeNews($allGames);
		} 

		$this->setRedirect(JRoute::_('index.php?option=com_hbmanager&view=gamesnext&nextStart='.$post['gameDates']['nextStart'].'&nextEnd='.$post['gameDates']['nextEnd'], false));
	}

}
