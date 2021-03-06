<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// $tz = true; //true: user-time, false:server-time
$tz = HbmanagerHelper::getHbTimezone();

// JFactory::getApplication()->enqueueMessage(JText::_('SOME_ERROR_OCCURRED'), 'warning');
JFactory::getApplication()->enqueueMessage('Does not work properly with Cache enabled', 'warning');

?>
<div id="j-sidebar-container" class="span2">
	<?php
	echo JHtmlSidebar::render();
	JToolBarHelper::preferences('com_hbmanager');
	?>
</div>
<div id="j-main-container" class="span10">

	<div id="hbemails">
	
	<?php
	// get the JForm object
	JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
	$form = JForm::getInstance('gamedateform', JPATH_COMPONENT_ADMINISTRATOR.'/models/forms/email.xml');
	?>

		<form action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=emails.updateEmailTemplate'); ?>" method="post" id="emailbodytemplate" name="emailbodytemplate">
				<fieldset class="emailbody">
					<legend>
						<?php echo JText::_('COM_HBMANAGER_EMAILS_LEGEND');	?>
					</legend>
					
					<dl>
						<dt>
							<?php echo $form->getLabel('emailbody', 'emailtemplate'); ?>
						</dt>
						<dd>
							<?php echo $form->getInput('emailbody', 'emailtemplate', $this->bodytemplate); ?>
						</dd>
					</dl>
					<div>
						Platzhalter:
						<ul>
							<li>%%TEAM%%</li>
							<li>%%SAISON%%</li>
							<li>%%EMAIL%%</li>
							<li>%%TEAMPAGE%%</li>
						</ul>
					</div>

					<input class="btn" type="submit" name="email_button" id="email_button" value="<?php echo JText::_('COM_HBMANAGER_EMAILS_SAVE');?>"/>
				</fieldset>
		</form>	


		<?php if (!empty($this->teams)) : ?>

		<?php if ($this->excelLink !== -1) : ?>
		<div class="excel">
			<div>
				<a href="<?php echo $this->excelLink ?>" target="_blank" rel="noopener noreferrer">
					<svg height="50" version="1.1" viewBox="0 0 12.905 15.852" xmlns="http://www.w3.org/2000/svg"
						xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/"
						xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
						<g>
							<path d="m12.486 4.2628v11.086h-12.068v-14.847h8.6098z" fill="#fff" />
							<path
								d="m10.063 5.9258h-2.114l-1.496 2.204-1.496-2.204h-2.114l2.534 3.788-2.859 4.212h3.935v-1.431h-0.784l0.784-1.172 1.741 2.603h2.194l-2.859-4.212z"
								fill="#207245" />
							<path
								d="m8.623 0c-0.0045601 1.9188e-5 -0.0072062 0.0037753-0.011719 0.0039062h-8.1113c-0.27613 2.76e-5 -0.49997 0.22387-0.5 0.5v14.848c2.76e-5 0.27613 0.22387 0.49997 0.5 0.5h11.889c0.27613-2.8e-5 0.49997-0.22387 0.5-0.5v-11.086c-1.4e-5 -0.13807-0.056004-0.26304-0.14648-0.35352l-3.7598-3.7617c-0.04524-0.04524-0.10034-0.082118-0.16016-0.10742-0.062511-0.026962-0.12977-0.043232-0.19922-0.042969zm-7.623 1.0039h7.1289v3.2617c2.81e-5 0.27613 0.22387 0.49997 0.5 0.5h3.2598v10.086h-10.889v-13.848zm8.1289 0.70703 2.0527 2.0547h-2.0527v-2.0547z"
								fill="#1a1a1a" />
						</g>
					</svg>
				</a>
			</div>

			<a href="<?php echo $this->excelLink ?>" target="_blank" rel="noopener noreferrer">
				Download als Excel-Datei
			</a>
		</div>

		<?php endif ?>

		<?php foreach ($this->teams as $team) : ?>

		<div>
			<h4><?php echo $team->team ?></h4>
			<span>Email: <a href="mailto:<?php echo $team->emailUri ?>" target="_blank" rel="noopener noreferrer"><?php echo $team->email ?></a></span>	
		</div>

		<?php endforeach; ?>
		<?php endif; ?>
	</div>

</div>