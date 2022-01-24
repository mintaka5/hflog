<!DOCTYPE html>
<html xmlns:g="http://base.google.com/ns/1.0">
<head>
	<meta name="google-site-verification"
		content="jLiPtfAzOPWQILe0V3WvbMdeRZuj5wIMarnu_5bU8EU" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en" />
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<?php if(!$this->manager->isPage("blog")):?>
	<meta name="description"
		content="Qualsh is a amateur radio operator (ham radio) (KJ6BBS), economist, debater, snowboarder, photographer, and an all around swell guy." />
	<meta name="keywords"
		content="amateur radio, ham radio, qrp, music, photography, solar terrestial data, sfi, politics, economics, economy, government, independent, solar data, snowboarding, frequency database, scanning" />
	<?php else:?>
	<link rel="alternate" type="application/rss+xml"
		title="The Loud Minority Blog RSS Feed" href="/rss" />
	<?php if($this->manager->isMode("view")):?>
	<meta name="description" content="<?php echo $this->post->post_excerpt; ?>" />
	<meta name="keywords" content="<?php echo $this->post->tagList(false); ?>" />
	<?php else:?>
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<?php endif;?>
	<?php endif;?>
	
	<title><?php echo $this->manager->getTitle(); ?></title>
	
	<link rel="icon" type="image/png"
		href="<?php echo $this->assets_url; ?>assets/images/favicon.png" />
		
	<link
		href="<?php echo $this->assets_url; ?>assets/jquery/css/smoothness/jquery-ui-1.8.7.custom.css"
		rel="stylesheet" type="text/css" />
		
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<link rel="stylesheet" media="all" href="<?php echo $this->assets_url; ?>assets/styles/less_framework.css" />
	
	<script type="text/javascript" src="<?php echo $this->assets_url; ?>assets/jquery/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->assets_url; ?>assets/jquery/jquery-ui-1.8.7.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->assets_url; ?>assets/jquery/jquery.formData.js"></script>
	<script type="text/javascript" src="<?php echo $this->assets_url; ?>assets/jquery/jquery.masonry.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->assets_url; ?>assets/js/map_functions.js"></script>
	
	<script type="text/javascript">
		var globals = {
			'relurl':  '<?php echo $this->assets_url; ?>',
			'siteurl': '<?php echo $this->manager->getURI(); ?>',
			'varmode': '<?php echo Ode_Manager::VAR_MODE; ?>'
		};
		globals.ajaxurl = globals.relurl + 'controllers/ajax/';
	</script>
	
	<!-- Google Maps API -->
	<script type="text/javascript"
		src="http://maps.google.com/maps/api/js?sensor=false"></script>
	
	<!-- Google Analytics -->
	<script type="text/javascript">
			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-23740512-1']);
			  _gaq.push(['_trackPageview']);
			
			  (function() {
			    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	
			    // digg js
			    var s = document.createElement('SCRIPT'), s1 = document.getElementsByTagName('SCRIPT')[0];
				s.type = 'text/javascript';
				s.async = true;
				s.src = 'http://widgets.digg.com/buttons.js';
				s1.parentNode.insertBefore(s, s1);
			  })();
			</script>
	<script type="text/javascript" src="http://apis.google.com/js/plusone.js"></script>

</head>
<body>
	
	<div>
		<a href="<?php echo $this->manager->friendlyAction("contact"); ?>">Contact Me</a>
			
		<a href="/">Qualsh</a>
		
		<a href="/blog">blog</a>
		<a href="/ham-radio">radio</a>
	</div>
			
	<div><?php echo $this->contentforlayout; ?></div>
	
	<div id="footer">
		<div class="module">
			<?php if($this->auth->isAuth()): ?>
			Welcome, <?php echo $this->auth->getSession()->fullname(); ?>! 
			[<a href="<?php echo $this->manager->friendlyAction("user"); ?>">account</a>]
			[<a href="<?php echo $this->manager->friendlyAction("auth", "logout"); ?>">logout</a>]
			<?php else: ?>
			Log in</legend>
			<?php echo $this->loginForm; ?>
			<button onclick="javascript:window.location.href='<?php echo $this->manager->friendlyAction("register"); ?>'">Register</button>
			<?php endif; ?>
		</div>
		
		<div class="module">
			This site does not run on money from trees, so please, donate to keep it and its services going.
			<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_donations" /> 
				<input type="hidden" name="business" value="cjwalsh@ymail.com" /> 
				<input type="hidden" name="item_name" value="Donation for website" /> 
				<input type="hidden" name="currency_code" value="USD" /> 
				<input type="hidden" name="item_name" value="Qualsh Web Site Donation" />
				<input type="hidden" name="lc" value="US" /> 
				<input type="hidden" name="no_note" value="0" /> 
				<input type="hidden" name="return" value="<?php echo $this->manager->friendlyAction("contact", "paypal", "thanks"); ?>">
				<select name="amount" class="paypal">
					<option value="1.00">$1</option>
					<option value="5.00">$5</option>
					<option value="10.00">$10</option>
					<option value="15.00">$15</option>
					<option value="25.00">$25</option>
					<option value="50.00">$50</option>
					<option value="100.00">$100</option>
				</select> 
				<input type="submit" value="Donate" name="submit" />
			</form>
		</div>
			
		<div class="module">	
			Copyright &copy; 2012 Qualsh of OdeWeb Designs. 
			<a href="<?php echo $this->manager->friendlyAction("privacy-policy"); ?>">Privacy Policy</a>
		</div>
		
		<br class="cleft" />
	</div>
	
</body>
</html>
