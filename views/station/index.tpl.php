<h2 class="page-header">Station logs for <?php echo utf8_decode($this->station->title()); ?> <small>[<a href="#scrollLogs">skip to list</a>]</small></h2>
<div>
    <?php echo $this->fetch("hflogs/map.tpl.php"); ?>
</div>
<div>
    <?php echo $this->fetch("hflogs/log_list.tpl.php"); ?>
</div>