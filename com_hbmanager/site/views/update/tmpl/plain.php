<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<pre>
<?php 
// echo JText::_('COM_HBMANAGER_CRONJOB_TITLE')."\n\n"; 

// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->result);echo'</pre>';

if ($this->result->success) echo "Success\n\n";

foreach ($this->result->teams as $team) {
	// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($team);echo'</pre>';

	// echo $team->response['teamkey'].": ".$team->response['date']."\n";
	if(!empty($team->response['error'])) echo $team->response['teamkey'].": ".$team->response['error']."\n";
}
