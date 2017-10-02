<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<div id="j-sidebar-container" class="span2">
	<?php 
	echo JHtmlSidebar::render(); 
	JToolBarHelper::preferences('com_hbmanager');
	?>
</div>
<div id="j-main-container" class="span10">
	<p>TEST default</p>
</div>


