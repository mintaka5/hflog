<script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/js/hflogs/station.js"></script>

<?php if ($this->manager->isMode("view")): ?>
    <?php echo $this->fetch("station/view.tpl.php"); ?>
<?php endif; ?>

<?php if ($this->manager->isMode()): ?>
    <?php echo $this->fetch("station/index.tpl.php"); ?>
<?php endif; ?>