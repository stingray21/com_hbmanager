<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

/**
 * HB Officials Model
 */
class HBofficialsModelHBofficials extends JModelLegacy {

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param       type    The table type to instantiate
	 * @param       string  A prefix for the table class name. Optional.
	 * @param       array   Configuration array for model. Optional.
	 * @return      JTable  A database object
	 * @since       2.5
	 */
	
	private $globalParameter;
	private $items;
	private $global_show;
	
	
	function __construct() 
	{
		parent::__construct();
		$this->globalParameter = self::getGlobalParameter();	
		$this->items = array('email', 'mobile', 'telephone');
		$this->global_show = self::getGlobalShowArray();
	}
	
	function getOfficials() 
	{
		$db = $this->getDbo();
		// getting officials information
		$query = $db->getQuery(true);
		$query->select('alias, amt, emailalias, name, email_to as email, telephone, mobile, address, postcode, suburb, params');
		$query->from('hb_funktionaer');
		$query->leftJoin('#__contact_details USING (' . $db->qn('alias') . ')');
		$query->where('amt != ' . $db->q('webmaster'));
		$query->order($db->qn('reihenfolge'));
		//$query->order($db->qn('reihenfolge'));
		//echo "<a>ModelHB->query: </a><pre>"; echo $query; echo "</pre>";
		$db->setQuery($query);
		$officials = $db->loadObjectList();
		//display and convert to HTML when SQL error
		if (is_null($posts = $db->loadRowList())) {
			$jAp->enqueueMessage(nl2br($db->getErrorMsg()), 'error');
			return;
		}
		//echo "officials<pre>";print_r($officials);echo "</pre>";
		$officials = self::setShowParameter($officials);
		$officials = self::formatContactInfo($officials);
		return $officials;
	}
	
	private function getGlobalShowArray()
	{
		$global_show = null;
		foreach ($this->items as $value) {
			$global_show[$value] = $this->globalParameter->get('show_' . $value);
		}
		//echo "global show<pre>";print_r($global_show);echo "</pre>";
		return $global_show;
	}	
	
	private function setShowParameter($officials )
	{
		// if 'show contact' parameter are not set, use global parameter
		foreach ($officials as $cur) {
			$par = $cur->params;
			$params = new JRegistry;
			$params->loadString($par);
			//echo "Parameter<pre>";print_r($params);echo "</pre>";

			$show = null;
			foreach ($this->items as $value) {

				$show[$value] = $params->get('show_' . $value);
				//echo "show[".$value."]: ".$show[$value]."<br>";
				if ($show[$value] === null) {
					$show[$value] = $this->global_show[$value];
				}
				if ($show[$value] === 0) {
					$cur->{$value} = null;
				}
			}
			//echo "show<pre>";print_r($show);echo "</pre>";
		}
		return $officials;
	}
	
	private function formatContactInfo($officials )
	{
		foreach ($officials as $cur) {
			$contact = array();
			if ($cur->emailalias != null)
				$contact[] = JHtml::_('email.cloak', $cur->emailalias);
			elseif ($cur->email != null)
				$contact[] = JHtml::_('email.cloak', $cur->email);

			if ($cur->mobile != null) {
				//$cur->mobile = preg_replace('/(\+49)(1\d\d)(\d{6,9})/', '$1 $2 / $3', $cur->mobile);
				//$cur->mobile = preg_replace('/(\+49)(1\d\d)(\d{6,9})/', '0$2 / $3', $cur->mobile);
				$cur->mobile = preg_replace('/(\+49)(1\d{2})(\d{2})(\d{2})(\d{2})(\d{2})?/', '$1 $2 / $3 $4 $5 $6', $cur->mobile);
				$cur->mobile = preg_replace('/ (\d)$/', '$1', $cur->mobile);
				$contact[] = $cur->mobile;
			}
			if ($cur->telephone != null) {
				$cur->telephone = preg_replace('/(\+49)(\d{2})(\d{2})(\d{2})(\d{2})?(\d{2})?(\d{2})?/', '$1 $2 $3 / $4 $5 $6 $7', $cur->telephone);
				$contact[] = $cur->telephone;
			}
			if (count($contact) > 0) {
				$cur->contact = implode('<br /> ', $contact);
			}
		}
		//echo "Officials<pre>";print_r($officials);echo "</pre>";
		//echo "show_email".$officials[0]->params->get('show_email')."<br>";
		return $officials;
	}

	private function getGlobalParameter()
	{
		$db = $this->getDbo();
		// getting global contact settings
		$query = $db->getQuery(true);
		$query->select('params');
		$query->from($db->qn('#__extensions'));
		$query->where('name = ' . $db->q('com_contact'));
		$db->setQuery($query);
		$contactSettings = $db->loadObject();
		//display and convert to HTML when SQL error
		if (is_null($posts = $db->loadRowList())) {
			$jAp->enqueueMessage(nl2br($db->getErrorMsg()), 'error');
			return;
		}
		$par = $contactSettings->params;
		$globalParameter = new JRegistry;
		$globalParameter->loadString($par);
		//echo "global parameter<pre>";print_r($globalParameter);echo "</pre>";
		return $globalParameter;
	}
}
