<?php if ($this->manager->isMode()): ?>
    <?php if ($this->manager->isTask()): ?>
        <div class="row">
            <script type="text/javascript" src="<?php echo APP_ASSETS_URL; ?>assets/js/hflogs/register_map.js"></script>
            <div class="col-md-7"><?php echo $this->form; ?></div>
            <div class="col-md-5">
                <div>
                    <? if (empty($_POST['latHdn']) && isset($_POST['submitBtn'])): ?>
                        <span class="text-danger">Required</span>
                    <? endif; ?>
                </div>
                <div class="input-group top-20">
                    <span class="input-group-addon">Search</span>
                    <input class="form-control" type="text" id="mapSearch" name="mapSearch"/>
            	<span class="input-group-btn">
                	<button type="button" class="btn btn-default" id="mapBtn">Locate</button>
            	</span>
                </div>
                <div class="top-20">
                    <div id="googleMap" style="width:350px; height:250px;"></div>
                    <div class="">
                        Latitude: <span class="text-info" id="latTxt"></span>
                        Longitude: <span class="text-info" id="lngTxt"></span>
                    </div>
                </div>
            </div>
        </div>
    <? endif; ?>
<?php endif; ?>

<? if ($this->manager->isMode("success")): ?>
    <div>
        <h2>You have successfully registered.</h2>

        <p>An email has been sent to the address you supplied. Please, use that to activate your account.</p>
    </div>
<?php endif; ?>
