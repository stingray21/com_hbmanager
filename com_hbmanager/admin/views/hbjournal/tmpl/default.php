<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::preferences('com_hbmanager');

$tz = 'Europe/Berlin'; //true: user-time, false:server-time

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/icon.php';

// get the JForm object
JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
$form = JForm::getInstance('myform', JPATH_COMPONENT_ADMINISTRATOR.
				'/models/forms/hbdates.xml');
?>

<form class="hbmanager form-validate" action="<?php 
	JRoute::_('index.php?option=com_hbmanager&task=showJournal') 
	?>" method="post" id="datesForm" name="datesForm">

	<div class="fltlft">
	
		<fieldset class="adminform">
			<legend>
				<?php 
				echo JText::_('COM_HBMANAGER_DATE_SETTINGS');
				?>
			</legend>
			<dl>
				<dt>
				<?php echo $form->getLabel('date', 'hbDates'); ?>
				</dt>
				<dd>
				<?php 
				if (isset($this->dates['date'])) $date = $this->dates['date'];
				else $date = null;
				echo $form->getInput('date', 'hbDates', 
						JHtml::_('date', $date, 'd.m.y', $tz)); 
				?>
				</dd>
			</dl>
			<div class="clr"></div>	
			
			<h3><?php echo JText::_('COM_HBMANAGER_DATE_PREV_GAMES');?></h3>
			<dl>
				<dt>
				<?php
					echo $form->getLabel('prevStart', 'hbdates'); 
				?>
				</dt>
				<dd>
				<?php
					echo $form->getInput('prevStart', 'hbdates', 
							$this->dates['prevStart']);
				?>
				</dd>
				
				<dt>
				<?php
					echo $form->getLabel('prevEnd', 'hbdates');
				?>
				</dt>
				<dd>
				<?php 
					echo $form->getInput('prevEnd', 'hbdates', 
							$this->dates['prevEnd']);
				?>
				</dd>
			</dl>
			
			<div class="clr"></div>	
			
			<h3><?php echo JText::_('COM_HBMANAGER_DATE_NEXT_GAMES');?></h3>
			
			<dl>
				<dt>
				<?php
					echo $form->getLabel('nextStart', 'hbdates'); 
				?>
				</dt>
				<dd>
				<?php
					echo $form->getInput('nextStart', 'hbdates', 
							$this->dates['nextStart']);
				?>
				</dd>
				
				<dt>
				<?php
					echo $form->getLabel('nextEnd', 'hbdates');
				?>
				</dt>
				<dd>
				<?php 
					echo $form->getInput('nextEnd', 'hbdates', 
							$this->dates['nextEnd']);
				?>
				</dd>
			</dl>
			
			<div class="clr"></div>
			<input class="submit" type="submit" name="date_button" id="date_button" value="<?php 
				echo JText::_('COM_HBMANAGER_DATE_UPDATE_BUTTON');?>"/>
		</fieldset>
	
	</div>
</form>	
<div class="clr"></div>

<?php 
	// word only keeps direct formatted text
	
	//general
	$style_font = "font-family: Arial; font-size: 9pt;";
	//$style_font .= " line-height: 10pt;";// fuer Aussehen wie im Amtsblatt
	
	// <h2>
	$style_h2 = ' class="AmtsblattUeberschrift" '.
					'style="padding: 0px; margin: 0; '.
					$style_font.' font-weight: bold;"';
	// <p>
	$style_p = ' class="AmtsblattText" '.
					'style="padding: 0px; margin: 0; text-align: justify; '.
					$style_font.'"';
	
	$styles = array('h2' => $style_h2, 
					'p' => $style_p);

?>

<div id="amtsblatt">
<?php 
$this->item = null;
$params = null;
echo JHtml::_('icon.msword', $this->item, $params);
?>
	
	<p style="font-family: Arial; font-size: 12pt; text-align: right; margin: 1em 0">
		<?php 
		echo JHTML::_('date', time(), 'l, d.m.Y', $tz);
		?>
	</p>
	<h1 style="font-family: Arial; font-size: 14pt; margin: 1em 0 2em; color: black;">
		<?php echo JText::_('COM_HBMANAGER_JOURNAL_HEADLINE');?>
	</h1>

	<div id="inhalt" style="width: 9cm">
	
		<!-- Section Top - Headline and hyper link -->
		<?php 
		$top = $this->model->getSectionTop();
		
		echo '<div>';
		echo "<p{$styles['p']}>&nbsp;</p>";
		echo "<h2{$styles['h2']}>{$top['headline']}</h2>";
		
		if (isset($top['link']))
		{
			echo "<p{$styles['p']}>";
			echo nl2br($top['link']);
			echo "</p>";
		}
		echo '</div>';
		?>
		
		<!-- Section Recent Games -->
		<?php 
		$recentGames = $this->model->getSectionRecentGames();
		
		if (!empty($recentGames))
		{
			echo '<div>';
			echo "<p{$styles['p']}>&nbsp;</p>";
			echo "<h2{$styles['h2']}>{$recentGames['headline']}</h2>";
			foreach ($recentGames['games'] as $gameday) {
				echo "\n<p{$styles['p']}>";
				echo nl2br($gameday);
				echo "</p>";
			}
			echo '</div>';
		}
		?>
		
	
	<!-- Section Reports-->
	<?php 
	$reports = $this->model->getSectionReports();
	if (!empty($reports))
	{
		echo '<div>';
		echo "<p{$styles['p']}>&nbsp;</p>";
		echo '<h2'.$styles['h2'].'>Berichte</h2>';

		foreach ($reports as $report)
		{

			echo "<h2{$styles['h2']}>{$report['headline']}</h2>";
			echo "<p{$styles['p']}>";
			echo $report['result']."\n";
			echo "</pre>";
			echo "<p{$styles['p']}>";
			echo nl2br($report['text']);
			if (isset($report['lineup'])) {
				echo "<br />Es spielten:<br />".nl2br($report['lineup']);
			}
			if (isset($report['add'])) {
				echo "<br />".nl2br($report['add']);
			}
			echo "</p>";
		}
		echo '</div>';
	}

	?>
		
	<!-- Section Upcoming Games -->
	<?php 
	$upcomingGames = $this->model->getSectionUpcomingGames();
	if (!empty($upcomingGames))
	{
		//echo __FUNCTION__."<pre>"; print_r($upcomingGames);echo "</pre>";
		echo '<div>';
		echo "<p{$styles['p']}>&nbsp;</p>";
		echo '<h2'.$styles['h2'].'>'.$upcomingGames['headline'].'</h2>';
		foreach ($upcomingGames['games'] as $gameday) {
			echo "\n<p{$styles['p']}>";
			echo nl2br($gameday);
			echo "</p>";
		}
		echo '</div>';
	}
	?>


	<!-- Section Preview -->
	<?php 
	$previews = $this->model->getSectionPreview();
	if (!empty($previews))
	{
		echo '<div>';
		echo "<p{$styles['p']}>&nbsp;</p>";
		echo '<h2'.$styles['h2'].'>Vorschau</h2>';

		foreach ($previews as $preview)
		{
			echo "<h2{$styles['h2']}>{$preview['headline']}</h2>";
			echo "<p{$styles['p']}>";
			echo $preview['game']."\n";
			if (!empty($preview['meetup']))
			{
				echo  $preview['meetup']."\n";
			}
			echo "</pre>";
			echo "<p{$styles['p']}>";
			echo nl2br($preview['text']);
			echo "</p>";
		}
		echo '</div>';
	}
	?>
		
	</div>
	

</div> <!-- End of amtsblatt -->



