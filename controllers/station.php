<?php
switch (\Ode\Manager::getInstance()->getMode()) {
    default:
        if (\Ode\Auth::getInstance()->hasMetQuota(LOG_VIEW_QUOTA)) {
            header("Location: " . \Ode\Manager::getInstance()->friendlyAction('info', 'gopro'));
            exit();
        }

        $logs = \Ode\DBO::getInstance()->query("
					SELECT " . \Ode\DBO\Hflog::COLUMNS . "
					FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
					LEFT JOIN " . \Ode\DBO\Hflog\SWLocation::TABLE_NAME . " AS b ON (b.hflog_id = a.id)
					LEFT JOIN " . \Ode\DBO\SWLocation::TABLE_NAME . " AS c ON (c.id = b.sw_loc_id)
					LEFT JOIN " . \Ode\DBO\SWLocation::TABLE_NAME . " AS d ON (d.id = c.station_id)
					WHERE c.station_id = " . \Ode\DBO::getInstance()->quote(trim($_GET['id']), \PDO::PARAM_STR) . "
					ORDER BY a.submitted
					DESC
				")->fetchAll(\PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);

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

        \Ode\View::getInstance()->assign("station", \Ode\DBO\SWStation::getOneById(trim($_GET['id'])));
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
    case 'view':
        if (\Ode\Auth::getInstance()->hasMetQuota(LOG_VIEW_QUOTA)) {
            header("Location: " . \Ode\Manager::getInstance()->friendlyAction('info', 'gopro'));
            exit();
        }

        $station = \Ode\DBO::getInstance()->query("
					SELECT a.*
					FROM " . \Ode\DBO\SWStation::TABLE_NAME . " AS a
					WHERE a.id = " . \Ode\DBO::getInstance()->quote(trim($_GET['id']), \PDO::PARAM_STR) . "
					LIMIT 0,1
				")->fetchObject(\Ode\DBO\SWStation::MODEL_NAME);

        \Ode\View::getInstance()->assign("station", $station);
        break;
}
