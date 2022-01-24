<? $iter = $this->posts->getIterator(); while($iter->valid()): ?>
<div class="bucket <?php echo ($iter->current()->link->type == "video") ? "" : "nonvid"; ?> <?php echo ($iter->current()->link->type == "flickr") ? "flickr" : ""; ?>">
	<? if($iter->current()->link->type == "video"): ?>
	<iframe title="YouTube video player" class="youtube-player" type="text/html" width="226" height="139" src="<?php echo $iter->current()->link->flash; ?>" frameborder="0" allowFullScreen></iframe>
	<? else: ?>
	<div class="img-holder">
		<a href="<?php echo $iter->current()->link->href; ?>" target="_blank">
			<img src="<?php echo $iter->current()->link->thumbnail; ?>" alt="<?php echo $iter->current()->link->title; ?>" />
		</a>
	</div>
	<? endif; ?>
	<h2>
		<a target="_blank" href="<?php echo $iter->current()->link->href; ?>"><?php echo $iter->current()->link->title; ?></a>
	</h2>
	<p><?= $this->twitterize($iter->current()->link->description); ?></p>
	<div class="read">
		<a href="<?php echo $iter->current()->link->href; ?>" target="_blank">
			<? if($iter->current()->link->type == "video"): ?>
			Watch this <strong>video</strong>
			<? elseif($iter->current()->link->type == "book"): ?>
			Buy from <strong>Amazon</strong>
			<?php elseif($iter->current()->link->type == "flickr"): ?>
			View this <strong>image</strong>
			<? else: ?>
			Read this <strong>story</strong>
			<? endif; ?>
		</a>
	</div>
</div>
<? $iter->next(); endwhile; ?>