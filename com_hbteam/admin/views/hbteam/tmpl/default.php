<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// get the JForm object
$form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.
		'/models/forms/hbgoalchart.xml');
?>
<!--		
<form class="" action="<?php 
		//echo JRoute::_('index.php?option=com_hbmanager&task=showTeams') 
		?>" method="post" id="updateTeams" name="updateTeams">

	<div class="fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('COM_TEAM_GOALCHART_FORM_TITLE'); ?>
			</legend>
			<?php	
			echo $form->getLabel('chartsettings', 'hbgoalchartsettings');
//			echo $form->getInput('chartsettings', 'hbgoalchartsettings', 'goals,total,penalties,twoMin,twoMinTotal');
			echo $form->getInput('chartsettings', 'hbgoalchartsettings');
			?>
		</fieldset>

			
		<input class="submit" type="submit" name="updateTeams_button" id="updateTeams_button" value="<?php 
			echo JText::_('COM_HBTEAM_GOALCHART_SUBMIT') ?>" />

	</div>
</form>	
-->

