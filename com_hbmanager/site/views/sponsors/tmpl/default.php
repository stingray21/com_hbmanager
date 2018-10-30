<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$sponsors = $this->sponsors;
// echo __FILE__.' ('.__LINE__.'):<pre>';print_r($sponsors);echo'</pre>';
?>

<div id="hbsponsors">
	
	<h1><?php echo JText::_('COM_HBMANAGER_SPONSORS_TITLE'); ?></h1>


	<?php if (!empty($sponsors)) : ?>
	<div class="sponsors">
		<?php foreach ($sponsors as $sponsor) : ?>
			
			<div class="sponsor">
				<a href="<?php echo $sponsor->url ?>" target="_blank" rel="noopener noreferrer">
					<div class="img" style="background-position-x: <?php echo (-240*$sponsor->order)?>px;" alt="<?php echo $sponsor->alt ?>"></div>
				</a>
			</div>

		<?php endforeach; ?>		
	</div>

	<?php else : ?>
	<h1><?php echo JText::_('COM_HBMANAGER_SPONSORS_NO_SPONSOR')?></h1>
	<?php endif; ?>

</div>
