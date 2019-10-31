<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>

<h1><?php echo JText::_('COM_HBMANAGER_UPDATE_TITLE'); ?></h1>

<?php
// $tz = true; //true: user-time, false:server-time
$tz = HbmanagerHelper::getHbTimezone();
// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->teamList);echo'</pre>';
// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->items);echo'</pre>';

JFactory::getDocument()->addScriptDeclaration('

	document.addEventListener("DOMContentLoaded", function(event) {
		//console.log("DOM fully loaded and parsed");
		var teams = '.json_encode($this->teamList).'
		updateTeams(teams);
  	});
');


?>
<div id="teamupdate">

			<table class="table table-striped table-hover">
				<thead>
				<tr>
					<th width="1%" class="hidden-phone">
						<?php echo JText::_('COM_HBMANAGER_UPDATE_NUM'); ?>		
					</th>
					<th width="">
						<?php echo JText::_('COM_HBMANAGER_UPDATE_TEAM'); ?>
					</th>
					<th width="" class="hidden-phone">
						<?php echo JText::_('COM_HBMANAGER_UPDATE_HVWLINK_PAGE'); ?>
					</th>
					<th width="" class="hidden-phone">
						<?php echo JText::_('COM_HBMANAGER_UPDATE_HVWLINK_JSON'); ?>
					</th>
					<th width="">
						<?php echo JText::_('COM_HBMANAGER_UPDATE_DATE'); ?>
					</th>
					<th width="20%">
						<?php echo JText::_('COM_HBMANAGER_UPDATE_STATUS'); ?>
					</th>
					<th width="2%" class="hidden-phone">
						<?php echo JText::_('COM_HBMANAGER_UPDATE_ID'); ?>
					</th>
				</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="5">
							<?php //	echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php if (!empty($this->items)) : ?>
						<?php foreach ($this->items as $i => $row) : ?>
							<tr id="update-team-<?php echo $row->teamkey; ?>">
								<td class="number hidden-phone"><?php echo $this->pagination->getRowOffset($i); ?></td>

								<td><?php echo $row->team;
										if (!empty($row->leagueKey)) echo ' <br class="visible-phone">('.$row->leagueKey.')'; 
									?></td>
								<td class="hidden-phone">
									<a href="<?php echo HbmanagerHelper::get_hvw_page_url($row->leagueIdHvw); ?>" title="<?php echo HbmanagerHelper::get_hvw_page_url($row->leagueIdHvw); ?>" target="_BLANK">
										<?php echo JText::_('COM_HBMANAGER_UPDATE_HVWLINK_PAGE_TEXT'); ?>
									</a>
								</td>
								<td class="hidden-phone">
									<a href="<?php echo HbmanagerHelper::get_hvw_json_url($row->leagueIdHvw); ?>" title="<?php echo HbmanagerHelper::get_hvw_json_url($row->leagueIdHvw); ?>" target="_BLANK">
										<?php echo $row->leagueIdHvw; ?> 
									</a>
								</td>
								<td class="update-date">
									<span>
									<span class="date hidden-phone"><?php echo JHTML::_('date', $row->update , $this->dateFormat, $tz); ?></span>
									<span class="dateMobile visible-phone"><?php echo JHTML::_('date', $row->update , $this->dateFormatMobile, $tz); ?></span>
									</span>
								</td>
								<td>
									<div class="updateBtn">
											
									</div>
									<div class="updateStatus">
										<span class="indicator"></span>
										<div class="details">
											<ul>
												<li class="schedule"><span class="flag"></span><?php echo JText::_('COM_HBMANAGER_UPDATE_SCHEDULE'); ?></li>
												<li class="standings"><span class="flag"></span><?php echo JText::_('COM_HBMANAGER_UPDATE_STANDINGS'); ?></li>
												<li class="standings-details"><span class="flag"></span><?php echo JText::_('COM_HBMANAGER_UPDATE_STANDINGS_DETAILS'); ?></li>
											</ul>
										</div>	
									</div>									
								</td>
								<td class="number hidden-phone">
									<?php echo $row->id; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>

</div>