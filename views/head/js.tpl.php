<script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/js/jquery-2.2.3.min.js"></script>
<script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/js/jquery-ui.min.js"></script>

<script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/bootstrap-fileinput/js/fileinput.min.js"></script>

<script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/bootstrap/js/bootstrap.min.js"></script>

<!--[if lt IE 9]>
<script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/bootstrap/assets/js/html5shiv.js"></script>
<script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/bootstrap/assets/js/respond.min.js"></script>
<![endif]-->

<script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/jquery/jquery.url.min.js"></script>
<script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/jquery/jQuery.jPlayer.2.1.0/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/jquery/jquery.blockUI.js"></script>

<script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/js/hflogs/main.js"></script>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCB5kXC7N46ao4uPAldzLbj_IjN76AlWh0"></script>

<script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/soundmanager/script/soundmanager2.js"></script>

<script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/js/map_functions.js"></script>
<? if (empty($this->mode_is_excluded) && !$this->manager->isPage('station')): // exclude on add mode, because it's a diffrent instance of the map functionality ?>
    <script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/js/log_maps.js"></script>
<? endif; ?>

<script type="text/javascript">
    globals.assetsurl = '<?php echo APP_ASSETS_URL; ?>assets/';
    globals.ajaxpath = '<?php echo $this->manager->getURI(); ?>controllers/ajax/';
</script>