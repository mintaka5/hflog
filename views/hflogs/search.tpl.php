<h2 class="page-header">
    Search results for &quot;<?php echo $this->query; ?>&quot;
    <?php if ($this->manager->isPage('logs') && $this->manager->isMode('search')): ?>
        <small>
            Number of results: <?php echo $this->numitems; ?>
        </small>
    <?php endif; ?>
    <small>[<a href="#scrollLogs">skip to list</a>]</small>
</h2>
<div>
    <?php echo $this->fetch("hflogs/map.tpl.php"); ?>
</div>
<div>
    <?php echo $this->fetch("hflogs/log_list.tpl.php"); ?>
</div>