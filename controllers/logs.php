<?php
require_once 'globals.php';

$gmapsExcludeModes = array("add", "audio", "beta");
\Ode\View::getInstance()->assign("mode_is_excluded", in_array(\Ode\Manager::getInstance()->getMode(), $gmapsExcludeModes));

switch (\Ode\Manager::getInstance()->getMode()) {
    default:
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                $logs = \Ode\DBO\Hflog::getAllActive();

                $pager = \Pager::factory(array(
                    'mode' => "Jumping",
                    'perPage' => 25,
                    'delta' => 10,
                    'append' => true,
                    'urlVar' => 'page',
                    'itemData' => $logs,
                    'nextImg' => 'Older &gt;&gt;',
                    'prevImg' => '&lt;&lt; Newer'
                ));

                if ($pager->getCurrentPageID() > 1 && \Ode\Auth::getInstance()->hasMetQuota(LOG_VIEW_QUOTA)) {
                    header("Location: " . \Ode\Manager::getInstance()->friendlyAction('info', 'gopro'));
                    exit();
                }

                \Ode\View::getInstance()->assign("logs", $pager->getPageData());
                \Ode\View::getInstance()->assign("page", $pager->getCurrentPageID());
                \Ode\View::getInstance()->assign('nextpage', $pager->getNextPageID());
                \Ode\View::getInstance()->assign('prevpage', $pager->getPreviousPageID());
                \Ode\View::getInstance()->assign('numpages', $pager->numPages());
                \Ode\View::getInstance()->assign('numitems', $pager->numItems());
                \Ode\View::getInstance()->assign('isfirstpage', $pager->isFirstPage());
                \Ode\View::getInstance()->assign('islastpage', $pager->isLastPage());
                \Ode\View::getInstance()->assign('islastcomplete', $pager->isLastPageComplete());
                \Ode\View::getInstance()->assign('pagerange', $pager->range);
                \Ode\View::getInstance()->assign("links", $pager->getLinks());
                break;
        }
        break;
    case 'audio':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                $dirPath = dirname(dirname(dirname(APP_PATH . DIRECTORY_SEPARATOR . "..") . DIRECTORY_SEPARATOR . ".."));
                $relPath = "/audio/stations";
                $filePath = $dirPath . $relPath;
                $files = \File_Find::search("*.mp3", $filePath, 'shell', false);
                $fileColl = new \ArrayObject();
                foreach ($files as $file) {
                    $mp3Obj = new stdClass();

                    $title = "Untitled";
                    try {
                        $id3v2 = new \Zend_Media_Id3v2($file);
                        $frame = $id3v2->getFramesByIdentifier("TIT2");
                        $title = $frame[0]->getText();
                    } catch (\Exception $e) {
                    }
                    $mp3Obj->title = $title;
                    $mp3Obj->src = str_replace($dirPath, "", $file);

                    $fileColl->append($mp3Obj);
                }

                \Ode\View::getInstance()->assign("audiofiles", $fileColl);
                break;
            case 'upload':
                $station = \Ode\DBO\SWStation::getOneById($_GET['id']);

                \Ode\View::getInstance()->assign("station", $station);
                break;
        }
        break;
    case 'whatson':
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
            case 'now':
                if (\Ode\Auth::getInstance()->hasMetQuota(LOG_VIEW_QUOTA)) {
                    header("Location: " . \Ode\Manager::getInstance()->friendlyAction('info', 'gopro'));
                    exit();
                }

                $dow = "";
                if (isset($_GET['dow'])) {
                    $dow = date('l');
                    $logs = \Ode\DBO\Hflog::getWhatsOnNow();
                } else {
                    $logs = \Ode\DBO\Hflog::getWhatsOnAnyDay();
                }

                $pager = \Pager::factory(array(
                    'mode' => "Jumping",
                    'perPage' => 10,
                    'delta' => 5,
                    'append' => true,
                    'urlVar' => 'page',
                    'itemData' => $logs,
                    'nextImg' => 'Older &gt;&gt;',
                    'prevImg' => '&lt;&lt; Newer'
                ));

                \Ode\View::getInstance()->assign("dow", $dow);
                \Ode\View::getInstance()->assign("logs", $pager->getPageData());
                \Ode\View::getInstance()->assign("page", $pager->getCurrentPageID());
                \Ode\View::getInstance()->assign('nextpage', $pager->getNextPageID());
                \Ode\View::getInstance()->assign('prevpage', $pager->getPreviousPageID());
                \Ode\View::getInstance()->assign('numpages', $pager->numPages());
                \Ode\View::getInstance()->assign('numitems', $pager->numItems());
                \Ode\View::getInstance()->assign('isfirstpage', $pager->isFirstPage());
                \Ode\View::getInstance()->assign('islastpage', $pager->isLastPage());
                \Ode\View::getInstance()->assign('islastcomplete', $pager->isLastPageComplete());
                \Ode\View::getInstance()->assign('pagerange', $pager->range);
                \Ode\View::getInstance()->assign("links", $pager->getLinks());
                break;
        }
        break;
    case 'search':
        if (\Ode\Auth::getInstance()->hasMetQuota(LOG_VIEW_QUOTA)) {
            header("Location: " . \Ode\Manager::getInstance()->friendlyAction('info', 'gopro'));
            exit();
        }

        $sQry = trim(strip_tags($_GET['q']));
        $qry = "%" . preg_replace("/[\s\t\r\n]+/", "%", $sQry) . "%";

        $sql = "SELECT " . \Ode\DBO\Hflog::COLUMNS . "
			FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
			LEFT JOIN hflog_sw_locations AS b ON (b.hflog_id = a.id)
			LEFT JOIN sw_locations AS c ON (c.id = b.sw_loc_id)
			LEFT JOIN sw_stations AS d ON (d.id = c.station_id)
			WHERE (
                            a.frequency = " . \Ode\DBO::getInstance()->quote($sQry, \PDO::PARAM_INT) . "
                            OR a.description LIKE " . \Ode\DBO::getInstance()->quote($qry, \PDO::PARAM_STR) . "
                            OR c.site LIKE " . \Ode\DBO::getInstance()->quote($qry, \PDO::PARAM_STR) . "
                            OR d.title LIKE " . \Ode\DBO::getInstance()->quote($qry, \PDO::PARAM_STR) . "
                        )
            AND a.id NOT IN (
                SELECT hflog_id 
                FROM " . \Ode\DBO\Hflog\Status::TABLE_NAME . "
                WHERE status IN (
                            " . \Ode\DBO::getInstance()->quote(\Ode\DBO\Hflog\Status::STATUS_INACTIVE) . ",
                            " . \Ode\DBO::getInstance()->quote(\Ode\DBO\Hflog\Status::STATUS_NOT_APPROVED) . "
                        )
            )
			ORDER BY a.submitted
			DESC";

        $logs = \Ode\DBO::getInstance()->query($sql)->fetchAll(PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);

        if (!isset($_GET['token'])) {
            $logs = false;
        } else {
            if (!$tokener->isValid($_GET['token'])) {
                $logs = false;
            }
        }

        $pager = \Pager::factory(array(
            'mode' => "Jumping",
            'perPage' => 10,
            'delta' => 5,
            'append' => true,
            'urlVar' => 'page',
            'itemData' => $logs
        ));

        \Ode\View::getInstance()->assign("logs", $pager->getPageData());
        \Ode\View::getInstance()->assign("page", $pager->getCurrentPageID());
        \Ode\View::getInstance()->assign('nextpage', $pager->getNextPageID());
        \Ode\View::getInstance()->assign('prevpage', $pager->getPreviousPageID());
        \Ode\View::getInstance()->assign('numpages', $pager->numPages());
        \Ode\View::getInstance()->assign('numitems', $pager->numItems());
        \Ode\View::getInstance()->assign('isfirstpage', $pager->isFirstPage());
        \Ode\View::getInstance()->assign('islastpage', $pager->isLastPage());
        \Ode\View::getInstance()->assign('islastcomplete', $pager->isLastPageComplete());
        \Ode\View::getInstance()->assign('pagerange', $pager->range);
        \Ode\View::getInstance()->assign("links", $pager->getLinks());
        \Ode\View::getInstance()->assign("query", $sQry);
        break;
    case 'add':
        if (!\Ode\Auth::getInstance()->isAuth()) {
            header('Location: ' . \Ode\Manager::getInstance()->friendlyAction('auth', null, null, array('referer', '/logs/add')));
            exit();
        }

        $form = new HTML_QuickForm2("hflogForm");
        $form->setAttribute("action", \Ode\Manager::getInstance()->friendlyAction("logs", "add"));
        $form->setAttribute('role', 'form');

        $locId = $form->addHidden("loc")->setAttribute("id", "locHdn");
        $latHdn = $form->addHidden("lat")->setAttribute("id", "latHdn");
        $lngHdn = $form->addHidden("lng")->setAttribute("id", "lngHdn");

        $freqTxt = $form->addText("freq")->setLabel("Frequency (kHz)")->setAttribute('class', 'form-control');
        $freqTxt->addRule("required", "Required");
        $freqTxt->addRule("regex", "Must be in kilohertz decimal form (i.e. 14070.15)", "/^\d{3,5}(\.\d{2,3})?/");

        $modeSel = $form->addSelect("mode")->setLabel("Mode")->setAttribute('class', 'form-control');
        $modeSel->addOption("- select -", "");
        $modeSel->addOption("AM", "AM");
        $modeSel->addOption("FM", "FM");
        $modeSel->addOption("CW", "CW");
        $ssbGrp = $modeSel->addOptgroup("SSB");
        $ssbGrp->addOption("USB", "USB");
        $ssbGrp->addOption("LSB", "LSB");
        $digGrp = $modeSel->addOptgroup("Digital");
        $digGrp->addOption("RTTY", "RTTY");
        $digGrp->addOption("PSK31", "PSK31");
        $digGrp->addOption("ALE", "ALE");
        $modeSel->addRule("required", "Required");

        $timeOnTxt = new HTML_QuickForm2_Element_InputText("timeOn");
        $timeOnTxt->setLabel("Time on (UTC)");
        $timeOnTxt->setAttribute("id", "timeOn");
        $timeOnTxt->setAttribute('class', 'form-control');
        $timeOnTxt->setAttribute("maxlength", 5);
        $timeOnTxt->setAttribute("size", 6);
        $timeOnTxt->addRule("required", "Required");
        $timeOnTxt->addRule("regex", "Must provide valid time format (hh:mm)", "/\d{1,2}\:\d{2}/");
        $form->addElement($timeOnTxt);

        $dateOnTxt = new HTML_QuickForm2_Element_InputText("dateOn");
        $dateOnTxt->setLabel("Date");
        $dateOnTxt->setAttribute("id", "dateOn");
        $dateOnTxt->setAttribute('class', 'form-control');
        $dateOnTxt->setAttribute("maxlength", 10);
        $dateOnTxt->setAttribute("size", 11);
        $dateOnTxt->addRule("required", "Required");
        $dateOnTxt->addRule("regex", "Not a valid date format (mm/dd/yyyy)", "/\d{1,2}\/\d{1,2}\/\d{4}/");
        $dateOnTxt->setValue(date('m/d/Y'));
        $form->addElement($dateOnTxt);

        $stnSel = $form->addSelect("station");
        $stnSel->setLabel("Station");
        $stnSel->setAttribute("id", "stationSel");
        $stnSel->setAttribute('class', 'form-control');
        $stnSel->addOption("- select -", "");
        $stations = \Ode\DBO::getInstance()->query("
                    SELECT " . \Ode\DBO\SWStation::COLUMNS . "
                    FROM " . \Ode\DBO\SWStation::TABLE_NAME . " AS a
                    ORDER BY a.title
                    ASC
                ")->fetchAll(\PDO::FETCH_CLASS, \Ode\DBO\SWStation::MODEL_NAME);
        foreach ($stations as $stn) {
            $stnSel->addOption(utf8_decode($stn->title()), $stn->station_name);
        }

        $addStnBtn = $form->addButton('addStnBtn')->setAttribute('type', 'button')->setAttribute('class', 'btn btn-block btn-default add-station')->setContent('Add station');

        $locSel = $form->addSelect("locSel")->setLabel("Location");
        $locSel->setAttribute("id", "locSel");
        $locSel->setAttribute('class', 'form-control');
        $locSel->setAttribute('disabled', 'disabled');

        $addLocBtn = $form->addButton('addLocBtn')->setAttribute('type', 'button')->setAttribute('class', 'btn btn-default btn-block add-location')->setContent('Add location');

        $descTxt = $form->addText("desc")->setLabel("Description");
        $descTxt->setAttribute("maxlength", 255);
        $descTxt->setAttribute('class', 'form-control');
        $descTxt->setAttribute("size", 65);
        $descTxt->addRule("required", "Required");

        $submitBtn = $form->addButton("submitBtn")->setAttribute('type', 'submit')->setAttribute("class", "btn btn-default")->setContent('Log');
        $form->addElement($submitBtn);

        if ($form->validate()) {
            $logId = UUID::get();

            \Ode\DBO::getInstance()->beginTransaction();

            $sth = \Ode\DBO::getInstance()->prepare("
				INSERT INTO hflogs (id, frequency, mode, description, time_on, lat, lng, user_id, submitted)
				VALUES (:id, :freq, :mode, :desc, :on, :lat, :lng, :user, UTC_TIMESTAMP())
			");
            $sth->bindValue(":id", $logId, PDO::PARAM_STR);
            $sth->bindValue(":freq", trim($_POST['freq']), PDO::PARAM_INT);
            $sth->bindValue(":mode", $_POST['mode'], PDO::PARAM_STR);
            $sth->bindValue(":desc", trim(strip_tags($_POST['desc'])), PDO::PARAM_STR);
            $on = date("Y-m-d H:i:s", strtotime(trim($_POST['dateOn']) . " " . trim($_POST['timeOn'])));
            $sth->bindValue(":on", $on, PDO::PARAM_STR);
            if (empty($_POST['lat']) || empty($_POST['lng'])) {
                $sth->bindValue(":lat", \Ode\Auth::getInstance()->getSession()->lat, PDO::PARAM_STR);
                $sth->bindValue(":lng", \Ode\Auth::getInstance()->getSession()->lng, PDO::PARAM_STR);
            } else {
                $sth->bindValue(":lat", $_POST['lat'], PDO::PARAM_STR);
                $sth->bindValue(":lng", $_POST['lng'], PDO::PARAM_STR);
            }
            $sth->bindValue(":user", \Ode\Auth::getInstance()->getSession()->id, PDO::PARAM_STR);

            try {
                $sth->execute();
            } catch (\PDOException $e) {
                error_log($e->getMessage(), 1, APP_SITE_CONTACT_EMAIL);
            }

            /**
             * If 'loc' is set, store the location of the station in the hf_sw_locations table
             */
            if (!empty($_POST['loc'])) {
                $sth = \Ode\DBO::getInstance()->prepare("
					INSERT INTO hflog_sw_locations (sw_loc_id, hflog_id)
					VALUES (:sw_loc_id, :hflog_id)
				");
                $sth->bindValue(":sw_loc_id", $_POST['loc'], PDO::PARAM_INT);
                $sth->bindValue(":hflog_id", $logId, PDO::PARAM_STR);

                try {
                    $sth->execute();
                } catch (\PDOException $e) {
                    error_log($e->getTraceAsString(), 0);
                }
            }

            /**
             * set log status to inactive for approval
             * only if the user is not an admin
             */
            if (!\Ode\Auth::getInstance()->getSession()->isAdmin()) {
                \Ode\DBO\Hflog\Status::setStatus($logId, \Ode\DBO\Hflog\Status::STATUS_INACTIVE);
            }

            \Ode\DBO::getInstance()->commit();

            if (isset($_POST['submitBtn'])) {
                header("Location: " . \Ode\Manager::getInstance()->friendlyAction("user", "logs"));
            } else {
                header("Location: " . \Ode\Manager::getInstance()->friendlyAction("logs", "add"));
            }
            exit();
        }

        \Ode\View::getInstance()->assign('languages', \Ode\DBO\Language::getAll());
        \Ode\View::getInstance()->assign("form", $form->render(\Ode\View::getInstance()->getFormRenderer()));
        break;
    case
    'recent':
        if (\Ode\Auth::getInstance()->hasMetQuota(LOG_VIEW_QUOTA)) {
            header("Location: " . \Ode\Manager::getInstance()->friendlyAction('info', 'gopro'));
            exit();
        }

        $recentLogs = \Ode\DBO\Hflog::getRecent();

        $pager = \Pager::factory(array(
            'mode' => "Jumping",
            'perPage' => 25,
            'delta' => 10,
            'append' => true,
            'urlVar' => 'page',
            'itemData' => $recentLogs,
            'nextImg' => 'Older &gt;&gt;',
            'prevImg' => '&lt;&lt; Newer'
        ));

        \Ode\View::getInstance()->assign("logs", $pager->getPageData());
        \Ode\View::getInstance()->assign("page", $pager->getCurrentPageID());
        \Ode\View::getInstance()->assign('nextpage', $pager->getNextPageID());
        \Ode\View::getInstance()->assign('prevpage', $pager->getPreviousPageID());
        \Ode\View::getInstance()->assign('numpages', $pager->numPages());
        \Ode\View::getInstance()->assign('numitems', $pager->numItems());
        \Ode\View::getInstance()->assign('isfirstpage', $pager->isFirstPage());
        \Ode\View::getInstance()->assign('islastpage', $pager->isLastPage());
        \Ode\View::getInstance()->assign('islastcomplete', $pager->isLastPageComplete());
        \Ode\View::getInstance()->assign('pagerange', $pager->range);
        \Ode\View::getInstance()->assign("links", $pager->getLinks());
        break;
    case 'log':
        switch (\Ode\Manager::getInstance()->getTask()) {
            case false:
            default:

                break;
            case 'delete':
                if (!\Ode\Auth::getInstance()->isAdmin()) {
                    header('Location: ' . \Ode\Manager::getInstance()->friendlyAction('logs'));
                    exit();
                }

                \Ode\DBO\Hflog::delete($_GET['id']);

                header('Location: ' . \Ode\Manager::getInstance()->friendlyAction('logs'));
                exit();
                break;
            case 'view':
                if (\Ode\Auth::getInstance()->hasMetQuota(LOG_VIEW_QUOTA)) {
                    header("Location: " . \Ode\Manager::getInstance()->friendlyAction('info', 'gopro'));
                    exit();
                }

                $log = \Ode\DBO\Hflog::getOneById(strval(trim($_GET['id'])));
                $userLogs = \Ode\DBO\Hflog::getAllByFrequency($log->frequency, $log->id);

                \Ode\View::getInstance()->assign('log', $log);
                \Ode\View::getInstance()->assign('userlogs', $userLogs);

                break;
            case 'edit':
                if (!\Ode\Auth::getInstance()->isAuth()/* || !\Ode\Auth::getInstance()->isPro()*/) {
                    header("Location: " . \Ode\Manager::getInstance()->friendlyAction('info', 'gopro'));
                    exit();
                }

                $log = \Ode\DBO\Hflog::getOneById(trim($_GET['id']));

                $form = new \HTML_QuickForm2('editLogForm');
                $form->setAttribute('action', \Ode\Manager::getInstance()->friendlyAction('logs', 'log', 'edit', array('id', $log->id)));
                $form->setAttribute('role', 'form');

                $locationId = $form->addHidden('loc')->setAttribute('id', 'locHdn');
                if ($log->hasLocation()) {
                    $locationId->setValue($log->location()->location()->id);
                }

                $latHdn = $form->addHidden('lat')->setAttribute('id', 'latHdn');
                $latHdn->setValue(\Ode\Auth::getInstance()->getSession()->lat);

                $lngHdn = $form->addHidden('lng')->setAttribute('id', 'lngHdn');
                $lngHdn->setValue(\Ode\Auth::getInstance()->getSession()->lng);

                $freqTxt = $form->addText('freq')->setLabel('Frequency (kHz)')->setAttribute('class', 'form-control');
                $freqTxt->addRule('required', 'Required');
                $freqTxt->addRule('regex', 'Must be in kilohertz decimal form (e.g. 14070.15)', "/^\d{3,5}(\.\d{2,3})?/");
                $freqTxt->setValue($log->freq());

                $modeSel = $form->addSelect('mode')->setLabel('Mode')->setAttribute('class', 'form-control');
                $modeSel->addOption("- select -", "");
                $modeSel->addOption("AM", "AM");
                $modeSel->addOption("FM", "FM");
                $modeSel->addOption("CW", "CW");
                $ssbGrp = $modeSel->addOptgroup("SSB");
                $ssbGrp->addOption("USB", "USB");
                $ssbGrp->addOption("LSB", "LSB");
                $digGrp = $modeSel->addOptgroup("Digital");
                $digGrp->addOption("RTTY", "RTTY");
                $digGrp->addOption("PSK31", "PSK31");
                $digGrp->addOption("ALE", "ALE");
                $modeSel->addRule("required", "Required");
                $modeSel->setValue($log->mode);

                $timeOnTxt = new \HTML_QuickForm2_Element_InputText("timeOn");
                $timeOnTxt->setLabel("Time on (UTC)");
                $timeOnTxt->setAttribute("id", "timeOn");
                $timeOnTxt->setAttribute('class', 'form-control');
                $timeOnTxt->setAttribute("maxlength", 5);
                $timeOnTxt->setAttribute("size", 6);
                $timeOnTxt->addRule("required", "Required");
                $timeOnTxt->addRule("regex", "Must provide valid time format (hh:mm)", "/\d{1,2}\:\d{2}/");
                $form->addElement($timeOnTxt);
                $timeOnTxt->setValue(date('H:i', strtotime($log->time_on)));

                $dateOnTxt = new \HTML_QuickForm2_Element_InputText("dateOn");
                $dateOnTxt->setLabel("Date");
                $dateOnTxt->setAttribute("id", "dateOn");
                $dateOnTxt->setAttribute('class', 'form-control');
                $dateOnTxt->setAttribute("maxlength", 10);
                $dateOnTxt->setAttribute("size", 11);
                $dateOnTxt->addRule("required", "Required");
                $dateOnTxt->addRule("regex", "Not a valid date format (mm/dd/yyyy)", "/\d{1,2}\/\d{1,2}\/\d{4}/");
                $form->addElement($dateOnTxt);
                $dateOnTxt->setValue(date('m/d/Y', strtotime($log->time_on)));

                $stnSel = $form->addSelect("station");
                $stnSel->setLabel("Station");
                $stnSel->setAttribute("id", "stationSel");
                $stnSel->setAttribute('class', 'form-control');
                $stnSel->addOption("- select -", "");
                $stations = \Ode\DBO::getInstance()->query("
                    SELECT " . \Ode\DBO\SWStation::COLUMNS . "
                    FROM " . \Ode\DBO\SWStation::TABLE_NAME . " AS a
                    ORDER BY a.title
                    ASC
                ")->fetchAll(\PDO::FETCH_CLASS, \Ode\DBO\SWStation::MODEL_NAME);
                foreach ($stations as $stn) {
                    $stnSel->addOption(utf8_decode($stn->title()), $stn->station_name);
                }

                $locSel = $form->addSelect("locSel")->setLabel("Location");
                $locSel->setAttribute("id", "locSel");
                $locSel->setAttribute('class', 'form-control');
                if ($log->hasLocation()) {
                    $stationLocations = \Ode\DBO::getInstance()->query("
                        SELECT " . \Ode\DBO\SWLocation::COLUMNS . "
                        FROM " . \Ode\DBO\SWLocation::TABLE_NAME . " AS a
                        WHERE a.station_id = " . \Ode\DBO::getInstance()->quote($log->location()->location()->station_id, \PDO::PARAM_STR) . "
                    ")->fetchAll(\PDO::FETCH_CLASS, \Ode\DBO\SWLocation::MODEL_NAME);
                    foreach ($stationLocations as $stnLoc) {
                        $locSel->addOption($stnLoc->site, $stnLoc->id);
                    }
                    $locSel->setValue($log->location()->location()->id);

                    $currentStation = \Ode\DBO\SWStation::getOneById($log->location()->location()->station_id);
                    $stnSel->setValue($currentStation->station_name);
                } else {
                    $locSel->setAttribute('disabled', 'disabled');
                }

                $descTxt = $form->addText("desc")->setLabel("Description");
                $descTxt->setAttribute("maxlength", 255);
                $descTxt->setAttribute('class', 'form-control');
                $descTxt->setAttribute("size", 65);
                $descTxt->addRule("required", "Required");
                $descTxt->setValue($log->description());

                $btnGrp = $form->addGroup('buttons', array('class' => 'btn-group'));

                $updateBtn = $btnGrp->addButton('updateBtn')->setContent('Save')->setAttribute('type', 'submit')->setAttribute('id', 'updateBtn')->setAttribute('class', 'btn btn-default btn-spaced');
                if (\Ode\Auth::getInstance()->isAdmin()) {
                    $disapproveBtn = $btnGrp->addButton('disapproveBtn')->setContent('Disapprove')->setAttribute('type', 'button')->setAttribute('class', 'btn btn-warning btn-spaced navto')->setAttribute('data-href', \Ode\Manager::getInstance()->action('admin_hflogs', 'log', 'unapprove', array('id', $log->id)));
                }
                $cancelBtn = $btnGrp->addButton('cancelBtn')->setContent('Cancel')
                    ->setAttribute('type', 'button')
                    ->setAttribute('data-url', \Ode\Manager::getInstance()->friendlyAction('logs', 'log', 'view', array('id', $log->id)))
                    ->setAttribute('class', 'btn btn-danger cancel btn-spaced');

                if ($form->validate()) {
                    $data = $_POST;

                    \Ode\DBO::getInstance()->beginTransaction();

                    $sth = \Ode\DBO::getInstance()->prepare("
                        UPDATE " . \Ode\DBO\Hflog::TABLE_NAME . "
                        SET
                            frequency = :freq,
                            mode = :mode,
                            description = :desc,
                            time_on = :time_on,
                            lat = :lat,
                            lng = :lng
                        WHERE id = :id
                    ");
                    $sth->bindValue(":freq", floatval(trim($data['freq'])), PDO::PARAM_INT);
                    $sth->bindValue(":mode", $data['mode'], PDO::PARAM_STR);
                    $sth->bindValue(":desc", $data['desc'], PDO::PARAM_STR);
                    $timeOn = date("Y-m-d H:i:s", strtotime(trim($data['dateOn']) . " " . trim($data['timeOn'])));
                    $sth->bindValue(":time_on", $timeOn, PDO::PARAM_STR);
                    if (empty($data['lat']) || empty($data['lng'])) {
                        $sth->bindValue(":lat", \Ode\Auth::getInstance()->getSession()->lat, \PDO::PARAM_STR);
                        $sth->bindValue(":lng", \Ode\Auth::getInstance()->getSession()->lng, \PDO::PARAM_STR);
                    } else {
                        $sth->bindValue(":lat", $data['lat'], \PDO::PARAM_STR);
                        $sth->bindValue(":lng", $data['lng'], \PDO::PARAM_STR);
                    }
                    $sth->bindValue(":id", $log->id, \PDO::PARAM_STR);

                    try {
                        $sth->execute();
                    } catch (\PDOException $e) {
                        error_log($e->getTraceAsString(), 0);
                    }

                    /**
                     * If 'loc' is set, store the location of the station in the hf_sw_locations table
                     */
                    if (!empty($data['loc'])) {
                        $curSwLoc = \Ode\DBO::getInstance()->query("
                            SELECT a.id
                            FROM " . \Ode\DBO\Hflog\SWLocation::TABLE_NAME . " AS a
                            WHERE a.hflog_id = " . \Ode\DBO::getInstance()->quote($log->id, \PDO::PARAM_STR) . "
                            LIMIT 0,1
                        ")->fetchColumn();
                        //Util::debug($curSwLoc, true);

                        /**
                         * if current location is set update location record
                         * or insert a new instance
                         */
                        if ($curSwLoc != false) {
                            $sth = \Ode\DBO::getInstance()->prepare("
                            UPDATE " . \Ode\DBO\Hflog\SWLocation::TABLE_NAME . "
                            SET
                                sw_loc_id = :sw_loc_id
                            WHERE id = :id
                        ");
                            $sth->bindValue(":sw_loc_id", $data['loc'], \PDO::PARAM_INT);
                            $sth->bindValue(":id", $curSwLoc, \PDO::PARAM_INT);

                            try {
                                $sth->execute();
                            } catch (\PDOException $e) {
                                error_log($e->getTraceAsString(), 0);
                            }
                        } else {
                            $sth = \Ode\DBO::getInstance()->prepare("
                                INSERT INTO hflog_sw_locations (sw_loc_id, hflog_id)
                                VALUES (:sw_loc_id, :hflog_id)
                            ");
                            $sth->bindValue(":sw_loc_id", $data['loc'], \PDO::PARAM_INT);
                            $sth->bindValue(":hflog_id", $log->id, \PDO::PARAM_STR);

                            try {
                                $sth->execute();
                            } catch (\PDOException $e) {
                                error_log($e->getTraceAsString(), 0);
                            }
                        }
                    } else {
                        // remove location assignment
                        $sth = \Ode\DBO::getInstance()->prepare("
                            DELETE FROM " . \Ode\DBO\Hflog\SWLocation::TABLE_NAME . "
                            WHERE hflog_id = :hflog_id
                        ");
                        $sth->bindValue(":hflog_id", $log->id, \PDO::PARAM_STR);

                        try {
                            $sth->execute();
                        } catch (\PDOException $e) {
                            error_log($e->getTraceAsString(), 0);
                        }
                    }

                    /**
                     * reset log status to inactive if not an admin user for approval
                     */
                    if (!\Ode\Auth::getInstance()->getSession()->isAdmin()) {
                        \Ode\DBO\Hflog\Status::setStatus($log->id, \Ode\DBO\Hflog\Status::STATUS_INACTIVE);
                    }

                    \Ode\DBO::getInstance()->commit();

                    header('Location: ' . \Ode\Manager::getInstance()->friendlyAction('logs', 'log', 'view', array('id', $log->id)));
                    exit();
                }

                \Ode\View::getInstance()->assign('form', $form->render(\Ode\View::getInstance()->getFormRenderer()));
                \Ode\View::getInstance()->assign('log', $log);

                break;
        }
        break;
}
