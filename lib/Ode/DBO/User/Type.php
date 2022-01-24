<?php
namespace Ode\DBO\User;

use Ode\DBO;

class Type {
    const TABLE = 'user_types';
    const MODEL = '\Ode\DBO\User\Type\Model';
    const COLUMNS = 'a.id,a.type_name,a.title,a.is_active';
    
    public static function getAllActive($order = 'title', $sort = 'ASC') {
        return DBO::getInstance()->query("
            SELECT " . self::COLUMNS . "
            FROM " . self::TABLE . " AS a
            WHERE a.is_active = 1
            AND a.type_name != 'admin'
            ORDER BY a." . $order . "
            " . $sort . "
        ")->fetchAll(\PDO::FETCH_CLASS, self::MODEL);
    }
}