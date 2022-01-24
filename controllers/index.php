<?php
switch (\Ode\Manager::getInstance()->getMode()) {
    case false:
    default:
        $topLoggers = \Ode\DBO\User::getTopLoggers();

        $topFreqs = \Ode\DBO::getInstance()->query("
			SELECT a.frequency, a.mode, COUNT(*) as numlogs
			FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
			GROUP BY a.frequency
			ORDER BY numlogs DESC
			LIMIT 0,5
		")->fetchAll(PDO::FETCH_OBJ);

        /**
         * get top 5 most recent logs
         */
        $recents = \Ode\DBO::getInstance()->query("
            SELECT " . \Ode\DBO\Hflog::COLUMNS . "
            FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
            WHERE a.id NOT IN (
                SELECT hflog_id
                FROM " . \Ode\DBO\Hflog\Status::TABLE_NAME . "
                WHERE status IN (
                    " . \Ode\DBO::getInstance()->quote(\Ode\DBO\Hflog\Status::STATUS_INACTIVE) . ",
                    " . \Ode\DBO::getInstance()->quote(\Ode\DBO\Hflog\Status::STATUS_NOT_APPROVED) . "
                )
            )
            ORDER BY a.submitted
            DESC
            LIMIT 0,5
        ")->fetchAll(PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);

        \Ode\View::getInstance()->assign('topFreqs', $topFreqs);
        \Ode\View::getInstance()->assign('recents', $recents);
        \Ode\View::getInstance()->assign('topLoggers', $topLoggers);
        break;
}
