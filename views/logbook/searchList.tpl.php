<div>
	<input type="hidden" id="searchTerms" value="<?= $this->terms; ?>" />
	<? if(!empty($this->logs)): ?>
	<div>
		<ul class="logList">
			<? foreach($this->logs as $num => $log): ?>
			<li class="logListItem">
				<? if($log->hasLocation()): ?>
				<div class="coords">
					<input type="hidden" class="logLat" value="<?= $log->location()->location()->lat; ?>" />
					<input class="logLng" type="hidden" value="<?= $log->location()->location()->lng; ?>" />
					<input type="hidden" class="logId" value="<?= $log->id; ?>" />
					<input class="mrkerIndex" type="hidden" value="<?= $num; ?>" />
				</div>
				<? endif; ?>
				<input class="logInfo" type="hidden" value="<?= $log->description(); ?>" />
				<div><?= $log->frequency; ?> kHz <?= $log->mode; ?></div>
				<div style="font-size:90%;">
					<span><?= $this->date($log->time_on, "M d, Y H:i"); ?> UTC</span>
					<span></span>
				</div>
				<div>
					<span class="logHasLoc">&nbsp;</span>
				</div>
			</li>
			<? endforeach; ?>
		</ul>
	</div>
	<div><?= $this->pagelinks['all']; ?></div>
	<? else: ?>
	<div>No logs available.</div>
	<? endif; ?>
</div>