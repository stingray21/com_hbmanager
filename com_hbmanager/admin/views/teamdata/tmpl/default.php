<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hbmanager
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// $tz = true; //true: user-time, false:server-time
$tz = HbmanagerHelper::getHbTimezone();

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task) 
	{
		if (task == "teamdata.update")
		{
			updateTeams();
		}
	}
');

JHtml::_('formbehavior.chosen', 'select');

$listOrder     = $this->escape($this->filter_order);
$listDirn      = $this->escape($this->filter_order_Dir);
?>
<div id="teamdata">
	<div id="j-sidebar-container" class="span2 ">
		<?php 
		echo JHtmlSidebar::render(); 
		JToolBarHelper::preferences('com_hbmanager');
		?>
	</div>
	<div id="j-main-container" class="span10">
	
		<form action="index.php?option=com_hbmanager&view=teamdata" method="post" id="adminForm" name="adminForm">
			<div class="row-fluid">
				<div class="span6">
					<?php echo JText::_('COM_HBMANAGER_FILTER'); ?>
					<?php
						echo JLayoutHelper::render(
							'joomla.searchtools.default',
							array('view' => $this)
						);
					?>
				</div>
			</div>
			<table class="table table-striped table-hover">
				<thead>
				<tr>
					<th width="1%"><?php echo JText::_('COM_HBMANAGER_TEAMS_NUM'); ?></th>
					<th width="2%">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="">
						<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_TEAM', 'team', $listDirn, $listOrder); ?>
					</th>
					<th width="">
						<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_HVWLINK_PAGE', 'hvwLink', $listDirn, $listOrder); ?>
					</th>
					<th width="">
						<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_HVWLINK_JSON', 'hvwLink', $listDirn, $listOrder); ?>
					</th>
					<th width="">
						<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_UPDATE', 'update', $listDirn, $listOrder); ?>
					</th>
					<th width="">
						<?php echo JText::_('COM_HBMANAGER_TEAMS_UPDATE_STATUS'); ?>
					</th>
					<th width="">
						<?php echo JText::_('COM_HBMANAGER_TEAMS_UPDATE_BTN'); ?>
					</th>
					<th width="2%">
						<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_ID', 'teamId', $listDirn, $listOrder); ?>
					</th>
				</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="5">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php if (!empty($this->items)) : ?>
						<?php foreach ($this->items as $i => $row) :
							$link = JRoute::_('index.php?option=com_hbmanager&task=team.edit&id=' . $row->teamId);
						?>
							<tr id="update-team-<?php echo $row->teamkey; ?>">
								<td><?php echo $this->pagination->getRowOffset($i); ?></td>
								<td>
									<?php echo JHtml::_('grid.id', $i, $row->teamId); ?>
								</td>

								<td><?php echo $row->team.' ('.$row->leagueKey.')'; ?></td>
								<td>
									<a href="<?php echo HbmanagerHelper::get_hvw_page_url($row->leagueIdHvw); ?>" title="<?php echo HbmanagerHelper::get_hvw_page_url($row->leagueIdHvw); ?>" target="_BLANK">
										<?php echo JText::_('COM_HBMANAGER_TEAMS_HVWLINK_PAGE_TEXT'); ?>
									</a>
								</td>
								<td>
									<a href="<?php echo HbmanagerHelper::get_hvw_json_url($row->leagueIdHvw); ?>" title="<?php echo HbmanagerHelper::get_hvw_json_url($row->leagueIdHvw); ?>" target="_BLANK">
										<?php //echo JText::_('COM_HBMANAGER_TEAMS_HVWLINK_JSON_TEXT');
										  		echo $row->leagueIdHvw; ?> 
									</a>
								</td>
								<td class="date"><?php echo JHTML::_('date', $row->update , $this->dateFormat, $tz); ?></td>
								<td width="20%">
									<div class="updateStatus">
										<span class="indicator"></span>
										<div class="details">
											<ul>
												<li class="schedule"><span class="flag"></span><?php echo JText::_('COM_HBMANAGER_TEAMS_UPDATE_SCHEDULE'); ?></li>
												<li class="standings"><span class="flag"></span><?php echo JText::_('COM_HBMANAGER_TEAMS_UPDATE_STANDINGS'); ?></li>
												<li class="standings-details"><span class="flag"></span><?php echo JText::_('COM_HBMANAGER_TEAMS_UPDATE_STANDINGS_DETAILS'); ?></li>
											</ul>
										</div>	
									</div>									
								</td>
								<td align="center">
									<div class="updateBtn">
										<a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="updateTeamBtn('<?php echo $row->teamkey; ?>');" title="" data-original-title="Update team">
											<span class="icon-loop" aria-hidden="true"></span>
										</a>		
									</div>
								</td>
								<td align="center">
									<?php echo $row->teamId; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>