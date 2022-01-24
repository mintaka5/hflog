<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
        <h2>Logs</h2>
        <ul class="nav nav-stacked">
            <li role="presentation">
                <a href="<?php echo $this->manager->action('admin_hflogs', 'inactive'); ?>">Inactive logs</a>
            </li>
            <li>
                <a href="<?php echo $this->manager->action('admin_hflogs', 'unapproved'); ?>">Unapproved logs</a>
            </li>
        </ul>
    </div>
    <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
        <div class="panel">
            <?php if ($this->manager->isMode()): ?>
                <div>
                    <?php if ($this->manager->isTask()): ?>
                        <?php echo $this->fetch('admin/hflogs/default.tpl.php'); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($this->manager->isMode("loc")): ?>
                <?php if ($this->manager->isTask("add")): ?>
                <div>
                    <div>
                        <?php echo $this->form; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($this->manager->isTask("set")): ?>
                <script type="text/javascript"
                        src="<?php echo APP_ASSETS_URL; ?>assets/js/hflogs/admin/loc.js"></script>

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
                                <div class="fmElement">
                                    <label for="stnName">Station name</label>

                                    <div class="input"><input type="text" name="stnName" id="stnName"/></div>
                                </div>
                                <div class="fmElement">
                                    <label for="stnTitle">Title</label>

                                    <div class="input"><input type="text" id="stnTitle" name="stnTitle"/></div>
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
                                <input type="hidden" name="locStnId" id="locStnId" value=""/>

                                <div class="fmElement">
                                    <label for="locSite">Site</label>

                                    <div class="input"><input type="text" name="locSite" id="locSite"/></div>
                                </div>
                                <div class="fmElement">
                                    <label for="locStart">Start time (UTC)</label>

                                    <div class="input"><input type="text" name="locStart" id="locStart" maxlength="5"
                                                              size="6"/>
                                    </div>
                                </div>
                                <div class="fmElement">
                                    <label for="locEnd">End time (UTC)</label>

                                    <div class="input"><input type="text" name="locEnd" id="locEnd" maxlength="5"
                                                              size="6"/>
                                    </div>
                                </div>
                                <div class="fmElement">
                                    <label for="locFreq">Frequency (kHz)</label>

                                    <div class="input"><input type="text" name="locFreq" id="locFreq" maxlength="9"
                                                              size="10"/>
                                    </div>
                                </div>
                                <div class="fmElement">
                                    <div>Coordinates</div>
                                    <div class="input">
                                        <label for="locLat">Latitude</label>
                                        <input type="text" name="locLat" id="locLat" maxlength="11" size="12"/>
                                        <label for="locLong">Longitude</label>
                                        <input type="text" name="locLong" id="locLong" maxlength="12" size="13"/>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="btnSaveLocation">Save Location
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h2>Set Location for Log</h2>
                        <table class="table">
                            <tr>
                                <th>Frequency:</th>
                                <td><?php echo $this->log->frequency; ?></td>
                            </tr>
                            <tr>
                                <th>Mode:</th>
                                <td><?php echo $this->log->mode; ?></td>
                            </tr>
                            <tr>
                                <th>Notes:</th>
                                <td><?php echo $this->log->description(); ?></td>
                            </tr>
                            <tr>
                                <th>Date &amp; Time</th>
                                <td><?php echo $this->date($this->log->time_on, "M d, Y H:i"); ?></td>
                            </tr>
                        </table>

                        <?php echo $this->form; ?>

                        <ul class="nav nav-pills">
                            <li><a href="#" id="aDlgAddStn" data-toggle="modal" data-target="#dlgAddStn">Add Station</a>
                            </li>
                            <li><a href="#" id="aDlgAddLoc">Add Location</a></li>
                            <li><a href="#" id="aDlgDelLoc">Remove Location</a></li>
                        </ul>
                    </div>

                    <div id="locationList" class="col-md-6" style="height:550px; overflow:auto;"></div>
                </div>
            <?php endif; ?>
            <?php endif; ?>

            <?php if ($this->manager->isMode("stn")): ?>
                <div>
                    <?php if ($this->manager->isTask("view")): ?>
                        <div>
                            <h2>
                                <?php echo $this->station->title; ?>
                            </h2>

                            <div>
                                <a href="<?php echo $this->manager->friendlyAction("admin_hflogs", "loc", "add", array("id", $this->station->id)); ?>"
                                   title="add location">Add location</a>
                            </div>
                            <div>
                                <?php $locs = $this->station->locations();
                                if (!empty($locs)): ?>
                                    <table class="data">
                                        <thead></thead>
                                        <tbody>
                                        <?php foreach ($locs as $num => $loc): ?>
                                            <tr class="<?php echo ($num % 2 != 0) ? "alt" : ""; ?>">
                                                <td><?php echo $loc->site; ?> <br/> <?php echo $loc->frequency; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div>No locations</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; // end stn mode ?>

            <?php if ($this->manager->isMode('inactive')): ?>
                <?php echo $this->fetch('admin/hflogs/inactive.tpl.php'); ?>
            <?php endif; ?>

            <?php if ($this->manager->isMode('unapproved')): ?>
                <?php echo $this->fetch('admin/hflogs/unapproved.tpl.php'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
