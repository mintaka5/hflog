<?php
namespace Ode\DBO;

use Ode\DBO;
use Ode\DBO\User\Metadata as UserMetadata;
use Ode\DBO\User\Metadata;
use Ode\Geo\Util;

class User
{
    const TABLE_NAME = 'users';
    const MODEL_NAME = '\Ode\DBO\User\Model';
    const COLUMNS = 'a.id, a.username, a.email, a.password, a.firstname, a.mi, a.lastname, a.lat, a.lng, a.is_deleted, a.created, a.modified';

    /**
     *
     * @param string $id
     * @return DBO\User\Model
     */
    public static function getOneById($id)
    {
        return DBO::getInstance()->query("
			SELECT " . self::COLUMNS . "
			FROM " . self::TABLE_NAME . " AS a
			WHERE a.id = " . DBO::getInstance()->quote($id, \PDO::PARAM_STR) . "
			LIMIT 0,1
		")->fetchObject(self::MODEL_NAME);
    }

    /**
     *
     * @return Metadata\Model
     */
    public static function getMeta()
    {
        $meta = new UserMetadata();

        return $meta;
    }

    /**
     * @param string $order
     * @param string $sort
     * @return DBO\User\Model[]
     */
    public static function getAll($order = 'username', $sort = 'ASC')
    {
        return DBO::getInstance()->query("
                SELECT " . self::COLUMNS . "
                FROM " . self::TABLE_NAME . " AS a
                ORDER BY a." . $order . "
                " . $sort . "
            ")->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    /**
     * @param $email
     * @return DBO\User\Model[]
     */
    public static function getOneByEmail($email)
    {
        $sql = "SELECT " . self::COLUMNS . "
                FROM " . self::TABLE_NAME . " AS a
                WHERE a.email = " . DBO::getInstance()->quote($email, \PDO::PARAM_STR) . "
                LIMIT 0,1";

        return DBO::getInstance()->query($sql)->fetchObject(self::MODEL_NAME);
    }

    /**
     * @param $email
     * @return bool
     */
    public static function emailExists($email)
    {
        $sql = "SELECT user.id
                FROM users AS user
                WHERE user.email = :email
                LIMIT 0,1";
        $sth = DBO::getInstance()->prepare($sql);
        $sth->bindValue(':email', $email, \PDO::PARAM_STR);

        try {
            $sth->execute();
            $result = $sth->fetchColumn();
            if ($result == false) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getTraceAsString(), 0);
        }

        return true;
    }

    /**
     * @param int $threshold
     * @param int $limit
     * @return DBO\User\Model[]
     */
    public static function getTopLoggers($threshold = 10, $limit = 5)
    {
        $sql = 'SELECT ' . self::COLUMNS . ', COUNT(b.id) AS logCount
                FROM ' . self::TABLE_NAME . ' AS a
                LEFT JOIN ' . Hflog::TABLE_NAME . ' AS b ON (b.user_id = a.id)
                WHERE a.is_deleted = 0
                AND a.id NOT IN (
                    SELECT hflog_id
                    FROM ' . DBO\Hflog\Status::TABLE_NAME . '
                    WHERE status IN (
                        ' . DBO::getInstance()->quote(DBO\Hflog\Status::STATUS_INACTIVE, \PDO::PARAM_STR) . ',
                        ' . DBO::getInstance()->quote(DBO\Hflog\Status::STATUS_NOT_APPROVED, \PDO::PARAM_STR) . '
                    )
                )
                GROUP BY a.id
                HAVING logCount > ' . $threshold . '
                ORDER BY logCount
                DESC
                LIMIT 0,' . $limit;

        return DBO::getInstance()->query($sql)->fetchAll(\PDO::FETCH_CLASS, self::MODEL_NAME);
    }

    public static function getLastLog($userId)
    {
        $sql = 'SELECT ' . Hflog::COLUMNS . '
                FROM ' . Hflog::TABLE_NAME . ' AS a
                WHERE a.user_id = ' . DBO::getInstance()->quote($userId, \PDO::PARAM_STR) . '
                AND a.id NOT IN (
                    SELECT hflog_id
                    FROM ' . DBO\Hflog\Status::TABLE_NAME . '
                    WHERE status IN (
                        ' . DBO::getInstance()->quote(DBO\Hflog\Status::STATUS_INACTIVE) . ',
                        ' . DBO::getInstance()->quote(DBO\Hflog\Status::STATUS_NOT_APPROVED) . '
                    )
                )
                ORDER BY a.submitted
                DESC
                LIMIT 0,1';

        return DBO::getInstance()->query($sql)->fetchObject(Hflog::MODEL_NAME);
    }
}
