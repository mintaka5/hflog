<!-- default mode -->
<?php if ($this->manager->isMode()): ?>
    <?php if ($this->manager->isTask()): ?>
        <script type="text/javascript"
                src="<?php echo $this->manager->getURI(); ?>assets/js/hflogs/default.js?v=<?php echo time(); ?>"></script>
        <h2 class="page-header">All logs
            <small>[<a href="#scrollLogs">skip to list</a>]</small>
        </h2>
        <div class="">
            <?php echo $this->fetch("hflogs/map.tpl.php"); ?>
        </div>
        <div class="">
            <?php echo $this->fetch("hflogs/log_list.tpl.php"); ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if ($this->manager->isMode("whatson")): ?>
    <script type="text/javascript"
            src="<?php echo $this->manager->getURI(); ?>assets/js/hflogs/whatson/default.js"></script>

    <?php if ($this->manager->isTask('now')): ?>
        <h2 class="page-header">What's on <?php echo $this->dow; ?> now
            <small>[<a href="#scrollLogs">skip to list</a>]</small>
        </h2>
        <div class="">
            <?php echo $this->fetch("hflogs/map.tpl.php"); ?>
        </div>
        <div class="">
            <?php echo $this->fetch("hflogs/log_list.tpl.php"); ?>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php if ($this->manager->isMode("recent")): ?>
    <script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/hflogs/recent.js?v=<?php echo time(); ?>"></script>

    <h2 class="page-header">Most recent logs
        <small>[<a href="#scrollLogs">skip to list</a>]</small>
    </h2>
    <div class="">
        <?php echo $this->fetch("hflogs/map.tpl.php"); ?>
    </div>
    <div class="">
        <?php echo $this->fetch("hflogs/log_list.tpl.php"); ?>
    </div>
<?php endif; ?>

<?php if ($this->manager->isMode("add")): ?>
    <?php if ($this->auth->isAuth()): ?>
        <script type="text/javascript"
                src="<?php echo $this->manager->getURI(); ?>assets/jquery/jquery.maskedinput.min.js"></script>
        <script type="text/javascript"
                src="<?php echo $this->manager->getURI(); ?>assets/jquery/jquery.validate.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/jquery.timer.js"></script>
        <script type="text/javascript"
                src="<?php echo $this->manager->getURI(); ?>assets/js/bootstrap3-typeahead.min.js"></script>
        <script type="text/javascript"
                src="<?php echo $this->manager->getURI(); ?>assets/js/hflogs/add/default.js?v=<?php echo time(); ?>"></script>

        <div id="selStnError" class="modal collapse" tabindex="-1" role="dialog" aria-labelledby="selStnErrorLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="selStnErrorLabel">Station selection required!</h4>
                    </div>
                    <div class="modal-body">
                        <p>Please, select a station first!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-primary">Okay</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="dlgAddStn" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dlgAddStnLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="dlgAddStnLabel">New Station</h4>
                    </div>
                    <div class="modal-body">
                        <p>
                            When providing station names, please use official terms or agencies. For example,
                            the <em>National Weather Service</em>, should be provided as <em>National Weather Service
                                (NWS)</em>.
                            If a station name is an abbreviation, please supply full name, as abbreviated names
                            will not be accepted.
                        </p>
                        <div class="fmElement form-group">
                            <label for="stnTitle">Name</label>
                            <input type="text" id="stnTitle" name="stnTitle" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="btnSaveStation">Save Station</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="dlgAddLoc" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="dlgAddLocLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="dlgAddLocLabel">New Location</h4>
                    </div>
                    <div class="modal-body">
                        <form id="addLocForm" method="get" action="">
                            <input type="hidden" name="locStnName" id="locStnName" value="">

                            <p id="selStnTitle"></p>

                            <div class="fmElement form-group">
                                <label for="locSite">Site</label>
                                <input type="text" name="locSite" id="locSite" class="form-control required"
                                       minlength="2">
                            </div>
                            <div class="fmElement form-group">
                                <label for="locStart">Start time (UTC)</label>
                                <input type="text" name="locStart" id="locStart" maxlength="5"
                                       class="form-control required time" placeholder="00:00">
                            </div>
                            <div class="fmElement form-group">
                                <label for="locEnd">End time (UTC)</label>
                                <input type="text" name="locEnd" id="locEnd" maxlength="5"
                                       class="form-control required time" placeholder="01:00">
                            </div>
                            <div class="fmElement form-group">
                                <label for="locFreq">Frequency (kHz)</label>
                                <input type="text" name="locFreq" id="locFreq" maxlength="9"
                                       class="form-control required frequency" placeholder="10057.00">
                            </div>
                            <div class="fmElement form-group">
                                <label for="locLat">Latitude</label>
                                <input type="text" name="locLat" id="locLat" maxlength="11"
                                       class="form-control required coordinate" placeholder="42.056">
                            </div>
                            <div class="form-group">
                                <label for="locLong">Longitude</label>
                                <input type="text" name="locLong" id="locLong" maxlength="12"
                                       class="form-control required coordinate" placeholder="-76.998">
                            </div>
                            <div class="form-group">
                                <label for="locLangIso">Language</label>
                                <select class="form-control" name="locLangIso" id="locLangIso">
                                    <option value="">- not applicable -</option>
                                    <?php foreach ($this->languages as $language): ?>
                                        <option
                                            value="<?php echo $language->iso; ?>"><?php echo $language->language; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="btnSaveLocation">Save Location</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h2>Log</h2>
                <?php echo $this->form; ?>
            </div>
            <div class="col-md-6">
                <h2>Reception Location</h2>

                <div class="input-group">
                    <span class="input-group-addon">Address search:</span>
                    <input type="text" id="mapSearch" class="form-control" name="mapSearch"/>
                    <span class="input-group-btn">
                                        <button id="mapBtn" type="button" class="btn btn-default">Locate</button>
                                    </span>
                </div>
                <div class="top-20">
                    <div id="googleMap" style="width: 350px; height: 250px;"></div>
                </div>
                <div class="top-20">
                    <h3>Reception Coordinates</h3>
                    Latitude: <span id="rxLat"></span>
                    Longitude: <span id="rxLng"></span>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if ($this->manager->isMode("search")): ?>
    <?php echo $this->fetch("hflogs/search.tpl.php"); ?>
<?php endif; ?>

<?php if ($this->manager->isMode("log")): ?>
    <div class="">
        <?php if ($this->manager->isTask("view")): ?>
            <?php echo $this->fetch("hflogs/log/view.tpl.php"); ?>
        <?php endif; ?>

        <?php if ($this->manager->isTask('edit')): ?>
            <?php echo $this->fetch('hflogs/log/edit.tpl.php'); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($this->manager->isMode("audio")): ?>
    <div>
        <?php if ($this->manager->isTask()): ?>
            <div>
                <?php $audios = $this->audiofiles->getIterator();
                while ($audios->valid()): ?>
                    <div style="float:left; margin:10px;">
                        <input type="hidden" class="audiofile" value="<?php echo $audios->current()->src; ?>"/>

                        <div class="audioplayer jp-player" id="jquery_jplayer_<?php echo $audios->key(); ?>"></div>
                        <div id="jp_container_<?php echo $audios->key(); ?>" class="jp-audio">
                            <div class="jp-type-single">
                                <div class="jp-gui jp-interface">
                                    <ul class="jp-controls">
                                        <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
                                        <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
                                        <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
                                        <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a>
                                        </li>
                                        <li><a href="javascript:;" class="jp-unmute" tabindex="1"
                                               title="unmute">unmute</a></li>
                                        <li><a href="javascript:;" class="jp-volume-max" tabindex="1"
                                               title="max volume">max volume</a></li>
                                    </ul>
                                    <div class="jp-progress">
                                        <div class="jp-seek-bar">
                                            <div class="jp-play-bar"></div>
                                        </div>
                                    </div>
                                    <div class="jp-volume-bar">
                                        <div class="jp-volume-bar-value"></div>
                                    </div>
                                    <div class="jp-time-holder">
                                        <div class="jp-current-time"></div>
                                        <div class="jp-duration"></div>
                                        <ul class="jp-toggles">
                                            <li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a>
                                            </li>
                                            <li><a href="javascript:;" class="jp-repeat-off" tabindex="1"
                                                   title="repeat off">repeat off</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="jp-title">
                                    <ul>
                                        <li><?php echo $audios->current()->title; ?></li>
                                    </ul>
                                </div>
                                <div class="jp-no-solution">
                                    <span>Update Required</span>
                                    To play the media you will need to either update your browser to a recent version or
                                    update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash
                                        plugin</a>.
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $audios->next(); endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>