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

JHtml::_('formbehavior.chosen', 'select');

$listOrder     = $this->escape($this->filter_order);
$listDirn      = $this->escape($this->filter_order_Dir);
?>
<form action="index.php?option=com_hbmanager&view=teams" method="post" id="adminForm" name="adminForm">
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
				<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_ORDER', 'order', $listDirn, $listOrder); ?>
			</th>
			<th width="">
				<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_TEAMKEY', 'teamkey', $listDirn, $listOrder); ?>
			</th>
			<th width="">
				<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_NAME', 'name', $listDirn, $listOrder); ?>
			</th>
			<th width="">
				<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_SHORTNAME', 'shortName', $listDirn, $listOrder); ?>
			</th>
			<th width="">
				<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_LEAGUE', 'league', $listDirn, $listOrder); ?>
			</th>
			<th width="">
				<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_SEX', 'sex', $listDirn, $listOrder); ?>
			</th>
			<th width="">
				<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_YOUTH', 'youth', $listDirn, $listOrder); ?>
			</th>
			<th width="">
				<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_HVWLINK', 'hvwLink', $listDirn, $listOrder); ?>
			</th>
			<th width="">
				<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_UPDATE', 'update', $listDirn, $listOrder); ?>
			</th>
			<th width="">
				<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_EMAIL', 'email', $listDirn, $listOrder); ?>
			</th>
			<th width="2%">
				<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_ID', 'id', $listDirn, $listOrder); ?>
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
					$link = JRoute::_('index.php?option=com_hbmanager&task=team.edit&id=' . $row->id);
				?>
					<tr>
						<td><?php echo $this->pagination->getRowOffset($i); ?></td>
						<td>
							<?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>

						<td>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_HBMANAGER_TEAM_EDIT'); ?>">
								<?php echo $row->team; ?>
							</a>
						</td>
						<td><?php echo $row->order; ?></td>
						<td><?php echo $row->teamkey; ?></td>
						<td><?php echo $row->name; ?></td>
						<td><?php echo $row->shortName; ?></td>
						<td><?php echo $row->league.'('.$row->leagueKey.')'; ?></td>
						<td><?php echo $row->sex; ?></td>
						<td><?php echo $row->youth; ?></td>
												<td>
							<a href="<?php echo $rwo->hvwLink; ?>" title="<?php echo $row->hvwLink;  ?>">
								<?php echo JText::_('COM_HBMANAGER_TEAMS_HVWLINK_TEXT');; ?>
							</a>
						</td>
						<td><?php echo $row->update; ?></td>
						<td><?php echo $row->email; ?></td>
						<td align="center">
							<?php echo $row->id; ?>
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