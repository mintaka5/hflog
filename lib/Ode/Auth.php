<?php
namespace Ode;
use Ode\DBO\User;

/**
 *
 * Generic class for managing site authorization
 * @author walshcj
 * @copyright Christopher Walsh 2011
 * @package Ode
 * @name Auth
 * @license This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class Auth
{
    const COOKIE_ID_NAME = "_hfid";

    private $sessName = "_user";

    const SESSION_ACTIVITY_NAME = '_activity';
    const SESSION_ACTIVITY_TIMEOUT = 3600;
    const SESSION_TIMEOUT_NAME = '_timeout';

    private static $_instance = false;

    public function __construct()
    {
        self::$_instance = $this;
    }

    public static function getInstance()
    {
        return self::$_instance;
    }

    public function setSessName($v)
    {
        $this->sessName = $v;
    }

    public function getSessName()
    {
        return $this->sessName;
    }

    public function checkCookie()
    {
        if (!isset($_COOKIE[self::COOKIE_ID_NAME])) {
            $this->killSession();
        }
    }

    public function hasCookie()
    {
        if (isset($_COOKIE[self::COOKIE_ID_NAME])) {
            return true;
        }

        return false;
    }

    public function isAuth()
    {
        if (isset($_SESSION[$this->getSessName()])) {
            return true;
        }

        return false;
    }

    public function checkSessionTime()
    {
        $now = time();
        if ($now > $_SESSION[self::SESSION_TIMEOUT_NAME]) {
            $this->killSession();
        }
    }

    public function setSession($user_id)
    {
        $_SESSION[$this->getSessName()] = User::getOneById($user_id);
        $_SESSION[self::SESSION_ACTIVITY_NAME] = time();
        $_SESSION[self::SESSION_TIMEOUT_NAME] = ($_SESSION[self::SESSION_ACTIVITY_NAME] + self::SESSION_ACTIVITY_TIMEOUT);

        $public_key = User::getMeta()->getValue($_SESSION[$this->getSessName()]->id, User\Metadata::META_PUBLIC_KEY);
        $private_key = User::getMeta()->getValue($_SESSION[$this->getSessName()]->id, User\Metadata::META_PRIVATE_KEY);

        $enc = new Encrypt();

        if ($public_key == false || $private_key == false) {
            $enc->setPrivateKey();
            $enc->setPublicKey();

            User::getMeta()->add($_SESSION[$this->getSessName()]->id, User\Metadata::META_PUBLIC_KEY, $enc->getPublicKey(), true);
            User::getMeta()->add($_SESSION[$this->getSessName()]->id, User\Metadata::META_PRIVATE_KEY, $enc->getPrivateKey(), true);

            $public_key = User::getMeta()->getValue($_SESSION[$this->getSessName()]->id, User\Metadata::META_PUBLIC_KEY);
            $private_key = User::getMeta()->getValue($_SESSION[$this->getSessName()]->id, User\Metadata::META_PRIVATE_KEY);
        }

        $enc->setPublicKey($public_key);
        $enc->encrypt($_SESSION[$this->getSessName()]->id, $enc->getPublicKey());

        // set cookie
        setcookie(self::COOKIE_ID_NAME, $enc->getEncoded(), time() + 31536000, '/', APP_DOMAIN);
    }

    public function killSession()
    {
        unset($_COOKIE[self::COOKIE_ID_NAME]);
        setcookie(self::COOKIE_ID_NAME, 'deleted', (time() - 3600), '/', APP_DOMAIN);

        unset($_SESSION[$this->getSessName()]);
    }

    public function getSession()
    {
        if (!empty($_SESSION[$this->getSessName()])) {
            return $_SESSION[$this->getSessName()];
        }

        return false;
    }
}

?>