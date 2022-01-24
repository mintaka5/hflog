<?php if ($this->manager->isMode("links")): ?>
    <h3 class="page-header">Links</h3>
    <div>
        <?php while ($this->links->valid()): ?>
            <div class="media">
                <?php if (!empty($this->links->current()->img)): ?>
                    <div class="media-left">
                        <a href="<?php echo $this->links->current()->href; ?>">
                            <img style="max-width: 200px;" class="media-object" src="<?php echo $this->links->current()->img; ?>" alt="<?php echo $this->links->current()->title; ?>">
                        </a>
                    </div>
                <?php endif; ?>
                <div class="media-body">
                    <h4 class="media-heading"><a href="<?php echo $this->links->current()->href; ?>"><?php echo $this->links->current()->title; ?></a></h4>
                    <p><?php echo $this->links->current()->description; ?></p>
                </div>
            </div>
            <?php $this->links->next(); endwhile; ?>
    </div>
<?php endif; ?>

<?php if ($this->manager->isMode("resources")): ?>
    <?php echo $this->fetch("info/resources.tpl.php"); ?>
<?php endif; ?>

<?php if ($this->manager->isMode("privacy-policy")): ?>
    <div><?php echo $this->fetch("privacy-policy.tpl.php"); ?></div>
<?php endif; ?>

<?php if ($this->manager->isMode("gopro")): ?>
    <div><?php echo $this->fetch("info/gopro.tpl.php"); ?></div>
<?php endif; ?>

<?php if ($this->manager->isMode("propagation")): ?>
    <div>
        <div id="mainPropChart">
            <img src="<?php echo $this->manager->friendlyAction("graphs"); ?>" alt=""/>
        </div>
        <div>
            <img src="<?php echo $this->manager->friendlyAction("graphs", "sun_spots"); ?>" alt=""/>
        </div>
    </div>
<?php endif; ?>