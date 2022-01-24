<script src="<?php echo $this->manager->getURI(); ?>assets/js/hflogs/edit/default.js?v=<?php echo time(); ?>"></script>
<div class="row">
    <div class="col-md-6">
        <h2>Log Editor</h2>

        <div>
            <?php echo $this->form; ?>
        </div>
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
            <div id="googleMap" style="width: 100%; height: 250px;"></div>
        </div>
        <div class="top-20">
            <h3>Reception Coordinates</h3>
            Latitude: <span id="rxLat"></span>
            Longitude: <span id="rxLng"></span>
        </div>

        <?php if($this->auth->isAdmin()): ?>
        <?php $audio = $this->log->audio(); ?>
        <?php if (empty($audio)): ?>
            <div class="top-20">
                <h3>Audio sample</h3>
                <div>
                    <label class="control-label">Select file</label>
                    <input type="file" id="audioUploader" name="file" class="file-loading" data-preview-file-type="text"
                           data-logid="<?php echo $this->log->id; ?>">
                </div>
            </div>
            <?php else: ?>
            <div class="panel panel-default top-20">
                <div class="panel-heading">Current audio assigned</div>
                <div class="panel-body">
                    <p><?php echo $audio[0]->filename; ?></p>
                    <p><?php echo $audio[0]->title; ?></p>
                    <a class="btn btn-danger" href="<?php echo $this->manager->action('admin_hflogs', 'audio', 'delete', array('id', $this->log->id)); ?>">Remove</a>
                </div>
            </div>
        <?php endif; ?>
        <?php endif; // end admin check ?>
    </div>
</div>