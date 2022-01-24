var adminloc = {
    getStations: function () {
        return $.post(globals.ajaxurl + 'station_selects.php', {}, null, 'json');
    },
    getLocations: function (station_id) {
        return $.post(globals.ajaxurl + 'location_selects.php', {stnId: station_id}, null, 'json');
    },
    getLocationHtml: function (station_id) {
        return $.post(globals.ajaxurl + 'logs.php', {
            _mode: 'stn',
            _task: 'locs',
            stationId: station_id
        }, null, 'html');
    },
    addStation: function (stn_name, stn_title) {
        return $.post(globals.ajaxurl + 'logs.php', {
            _mode: 'stn',
            _task: 'add',
            'name': stn_name,
            'title': stn_title
        }, null, 'json');
    },
    addLocation: function (stnid, lat, lng, site, start, end, freq) {
        return $.post(globals.ajaxurl + 'logs.php', {
            _mode: 'loc',
            _task: 'add',
            stn: stnid,
            'lat': lat,
            'lng': lng,
            'site': site,
            'start': start,
            'end': end,
            'freq': freq
        }, null, 'json');
    },
    removeLocation: function (log_id) {
        return $.post(globals.ajaxurl + 'logs.php', {
            _mode: 'loc',
            _task: 'del',
            id: log_id
        }, null, 'json');
    }
};

$(function () {
    var txtStnName = $('#stnName');
    var txtStnTitle = $('#stnTitle');
    var locStnId = $('#locStnId');
    var locSite = $('#locSite');
    var locStart = $('#locStart');
    var locEnd = $('#locEnd');
    var locFreq = $('#locFreq');
    var locLat = $('#locLat');
    var locLong = $('#locLong');
    var btnSubmit = $('input[name="submitBtn"]');
    var locationList = $('#locationList');

    var getLocationList = function (stationId) {
        $.when(adminloc.getLocationHtml(stationId)).done(function (a) {
            locationList.html(a);
        });
    };

    $('input[name="submitBtn"]').attr({'disabled': 'disabled'});

    /*$('#dlgAddStn').dialog({
     autoOpen:false,
     modal:true,
     buttons:[
     {
     text:'OK',
     click: function() {
     $.when(adminloc.addStation(txtStnName.val(), txtStnTitle.val())).done(function(a) {
     buildStationOptions(a.result);

     buildLocationOptions();

     $('#dlgAddStn').dialog('close');
     });
     }
     }, {
     text:'Cancel',
     click:function() {
     $('#dlgAddStn').dialog('close');
     }
     }
     ]
     });*/

    /*$('#dlgAddLoc').dialog({
     autoOpen:false,
     modal:true,
     buttons: [
     {
     text:'OK',
     click:function() {
     $.when(adminloc.addLocation(locStnId.val(), locLat.val(), locLong.val(), locSite.val(), locStart.val(), locEnd.val(), locFreq.val())).done(function(a) {
     buildLocationOptions(a.result);

     $('#dlgAddLoc').dialog('close');

     btnSubmit.removeAttr('disabled');
     });
     }
     },
     {
     text:'Cancel',
     click: function() {
     $('#dlgAddLoc').dialog('close');
     }
     }
     ]
     });*/

    $(document).on('click', '#aDlgDelLoc', function (e) {
        e.preventDefault();
        var logid = $('input[name="logid"]').val();

        var confirm = window.confirm('Confirm location removal!');
        if (confirm === true) {
            $.when(adminloc.removeLocation(logid)).done(function (a) {
                $('select[name="stn"]').val('');
            });
        }
    });

    /*$(document).on('click', '#aDlgAddStn', function(e) {
     e.preventDefault();

     $('#dlgAddStn').dialog('open');
     });*/

    $(document).on('click', '#aDlgAddLoc', function (e) {
        e.preventDefault();

        var station_id = $('select[name="stn"]').val();

        if (station_id !== "") {
            $('#locStnId').val(station_id);

            $('#dlgAddLoc').modal('show');
        } else {
            alert('Please, select a station.');
        }
    });

    $(document).on('change', 'select[name="stn"]', function (e) {
        var stnid = $(this).val();

        locStnId.val(stnid);

        buildLocationOptions();

        getLocationList(stnid);
    });

    $(document).on('blur', 'select[name="stn"], select[name="loc"]', function (e) {
        if ($('select[name="loc"]').val() !== "") {
            btnSubmit.removeAttr('disabled');
        }
    });

    function buildStationOptions(selected) {
        var sel = (selected) ? selected : false;

        $('select[name="stn"]').empty();

        $.when(adminloc.getStations()).done(function (a) {
            $(a.result).each(function (i, v) {
                var opt = $('<option />').val(v.id).html(v.title);

                if (sel === v.id) {
                    opt.attr({'selected': 'selected'});
                }

                opt.appendTo('select[name="stn"]');
            });
        });
    }

    function buildLocationOptions(selected) {
        var sel = (selected) ? selected : false;

        $('select[name="loc"]').empty();

        $.when(adminloc.getLocations($('select[name="stn"]').val())).done(function (a) {
            $(a.result).each(function (i, v) {
                var html_str = v.site + ': ';
                html_str += v.time_slot;
                html_str += ' - ' + v.frequency;

                var opt = $('<option />').val(v.id).html(html_str);

                if (sel === v.id) {
                    opt.attr({'selected': 'selected'});
                }

                opt.appendTo('select[name="loc"]');
            });
        });
    }
});