<?php $locations = $this->station->locations(); ?>

<?php if(!empty($locations)): ?>
<dic class="list-group">
    <?php foreach($locations as $loc): ?>
    <h4 class="list-group-item-heading"><?php echo $loc->site; ?></h4>
    <p class="list-group-item-text">
        <div><?php echo $loc->coordinates(); ?></div>
        <div>Start UTC: <?php echo $loc->start_utc; ?>; End UTC: <?php echo $loc->end_utc; ?></div>
        <div>Frequency: <?php echo $loc->frequency; ?></div>
    </p>
    <?php endforeach; ?>
</ul>
<?php else: ?>
<div>No locations.</div>
<?php endif; ?>