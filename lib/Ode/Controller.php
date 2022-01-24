<?php
namespace Ode;

use Ode\Manager;
use Ode\View;
use Ode\Auth;
/**
 * Establishes and includes PHP code necessary for
 * supplying data model information to the view.
 *
 * @author cjwalsh
 * @copyright Christopher Walsh 2011
 * @package Ode
 * @name Controller
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
 * @todo Localize the path variable to the controller rather than the site variable for $filename in init()
 *
 */
class Controller
{
    /**
     *
     * @var \Ode\Controller
     * @access private
     */
    private static $instance = false;

    /**
     *
     * @var string
     * @access private
     */
    private $name = false;

    private $_view = false;

    private $_auth;

    private $_requireAuth = false;

    private $_fileCreation = false;

    /**
     *
     * @var string
     * @access public
     */
    const CONTROLLER_EXT = ".php";

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->setName(Manager::getInstance()->getPage());
        $this->setAuth();
        self::$instance = $this;

        //Ode_Log::getInstance()->log("Done initializing Controller.", PEAR_LOG_INFO);
    }

    /**
     * Retrieves an instance of Ode_Controller
     *
     * @return Ode_Controller
     * @access public
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    public function createFiles()
    {
        return $this->_fileCreation;
    }

    public function setFileCreation($bool = true)
    {
        $this->_fileCreation = $bool;
    }

    /**
     * Initialize the controller
     *
     * @access public
     * @return void
     */
    public function init(View $view)
    {
        $this->setView($view);
        $this->getView()->assign("auth", $this->getAuth());

        $filename = APP_CONTROLLER_PATH . DIRECTORY_SEPARATOR . $this->getName() . self::CONTROLLER_EXT;
        if (!$this->controllerExists($filename)) {
            if ($this->createFiles() == true) {
                File::writeLine($filename, '<?php', FILE_MODE_WRITE);
                File::writeLine($filename, "// placeholder for " . $this->getName() . self::CONTROLLER_EXT, FILE_MODE_APPEND);
                File::writeLine($filename, '\Ode\View::getInstance()->assign("controllerName", "' . $this->getName() . '");', FILE_MODE_APPEND);
                File::writeLine($filename, '?>', FILE_MODE_APPEND);
            } else {
                header("HTTP/1.0 404 Not Found");
                echo $this->getView()->fetch("404.tpl.php");
                exit();
            }
        }

        try {
            require_once $filename;
        } catch (\Exception $e) {
            error_log($e->getTraceAsString(), 0);
        }
    }

    private function setView(View $view)
    {
        $this->_view = $view;
    }

    public function getView()
    {
        return $this->_view;
    }

    /**
     * Checks to see if the controller file exists already
     *
     * @param string $filename
     * @return boolean
     * @access private
     */
    private function controllerExists($filename)
    {
        if (!file_exists($filename)) {
            return false;
        }

        return true;
    }

    /**
     * Sets the filename of the controller
     *
     * @param string $name
     * @return void
     * @access private
     */
    private function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Retrieves the filename of the controller
     *
     * @access private
     * @return string
     */
    private function getName()
    {
        return $this->name;
    }

    private function setAuth()
    {
        $this->_auth = Auth::getInstance();
    }

    public function getAuth()
    {
        return $this->_auth;
    }

    public function requireAuth($bool = true)
    {
        $this->_requireAuth = $bool;
    }

    public function pageRequiresAuth()
    {
        return $this->_requireAuth;
    }
}
