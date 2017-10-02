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

// $inipath = php_ini_loaded_file();
// if ($inipath) {
//     echo 'Loaded php.ini: ' . $inipath;
// } else {
//     echo 'A php.ini file is not loaded';
// }

// JHTML data expects input time as UTC --> use 'now' instead of date()
echo 'Joomla with date(...):<pre>';print_r(JHTML::_('date', date("Y-m-d H:i:s"), 'Y-m-d H:i:s e', true));echo'</pre>';
echo 'Joomla with \'now\':<pre>';print_r(JHTML::_('date', 'now', 'Y-m-d H:i:s e', true));echo'</pre>';

$tz = true; //true: user-time, false:server-time
echo 'date()<pre>';print_r(date("Y-m-d H:i:s e"));echo'</pre>';
// echo 'php:<pre>';print_r(date_default_timezone_get());echo'</pre>'; 
// echo 'ini:<pre>';print_r(ini_get('date.timezone'));echo'</pre>'; 
// $user   = JFactory::getUser();
// echo 'user:<pre>';print_r($user->params);echo'</pre>';
$usertime = JHTML::_('date', 'now', 'Y-m-d H:i:s e', true);
echo 'Joomla user:<pre>';print_r($usertime);echo'</pre>';
// $config = JFactory::getConfig();
// echo 'Joomla:<pre>';print_r($config['offset']);echo'</pre>';
$servertime = JHTML::_('date', 'now', 'Y-m-d H:i:s e', false);
echo 'Joomla server:<pre>';print_r($servertime);echo'</pre>';
// $costumtime = JHTML::_('date', date("Y-m-d H:i:s"), 'Y-m-d H:i:s e', 'UTC');
// echo 'Joomla custom:<pre>';print_r($customtime);echo'</pre>';
$date = JFactory::getDate('now', 'UTC');
echo 'JFactory now:<pre>';print_r($date);echo'</pre>';

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
				<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_HVWLINK', 'hvwLink', $listDirn, $listOrder); ?>
			</th>
			<th width="">
				<?php echo JHtml::_('grid.sort', 'COM_HBMANAGER_TEAMS_UPDATE', 'update', $listDirn, $listOrder); ?>
			</th>
			<th width="">
				<?php echo JText::_('COM_HBMANAGER_TEAMS_UPDATE'); ?>
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

						<td><?php echo $row->team.' ('.$row->leagueKey.')'; ?></td>
						<td>
							<a href="<?php echo $rwo->hvwLink; ?>" title="<?php echo $row->hvwLink;  ?>">
								<?php echo JText::_('COM_HBMANAGER_TEAMS_HVWLINK_TEXT');; ?>
							</a>
						</td>
						<td><?php echo $row->update; ?></td>
						<td align="center">
							<a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="console.log('test')" title="" data-original-title="Update team">
								<span class="icon-arrow-down-4" aria-hidden="true"></span>
							</a>		
						</td>
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