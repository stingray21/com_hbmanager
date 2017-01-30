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
		$i = 0;
		foreach ($this->teams as $key => $value)
		{
			//echo __FILE__.'('.__LINE__.'):<pre>';print_r($value);echo'</pre>';
			?>
			<div class="team pic">

				<h4 class="teampic"> <?php echo $value->mannschaft ?></h4>
				
				<div class="picture">
					<?php
					//echo __FILE__.'('.__LINE__.'):<pre>';print_r($value->dateiname);echo'</pre>'; 
					$res = 500; //resolution
					$filename = 'team_'.$value->kuerzel.'_'.$value->saison.'_'.$res.'px';
					$file = '../'.$this->picFolder.'/'.$value->saison.'/'.$filename.'.png';
					//echo __FILE__.'('.__LINE__.'):<pre>';print_r($file);echo'</pre>';
					?>
					<img src="<?php 
					echo $file;
					?>" id="teampic_<?php
					echo $value->kuerzel;
					?>" class="teampic" alt="Mannschaftsbild <?php
					echo $value->mannschaft;
					?>" />
				</div>
				<?php	
				
				//echo hbhelper::formatInput($form->getInput('id', 'hbpictures', $value->id), $i);
				echo hbhelper::formatInput($form->getInput('kuerzel', 'hbpictures', $value->kuerzel), $i);
				?>
				<div class="btn-group">
				<a class="btn" href="index.php/?option=com_hbmanager&task=addpicture&teamkey=<?php echo $value->kuerzel; ?>" title="<?php 
						echo JText::_('COM_HBMANAGER_PICTURES_CHANGE_BUTTON');?>" ><?php
						echo JText::_('COM_HBMANAGER_PICTURES_CHANGE_BUTTON');?></a>
				</div>
				
				<?php
				//echo $form->getLabel('dateiname', 'hbpictures');
				//echo hbhelper::formatInput($form->getInput('dateiname', 'hbpictures', $value->dateiname), $i);
				
				echo $form->getLabel('saison', 'hbpictures');
				echo hbhelper::formatInput($form->getInput('saison', 'hbpictures', $value->saison), $i);
				
				echo $form->getLabel('kommentar', 'hbpictures');
				echo hbhelper::formatInput($form->getInput('kommentar', 'hbpictures', $value->kommentar), $i);
				
				//echo __FILE__.'('.__LINE__.'):<pre>';print_r($value->liste);echo'</pre>';
				foreach ($value->liste as $nr => $line) {
					//echo __FILE__.'('.__LINE__.'):<pre>';print_r($line);echo'</pre>';
					echo $form->getLabel('titel'.($nr+1), 'hbpictures');
					echo hbhelper::formatInput($form->getInput('titel'.($nr+1), 'hbpictures', $line['titel']), $i);
					echo hbhelper::formatInput($form->getInput('namen'.($nr+1), 'hbpictures', $line['namen']), $i);
				}
				
				?>
			
				<input class="submit" type="submit" name="update_button" id="update_button" value="<?php
						echo JText::_('COM_HBMANAGER_PICTURES_SAVE_BUTTON');?>" />
			
			</div> <!-- div team -->
			
			<div class="clearfix"> </div>
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
