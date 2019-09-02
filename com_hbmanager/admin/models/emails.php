<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HbManager Model
 *
 * @since  0.0.1
 */
class HBmanagerModelEmails extends JModelList
{
	protected $tz = false; //true: user-time, false:server-time
 	protected $season;
 	protected $dateFormat = 'D, d.m.Y - H:i:s';
 	protected $dateFormatMobile = 'd.m. H:i';

 	protected $table_team 				= '#__hb_team';
	protected $table_game 				= '#__hb_game';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		$this->season = HbmanagerHelper::getCurrentSeason();
		$this->tz = HbmanagerHelper::getHbTimezone();

		parent::__construct($config);
    }

	
	public function getTeams()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($this->table_team);
		$db->setQuery($query);
		$teams = $db->loadObjectList();
		$teams = $this->addEmail($teams);
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $teams ,1).'</pre>';
		return $teams;
	}

	protected function addEmail($teams) {
		$params = JComponentHelper::getParams( 'com_hbmanager' );
		$template = $params->get( 'emailtemplate' );
		$emaildomain = $params->get( 'emaildomain' );
		$season = $params->get( 'season' );
		
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $template ,1).'</pre>';

		foreach ($teams as &$team) {
			$team->alias = $team->email;
			$team->email = $team->email.'@'.$emaildomain;

			$subject = 'HKOG Trainer '.$team->team.' '.$season;
			$team->subject = rawurlencode($subject);

			$body = $template;
			// %25%25TEAM%25%25
			$body = str_replace('%25%25TEAM%25%25', $team->team ,$body);
			// %25%25SAISON%25%25
			$body = str_replace('%25%25SAISON%25%25', $season ,$body);
			// %25%25EMAIL%25%25
			$body = str_replace('%25%25EMAIL%25%25', $team->email ,$body);
			// %25%25TEAMPAGE%25%25
			if (strcmp($team->youth,'aktiv') === 0) {
				$url = JURI::root().'index.php/aktive/';
			} else {
				$url = JURI::root().'index.php/jugend/'; 
			}
			$body = str_replace('%25%25TEAMPAGE%25%25', $url. strtolower($team->teamkey) ,$body);
			// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( rawurldecode($body) ,1).'</pre>'; ; // for testing
			$team->body = $body;
	
			$uri = "mailto:$team->email?subject=$team->subject&body=$body";
			$team->emailUri = $uri;
			$team->emailUri = htmlspecialchars($uri);
			// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $team->emailUri ,1).'</pre>';
		}
		return $teams;
	}

	public function getEmailTemplate() {
		
		// get compontent config parameter
		$params = JComponentHelper::getParams( 'com_hbmanager' );
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $params ,1).'</pre>';
		$template = $params->get( 'emailtemplate' );
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $template ,1).'</pre>';

		$template = rawurldecode($template);

		if (empty($template)) {
			$template = "Hallo %%TEAM%% Trainer,

diese E-Mail ist an die Trainer der %%TEAM%% (Saison %%SAISON%%) gerichtet und wurde an %%EMAIL%% geschickt um zu testen, ob die Trainer Email-Weiterleitungen funktionieren.

Bitte schreibt kurz zurück, falls ihr die E-Mail erhalten habt und überprüft bitte auch, ob alle Informationen auf der Seite eurer Mannschaft korrekt sind oder ob etwas fehlt (z.B. die Telefonnummer o.ä.).

%%TEAM%%: %%TEAMPAGE%%


Falls ihr während der Runde Berichte oder Meldungen fürs Amtsblatt habt, könnt ihr diese an amtsblatt@hkog.de schicken.

Meldungen oder Kommentare bzgl. der HKOG Seite (www.hkog.de) bitte an webadmin@hkog.de


Vielen Dank";

		$this->updateTemplateInDB($template);
		}
		return $template;
	}

	public function updateTemplateInDB($emailbody) {
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $emailbody ,1).'</pre>';die;
		$emailbody = rawurlencode($emailbody);
		// get compontent config parameter
		$params = JComponentHelper::getParams( 'com_hbmanager' );
		$params->set( 'emailtemplate', $emailbody );
		// echo __FILE__ . '(' . __LINE__ . ')<pre>'.print_r( $params ,1).'</pre>';die();
		// Save the parameters
		$componentid = JComponentHelper::getComponent('com_hbmanager')->id;
		$table = JTable::getInstance('extension');
		$table->load($componentid);
		$table->bind(array('params' => $params->toString()));

		// check for error
		if (!$table->check()) {
			echo $table->getError();
			return false;
		}
		// Save to database
		if (!$table->store()) {
			echo $table->getError();
			return false;
		}
	}

}