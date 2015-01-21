<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


// get the JForm object
$form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.
		'/models/forms/hbgoals.xml');
?>
		
<form class="hbmanager form-validate" action="<?php 
		echo JRoute::_('index.php?option=com_hbmanager&task=addGoals') 
		?>" method="post" id="addGoals" name="addGoals">

	<div class="fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php echo JText::_('COM_HBMANAGER_GOALSINPUT_FORM_TITLE'); ?>
			</legend>
			
				<?php
			//echo __FILE__.' - '.__LINE__.'<pre>';print_r($fields); echo'</pre>';
			
			foreach ($form->getFieldset('goals') as $field) {
				echo $field->label;
				echo $field->input;
			}
			?>
			<div class="clr"></div>
			
			<input class="submit" type="submit" name="addGoals_button" id="addGoals_button" value="<?php 
				echo JText::_('COM_HBMANAGER_GOALSINPUT_SUBMIT_ADDGOALS') ?>" />
		
	
	</div>
</form>	


