<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HB Team Home Model
 */
class hbteamModelHBteamSummary extends JModelLegacy
{

	
	function __construct() 
	{
		parent::__construct();
		
		//request the selected teamkey
			$menuitemid = JRequest::getInt('Itemid');
			if ($menuitemid)
			{
				$menu = JFactory::getApplication()->getMenu();
				$menuparams = $menu->getParams($menuitemid);
			}
			$this->youth = $menuparams->get('youth');
			$this->saison = $menuparams->get('saison');
			$this->showHomeGym = $menuparams->get('showhomegym');
			
			if ($this->youth) $this->link = 'jugend';
			else $this->link = 'aktive';
			
			$this->items = array('email','mobile','telephone');
			$this->global_show = self::getGlobalContactSettings();
	}
	
	function getTeams()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('hb_mannschaft');
		$youth = '';
		if ($this->youth) {
			$youth = '!';
		}
		$query->where($db->qn('jugend').' '.$youth.'= '.$db->q('aktiv'));
		$query->leftJoin($db->qn('hb_mannschaftsfoto').' USING ('.
			$db->qn('kuerzel').')');
		$query->order('ISNULL('.$db->qn('reihenfolge').'), '.
			$db->qn('reihenfolge').' ASC');
		//echo '=> model->$query <br><pre>"; print_r($query); echo "</pre>';
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		$teams = self::addTrainingInfo($teams);
		return $teams;
	}
	
	protected function addTrainingInfo($teams)
	{
		foreach ($teams as $team) {
			$team->trainings = self::getTrainings ($team->kuerzel);
			$team->trainer = self::getTrainer ($team->kuerzel);
		}
		return $teams;
	}
	
	protected function getTrainings ($teamkey) 
	{
		$db = $this->getDbo();
		// getting training information
		$query = $db->getQuery(true);
		//$query->select('*');
		$query->select('tag, DATE_FORMAT(beginn, \'%H:%i\') as beginn'.
			', DATE_FORMAT(ende, \'%H:%i\') as ende, bemerkung, sichtbar,'.
			' hallenNr, kurzname, hallenName, strasse, plz, stadt');
		$query->from($db->qn('hb_mannschaft_training'));
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
		$query->leftJoin('hb_training USING ('.$db->qn('trainingID').')');
		$query->leftJoin('hb_halle USING (hallenNr)');
		$query->order('FIELD('.$db->qn('tag').','.$db->q('Mo').','.
			$db->q('Di').','.$db->q('Mi').','.$db->q('Do').','.
			$db->q('Fr').','.$db->q('Sa').','.$db->q('So').')');
		//echo '=> model->$query <br><pre>'.$query.'</pre>';
		$db->setQuery($query);
		$trainings = $db->loadObjectList ();
		//echo "Trainings<pre>"; print_r($trainings); echo "</pre>";

		//display and convert to HTML when SQL error
		if (is_null($posts=$db->loadRowList()))
		{
			$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
			return;
		}
		$trainings = self::formatTraining($trainings);
		return $trainings;
	}
	
	protected function formatTraining($trainings)
	{
		foreach ($trainings as $training) {
			if ($this->showHomeGym == '1' OR $training->hallenNr != 7014){
				$training->halleAnzeige = $training->hallenName;
			}
		}
		return $trainings;
	}
		
	protected function getGlobalContactSettings() 
	{
		$db = $this->getDbo();
		// getting global contact settings
		$query = $db->getQuery(true);
		$query->select('params');
		$query->from($db->qn('#__extensions'));
		$query->where('name = '.$db->q('com_contact'));
		$db->setQuery($query);
		$contactSettings = $db->loadObject();
		//display and convert to HTML when SQL error
		if (is_null($posts=$db->loadRowList()))
		{
			$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
			return;
		}
		$par=$contactSettings->params;
		$globalParams = new JRegistry;
		$globalParams->loadString($par);
		//echo "global params<pre>";print_r($globalParams);echo "</pre>";
		$global_show = null;
		foreach ($this->items as $value){
			$global_show[$value] = $globalParams->get('show_'.$value);
		}
		return $global_show;
		//echo "global show<pre>";print_r($global_show);echo "</pre>";
	}

	protected function getTrainer($teamkey) 
	{
		$db = $this->getDbo();
		// getting trainer information
		$query = $db->getQuery(true);
		$query->select('alias, trainerID, rangfolge, name, email_to as email, telephone, mobile, address, postcode, suburb, params');
		$query->from($db->qn('hb_mannschaft_trainer'));
		$query->where('kuerzel = '.$db->Quote($teamkey));
		$query->leftJoin('hb_trainer USING (trainerID)');
		$query->leftJoin('#__contact_details USING (alias)');
		$query->order('IF(ISNULL(`rangfolge`),1,0),`rangfolge`');
		$db->setQuery($query);
		$trainer = $db->loadObjectList ();
		//display and convert to HTML when SQL error
		if (is_null($posts=$db->loadRowList()))
		{
			$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
			return;
		}
		//echo "Trainer<pre>";print_r($trainer);echo "</pre>";
		$trainer = self::formatContactInfo($trainer);
		return $trainer;
	}

	protected function formatContactInfo($trainer) 
	{
		foreach ($trainer as $curTrainer)
		{
			$par=$curTrainer->params;
			$params = new JRegistry;
			$params->loadString($par);
			//echo "Parameter<pre>";print_r($params);echo "</pre>";

			$show = null;
			foreach ($this->items as $value){

				$show[$value] = $params->get('show_'.$value);
				//echo "show[".$value."]: ".$show[$value]."<br>";
				if ($show[$value] === null) 
				{
					$show[$value] = $this->global_show[$value];
				}
				if ($show[$value] === 0) {
				$curTrainer->{$value} = null;
				}
			}
			//echo "show<pre>";print_r($show);echo "</pre>";

			$trainerContact = array();
				if($curTrainer->email != null) $trainerContact[] = JHtml::_('email.cloak', $curTrainer->email);

				if($curTrainer->mobile != null) {
					$curTrainer->mobile = preg_replace('/(\+49)(1\d\d)(\d{6,9})/', '$1 $2 / $3', $curTrainer->mobile);
					$trainerContact[] = $curTrainer->mobile;
				}
				if($curTrainer->telephone != null) {
					$curTrainer->telephone = preg_replace('/(\+49)(\d{4})(\d{3,9})/', '$1 $2 / $3', $curTrainer->telephone);
					$trainerContact[] = $curTrainer->telephone;
				}
			if(count($trainerContact) > 0) {
				$curTrainer->contact = $trainerContact;
				//$curTrainer->contact = implode(', ', $trainerContact);
			}
		}
		return $trainer;
	}


}