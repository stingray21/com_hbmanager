<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<h1><?php echo JText::_('COM_HBOFFICIALS_TITLE');?></h1>

<div><?php echo $this->message;?></div>

<?php
if (!empty($this->officials)) 
{
	foreach ($this->officials as $item)
	{
	?>
	<div>
		<h3><?php echo $item->amt?></h3>
		<p><?php echo $item->name;?></p>
		<?php 
		//if ($item->address.$item->postcode.$item->suburb != '') {
		if ($item->address != '') {
			?><address><?php echo $item->address?><br />
			<?php echo $item->postcode.' '.$item->suburb;?></address>
<?php
		}
		if (isset($item->contact))
		{	
?>
		<p><?php echo $item->contact;?></p>
<?php 
		}
?>
	</div>
<?php
	}	
}