<?php
switch (\Ode\Manager::getInstance()->getMode()) {
    default:
        break;
    case 'links':
        $links = new ArrayObject();

        $results = \Ode\DBO::getInstance()->query("
          SELECT a.*
          FROM sites AS a
          ORDER BY a.title ASC
")->fetchAll(PDO::FETCH_OBJ);

        foreach ($results as $link) {
            $links->append($link);
        }

        \Ode\View::getInstance()->assign("links", $links->getIterator());
        break;
    case 'propagation':

        break;
}