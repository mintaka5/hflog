<div class="nav-items">
    <ul>
        <li>
            <a href="<?= $this->manager->friendlyAction("frequencies"); ?>">Frequency Database</a>
        </li>
        <li>
            <li><a href="<?= $this->manager->friendlyAction("index"); ?>" title="High Frequency/Shortwave Listening Logs">HF/SWL Logs</a>
        </li>
    </ul>
</div>

<div class="nav-desc">
    <p>
        Qualsh's apps mostly include data management tools to better analyze and seek
        out frequencies across the spectrum. The <strong>frequency database</strong>
        is geared towards those interested in scanning 25 MHz and up through UHF.
    </p>
    <p>
        The <strong>HF/SWL</strong> logs is a logbook the will allow you to search and log frequencies within the
        shortwave and high frequency bands.
    </p>
</div>

<? if($this->recentlog->location()): ?>
<? if($this->recentlog->location()->location()->coordinates()): ?>
<div class="nav-img">
	<a href="/">
		<img src="http://maps.googleapis.com/maps/api/staticmap?size=297x200&sensor=false&markers=icon:http://qualsh.com/ode/assets/images/maps/markers/antenna/mobilephonetower3.png%7C<?= $this->recentlog->location()->location()->lat; ?>,<?= $this->recentlog->location()->location()->lng; ?>&zoom=3" alt="" />
	</a>
	<p>
		<?= $this->recentlog->frequency; ?> kHz
		<?= $this->recentlog->mode; ?>
		<br />
		<?= $this->recentlog->description(); ?>
	</p>
</div>
<? endif; ?>
<? endif; ?>