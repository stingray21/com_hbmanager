<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$tz = 'Europe/Berlin';
?>
<table>
<?php foreach ($this->links as $link) : ?>
	
	<tr><td><?php echo JHtml::_('date', $link->datumZeit, 'D, d.m.y', $tz) ?><td>
	<td><a href="<?php echo $link->hvwLink ?>" target="_BLANK"><?php echo $link->mannschaft ?></a></td>
	<td><?php echo $link->spielIdHvw ?></td>
	<td><a href="<?php echo 
			//'http://www.hvw-online.org/misc/sboPublicReports.php?sGID='.$link->berichtLink.
			// Saison 2016/2017
			'http://spo.handball4all.de/misc/sboPublicReports.php?sGID='.$link->berichtLink
			?>" target="_BLANK">pdf</a></td>
	<td><?php if ($link->file) : ?><a href="<?php echo 
			'index.php?option=com_hbmanager&task=importReport&gameId=' . $link->spielIdHvw
			?>">IMPORT</a><?php endif ?></td>		
	</tr>

<?php endforeach ?>



</table>
		
		

