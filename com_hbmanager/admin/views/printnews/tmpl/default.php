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

	function switchPrintSheet(sheet_id){
		// document.getElementById(\'printsheet-\'+sheet_id).classList.toggle(\'hidden\');
		document.getElementById(\'printsheet-bl\').classList.toggle(\'hidden\');
		document.getElementById(\'printsheet-ge\').classList.toggle(\'hidden\');
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


		<div id="printsheet-ge">
			<button class="btn" onclick="switchPrintSheet()"><?php echo JText::_('COM_HBMANAGER_PRINTREPORT_ISSUE');?> <?php echo JText::_('COM_HBMANAGER_PRINTREPORT_BL');?></button>
			
			<button class="btn" onclick="copyToClipboard('printsheet-ge')"><span class="icon-copy" aria-hidden="true"></span> <?php echo JText::_('COM_HBMANAGER_PRINTREPORT_COPY_BUTTON');?></button>	
			<div class="printsheet">
				<?php echo $this->loadTemplate('ge'); ?>
			</div>
		</div>

		<div id="printsheet-bl" class="hidden">
			<button class="btn" onclick="switchPrintSheet()"><?php echo JText::_('COM_HBMANAGER_PRINTREPORT_ISSUE');?> <?php echo JText::_('COM_HBMANAGER_PRINTREPORT_GE');?></button>	
			
			<button class="btn" onclick="copyToClipboard('printsheet-bl')"><span class="icon-copy" aria-hidden="true"></span> <?php echo JText::_('COM_HBMANAGER_PRINTREPORT_COPY_BUTTON');?></button>	
			<div class="printsheet">
				<?php echo $this->loadTemplate('bl'); ?>
			</div>
		</div>

		<p></p>



	</div>
</div>