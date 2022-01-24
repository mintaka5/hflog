<div class="row">
    <div class="col-md-6" itemscope itemtype="http://schema.org/BroadcastService">
        <h2><?php echo $this->log->freq(); ?>&nbsp;<?php echo $this->log->mode; ?></h2>

        <div itemscope itemtype="http://schema.org/BroadcastFrequencySpecification">
            <div itemprop="broadcastFrequency">
                <meta content="<?php echo $this->log->freq(); ?>" itemprop="broadcastFrequencyValue">
                <meta content="<?php echo $this->log->mode; ?>" itemprop="broadcastSignalModulation">
            </div>
        </div>

        <p><?php echo $this->log->description(); ?></p>

        <p><?php echo $this->date($this->log->time_on, "D M d, Y H:i"); ?></p>

        <ul class="nav nav-pills top-10">
            <?php if (($this->log->isUsers($this->auth->getSession()->id) && !$this->log->status()) || $this->auth->isAdmin()): // only pro users can edit their own logs ?>
                <li>
                    <a href="<?php echo $this->manager->friendlyAction('logs', 'log', 'edit', array('id', $this->log->id)); ?>">Edit
                        Log</a>
                </li>
            <?php endif; ?>

            <?php if ($this->auth->isAdmin()): ?>
                <li>
                    <a class="confirm" data-confirm="Are you sure you want to delete this log?"
                       href="<?php echo $this->manager->friendlyAction('logs', 'log', 'delete', array('id', $this->log->id)); ?>">Delete</a>
                </li>
            <?php endif; ?>
        </ul>

        <?php $audio = $this->log->audio();
        if (!empty($audio)): ?>
            <p>
                   <span class="label label-success">
                       <span class="glyphicon glyphicon-volume-up"></span> Audio
                   </span>
            <div class="clearfix audio">
                <div class="logPlayer btn-group btn-group-lg" id="logPlayer<?php echo $num; ?>"
                     data-id="<?php echo $this->log->id; ?>">
                    <button type="button" class="logPlay btn btn-default" disabled="disabled">
                        <span class="glyphicon glyphicon-play"></span> Play
                    </button>
                    <button type="button" class="logStop btn btn-default" disabled="disabled">
                        <span class="glyphicon glyphicon-stop"></span> Stop
                    </button>
                </div>
            </div>
            </p>
        <?php endif; ?>

        <h3>Station information</h3>

        <div>
            <? if ($this->log->hasLocation()): ?> <!-- Not all locations have coordinates, so do not show location on map -->
                <? if ($this->log->location()->location()->coordinates()): ?>
                    <span itemscope itemtype="http://schema.org/GeoCoordinates">
                        <input type="hidden" class="locLat"
                               value="<?php echo $this->log->location()->location()->lat; ?>" itemprop="latitude">
                        <input type="hidden" class="locLng"
                               value="<?php echo $this->log->location()->location()->lng; ?>" itemprop="longitude">
                        <input type="hidden" class="usrLat" value="<?php echo $this->log->user()->coords()->lat(); ?>">
                        <input type="hidden" class="usrLng" value="<?php echo $this->log->user()->coords()->lng(); ?>">
                    </span>
                <? endif; ?>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Site</h4>
                    </div>
                    <div class="panel-body">
                        <div class="btn-group" role="group" aria-label="Location information">
                            <a role="button" class="btn btn-default"
                               href="<?php echo $this->manager->friendlyAction('station', null, null, array('id', $this->log->location()->location()->station()->id)); ?>"
                               title="List all logs for <?php echo $this->log->location()->location()->station()->title(); ?>"><span
                                    itemprop="broadcastDisplayName"><?php echo $this->log->location()->location()->station()->title(); ?></span></a>

                            <a role="button" class="btn btn-info"
                               title="View station information, including site locations."
                               href="<?php echo $this->manager->friendlyAction('station', 'view', null, array('id', $this->log->location()->location()->station()->id)); ?>">
                                <span class="glyphicon glyphicon-info-sign"></span> Info</a>
                        </div>

                        <?php if ($this->auth->getSession()): ?>
                            <div class="top-10">
                                Bearing: <?php echo ceil(GreatCircle::bearing($this->auth->getSession()->lat, $this->auth->getSession()->lng, $this->log->location()->location()->lat, $this->log->location()->location()->lng)); ?>&deg;
                                Distance
                                (miles): <?php echo ceil(GreatCircle::distance($this->auth->getSession()->lat, $this->auth->getSession()->lng, $this->log->location()->location()->lat, $this->log->location()->location()->lng, GreatCircle::MI)); ?>
                            </div>
                        <?php endif; ?>

                        <div class="top-10" itemprop="name">
                            <?php echo $this->log->location()->location()->site; ?>
                        </div>
                    </div>
                </div>
            <? else: ?>
                No information available.
            <? endif; ?>
        </div>
    </div>

    <div id="mapContainer" class="col-md-6">
        <?php echo $this->fetch("hflogs/map.tpl.php"); ?>
    </div>
</div>
<div class="top-20">
    <?php if (!empty($this->userlogs)): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Other logs on <?php echo $this->log->frequency(); ?> kHz</h4>
            </div>
            <div class="panel-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Frequency</th>
                        <th>Time</th>
                        <th>Description</th>
                        <th>RX loc.</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->userlogs as $userLog): ?>
                        <tr>
                            <td>
                                <a href="<?php echo $this->manager->friendlyAction('logs', 'log', 'view', array('id', $userLog->id)); ?>"><?php echo $userLog->frequency(); ?>
                                    &nbsp;<?php echo $userLog->mode; ?></a>
                            </td>
                            <td><?php echo $this->date($userLog->time_on, 'M d, Y H:i'); ?></td>
                            <td><?php echo $userLog->description(); ?></td>
                            <td><?php echo $userLog->user()->meta(\Ode\DBO\User\Metadata::META_LOCATION_NAME, true)->meta_value; ?>
                                &nbsp;</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>