<?php
class HflogManager {
	private static $instance;
	private $query = false;
	private $order = false;
	private $sort = 0;
	
	const queryVar = "q";
	const orderVar = "orderby";
	const sortVar = "sort";
	
	public function __construct() {
            $this->setQuery();
            $this->setOrder();
            $this->setSort();

            self::$instance = $this;
	}
	
	public function setQuery() {
            if(isset($_REQUEST[self::queryVar])) {
                if(!empty($_REQUEST[self::queryVar])) {
                    $this->query = trim($_REQUEST[self::queryVar]);
                }
            }
	}
	
	public function setOrder() {
		if(isset($_REQUEST[self::orderVar])) {
			if(!empty($_REQUEST[self::orderVar])) {
				$this->order = trim($_REQUEST[self::orderVar]);
			}
		}
	}
	
	public function setSort() {
            if(isset($_REQUEST[self::sortVar])) {
                if(!empty($_REQUEST[self::sortVar])) {
                    $this->sort = trim($_REQUEST[self::sortVar]);
                }
            }
	}
	
	public static function getInstance() {
            return self::$instance;
	}
	
	public function getSQL() {
            $sql = "SELECT " . \Ode\DBO\Hflog::COLUMNS . "
                    FROM " . \Ode\DBO\Hflog::TABLE_NAME . " AS a";


            if($this->query != false) {
                $wildcarded = "%" . preg_replace("/[\s\t\r\n]+/", "%", $this->query) . "%";

                $sql .= " LEFT JOIN hflog_sw_locations AS b ON (b.hflog_id = a.id)
                            LEFT JOIN sw_locations AS c ON (c.id = b.sw_loc_id)
                            LEFT JOIN sw_stations AS d ON (d.id = c.station_id)
                            WHERE a.frequency = " . \Ode\DBO::getInstance()->quote($this->query, PDO::PARAM_INT) . "
                            OR a.description LIKE " . \Ode\DBO::getInstance()->quote($wildcarded, PDO::PARAM_STR) . "
                            OR c.site LIKE " . \Ode\DBO::getInstance()->quote($wildcarded, PDO::PARAM_STR) . "
                            OR c.frequency = " . \Ode\DBO::getInstance()->quote($this->query, PDO::PARAM_INT) . "
                            OR d.title LIKE " . \Ode\DBO::getInstance()->quote($wildcarded, PDO::PARAM_STR) . "";
            }

            if($this->order == false) {
                $sql .= " ORDER BY a.submitted";
            } else {
                $sql .= " ORDER BY a." . $this->order;
            }

            if(intval($this->sort) > 0) {
                $sql .= " ASC";
            } else {
                $sql .= " DESC";
            }

            return $sql;
	}
}
?>