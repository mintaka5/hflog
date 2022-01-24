<div class="row">
    <?php if ($this->manager->isMode()): ?>
        <?php if ($this->manager->isTask()): ?>
            <div class="col-md-6">
                <h4>Logbook User Details:</h4>
                <div>
                    <table class="table">
                        <tr>
                            <th>Name:</th>
                            <td><?php echo $this->user->fullname(); ?>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><?php echo $this->user->email; ?></td>
                        </tr>
                        <tr>
                            <th>Location:</th>
                            <td>
                                <input type="hidden" class="locLat" value=""/>
                                <input type="hidden" class="locLng" value=""/>
                                <input type="hidden" class="usrLat"
                                       value="<?php echo $this->user->coords()->lat(); ?>"/>
                                <input type="hidden" class="usrLng"
                                       value="<?php echo $this->user->coords()->lng(); ?>"/>
                                <?php echo $this->user->coords()->toDegrees(); ?> <br/>
                                (<?php echo $this->user->coords(); ?>)
                            </td>
                        </tr>
                        <tr>
                            <th>Active log(s):</th>
                            <td><?php echo $this->user->numActiveLogs(); ?></td>
                        </tr>
                    </table>
                </div>
                <!--<ul class="nav nav-pills">
                    <li>
                        <a href="<?php //echo $this->manager->friendlyAction('user', 'profile', 'edit'); ?>">Edit my
                            details</a>
                    </li>
                </ul> -->
            </div>

            <div id="mapContainer" class="col-md-6">
                <h4>Your location</h4>
                <div id="logMap"></div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($this->manager->isMode('logs')): ?>
        <?php if ($this->manager->isTask()): ?>
            <div class="col-md-6">
                <h4>My logs</h4>
                <?php echo $this->fetch("hflogs/log_list.tpl.php"); ?>
            </div>
            <div class="col-md-6">
                <?php echo $this->fetch("hflogs/map.tpl.php"); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($this->manager->isMode('profile')): ?>
        <?php if ($this->manager->isTask('edit')): ?>
            <div class="col-md-6"></div>
            <div class="col-md-6"></div>
        <?php endif; ?>
    <?php endif; ?>
</div>