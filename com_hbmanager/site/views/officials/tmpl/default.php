<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>

<div id="hbofficials">

	<h1><?php echo JText::_('COM_HBMANAGER_OFFICIALS_HEADLINE') ?></h1>

	<p>Folgende Vertreter wurden am 21.3.2014 gewählt</p>

	<p>Wir möchten uns für ihr Engagement bedanken</p>

	<?php foreach ($this->officials as $item) : ?>

		<div>
			<h3><?php $item->amt ?></h3>
			<p><?php $item->name ?></p>
			<?php if ($item->address != '') : ?>
			<address>
				<?php echo $item->address ?><br />
				<?php echo $item->postcode.' '.$item->suburb ?>
			</address>
			<?php endif; ?>
			<?php if (isset($item->contact)) : ?>
			<p><?php echo $item->contact; ?></p>
			<?php endif; ?>
		</div>

	<?php endforeach; ?>

</div>
