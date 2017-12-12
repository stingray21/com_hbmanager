<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$team = $this->team;
?>

			<div class="hbhvwLink">
				<a href="<?php echo $team->hvwLinkUrl ?>" target="_BLANK"><img src="<?php echo JUri::base().'media/com_hbmanager/images/hvw_full.png'; ?>"></a>
				<p><?php echo JText::_('COM_HBMANAGER_TEAM_HVWLINK_TEXT') ?> <br> 
					<a href="<?php echo $team->hvwLinkUrl ?>" target="_BLANK">www.hvw-online.org</a>
				</p>

			</div>
