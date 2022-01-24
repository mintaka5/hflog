<?php
if (!\Ode\Auth::getInstance()->isLoggedIn()) {
    header("Location:" . \Ode\Manager::getInstance()->friendlyAction('auth', null, null, array('referer', \Ode\Manager::getInstance()->friendlyAction('user'))));
    exit();
}

switch (\Ode\Manager::getInstance()->getMode()) {
    default:
    case false:
        $user = \Ode\DBO::getInstance()->query("
                                        SELECT " . \Ode\DBO\User::COLUMNS . "
                                        FROM " . \Ode\DBO\User::TABLE_NAME . " AS a
                                        WHERE a.id = " . \Ode\DBO::getInstance()->quote(\Ode\Auth::getInstance()->getSession()->id, PDO::PARAM_STR) . "
                                        LIMIT 0,1
                                        ")->fetchObject(\Ode\DBO\User::MODEL_NAME);

        \Ode\View::getInstance()->assign("user", $user);
        break;
    case 'logs':
        $hflogs = \Ode\DBO::getInstance()->query("
                    SELECT " . \Ode\DBO\Hflog::COLUMNS . "
                    FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a
                    WHERE a.user_id = " . \Ode\DBO::getInstance()->quote(\Ode\Auth::getInstance()->getSession()->id, PDO::PARAM_STR) . "
                    ORDER BY a.submitted
                    DESC
                  ")->fetchAll(\PDO::FETCH_CLASS, \Ode\DBO\Hflog::MODEL_NAME);

        $pager = \Pager::factory(array(
            'mode' => "Jumping",
            'perPage' => 10,
            'delta' => 10,
            'append' => true,
            'urlVar' => 'page',
            'itemData' => $hflogs,
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
        break;
    case 'profile':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:

                break;
            case 'edit':
                $user = \Ode\Auth::getInstance()->getSession();

                $form = new \HtmlQuickForm2('userProfileForm');
                break;
        }
        break;
}