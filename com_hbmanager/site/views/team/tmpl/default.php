<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$team = $this->team;
// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($team);echo'</pre>';
?>

<div id="hbteam">
	
	<?php if (!empty($team)) : ?>
	
	<h1><?php echo $team->team; ?> <span><?php echo $team->league; ?></span></h1>
	
	<div id="teaminfo">


	<?php if ($this->show['team']) : ?>

		<?php if ($this->show['picture']) : ?>
			
			<!-- <h2><?php echo JText::_('COM_HBMANAGER_TEAM_PICTURE'); ?></h2> -->
			<div id="teampic">
				<a href="<?php echo $team->paths['1200px'] ?>" target="_BLANK">
				<img src="<?php echo $team->paths['800px'] ?>" id="teampic_image" alt="<?php echo $team->comment ?>"  />
				</a>
				
				<?php if (!empty($team->caption)) :?>
					<dl>
						<?php foreach ($team->caption as $line) :?>
						 	<dt><?php echo $line->heading;?></dt>
						 	<dd><?php echo $line->list;?></dd>
						 <?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>

		<?php endif; ?>		


		<?php if ($this->show['training']) : ?>
			
			<h2><?php echo JText::_('COM_HBMANAGER_TEAM_TRAINING'); ?></h2>


			<div class="hbtraining">
				<dl>
				
				<?php if (!empty($team->emailAlias)) : ?> 
					<dt><?php echo JText::_('COM_HBMANAGER_TEAM_TRAINING_CONTACT') ?></dt>
					<dd><?php echo JHtml::_('email.cloak', $team->emailAlias); ?></dd>
				<?php endif; ?>
					
				<?php if (!empty($this->coaches)) : ?>
					<dt><?php echo JText::_('COM_HBMANAGER_TEAM_TRAINING_COACH') ?></dt>
					<?php foreach ($this->coaches as $coach) : ?>
					<dd>
						<?php echo (isset($coach->name)) ? ' <span>'.$coach->name.'</span>' : ''; ?>
						<span>
							<?php echo (!empty($coach->telephone)) ? ' | '.$coach->telephone : ''; ?>
							<?php echo (!empty($coach->mobile)) ? ' | '.$coach->mobile : ''; ?>
							<?php echo (!empty($coach->email_to)) ? ' | '.$coach->email_to : ''; ?>
						</span>
					</dd>
					<?php endforeach; ?>
				<?php endif; ?>
							
				<?php if (!empty($this->trainings)) : ?> 
					<dt><?php echo JText::_('COM_HBMANAGER_TEAM_TRAINING_TIMES') ?></dt>
					<?php foreach ($this->trainings as $training) : ?>
					<dd> 
						<span>
							<span><?php echo $training->day ?></span>
							<span><?php echo $training->start ?></span> - <span><?php echo $training->end ?></span> <?php echo JText::_('COM_HBMANAGER_TEAM_CLOCK'); ?>
						</span>
						<?php if (!empty($training->training_comment)) : ?>
							<span><?php echo $training->training_comment; ?></span>
						<?php endif; ?>
						<?php if (!empty($training->gymId)) : ?>
							<span> | <?php echo $training->gymName; ?></span>
						<?php endif; ?>
					</dd>
					<?php endforeach; ?>
				<?php endif; ?>

				</dl>
			</div>

		
		<?php endif; ?>	


		<?php if ($this->show['schedule']) : ?>
			
			<h2><?php echo JText::_('COM_HBMANAGER_TEAM_SCHEDULE'); ?></h2>
			
			<?php if (count($this->schedule) > 0) : ?>

				<?php echo $this->loadTemplate('schedule'); ?>

			<?php endif; ?>


		<?php endif; ?>		


		<?php if ($this->show['standings']) : ?>
			
			<h2><?php echo JText::_('COM_HBMANAGER_TEAM_STANDINGS'); ?></h2>

			<?php if (count($this->standings) > 0) : ?>
				
				<?php echo $this->loadTemplate('standings_'.$this->show['standings_type']); ?>

			<?php endif; ?>

		<?php endif; ?>


		<?php if ($this->show['hvwLink']) : ?>
			<?php if ($this->team->hvwLinkUrl) : ?>
				
				<?php echo $this->loadTemplate('hvwLink'); ?>

			<?php endif; ?>

		<?php endif; ?>

	<?php endif; ?>

	</div>

<?php else : ?>
<h1><?php echo JText::_('COM_HBMANAGER_TEAM_NO_TEAM')?></h1>
<?php endif; ?>

</div>
