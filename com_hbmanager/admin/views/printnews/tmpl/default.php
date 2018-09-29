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

// get the JForm object
JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
// $form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.
// 				'/models/forms/hbdates.xml');

// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->games);echo'</pre>';

JFactory::getDocument()->addScriptDeclaration('

	function copyToClipboard(element_id){
	  var aux = document.createElement("div");
	  aux.setAttribute("contentEditable", true);
	  //console.log(aux);
	  aux.innerHTML = document.getElementById(element_id).innerHTML;
	  // aux.style.fontFamily = "Arial,sans-serif"; 
	  aux.setAttribute("onfocus", "document.execCommand(\'selectAll\',false,null)");
	  document.body.appendChild(aux);
	  aux.focus();
	  document.execCommand("copy");
	  document.body.removeChild(aux);
	}

');


?>
<div id="printnews">
	<div id="j-sidebar-container" class="span2">
	    <?php 
		echo JHtmlSidebar::render(); 
		JToolBarHelper::preferences('com_hbmanager');
		?>
	</div>


	<div id="j-main-container" class="span10">
		
	<?php
	// get the JForm object
	JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
	$form = JForm::getInstance('gamedateform', JPATH_COMPONENT_ADMINISTRATOR.'/models/forms/gamedates.xml');
	?>

		<form action="" method="post" id="gamedatesForm" name="gamedatesForm">
				<fieldset class="gamedates">
					<legend>
						<?php echo JText::_('COM_HBMANAGER_GAMEDATES_LEGEND');	?>
					</legend>
					<div>
						<p><?php echo JText::_('COM_HBMANAGER_GAMEDATES_PREV'); ?></p>

						<dl>
							<dt>
								<?php echo $form->getLabel('prevStart', 'gameDates'); ?>
							</dt>
							<dd>
								<?php echo $form->getInput('prevStart', 'gameDates', $this->dates['prevStart']); ?>
							</dd>
							
							<dt>
								<?php echo $form->getLabel('prevEnd', 'gameDates'); ?>
							</dt>
							<dd>
								<?php echo $form->getInput('prevEnd', 'gameDates', $this->dates['prevEnd']); ?>
							</dd>
						</dl>

						<p><?php echo JText::_('COM_HBMANAGER_GAMEDATES_NEXT'); ?></p>

						<dl>
							<dt>
								<?php echo $form->getLabel('nextStart', 'gameDates'); ?>
							</dt>
							<dd>
								<?php echo $form->getInput('nextStart', 'gameDates', $this->dates['nextStart']); ?>
							</dd>
							
							<dt>
								<?php echo $form->getLabel('nextEnd', 'gameDates'); ?>
							</dt>
							<dd>
								<?php echo $form->getInput('nextEnd', 'gameDates', $this->dates['nextEnd']); ?>
							</dd>
						</dl>
					</div>

					<input class="btn" type="submit" name="date_button" id="date_button" value="<?php echo JText::_('COM_HBMANAGER_GAMEDATES_UPDATE_BUTTON');?>"/>
				</fieldset>
		</form>	

		<button class="btn" onclick="copyToClipboard('printsheet')"><span class="icon-copy" aria-hidden="true"></span> <?php echo JText::_('COM_HBMANAGER_PRINTNEWS_COPY_BUTTON');?></button>	

		<div id="printsheet">
			
		<?php 
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->prevgames);echo'</pre>'; 
		?>
		<?php if (!empty($this->prevgames)) : ?>
			<div id="articlePrevGames">
				<p class="headline"><b><u><?php echo JText::_('COM_HBMANAGER_PRINTNEWS_HEADLINE_PREV_GAMES');?></u></b></p>
			<?php foreach ($this->prevgames as $date => $days) : ?>
				<p><b><?php echo JHTML::_('date', $date , 'l, d.m.Y', $tz); ?></b><br>
				<?php foreach ($days as $game) : ?>
					<span class="game">
						<b><?php echo $game->team ?></b> (<?php echo $game->leagueKey ?>)<br>
						<i><?php echo $game->home ?> - <?php echo $game->away ?> | <?php echo $game->goalsHome ?>:<?php echo $game->goalsAway ?></i><br>
					</span>
				<?php endforeach; ?>
				</p>
			<?php endforeach; ?>
			</div>
		<?php endif; ?>


		<?php 
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->reports);echo'</pre>'; 
		?>
		<?php if (!empty($this->reports)) : ?>
			<div id="articleReports">
				<p class="headline"><b><u><?php echo JText::_('COM_HBMANAGER_PRINTNEWS_HEADLINE_REPORTS');?></u></b></p>
			<?php foreach ($this->reports as $game) : ?>

				<p class="report">
					<b><?php echo $game->team ?></b> (<?php echo $game->league ?>)<br>
					<i><?php echo $game->home ?> - <?php echo $game->away ?> | <?php echo $game->goalsHome ?>:<?php echo $game->goalsAway ?></i><br>
					<?php if (!empty($game->report)) : ?>
					<?php echo $game->report; ?><br>
					<?php endif; ?>
					<?php if (!empty($game->playerList)) : ?>
					Es spielten: <br>
					<?php echo $game->playerList; ?><br>
					<?php endif; ?>
					<?php if (!empty($game->extra)) : ?>
					<?php echo $game->extra; ?><br>
					<?php endif; ?>
				</p>

			<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php 
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->nextgames);echo'</pre>'; 
		?>
		<?php if (!empty($this->nextgames)) : ?>
			<div id="articleNextGames">
				<p class="headline"><b><u><?php echo JText::_('COM_HBMANAGER_PRINTNEWS_HEADLINE_NEXT_GAMES');?></u></b></p>
			<?php foreach ($this->nextgames as $date => $days) : ?>
				<p><b><?php echo JHTML::_('date', $date , 'l, d.m.Y', $tz); ?></b><br>
				<?php foreach ($days as $game) : ?>
					<span class="game">
						<b><?php echo $game->team ?></b> (<?php echo $game->leagueKey ?>)<br>
						<i><?php echo $game->gymName ?>, <?php echo $game->town ?></i><br>
						<i><?php 
							if (isset($game->details)) echo JText::_('COM_HBMANAGER_PRINTNEWS_MULTIGAMES');
							echo JHTML::_('date', $game->dateTime , 'H:i', $tz); 
							echo JText::_('COM_HBMANAGER_PRINTNEWS_CLOCK');?> <?php echo $game->home ?> - <?php echo $game->away ?></i><br>
					</span>
				<?php endforeach; ?>
				</p>
			<?php endforeach; ?>
			</div>
		<?php endif; ?>


		<?php 
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->homegames);echo'</pre>'; 
		?>
		<?php if (!empty($this->homegames)) : ?>
			<div id="articleHomeGames">
				<p class="headline"><b><u><?php 
					if (count($this->homegames) > 1) echo JText::_('COM_HBMANAGER_PRINTNEWS_HEADLINE_HOME_GAMES_PLURAL');
					else echo JText::_('COM_HBMANAGER_PRINTNEWS_HEADLINE_HOME_GAMES');
					?></u></b></p>
			<?php foreach ($this->homegames as $date => $days) : ?>
				<p><b><?php echo JHTML::_('date', $date , 'l, d.m.Y', $tz); ?></b><br>
				<?php foreach ($days as $gym) : ?>
					<i><?php echo $gym[0]->gymName ?>, <?php echo $gym[0]->town ?></i><br>
					<?php foreach ($gym as $game) : ?>
						<span class="game">
							<b><?php echo $game->team ?></b> (<?php echo $game->leagueKey ?>)<br>
							<i><?php echo JHTML::_('date', $game->dateTime , 'H:i', $tz); ?> <?php echo JText::_('COM_HBMANAGER_PRINTNEWS_CLOCK');?> <?php echo $game->home ?> - <?php echo $game->away ?></i><br>
						</span>
					<?php endforeach; ?>
				<?php endforeach; ?>
				</p>
			<?php endforeach; ?>
			</div>
		<?php endif; ?>


		<?php 
		// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($this->reports);echo'</pre>'; 
		?>
		<?php if (!empty($this->pregames)) : ?>
			<div id="articlePregames">
				<p class="headline"><b><u><?php echo JText::_('COM_HBMANAGER_PRINTNEWS_HEADLINE_PREGAMES');?></u></b></p>
			<?php foreach ($this->pregames as $game) : ?>

				<p class="report">
					<b><?php echo $game->team ?></b> (<?php echo $game->league ?>)<br>
					<i><?php echo $game->home ?> - <?php echo $game->away ?></i><br>
					<?php if (!empty($game->pregame)) : ?>
					<?php echo $game->pregame; ?><br>
					<?php endif; ?>
					<?php if (!empty($game->meetupLoc)) : ?>
					<?php echo $game->meetupLoc; ?><br>
					<?php endif; ?>
					<?php if (!empty($game->meetupTime)) : ?>
					<?php echo $game->meetupTime; ?><br>
					<?php endif; ?>
				</p>

			<?php endforeach; ?>
			</div>
		<?php endif; ?>

		</div>

	</div>
</div>