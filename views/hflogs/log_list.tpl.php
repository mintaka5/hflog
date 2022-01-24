<?php if (!empty($this->logs)): ?>
    <?php if ($this->numpages > 1): ?>
        <nav>
            <ul class="pager">
                <li class="previous <?php echo ($this->isfirstpage) ? 'disabled' : ''; ?>">
                    <a href="<?php echo $this->manager->maskAction($this->links['linkTagsRaw']['prev']['url']); ?>"><span
                            aria-hidden="true">&larr;</span> Newer</a>
                </li>
                <li class="next <?php echo ($this->islastpage) ? 'disabled' : ''; ?>">
                    <a href="<?php echo $this->manager->maskAction($this->links['linkTagsRaw']['next']['url']); ?>">Older
                        <span aria-hidden="true">&rarr;</span></a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

    <div id="scrollLogs">
        <table class="table table-condensed table-striped">
            <thead></thead>
            <tbody>
            <?php foreach ($this->logs as $num => $log): ?>
                <tr itemscope itemtype="http://schema.org/BroadcastService">
                    <td class="col-md-7 col-sm-6">
                        <div itemscope itemtype="http://schema.org/BroadcastFrequencySpecification">
                            <div itemprop="broadcastFrequency">
                                <meta itemprop="broadcastFrequencyValue" content="<?php echo $log->freq(); ?>">
                                <meta itemprop="broadcastSignalModulation" content="<?php echo $log->mode; ?>">
                                <strong><?php echo $log->freq(); ?>&nbsp;<?php echo $log->mode; ?></strong>
                            </div>
                        </div>

                        <div>
                            <small><?php echo $this->date($log->time_on, 'D M d, Y H:i'); ?></small>
                        </div>

                        <div>
                            <small><?php echo $log->description(); ?></small>
                        </div>

                        <ul class="nav nav-pills top-10">
                            <li>
                                <a href="<?php echo $this->manager->friendlyAction("logs", "log", "view", array("id", $log->id)); ?>">View
                                    Log</a>
                            </li>
                            <?php if (($log->isUsers($this->auth->getSession()->id) && !$log->status()) || $this->auth->isAdmin()): ?>
                                <li>
                                    <a href="<?php echo $this->manager->friendlyAction('logs', 'log', 'edit', array('id', $log->id)); ?>">Edit
                                        Log</a>
                                </li>
                            <?php endif; ?>

                            <?php if ($this->auth->isAdmin()): ?>
                                <li>
                                    <a class="confirm" data-confirm="Are you sure you want to delete this log?"
                                       href="<?php echo $this->manager->friendlyAction('logs', 'log', 'delete', array('id', $log->id)); ?>">Delete</a>
                                </li>
                            <?php endif; ?>
                        </ul>

                        <?php $audio = $log->audio();
                        if (!empty($audio)): ?>
                            <span class="label label-success">
                            <span class="glyphicon glyphicon-volume-up"></span> Audio
                        </span>
                        <?php endif; ?>

                        <?php if ($log->hasLocation()): ?>
                            <span class="label label-info">
                        <span class="glyphicon glyphicon-globe"></span> On Map
                    </span>
                        <?php endif; ?>

                        <?php if ($log->isUsers($this->auth->getSession()->id)): ?>
                            <?php if ($log->status() === \Ode\DBO\Hflog\Status::STATUS_INACTIVE): ?>
                                <span class="label label-warning">
                        Awaiting approval
                    </span>
                            <?php endif; ?>

                            <?php if ($log->status() === \Ode\DBO\Hflog\Status::STATUS_NOT_APPROVED): ?>
                                <span class="label label-danger">Not approved</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($log->hasLocation()): ?>
                            <span itemscope itemtype="http://schema.org/GeoCoordinates">
                            <input type="hidden" class="location"
                                   data-coords="<?php echo $log->location()->location()->coordinates()->toCoordinate(); ?>">
                            <input type="hidden" class="user-location"
                                   data-coords="<?php echo $log->user()->coords()->toCoordinate(); ?>">
                            <input type="hidden" class="locLat" value="<?php echo $log->location()->location()->lat; ?>"
                                   itemprop="latitude">
                            <input type="hidden" class="locLng" value="<?php echo $log->location()->location()->lng; ?>"
                                   itemprop="longitude">
                            <input type="hidden" class="usrLat" value="<?php echo $log->user()->coords()->lat(); ?>">
                            <input type="hidden" class="usrLng" value="<?php echo $log->user()->coords()->lng(); ?>">
                            <input type="hidden" class="logid" value="<?php echo $log->id; ?>">
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="col-md-5 col-sm-6">
                        <?php if ($log->hasLocation()): ?>
                            <div>
                                <a href="<?php echo $this->manager->friendlyAction('station', null, null, array('id', $log->location()->location()->station()->id)); ?>"
                                   title="List all logs for <?php echo utf8_decode($log->location()->location()->station()->title()); ?>"><span
                                        itemprop="broadcastDisplayName"><?php echo utf8_decode($log->location()->location()->station()->title()); ?></span></a>

                                <a role="button" class="btn btn-xs btn-info"
                                   title="View station information, including site locations."
                                   href="<?php echo $this->manager->friendlyAction('station', 'view', null, array('id', $log->location()->location()->station()->id)); ?>">
                                    <span class="glyphicon glyphicon-info-sign"></span> Info</a>
                            </div>
                            <div>
                                <small itemprop="name"><?php echo $log->location()->location()->site(); ?></small>
                            </div>

                            <?php if ($this->auth->getSession()): // if user is logged in get bearing and distance to log transmitter location ?>
                                <div>
                                    <small>
                                        Bearing: <?php echo ceil(GreatCircle::bearing($this->auth->getSession()->lat, $this->auth->getSession()->lng, $log->location()->location()->lat, $log->location()->location()->lng)); ?>&deg;
                                        Distance
                                        (miles): <?php echo ceil(GreatCircle::distance($this->auth->getSession()->lat, $this->auth->getSession()->lng, $log->location()->location()->lat, $log->location()->location()->lng, GreatCircle::MI)); ?>
                                    </small>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div>
                                <small>No location found.</small>
                            </div>
                        <?php endif; ?>

                        <? if ($this->auth->isAdmin()): ?>
                            <div class="btn-group-sm btn-group top-10" role="group" aria-label="Admin menu">
                                <a role="button" class="btn btn-default"
                                   href="<?php echo $this->manager->action("admin_hflogs", "loc", "set", array("id", $log->id), array("page", $this->page)); ?>">Edit
                                    location</a>
                            </div>
                        <? endif; ?>

                        <?php if($this->auth->isAuth()): // don't let unregistered users get audio! ?>
                        <?php if (!empty($audio)): ?>
                            <div class="logPlayer btn-group btn-group-sm top-10" id="logPlayer<?php echo $num; ?>"
                                 data-id="<?php echo $log->id; ?>">
                                <button type="button" class="logPlay button btn btn-default" disabled="disabled">
                                    <span class="glyphicon glyphicon-play"></span> Play
                                </button>
                                <button type="button" class="logStop button btn btn-default" disabled="disabled">
                                    <span class="glyphicon glyphicon-stop"></span> Stop
                                </button>
                            </div>
                        <?php endif; // end location exists check ?>
                        <?php endif; // end isAuth check ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($this->numpages > 1): ?>
        <nav>
            <ul class="pager">
                <li class="previous <?php echo ($this->isfirstpage) ? 'disabled' : ''; ?>">
                    <a href="<?php echo $this->manager->maskAction($this->links['linkTagsRaw']['prev']['url']); ?>"><span
                            aria-hidden="true">&larr;</span> Newer</a>
                </li>
                <li class="next <?php echo ($this->islastpage) ? 'disabled' : ''; ?>">
                    <a href="<?php echo $this->manager->maskAction($this->links['linkTagsRaw']['next']['url']); ?>">Older
                        <span aria-hidden="true">&rarr;</span></a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

<?php else: ?>
    <div>No logs were found.</div>
<?php endif; // end log count check ?>