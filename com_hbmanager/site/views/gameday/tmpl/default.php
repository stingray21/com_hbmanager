<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$datePattern = 'D, d.m.Y, H:i';
$timePattern = 'H:i';
?>

<div id="hbgameday">

<h1><?php echo JText::_('COM_HBMANAGER_GAMEDAY_HEADLINE') ?></h1>



<?php if (!empty($this->prevGames)) : ?>
	
	<h3><?php echo JText::_('COM_HBMANAGER_GAMEDAY_PREV_TITLE') ?></h3>
	
	<dl>
	<?php foreach ($this->prevGames as $game) : ?>
		<dt><?php echo $game->mannschaft; ?></dt>
		<dd>
			<span><?php echo $game->heim ?></span>
			<span><?php echo $game->gast ?></span>
			<span><?php echo $game->toreHeim ?></span>
			<span><?php echo $game->toreGast ?></span>
		</dd>
	<?php endforeach; ?>
	</dl>

<?php endif; ?>


<?php if (!empty($this->nextGames)) : ?>

	<h3><?php echo JText::_('COM_HBMANAGER_GAMEDAY_NEXT_TITLE') ?></h3>
	
	<dl>
	<?php foreach ($this->nextGames as $game) : ?>
		<dt><?php echo $game->mannschaft; ?></dt>
		<dd>
			<?php echo JHTML::_('date', $game->dateTime , $datePattern, $tz);
			<span><?php echo $game->heim ?></span>
			<span><?php echo $game->gast ?></span>
		</dd>
	<?php endforeach; ?>
	</dl>
<?php endif; ?>


</div>

