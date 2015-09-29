<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).
						'/components/com_hbmanager/css/default.css');


// get the JForm object
JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
$form = JForm::getInstance('myformpics', JPATH_COMPONENT_ADMINISTRATOR.
		'/models/forms/hbpictures.xml');
?>

<form class="hbmanager form-validate" action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=showPictures') ?>" method="post" id="picturesForm" name="picturesForm">

<div class="fltlft pictures_form">

		<fieldset class="adminform">		
			<legend>
				<?php 
				echo JText::_('Mannschafts-Fotos'); 
				?>
			</legend>

<?php 
		$value = $this->teams;
		?>
			<div class="team pic">

				<h4 class="teampic"> <?php echo $value->mannschaft ?></h4>
				
				<?php	
				//echo $form->getInput('id', 'hbpictures', $value->id);
				echo $form->getInput('kuerzel', 'hbpictures', $value->kuerzel);
				?>
				<?php
				echo $form->getLabel('dateiname', 'hbpictures');
				echo $form->getInput('dateiname', 'hbpictures', $value->dateiname);
				
				echo $form->getLabel('saison', 'hbpictures');
				echo $form->getInput('saison', 'hbpictures', $value->saison);
				
				echo $form->getLabel('kommentar', 'hbpictures');
				echo $form->getInput('kommentar', 'hbpictures', $value->kommentar);
				
				echo $form->getLabel('untertitel_dt1', 'hbpictures');
				echo $form->getInput('untertitel_dt1', 'hbpictures', $value->untertitel_dt1);
				echo $form->getInput('untertitel_dd1', 'hbpictures', $value->untertitel_dd1);
				
				echo $form->getLabel('untertitel_dt2', 'hbpictures');
				echo $form->getInput('untertitel_dt2', 'hbpictures', $value->untertitel_dt2);
				echo $form->getInput('untertitel_dd2', 'hbpictures', $value->untertitel_dd2);
				
				echo $form->getLabel('untertitel_dt3', 'hbpictures');
				echo $form->getInput('untertitel_dt3', 'hbpictures', $value->untertitel_dt3);
				echo $form->getInput('untertitel_dd3', 'hbpictures', $value->untertitel_dd3);
				
				echo $form->getLabel('untertitel_dt4', 'hbpictures');
				echo $form->getInput('untertitel_dt4', 'hbpictures', $value->untertitel_dt4);
				echo $form->getInput('untertitel_dd4', 'hbpictures', $value->untertitel_dd4);
				
				?>
			
			</div> <!-- div team -->
			
			<div class="clearfix"> </div>
			<?php
			//echo __FILE__.'('.__LINE__.'):<pre>';print_r($value);echo'</pre>';
	
?>
<div class="clr"></div>
			<input class="submit" type="submit" name="update_pic_button" id="update_pic_button" value="speichern" />
		</fieldset>
		
	</div>
	
</form>	
