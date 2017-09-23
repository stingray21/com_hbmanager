<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');



echo '<h1>Funktionäre</h1>'."\n\n";

echo '<p>Folgende Vertreter wurden am 21.3.2014 gewählt</>';

echo '<p>Wir möchten uns für ihr Engagement bedanken</p>';

foreach ($this->officials as $item)
{
	echo '<div class="funkblock">'."\n";
	echo '<h3>'.$item->amt.'</h3>'."\n";
	echo '<p class="name">'.$item->name;
	echo '</p>'."\n";
	//if ($item->address.$item->postcode.$item->suburb != '') {
	if ($item->address != '') {
		echo '<address>';
		echo $item->address;
		echo '<br />';
		echo $item->postcode.' '.$item->suburb;
		echo '</address>'."\n";
	}
	if (isset($item->contact))
	{	
		echo '<p>';
		echo $item->contact;
		echo '</p>'."\n";
	}
	echo '</div>';
	echo "\n";
}	