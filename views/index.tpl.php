<?php if ($this->manager->isMode()): ?>
    <!--<script type="text/javascript"
            src="<?php echo APP_ASSETS_URL; ?>assets/js/gmapsutil/daynightoverlay/daynightoverlay.js"></script> -->
    <script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/js/nite-overlay.js"></script>
    <script type="text/javascript"
            src="<?php echo APP_ASSETS_URL; ?>assets/js/gmapsutil/markerclusterer/src/markerclusterer_compiled.js"></script>
    <script type="text/javascript"
            src="<?php echo APP_ASSETS_URL; ?>assets/js/gmapsutil/markerwithlabel/src/markerwithlabel_packed.js"></script>
    <script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/js/index/default.js"></script>
    <div>
        <div id="homeMap" style="width:100%; height: 500px;"></div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h2>Recent logs</h2>
            <table class="table table-bordered table-striped">
                <tbody>
                <?php foreach ($this->recents as $num => $recentLog): ?>
                    <tr itemscope itemtype="http://schema.org/BroadcastService">
                        <td>
                            <div itemscope itemtype="http://schema.org/BroadcastFrequencySpecification">
                                <div itemprop="broadcastFrequency">
                                    <meta itemprop="broadcastFrequencyValue"
                                          content="<?php echo $recentLog->freq(); ?>">
                                    <meta itemprop="broadcastSignalModulation"
                                          content="<?php echo $recentLog->mode; ?>">
                                    <a href="<?php echo $this->manager->friendlyAction("logs", "log", "view", array("id", $recentLog->id)); ?>"><span style="font-weight: bold; font-size: larger;"><?php echo $recentLog->freq(); ?>
                                            &nbsp;<?php echo $recentLog->mode; ?></span></a>
                                </div>
                            </div>
                            <div>
                                <?php echo $this->date($recentLog->time_on, 'D M d, Y H:i'); ?>
                            </div>
                            <div>
                                <?php echo $recentLog->description(); ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($recentLog->hasLocation()): ?>

                                <div>
                                    <a href="<?php echo $this->manager->friendlyAction('station', null, null, array('id', $recentLog->location()->location()->station()->id)); ?>"
                                       title="List all logs for <?php echo utf8_decode($recentLog->location()->location()->station()->title()); ?>"><span style="font-size: larger;"
                                            itemprop="broadcastDisplayName"><?php echo utf8_decode($recentLog->location()->location()->station()->title()); ?></span></a>

                                    <a role="button" class="btn btn-xs btn-info"
                                       title="View station information, including site locations."
                                       href="<?php echo $this->manager->friendlyAction('station', 'view', null, array('id', $recentLog->location()->location()->station()->id)); ?>">
                                        <span class="glyphicon glyphicon-info-sign"></span> Info</a>
                                </div>
                                <div itemprop="name">
                                    <?php echo $recentLog->location()->location()->site(); ?>
                                </div>
                            <?php else: ?>
                                <div>
                                    No location found.
                                </div>
                            <?php endif; ?>

                            <?php if($this->auth->isAuth()): // no audio for unregistered users ?>
                            <?php $audio = $recentLog->audio(); if(!empty($audio)): ?>
                                <h3 style="font-size: 98%; margin-top: 10px; margin-bottom: 5px;">Audio</h3>
                                <div class="logPlayer btn-group btn-group-sm top-10" id="logPlayer<?php echo $num; ?>"
                                     data-id="<?php echo $recentLog->id; ?>">
                                    <button type="button" class="logPlay button btn btn-default" disabled="disabled">
                                        <span class="glyphicon glyphicon-play"></span> Play
                                    </button>
                                    <button type="button" class="logStop button btn btn-default" disabled="disabled">
                                        <span class="glyphicon glyphicon-stop"></span> Stop
                                    </button>
                                </div>
                            <?php endif; ?>
                            <?php endif; // end isAuth check ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?php if (!empty($this->topLoggers)): ?>
                <h2>Top loggers</h2>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>User</th>
                        <th>Log count</th>
                        <th>Last log</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->topLoggers as $topLogger): ?>
                        <tr>
                            <td><?php echo $topLogger->username; ?></td>
                            <td><?php echo $topLogger->numActiveLogs(); ?></td>
                            <td>
                                <div>
                                    <strong>
                                        <a href="<?php echo $this->manager->friendlyAction('logs', 'log', 'view', array('id', $topLogger->lastLog()->id)); ?>"><?php echo $topLogger->lastLog()->freq(); ?>
                                            &nbsp;<?php echo $topLogger->lastLog()->mode; ?></a>
                                    </strong>
                                </div>
                                <div>
                                    <small><?php echo $this->date($topLogger->lastLog()->time_on, 'D M d, Y H:i'); ?></small>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?php if (!empty($this->topFreqs)): ?>
                <h2>Top frequencies</h2>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Frequency (kHz)</th>
                        <th>Log count</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->topFreqs as $topFreq): ?>
                        <tr>
                            <td>
                                <strong>
                                    <?php echo number_format($topFreq->frequency, 2, '.', ''); ?><?php echo $topFreq->mode; ?>
                                </strong>
                            </td>
                            <td><?php echo $topFreq->numlogs; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="jumbotron top-20">
        <h2>What's New? August 9, 2016</h2>
        <div class="lead">
            <div class="media">
                <div class="media-left">
                    <a href="https://discord.gg/VZ3t7y9">
                        <img style="height:50px;"
                             src="<?php echo $this->manager->getURI(); ?>assets/images/Discord-Logo-Color.png"
                             class="media-object">
                    </a>
                </div>
                <div class="media-body">
                    We are now offering support and discussion on Discord, a text and voice IRC-like client.
                    Accept our <a href="https://discord.gg/VZ3t7y9">invitation</a>, but before you do, download the <a
                        href="https://discordapp.com/" target="_blank">Discord client</a>.
                </div>
            </div>
            Happy logging.
        </div>
        <p>
            <a class="btn btn-lg btn-success" role="button"
               href="<?php echo $this->manager->friendlyAction('logs'); ?>">Give me logs!</a>
        </p>
    </div>
<?php endif; ?>