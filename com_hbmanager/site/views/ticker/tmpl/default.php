<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>

<h1><?php echo JText::_('COM_HBMANAGER_TICKER_HEADLINE') ?></h1>

<div id="gameticker">

<?php if(!empty($this->ticker)) : ?>	

	<div id="ticker">
		<div>
			<div id="gameinfo">
				<div class="update">
					<button id="updateTickerBtn" onclick="updateTicker()"><span class="icon-loop"></span></button>	
					<span>letzte Aktualisierung: <span id="updateTimer"></span> min</span>
				</div>
				<div class="game">
					<span class="teams"></span>
					<span class="location"></span>
					<span class="referee"></span>
				</div>
			</div>
			<div id="scoreboardframe" class="noselect">
				
			</div>
			<div id="homePlayerframe" class="noselect">
				<p class="team"></p>
				<div></div>
			</div>
			<div id="awayPlayerframe" class="noselect">
				<p class="team"></p>
				<div></div>
			</div>
			<div id="detailsframe" class="noselect">
				<div>
					<div><p class="currentEvent"></p></div>
				</div>
			</div>
			<div id="historyframe" class="noselect"></div>
			<div id="scoregraphframe" class="noselect"></div>
		</div>
	</div>

<?php else : ?>
<p><?php echo JText::_('COM_HBMANAGER_TICKER_NO_TICKER')?></p>
<?php endif; ?>

</div>