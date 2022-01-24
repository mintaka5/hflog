<?php
namespace Ode\DBO\Hflog\Status;

use Ode\DBO;
use Ode\DBO\Hflog\Status as LogStatus;

class Model {
    public $id;
    public $hflog_id;
    public $status;

    public function isActive() {
        $status = DBO::getInstance()->query('
            SELECT ' . LogStatus::COLUMNS . '
            FROM ' . LogStatus::TABLE_NAME . ' AS a
            WHERE a.id = ' . $this->id . '
            LIMIT 0,1
        ')->fetchObject(LogStatus::MODEL_NAME);

        if(!empty($status)) {
            if($status->status === LogStatus::STATUS_ACTIVE) {
                return true;
            } else {
                return false;
            }
        }
        // return true if not in the table (backward compatbility
        // for existing logs
        return true;
    }
}