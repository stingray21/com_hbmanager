<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).
						'/components/com_hbmanager/css/default.css');


// get the JForm object
JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
$form = JForm::getInstance('myformpics', JPATH_COMPONENT_ADMINISTRATOR.DS.
		'models'.DS.'forms'.DS.'hbpictures.xml');
?>

<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_hbmanager&task=showPictures') ?>" method="post" id="picturesForm" name="picturesForm">

<div class="width-100 fltlft pictures_form">

		<fieldset class="adminform">		
			<legend>
				<?php 
				echo JText::_('Mannschafts-Fotos'); 
				?>
			</legend>

<?php 
		$i = 0;
		foreach ($this->teams as $key => $value)
		{
			echo '<div class="teampic">'."\n";

				echo '<h4 class="teampic">'.$value->mannschaft.'</h4>'."\n";
				?>
				<table class="teampic">
				
				<tr>
					<td>
						<img src="../hbdata/images/teams/<?php 
						echo $value->dateiname;
						?>" id="teampic_<?php
						echo $value->kuerzel;
						?>" class="teampic" alt="Mannschaftsbild <?php
						echo $value->mannschaft;
						?>" />
					</td>
					<td>
				<?php	
//				echo $form->getLabel('foto', 'hbpictures');
//				echo hbhelper::formatInput($form->getInput('foto', 'hbpictures', $value->dateiname), $i);
				
				echo hbhelper::formatInput($form->getInput('id', 'hbpictures', $value->id), $i);
				echo hbhelper::formatInput($form->getInput('kuerzel', 'hbpictures', $value->kuerzel), $i);
				echo hbhelper::formatInput($form->getInput('dateiname', 'hbpictures', $value->dateiname), $i);
				
				echo $form->getLabel('saison', 'hbpictures');
				echo hbhelper::formatInput($form->getInput('saison', 'hbpictures', $value->saison), $i);
				
				echo $form->getLabel('kommentar', 'hbpictures');
				echo hbhelper::formatInput($form->getInput('kommentar', 'hbpictures', $value->kommentar), $i);
				
				echo $form->getLabel('untertitel_dt1', 'hbpictures');
				echo hbhelper::formatInput($form->getInput('untertitel_dt1', 'hbpictures', $value->untertitel_dt1), $i);
				echo hbhelper::formatInput($form->getInput('untertitel_dd1', 'hbpictures', $value->untertitel_dd1), $i);
				
				echo $form->getLabel('untertitel_dt2', 'hbpictures');
				echo hbhelper::formatInput($form->getInput('untertitel_dt2', 'hbpictures', $value->untertitel_dt2), $i);
				echo hbhelper::formatInput($form->getInput('untertitel_dd2', 'hbpictures', $value->untertitel_dd2), $i);
				
				echo $form->getLabel('untertitel_dt3', 'hbpictures');
				echo hbhelper::formatInput($form->getInput('untertitel_dt3', 'hbpictures', $value->untertitel_dt3), $i);
				echo hbhelper::formatInput($form->getInput('untertitel_dd3', 'hbpictures', $value->untertitel_dd3), $i);
				
				echo $form->getLabel('untertitel_dt4', 'hbpictures');
				echo hbhelper::formatInput($form->getInput('untertitel_dt4', 'hbpictures', $value->untertitel_dt4), $i);
				echo hbhelper::formatInput($form->getInput('untertitel_dd4', 'hbpictures', $value->untertitel_dd4), $i);
				
				?>
				</td>
				<td>
					<input class="submit" type="submit" name="update_button" id="update_button" value="speichern" />
				</td>
			</tr>
			</table>
			<?php
			//echo __FILE__.'('.__LINE__.'):<pre>';print_r($value);echo'</pre>';

			$i++;

			
		}	
	
?>
<div class="clr"></div>
			<input class="submit" type="submit" name="update_button" id="update_button" value="speichern" />
		</fieldset>
		
	</div>
	
</form>	
