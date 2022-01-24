<?php
namespace Ode;
use Ode\Geo\Util;

/**
 * Manages page requests throughout the site application
 * 
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 * @package Ode
 * @name Manager
 *
 */
class Manager {
	/**
	 * @var Manager
	 * @access private
	 */
	private static $instance = null;
	
	/**
	 * @var string
	 * @access private
	 */
	private $page = "index";
	
	/**
	 * @var string
	 * @access private
	 */
	private $mode = false;
	
	/**
	 * @var string
	 * @access private
	 */
	private $task = false;
	
	/**
	 * 
	 */
	private $_page_title = false;
	
	/**
	 * 
	 */
	private $_full_url = false;
	
	/**
	 * 
	 * @var string
	 * @access public
	 */
	const VAR_PAGE = "_page";
	
	/**
	 * @var string
	 * @access public
	 */
	const VAR_MODE = "_mode";
	
	/**
	 * @var string
	 * @access public
	 */
	const VAR_TASK = "_task";
	
	/**
	 * Constructor
	 * 
	 * @access public
	 * @return void
	 */
	
	private $_uri = false;
	
	public function __construct() {
            $this->setPage();
            $this->setMode();
            $this->setTask();

            self::$instance = $this;

            //Ode_Log::getInstance()->log("Done initializing Manager.", PEAR_LOG_INFO);
	}
	
	/**
	 * Sets the current page name if it is available
	 * 
	 * @param string $page
	 * @access private
	 * @return void
	 */
	private function setPage($page = null) {
            if(isset($_REQUEST[self::VAR_PAGE])) {
                $this->page = trim($_REQUEST[self::VAR_PAGE]);
            }
	}
	
	/**
	 * Retrieves the current page name.
	 * 
	 * @access public
	 * @return string
	 */
	public function getPage() {
            return $this->page;
	}
	
	/**
	 * Sets current page mode if it is available
	 * 
	 * @param string $mode
	 * @access private
	 * @return void
	 */
	private function setMode($mode = null) {
            if(isset($_REQUEST[self::VAR_MODE])) {
                $this->mode = trim($_REQUEST[self::VAR_MODE]);
            }
	}
	
	/**
	 * Retrieves current page mode
	 * 
	 * @access public
	 * @return string
	 */
	public function getMode() {
            return $this->mode;
	}
	
	/**
	 * Sets the current mode's task if it is available
	 * 
	 * @param string $task
	 * @access private
	 * @return void
	 */
	private function setTask($task = null) {
            if(isset($_REQUEST[self::VAR_TASK])) {
                $this->task = trim($_REQUEST[self::VAR_TASK]);
            }
	}
	
	/**
	 * Retrieves the current mode's task if it is available
	 * 
	 * @access public
	 * @return string
	 */
	public function getTask() {
            return $this->task;
	}
	
	public function isTask($task = false) {
            if($this->getTask() == $task) {
                return true;
            }

            return false;
	}
	
	public function isPage($page = false) {
            if($this->getPage() == $page) {
                return true;
            }

            return false;
	}
	
	/**
	 * Retrieves an instance of Ode_Manager
	 * 
	 * @access public
	 * @return Ode_Manager
	 */
	public static function getInstance() {
		return self::$instance;
	}
	
	public function action($page, $mode= null, $task = null) {
            $url = new \Net_URL2($this->getURI());
            $url->setQueryVariable(self::VAR_PAGE, $page);

            if(!is_null($mode)) {
                $url->setQueryVariable(self::VAR_MODE, $mode);
            }

            if(!is_null($mode) && !is_null($task)) {
                $url->setQueryVariable(self::VAR_TASK, $task);
            }

            /**
             * If the length of function arguments is more than 3
             * then we have additional query parameters to add
             * to the URL
             */
            $args = func_get_args();
            $numArgs = count($args);
            if($numArgs > 3) {
                for($i=3; $i<$numArgs; $i++) {
                    $ary = $args[$i];

                    /**
                     * If the length of the argument array is less
                     * then 2, we fail to acquire a valid parameter set
                     */
                    if(count($ary) == 2) {
                            $url->setQueryVariable($ary[0], $ary[1]);
                    }
                }
            }

            return $url->getURL();
	}
	
	public function friendlyAction($page, $mode = null, $task = null) {
		$url = new \Net_URL2("");
		
		$path = $this->getURI() . $page;
		
		if(!empty($mode)) {
			$path .= "/" . $mode;
		}
		
		if(!empty($task) && !empty($mode)) {
			$path .= "/" . $task;
		}
		
		$url->setPath($path);
		
		$args = func_get_args();
		$numArgs = count($args);
		if($numArgs > 3) {
			for($i=3; $i<$numArgs; $i++) {
				$ary = $args[$i];
				
				if(count($ary) == 2) {
					$url->setQueryVariable($ary[0], $ary[1]);
				}
			}
		}
		
		return $url->getURL();
	}

	/**
	 * Reverse a raw site url to friendly action url
	 * @param string $url
	 */
	public function maskAction($url) {
		$newurl = new \Net_URL2('');

		$parsed = parse_url($url);
		$queryStr = $parsed['query'];

		$properties = explode('&', $queryStr);
		$nons = array();
		$managed = array();
		foreach($properties as $property) {
			$item = explode('=', preg_replace("/(amp;|&)/", "", $property), 2);

			$excluded = array(self::VAR_MODE, self::VAR_TASK, self::VAR_PAGE);
			if(!in_array($item[0], $excluded)) {
				$nons[] = $item;
			} else {
				switch ($item[0]) {
					case self::VAR_PAGE:
						$managed[self::VAR_PAGE] = $item[1];
						break;
					case self::VAR_TASK:
						$managed[self::VAR_TASK] = $item[1];
						break;
					case self::VAR_MODE:
						$managed[self::VAR_MODE] = $item[1];
						break;
				}
			}
		}

		$path = $this->getURI() . $managed[self::VAR_PAGE];

		if(!empty($managed[self::VAR_MODE])) {
			$path .= "/" . $managed[self::VAR_MODE];
		}

		if(!empty($managed[self::VAR_TASK]) && !empty($managed[self::VAR_MODE])) {
			$path .= "/" . $managed[self::VAR_TASK];
		}

		$newurl->setPath($path);

		foreach($nons as $non) {
			$newurl->setQueryVariable($non[0], $non[1]);
		}

		return $newurl->getURL();
	}
	
	public function setURI($uri) {
		$this->_uri = $uri;
	}
	
	public function getURI() {
		return $this->_uri;
	}
	
	public function isMode($mode = false) {
		if($this->getMode() == $mode) {
			return true;
		}
		
		return false;
	}
	
	public function setTitle($title) {
		$this->_page_title = $title;
	}
	
	public function appendTitle($title, $sep = "::") {
		$this->_page_title .= " " . $sep . " " . $title;
	}
	
	public function getTitle() {
		return $this->_page_title;
	}
	
	public function setFullURL($url) {
		$this->_full_url = $url;
	}
	
	public function getFullURL() {
		return $this->_full_url;
	}
	
	public function fullFriendlyAction($page, $mode = null, $task = null) {
		$path = $this->friendlyAction($page, $mode, $task);
		$path = substr($path, 1);
		
		return $this->getFullURL() . $path;
	}
	
	public function isMobile() {
		require_once 'Mobile_Detect.php';
		
		$detect = new \Mobile_Detect();
		
		/**
		 * for testing in browser; strictly debugging
		 */
		if(isset($_REQUEST['mobile'])) {
			return true;
		}
		
		return $detect->isMobile();
	}

    public function __toString() {
        return json_encode(array(
            self::VAR_PAGE => $this->getPage(),
            self::VAR_MODE => $this->getMode(),
            self::VAR_TASK => $this->getTask()
        ));
    }
}