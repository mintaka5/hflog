<div>
	<? if($this->manager->isMode()): ?>
	<div style="padding:20px 70px 20px 20px;">
		<? foreach($this->posts as $post): ?>
		<div style="border-top:1px dotted #bfbfbf; padding: 10px 0 10px 0;">
			<h2 id="<?= $post->post_name; ?>" style="font-size:24px;">
				<a href="<?= $this->manager->friendlyAction("blog", "view", $post->post_name); ?>" title="<?= $post->post_title; ?>"><?= $post->post_title; ?></a>
			</h2>
			<h3>Posted on <?= $this->date($post->post_date, "F d, Y"); ?></h3>
			<div class="postContent"><?= $post->content(); ?></div>
			<div style="margin:20px 0 0 0;">
				<div style="position:relative;">
					<iframe src="https://www.facebook.com/plugins/like.php?locale=en_US&amp;href=<?= urlencode($this->manager->fullFriendlyAction("blog", "view", $post->post_name)); ?>&amp;layout=button_count&amp;show_faces=false&amp;width=90px&amp;height=21px" style="border: medium none; overflow: hidden; width: 90px; height: 21px;" allowtransparency="true" frameborder="0" scrolling="no"></iframe>
				</div>
				<div style="left: 85px; position: relative; top: -18px; width: 90px;">
					<g:plusone width="90" size="small" annotation="inline" href="<?= $this->manager->fullFriendlyAction("blog", "view", $post->post_name); ?>"></g:plusone>
				</div>
				<div style="left: 180px; position: relative; top: -36px;  width: 105px;">
					<!-- Twitter button -->
					<a href="http://twitter.com/share" class="twitter-share-button" data-text="The Loud Minority - <?= $post->post_title; ?>" data-url="<?= $this->manager->fullFriendlyAction("blog", "view", $post->post_name); ?>" data-count="horizontal" data-via="qualsh">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
				</div>
				<div style="left: 310px; position: relative; top: -56px; width: 90px;">
					<!-- digg button -->
					<a class="DiggThisButton DiggCompact" href="http://digg.com/submit?url=<?= urlencode($this->manager->fullFriendlyAction("blog", "view", $post->post_name)); ?>&amp;title=<?= $post->post_title; ?>" rev="news, politics, offbeat"></a>
				</div>
                                <div style="position:relative; top:-30px;">
                                    <script src="http://feeds.feedburner.com/~s/blogspot/OFqoy?i=http://<?= APP_DOMAIN; ?><?= $this->manager->friendlyAction("blog", "view", $post->post_name); ?>" type="text/javascript"></script>
                                </div>
			</div>
			<div style="margin: -30px 0 0; padding: 0 0 30px;">
				Posted in <?= $post->tagList(); ?>
			</div>
		</div>
		<? endforeach; ?>
		<div>
			<div style="float:left;"><?= $this->links["back"]; ?></div>
			<div style="float:right;"><?= $this->links["next"]; ?></div>
			<br style="clear:both;" />
		</div>
	</div>
	<? endif; ?>
	
	<? if($this->manager->isMode("search")): ?>
	<div style="padding:20px 70px 20px 20px;">
		<h2><?= $this->numResults; ?> result(s) for <?= $this->query; ?></h2>
		<? if($this->posts): ?>
		<div>
			<ol>
				<? foreach($this->posts as $num => $post): ?>
				<li>
					<h3>
						<a href="<?= $this->manager->friendlyAction("blog", "view", $post->post_name); ?>" title="<?= $post->post_title; ?>"><?= $post->post_title; ?></a>
					</h3>
					<div>Posted on <?= $this->date($post->post_date, "F d, Y"); ?></div>
				</li>
				<? endforeach; ?>
			</ol>
		</div>
		<? else: ?>
		<div>No results.</div>
		<? endif; ?>
	</div>
	<? endif; ?>
	
	<? if($this->manager->isMode("view")): ?>
	<script type="text/javascript" src="/ode/assets/jquery/jquery.url.min.js"></script>
        <script type="text/javascript">
            // have to pass Post ID to javascript
            var pid = <?= $this->post->ID; ?>;
        </script>
	<script type="text/javascript" src="/ode/assets/js/blog/view.js"></script>
	<div style="padding:20px 70px 20px 20px;">
		<h2 style="font-size:24px;"><?= $this->post->post_title; ?></h2>
		<h3>Posted on <?= $this->date($this->post->post_date, "F d, Y"); ?></h3>
		<div class="postContent"><?= $this->post->content(); ?></div>
		<div style="margin:20px 0 0 0;">
			<div style="position:relative;">
				<iframe src="https://www.facebook.com/plugins/like.php?locale=en_US&amp;href=<?= urlencode($this->manager->fullFriendlyAction("blog", "view", $this->post->post_name)); ?>&amp;layout=button_count&amp;show_faces=false&amp;width=90px&amp;height=21px" style="border: medium none; overflow: hidden; width: 90px; height: 21px;" allowtransparency="true" frameborder="0" scrolling="no"></iframe>
			</div>
			<div style="left: 85px; position: relative; top: -18px; width: 90px;">
				<g:plusone width="90" size="small" annotation="inline" href="<?= $this->manager->fullFriendlyAction("blog", "view", $this->post->post_name); ?>"></g:plusone>
			</div>
			<div style="left: 180px; position: relative; top: -36px;  width: 105px;">
				<!-- Twitter button -->
				<a href="http://twitter.com/share" class="twitter-share-button" data-text="The Loud Minority - <?= $this->post->post_title; ?>" data-url="<?= $this->manager->fullFriendlyAction("blog", "view", $this->post->post_name); ?>" data-count="horizontal" data-via="qualsh">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
			</div>
			<div style="left: 310px; position: relative; top: -56px; width: 90px;">
				<!-- digg button -->
				<a class="DiggThisButton DiggCompact" href="http://digg.com/submit?url=<?= urlencode($this->manager->fullFriendlyAction("blog", "view", $this->post->post_name)); ?>&amp;title=<?= $this->post->post_title; ?>" rev="news, politics, offbeat"></a>
			</div>
		</div>
		<div style="margin: -30px 0 0; padding: 0 0 30px;">
			Posted in <?= $this->post->tagList(); ?>
		</div>
		<div>
			<h3>Comments</h3>
			<div>
				<? $comments = $this->post->comments(); if(!empty($comments)): ?>
				<? foreach($comments as $comment): ?>
				<div style="border-top:1px dotted #e0e0e0; font-size:95%; padding:20px;">
					<span>by <?= $comment->comment_author; ?> on <?= $this->date($comment->comment_date_gmt, "F d, Y H:i"); ?> UTC</span>
					<div style="margin:10px 0 0 0;"><?= $comment->content(); ?></div>
				</div>
				<? endforeach; ?>
				<? else: ?>
				<div>No comments.</div>
				<? endif; ?>
			</div>
		</div>
		<div>
			<h3>Submit a Comment</h3>
			<div id="cfHolder"></div>
		</div>
	</div>
	<? endif; ?>
</div>