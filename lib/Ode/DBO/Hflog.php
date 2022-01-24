<?php
namespace Ode\DBO;

use Ode\DBO;
use Ode\DBO\Hflog\Model as LogModel;
use Ode\DBO\Hflog\Status as LogStatus;

class Hflog
{
    const TABLE_NAME = 'hflogs';
    const MODEL_NAME = '\Ode\DBO\Hflog\Model';
    const COLUMNS = 'a.id, a.frequency, a.mode, a.description, a.time_on, a.time_off, a.lat, a.lng, a.user_id, a.submitted';

    /**
     * Grabs all logs of a normalized frequency.
     * If frequency is 10051.15 round down to 10051 whole
     *
     * @param float $frequency
     * @param bool|string $excludeSelf ID of log to exclude from query
     * @param float $delta range to search within
     * @return array :Ode_DBO_Hflog_Model
     */
    public static function getAllByFrequency($frequency, $excludeSelf = false, $delta = 0.5)
    {
        $sql = '
            SELECT ' . self::COLUMNS . '
            FROM ' . self::TABLE_NAME . ' AS a
            WHERE a.frequency BETWEEN (FLOOR(' . $frequency . ') + -' . $delta . ') AND (FLOOR(' . $frequency . ') + ' . $delta . ')';

        if ($excludeSelf !== false) {
            $sql .= " AND a.id != '" . $excludeSelf . "'";
        }

        $sql .= ' ORDER BY a.time_on
            DESC';

        return DBO::getInstance()->query($sql)->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    public static function getOneById($id)
    {
        return DBO::getInstance()->query('
                SELECT ' . self::COLUMNS . '
                FROM ' . self::TABLE_NAME . ' AS a
                WHERE a.id = ' . DBO::getInstance()->quote($id, \PDO::PARAM_STR) . '
                LIMIT 0,1
            ')->fetchObject(self::MODEL_NAME);
    }

    public static function toObject(LogModel $log)
    {
        if ($log instanceof LogModel) {
            $obj = new \stdClass();
            $obj->id = $log->id;
            $obj->frequency = $log->frequency();
            $obj->mode = $log->mode;
            $obj->description = $log->description();
            $obj->time_on = $log->time_on;
            $obj->time_on_iso8601 = date(\DateTime::ISO8601, strtotime($log->time_on));
            $obj->time_off = $log->time_off;
            $obj->time_off_iso8601 = (!empty($log->time_off)) ? date(\DateTime::ISO8601, strtotime($log->time_off)) : null;
            $obj->lat = $log->lat;
            $obj->lng = $log->lng;
            $obj->submitted = $log->submitted;
            $obj->html = $log->html();
            $obj->location = new \stdClass();
            if ($log->hasLocation()) {
                $obj->location->id = $log->location()->location()->id;
                $obj->location->lat = $log->location()->location()->lat;
                $obj->location->lng = $log->location()->location()->lng;
                $obj->location->site = $log->location()->location()->site;
                $obj->location->station = new \stdClass();
                $obj->location->station->id = $log->location()->location()->station()->id;
                $obj->location->station->title = $log->location()->location()->station()->title;
            }

            return $obj;
        }

        return false;
    }

    public static function toJSON($data)
    {
        if ($data instanceof LogModel) {
            return json_encode(self::toObject($data));
        }

        if (is_array($data)) {
            $a = array();

            foreach ($data as $log) {
                if ($log instanceof LogModel) {
                    $a[] = self::toObject($log);
                }
            }

            return json_encode($a);
        }
    }

    public static function getAllActive($limit = false)
    {
        $sql = "
                    SELECT " . self::COLUMNS . "
                    FROM " . self::TABLE_NAME . " AS a
                    WHERE a.id NOT IN (
                        SELECT hflog_id
                        FROM " . LogStatus::TABLE_NAME . "
                        WHERE status IN (
                            " . DBO::getInstance()->quote(LogStatus::STATUS_INACTIVE) . ",
                            " . DBO::getInstance()->quote(LogStatus::STATUS_NOT_APPROVED) . "
                        )
                    )
                    ORDER BY a.submitted
                    DESC
		";

        if ($limit !== false) {
            $sql .= " LIMIT 0," . intval($limit);
        }

        return DBO::getInstance()->query($sql)->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    public static function getRecent()
    {
        return DBO::getInstance()->query("SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.time_on BETWEEN DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 WEEK) AND UTC_TIMESTAMP() 
			AND a.id NOT IN (
			    SELECT hflog_id
                FROM " . LogStatus::TABLE_NAME . "
                WHERE status = " . DBO::getInstance()->quote(LogStatus::STATUS_INACTIVE) . "
			)
			ORDER BY a.submitted
			DESC")->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    public static function getWhatsOnNow()
    {
        $sql = "SELECT " . self::COLUMNS . "
				FROM " . self::TABLE_NAME . " AS a
				WHERE (HOUR(time_on) = HOUR(UTC_TIMESTAMP())
				AND DAYOFWEEK(time_on) = DAYOFWEEK(UTC_TIMESTAMP()))
				OR (HOUR(time_off) = HOUR(UTC_TIMESTAMP())
				AND DAYOFWEEK(time_off) = DAYOFWEEK(UTC_TIMESTAMP()))
				AND a.id NOT IN (
				    SELECT hflog_id
                    FROM " . LogStatus::TABLE_NAME . "
                    WHERE status IN (
                        " . DBO::getInstance()->quote(LogStatus::STATUS_INACTIVE) . ",
                        " . DBO::getInstance()->quote(LogStatus::STATUS_NOT_APPROVED) . "
                    )
				)";

        return DBO::getInstance()->query($sql)->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    public static function getWhatsOnAnyDay()
    {
        $sql = "SELECT " . self::COLUMNS . "
				FROM " . self::TABLE_NAME . " AS a
				WHERE HOUR(time_on) = HOUR(UTC_TIMESTAMP())
				OR HOUR(time_off) = HOUR(UTC_TIMESTAMP())
				AND a.id NOT IN (
				    SELECT hflog_id
                    FROM " . LogStatus::TABLE_NAME . "
                    WHERE status IN (
                        " . DBO::getInstance()->quote(LogStatus::STATUS_INACTIVE) . ",
                        " . DBO::getInstance()->quote(LogStatus::STATUS_NOT_APPROVED) . "
                    )
				)";

        return DBO::getInstance()->query($sql)->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    public static function getAllInactive($limit = false)
    {
        $sql = 'SELECT ' . self::COLUMNS . '
                FROM ' . self::TABLE_NAME . ' AS a
                WHERE a.id IN (
                    SELECT hflog_id
                    FROM ' . LogStatus::TABLE_NAME . '
                    WHERE status IN (
                        ' . DBO::getInstance()->quote(LogStatus::STATUS_INACTIVE) . '
                    )
                )
                ORDER BY a.submitted
                ASC';

        if ($limit !== false) {
            $sql .= ' LIMIT 0,' . intval($limit);
        }

        return DBO::getInstance()->query($sql)->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    public static function approve($id)
    {
        $hasStatus = LogStatus::hasStatus($id);

        if ($hasStatus) {
            LogStatus::delete($id);
        }
    }

    public static function unapprove($id)
    {
        LogStatus::setStatus($id, LogStatus::STATUS_NOT_APPROVED);
    }

    public static function delete($id)
    {
        $sth = DBO::getInstance()->prepare("
                    DELETE FROM " . self::TABLE_NAME . "
                    WHERE id = :id
                ");
        $sth->bindValue(":id", $id, \PDO::PARAM_STR);

        try {
            $sth->execute();
        } catch (\Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public static function getAllNotApproved()
    {
        $sql = 'SELECT ' . self::COLUMNS . '
                FROM ' . self::TABLE_NAME . ' AS a
                WHERE a.id IN (
                    SELECT hflog_id
                    FROM ' . LogStatus::TABLE_NAME . '
                    WHERE status = ' . DBO::getInstance()->quote(LogStatus::STATUS_NOT_APPROVED, \PDO::PARAM_STR) . '
                )
                ORDER BY a.submitted
                ASC';

        return DBO::getInstance()->query($sql)->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    public static function getUserCount($userId)
    {
        return DBO::getInstance()->query('
            SELECT COUNT(a.id)
            FROM ' . self::TABLE_NAME . ' AS a
            WHERE a.user_id = ' . DBO::getInstance()->quote($userId, \PDO::PARAM_STR) . '
            AND a.id NOT IN (
                SELECT hflog_id
                FROM ' . LogStatus::TABLE_NAME . '
                WHERE status IN (
                    ' . DBO::getInstance()->quote(LogStatus::STATUS_INACTIVE, \PDO::PARAM_STR) . ',
                    ' . DBO::getInstance()->quote(LogStatus::STATUS_NOT_APPROVED, \PDO::PARAM_STR) . '
                )
            )
        ')->fetchColumn();
    }
}
