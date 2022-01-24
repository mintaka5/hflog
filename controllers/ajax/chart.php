<?php
require_once './init.php';

switch(\Ode\Manager::getInstance()->getMode()) {
    default:
        switch (\Ode\Manager::getInstance()->getTask()) {
            default:
                $farBack = (isset($_POST['spanStr'])) ? strtoupper($_POST['spanStr']) : "1 WEEK";
			
                $sql = "SELECT a.*
                        FROM propagation AS a
                        WHERE a.reported BETWEEN DATE_SUB(UTC_TIMESTAMP(), INTERVAL " . $farBack . ") AND UTC_TIMESTAMP()
                        ORDER BY a.reported
                        ASC";
                
                break;
        }
        break;
}
?>
