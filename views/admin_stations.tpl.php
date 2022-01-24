<?php if ($this->manager->isMode()): ?>
    <?php if ($this->manager->isTask()): ?>
        <script type="text/javascript"
                src="<?php echo $this->manager->getURI(); ?>assets/js/angular.min.js"></script>
        <script type="text/javascript"
                src="<?php echo $this->manager->getURI(); ?>assets/js/angu-fixed-header-table.js"></script>
        <script type="text/javascript"
                src="<?php echo $this->manager->getURI(); ?>assets/js/ui-bootstrap-tpls-1.3.3.min.js"></script>
        <script type="text/javascript"
                src="<?php echo $this->manager->getURI(); ?>assets/js/angular-ui/mask.min.js"></script>
        <script type="text/javascript"
                src="<?php echo $this->manager->getURI(); ?>assets/js/angular-ui/validate.min.js"></script>
        <script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/hflogs/admin/stations/stationsApp.js"></script>
        <div ng-app="stationsApp">

            <div class="row" ng-controller="MainController" ng-init="init()">
                <script type="text/ng-template" id="station_form.html">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Station</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" ng-model="save.station.station_name">
                        <div class="form-group">
                            <label for="stationTitle">Title</label>
                            <input type="text" class="form-control" id="stationTitle" ng-model="save.station.title">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" ng-click="cancelStation()">Cancel</button>
                        <button type="button" class="btn btn-primary" ng-click="saveStation()">Save</button>
                    </div>
                </script>

                <script type="text/ng-template" id="location_form.html">
                    <form id="saveLocationForm" name="saveLocationForm">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">Location</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" ng-model="save.location.id">
                            <div class="form-group">
                                <label for="locStn">Station</label>
                                <select class="form-control" ng-model="save.location.station" ng-options="station as station.title for station in stations track by station.id" name="locStn" id="locStn" required ng-></select>
                            </div>
                            <div class="form-group">
                                <label for="locSite">Site</label>
                                <input type="text" id="locSite" name="locSite" ng-model="save.location.site"
                                       class="form-control" required minlength="2">
                                <span class="alert-danger alert help-block" ng-show="saveLocationForm.locSite.$error.minlength">Required and must be at least 2 characters in length</span>

                            </div>
                            <div class="form-group">
                                <label for="locStart">Start (UTC)</label>
                                <input type="text" id="locStart" name="locStart" class="form-control" placeholder="00:00" maxlength="5"
                                       ng-model="save.location.start_utc" ng-pattern="/^\d{2}:\d{2}$/" required ui-mask="99:99" ui-mask-placeholder model-view-value="true">
                                <span class="alert alert-danger help-block" ng-show="saveLocationForm.locStart.$error.parse">Not a valid time format.</span>
                            </div>
                            <div class="form-group">
                                <label for="locEnd">End (UTC)</label>
                                <input type="text" id="locEnd" name="locEnd" class="form-control" ng-model="save.location.end_utc"
                                       maxlength="5" placeholder="01:00" required ng-pattern="/^\d{2}:\d{2}$/" ui-mask="99:99" ui-mask-placeholder model-view-value="true">
                                <span class="alert alert-danger help-block" ng-show="saveLocationForm.locEnd.$error.parse">Not a valid time format.</span>
                            </div>
                            <div class="form-group">
                                <label for="locFreq">Frequency (kHz)</label>
                                <input type="text" id="locFreq" name="locFreq" class="form-control" maxlength="9" placeholder="10000"
                                       ng-model="save.location.frequency" required ng-pattern="/^\d{3,5}(\.\d{1,2})?$/">
                                <span class="alert alert-danger help-block" ng-show="saveLocationForm.locFreq.$error.parse">Not a valid time format.</span>
                            </div>
                            <div class="form-group">
                                <label for="locLat">Latitude</label>
                                <input type="text" id="locLat" name="locLat" class="form-control" maxlength="11" placeholder="42.056"
                                       ng-model="save.location.coordinates.lat" required ng-pattern="/^\-?\d{1,3}(\.\d{1,5})?$/">
                                <span class="alert alert-danger help-block" ng-show="saveLocationForm.locLat.$error.parse">Not a valid time format.</span>
                            </div>
                            <div class="form-group">
                                <label for="locLng">Longitude</label>
                                <input type="text" id="locLng" name="locLng" class="form-control" maxlength="12" placeholder="-76.998"
                                       ng-model="save.location.coordinates.lng" required ng-pattern="/^\-?\d{1,3}(\.\d{1,5})?$/">
                                <span class="alert alert-danger help-block" ng-show="saveLocationForm.locLng.$error.parse">Not a valid time format.</span>
                            </div>
                            <div class="form-group">
                                <label for="locLang">Language</label>
                                <select class="form-control" id="locLang" name="locLang" ng-model="save.location.language"
                                        ng-options="language as language.language for language in languages track by language.iso">
                                    <option value="">- not applicable -</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" ng-click="cancelLocation()">Cancel</button>
                            <button type="button" class="btn btn-primary" ng-disabled="saveLocationForm.$invalid" ng-click="saveLocations()">Save
                            </button>
                        </div>
                    </form>
                </script>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label for="stnSearch">Stations</label>
                        <input type="text" id="stnSearch" ng-model="stationQuery" class="form-control"
                               placeholder="search...">
                    </div>
                    <ul class="nav nav-pills">
                        <li role="presentation">
                            <a href="#" ng-click="openStationForm()">New...</a>
                        </li>
                        <li role="presentation" class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                               aria-haspopup="true" aria-expanded="false">List <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li role="presentation">
                                    <a href="#" ng-click="showAllStations()">All</a>
                                </li>
                                <li>
                                    <a href="#" ng-click="showInactiveStations()">Not approved</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <table class="table table-striped table-bordered" fixed-header table-height="300px">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>No. logs</th>
                            <th>No. locations</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="station in stations | filter: stationQuery"
                            ng-click="selectStation(station.id)" ng-class="{1:'',0:'danger'}[station.is_active]">
                            <td>
                                <div>{{station.title}}</div>
                                <div>
                                    <button ng-show="station.is_active == 0" type="button"
                                            class="btn btn-xs btn-success" ng-click="setStationActive()"><span
                                            class="glyphicon glyphicon-thumbs-up"></span></button>
                                    <button ng-show="station.is_active == 1" type="button"
                                            class="btn btn-xs btn-warning" ng-click="setStationInactive()"><span
                                            class="glyphicon glyphicon-thumbs-down"></span></button>
                                    <button type="button" class="btn btn-xs btn-info" ng-click="openStationEdit()"><span
                                            class="glyphicon glyphicon-pencil"></span></button>
                                    <button type="button" class="btn btn-xs btn-danger" ng-click="deleteStation()"><span
                                            class="glyphicon glyphicon-remove"></span></button>
                                </div>
                            </td>
                            <td>{{station.num_logs}}</td>
                            <td>{{station.num_locations}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                    <div class="form-group">
                        <label for="locSearch">Locations</label>
                        <input type="text" id="locSearch" ng-model="locationQuery" class="form-control"
                               placeholder="search...">
                    </div>
                    <ul class="nav nav-pills">
                        <li role="presentation">
                            <a href="#" ng-click="openLocationForm()">New...</a>
                        </li>
                        <li class="dropdown" role="presentation">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                               aria-haspopup="true" aria-expanded="false">List <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li role="presentation">
                                    <a href="#" ng-click="showAllLocations()">All</a>
                                </li>
                                <li role="presentation">
                                    <a href="#" ng-click="showInactiveLocations()">Not approved</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <table class="table table-bordered table-striped" fixed-header table-height="300px">
                        <thead>
                        <tr>
                            <th>Site</th>
                            <th>Frequency</th>
                            <th>Language</th>
                            <th>Coordinate</th>
                            <th>No. logs</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="location in locations | filter: locationQuery"
                            ng-class="{1:'',0:'danger'}[location.is_active]">
                            <td>
                                <div>{{ location.site }}</div>
                                <div style="font-size: 90%;">{{ location.station_title }}</div>
                                <div style="font-size: 90%;">{{ location.times }}</div>
                                <div>
                                    <button ng-show="location.is_active == 0" type="button"
                                            class="btn btn-xs btn-success" ng-click="setLocationActive()"><span
                                            class="glyphicon glyphicon-thumbs-up"></span></button>
                                    <button ng-show="location.is_active == 1" type="button"
                                            class="btn btn-xs btn-warning" ng-click="setLocationInactive()"><span
                                            class="glyphicon glyphicon-thumbs-down"></span></button>
                                    <button type="button" class="btn btn-xs btn-info" ng-click="openEditLocationForm()"><span
                                            class="glyphicon glyphicon-pencil"></span></button>
                                    <button type="button" class="btn btn-xs btn-danger"
                                            ng-click="deleteLocation()"><span
                                            class="glyphicon glyphicon-remove"></span></button>
                                </div>
                            </td>
                            <td>{{ location.frequency }}</td>
                            <td>{{ location.language.language }}&nbsp;</td>
                            <td style="white-space: nowrap;">{{ location.coordinates.lat }}, {{ location.coordinates.lng
                                }}
                            </td>
                            <td>{{ location.num_logs }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif;
