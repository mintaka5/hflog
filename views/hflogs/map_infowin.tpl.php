<div class="infowin">
	<h3><?php echo $this->log->frequency(); ?> <?php echo $this->log->mode; ?></h3>
	<div><?php echo $this->date($this->log->time_on, 'D M d, Y H:i'); ?></div>
	<div class="desc"><?php echo utf8_encode($this->log->description()); ?></div>
	
	<?php if($this->log->location()): ?>
	<div>
		<a href="<?php echo $this->manager->friendlyAction('station', null, null, array('id', $this->log->location()->location()->station()->id)); ?>"
			title="List all logs for <?php echo $this->log->location()->location()->station()->title(); ?>"><?php echo $this->log->location()->location()->station()->title(); ?></a>
							 
		[<a title="View station information, including site locations." href="<?php echo $this->manager->friendlyAction('station', 'view', null, array('id', $this->log->location()->location()->station()->id)); ?>">info</a>]
	</div>
	<div><?php echo $this->log->location()->location()->site(); ?></div>
	<?php endif; ?>
</div>