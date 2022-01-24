<input id="stnTitle" type="hidden" value="<?php echo utf8_decode($this->station->title()); ?>"/>
<div>
    <h2><?php echo utf8_decode($this->station->title()); ?></h2>
    <ul class="nav nav-pills">
        <li>
            <a href="<?php echo $this->manager->friendlyAction("station", null, null, array("id", $this->station->id)); ?>"
               title="<?php echo $this->station->numLogs(); ?> total logs"><?php echo $this->station->numLogs(); ?>
                logs</a>
        </li>
    </ul>
    <div class="row">
        <div class="col-md-6">
            <h3>Locations:</h3>
            <?php if ($this->station->locations()): ?>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Frequency (kHz)</th>
                        <th>Site</th>
                        <th>Times</th>
                        <th>Language</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $locs = $this->station->locations();
                    foreach ($locs as $lNum => $loc): ?>
                        <tr class="location" data-coords="<?php echo $loc->coordinates(); ?>">
                            <td><?php echo $loc->freq(); ?></td>
                            <td>
                                <?php echo $loc->site(); ?>
                            </td>
                            <td><?php echo $loc->times(); ?></td>
                            <td><?php echo $loc->language(); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div>No locations.</div>
            <?php endif; ?>
        </div>
        <div id="mapContainer" class="col-md-6">
            <?php echo $this->fetch("hflogs/map.tpl.php"); ?>

            <?php if ($this->auth->isAdmin()): ?>
                <div class="top-20">
                    <h3>Upload audio</h3>
                    <div>
                        <label class="control-label">Select file</label>
                        <input type="file" id="audioUploader" name="file" class="file-loading" multiple="true"
                               data-preview-file-type="text" data-stationid="<?php echo $this->station->id; ?>">
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($this->auth->isAuth()): ?>
                <?php if ($this->station->hasAudio()): $audio = $this->station->audio(); ?>
                    <ul class="top-10 list-group">
                        <?php foreach ($audio as $audioItem): /*Util::debug($audioItem);*/ ?>
                            <li class="list-group-item row">
                                <div class="audioSample col-lg-6" data-id="<?php echo $audioItem->id; ?>"
                                     id="audioSample<?php echo $audioItem->id; ?>">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="audioPlay btn btn-default"
                                                data-referer="audioSample<?php echo $audioItem->id; ?>" disabled>
                                            <span class="glyphicon glyphicon-play"></span> Play
                                        </button>
                                        <button type="button" class="audioStop btn btn-danger"
                                                data-referer="audioSample<?php echo $audioItem->id; ?>" disabled>
                                            <span class="glyphicon glyphicon-stop"></span> Stop
                                        </button>
                                    </div>
                                    <div class="progress top-10">
                                        <div class="progress-bar progress-bar-striped" role="progressbar"
                                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                             style="width: 0%; min-width: 2em;">0%
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div><?php echo $audioItem->title; ?></div>
                                    <div class="audioTimer text-info">00:00</div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>