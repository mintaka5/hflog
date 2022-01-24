var globals = {
    'relurl': '/',
    'siteurl': '/',
    getDay: function (num) {
        var days = new Array(7);
        days[0] = 'Sun.';
        days[1] = 'Mon.';
        days[2] = 'Tues.';
        days[3] = 'Weds.';
        days[4] = 'Thurs.';
        days[5] = 'Fri.';
        days[6] = 'Sat.';

        return days[num];
    },
    padNum: function (num, padding) {
        return ('00' + num).slice(-(padding));
    }
};
globals.ajaxurl = globals.relurl + 'controllers/ajax/';

var logplayer = {
    audioposition: 0,
    audiocurrent: false
};

$(function () {
    $.fn.serializeFormJSON = function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    $('#logContainer').css({'display': 'block'});

    $('.audioplayer').each(function (i, v) {
        var mp3file = $(v).prev().val();

        $('#jquery_jplayer_' + i).jPlayer({
            cssSelectorAncestor: '#jp_container_' + i,
            swfPath: globals.relurl + 'assets/jquery/jQuery.jPlayer.2.1.0',
            solution: 'flash,html',
            supplied: 'mp3',
            ready: function () {
                //console.log(mp3file);
                $('#jquery_jplayer_' + i).jPlayer('setMedia', {mp3: mp3file});
            }
        });

        $('#jquery_jplayer_' + i).bind($.jPlayer.event.play, function () {
            $(this).jPlayer('pauseOthers');
        });
    });

    // set all play buttons text to loading status
    $('.logPlay').text('...');

    soundManager.setup({
        url: globals.relurl + 'assets/soundmanager/swf/soundmanager2.swf',
        flashVersion: 9,
        useFlashBlock: false,
        debugMode: false,
        debugFlash: false,
        onready: function () {
            $('.logPlayer').each(function (i, v) {
                soundManager.createSound({
                    id: $(v).attr('id'),
                    url: globals.relurl + 'audio/?id=' + $(v).data('id'),
                    autoLoad: true,
                    autoPlay: false,
                    onload: function () {
                        this.setPosition(0);

                        var jqId = '#' + this.id;

                        $(jqId).children('.logPlay').html('<span class="glyphicon glyphicon-play"></span> Play');
                        $(jqId).children('.logPlay').removeAttr('disabled');
                    },
                    onplay: function () {
                        logplayer.audiocurrent = this;

                        var jqId = '#' + this.id;

                        $(jqId).children('.logPlay').attr('disabled', 'disabled');
                        $(jqId).children('.logStop').removeAttr('disabled');
                    },
                    onstop: function () {
                        logplayer.audiocurrent = false;

                        this.setPosition(0);

                        var jqId = '#' + this.id;

                        $(jqId).children('.logPlay').removeAttr('disabled');
                        $(jqId).children('.logPlay').html('<span class="glyphicon glyphicon-play"></span> Play');
                        $(jqId).children('.logStop').attr('disabled', 'disabled');
                    },
                    onfinish: function () {
                        this.setPosition(0);

                        var jqId = '#' + this.id;

                        $(jqId).children('.logPlay').removeAttr('disabled');
                        $(jqId).children('.logPlay').html('<span class="glyphicon glyphicon-play"></span> Play');
                        $(jqId).children('.logStop').attr('disabled', 'disabled');
                    },
                    multishot: false,
                    volume: 80
                });
            });
        }
    });

    $('.logPlay').on('click', function (e) {
        e.preventDefault();

        soundManager.stopAll();

        var sound_id = $(e.currentTarget).parent('.logPlayer').attr('id');

        soundManager.play(sound_id, {
            whileplaying: function () {
                var fraction = (this.position / this.duration);
                var jqId = '#' + this.id;

                $(jqId).children('.logPlay').text(Math.round(fraction * 100) + '%');
            }
        });
    });

    $('.logStop').on('click', function (e) {
        e.preventDefault();

        var sound_id = $(e.currentTarget).parent('.logPlayer').attr('id');

        soundManager.stop(sound_id);
    });

    $('.cancel').on('click', function (e) {
        var url = $(this).data('url');

        window.location.href = url;
    });

    $('a.confirm').on('click', function (e) {
        e.preventDefault();

        var href = $(this).prop('href');
        var msg = $(this).data('confirm');

        var confirm = window.confirm(msg);

        if (confirm == true) {
            window.location.href = href;
        }
    });

    $('.navto').on('click', function (e) {
        e.preventDefault();

        var href = $(this).data('href');

        window.location.href = href;
    });
});