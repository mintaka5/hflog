<? foreach($this->logs as $log): ?>
<li data-icon="info">
	<? if($log->location()): ?>
	<? if($log->location()->location()->coordinates()): ?>
	<input type="hidden" class="stnLat" value="<?= $log->location()->location()->lat; ?>" />
	<input type="hidden" class="stnLng" value="<?= $log->location()->location()->lng; ?>" />
	<? endif; ?>
	<? endif; ?>
	<a href="<?= $this->manager->friendlyAction("m_home", "logs", "view", array("id", $log->id)); ?>" class="log-list-item" data-rel="dialog" data-direction="pop">
		<p>
			<strong>
				<?= $log->frequency; ?>
				<?= $log->mode; ?>
			</strong>
		</p>
		<p><?= $log->description; ?></p>
		<p><?= $this->date($log->time_on, "M d, Y H:i"); ?></p>
	</a>
</li>
<? endforeach; ?>