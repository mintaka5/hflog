<div style="padding:5px;">
	<h3><?= $this->log->frequency; ?> kHz <?= $this->log->mode; ?></h3>
	<div>&amp;<?= $this->date($this->log->time_on, "M d, Y H:i"); ?> UTC</div>
	<div style="font-size:90%;">
		<div><?= $this->log->description(); ?></div>
		<? if($this->log->hasLocation()): ?>
		<div><?= $this->log->location()->location()->site; ?></div>
		<? endif; ?>
	</div>
</div>