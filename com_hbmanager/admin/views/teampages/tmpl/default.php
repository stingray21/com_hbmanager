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


// JFactory::getDocument()->addScriptDeclaration('
// 	Joomla.submitbutton = function(task) 
// 	{
// 		if (task == "teampages.save")
// 		{
// 			alert("save");
// 		}
// 	}
// ');

?>
<div id="gamedetails">
	<div id="j-sidebar-container" class="span2">
	    <?php 
		echo JHtmlSidebar::render(); 
		JToolBarHelper::preferences('com_hbmanager');
		?>
	</div>

	<div id="j-main-container" class="span10">
		

	<?php 
	$form = JForm::getInstance('formgamedetails', JPATH_COMPONENT_ADMINISTRATOR.'/models/forms/teampages.xml');
	?>
		<form action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=teampages.save') ?>" method="post" id="adminForm" name="adminForm">

			<table class="table table-striped table-hover">
				<thead>
				<tr>
					<th width="1%">
						<?php echo JText::_('COM_HBMANAGER_TEAMPAGES_NUM'); ?>
					</th>
					<th width="15%">
						<?php echo JText::_('COM_HBMANAGER_TEAMPAGES_NAME'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_HBMANAGER_TEAMPAGES_PAGE_TEAM'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_HBMANAGER_TEAMPAGES_PAGE_PLAYER'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_HBMANAGER_TEAMPAGES_PAGE_REPORTS'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_HBMANAGER_TEAMPAGES_PAGE_STATS'); ?>
					</th>
					<th width="">
						<?php  ?>
					</th>
				</tr>
				</thead>
				<tbody id="importGamesList">
							
			<?php if (!empty($this->teams)) : ?>
				<?php foreach ($this->teams as $i => $team) : ?>

					<tr>
						<td><?php echo HbmanagerHelper::formatInput($form->getInput('team', 'teampages', $team->team), $i); 
								  echo HbmanagerHelper::formatInput($form->getInput('teamkey', 'teampages', $team->teamkey), $i); 
								  echo HbmanagerHelper::formatInput($form->getInput('teamCategory', 'teampages', $team->teamCategory), $i);	?></td>
						<td><?php echo $team->team ; ?></td>
						<td><?php echo HbmanagerHelper::formatInput($form->getInput('add][main', 'teampages', 0), $i)?></td>
						<td><?php echo HbmanagerHelper::formatInput($form->getInput('add][player', 'teampages', 0), $i)?></td>
						<td><?php echo HbmanagerHelper::formatInput($form->getInput('add][reports', 'teampages', 0), $i)?></td>
						<td><?php echo HbmanagerHelper::formatInput($form->getInput('add][stats', 'teampages', 0), $i)?></td>
					</tr>

				<?php endforeach; ?>
				</tbody>
			</table>
	<?php endif; ?>


			<input type="hidden" name="task" value=""/>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>