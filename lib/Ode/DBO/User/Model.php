<?php
namespace Ode\DBO\User;

use Ode\DBO;
use Ode\Geo\LatLng;

class Model
{
    public $id;
    public $username;
    public $email;
    public $firstname;
    public $lastname;
    public $lat;
    public $lng;
    public $mi;
    public $is_deleted;
    public $created;
    public $modified;

    public function fullname()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function coords()
    {
        return new LatLng((float)$this->lat, (float)$this->lng);
    }

    public function type()
    {
        return DBO::getInstance()->query("
			SELECT " . Type::COLUMNS . "
			FROM " . DBO\User\Type\Cnx::TABLE . " AS cnx
			LEFT JOIN " . Type::TABLE . " AS a ON (a.id = cnx.type_id)
			WHERE cnx.user_id = " . DBO::getInstance()->quote($this->id, \PDO::PARAM_INT) . "
			LIMIT 0,1
		")->fetchObject(Type::MODEL);
    }

    public function typeTitle() {
        $type = $this->type();

        if(empty($type)) {
            return 'Guest';
        }

        return $type->title;
    }

    public function isAdmin()
    {
        if ($this->type()->type_name == 'admin') {
            return true;
        }

        return false;
    }

    public function irc()
    {
        return IRC::getOneByUser($this->id);
    }

    public function meta($key, $single = false) {
        return DBO\User::getMeta()->get($this->id, $key, $single);
    }

    public function numActiveLogs() {
        return DBO\Hflog::getUserCount($this->id);
    }

    public function lastLog() {
        return DBO\User::getLastLog($this->id);
    }
}