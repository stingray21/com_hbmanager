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
				
				<div class="picture">
					<?php
					//echo __FILE__.'('.__LINE__.'):<pre>';print_r($value->dateiname);echo'</pre>'; 
					$res = 500; //resolution
					$filename = 'team_'.$value->kuerzel.'_'.$value->saison.'_'.$res.'px';
					$file = '../'.$this->picFolder.'/'.$value->saison.'/'.$filename.'.png';
					//echo __FILE__.'('.__LINE__.'):<pre>';print_r($file);echo'</pre>';
					// echo '<a href="'<?php echo '.$file.'">test</a>';
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
				
				//echo __FILE__.'('.__LINE__.'):<pre>';print_r($value->liste);echo'</pre>';
				foreach ($value->liste as $nr => $line) {
					//echo __FILE__.'('.__LINE__.'):<pre>';print_r($line);echo'</pre>';
					echo $form->getLabel('titel'.($nr+1), 'hbpictures');
					echo $form->getInput('titel'.($nr+1), 'hbpictures', $line['titel']);
					echo $form->getInput('namen'.($nr+1), 'hbpictures', $line['namen']);
				}
				
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
