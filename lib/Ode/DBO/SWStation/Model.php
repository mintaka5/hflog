<?php
namespace Ode\DBO\SWStation;

use Ode\DBO;
use Ode\DBO\SWLocation;
use Ode\DBO\Hflog\SWLocation as LogLocation;

class Model implements \JsonSerializable
{
    public $id;
    public $station_name;
    public $title;
    public $is_active;
    public $modified;

    public function locations()
    {
        return SWLocation::getAllByStation($this->id);
    }

    public function numLogs()
    {
        return DBO::getInstance()->query("
			SELECT COUNT(*)
			FROM " . DBO\Hflog::TABLE_NAME . " AS a
			LEFT JOIN " . LogLocation::TABLE_NAME . " AS b ON (b.hflog_id = a.id)
			LEFT JOIN " . SWLocation::TABLE_NAME . " AS c ON (c.id = b.sw_loc_id)
			LEFT JOIN " . DBO\SWStation::TABLE_NAME . " AS d ON (d.id = c.station_id)
			WHERE d.id = " . DBO::getInstance()->quote($this->id, \PDO::PARAM_STR) . "
		")->fetchColumn();
    }

    public function audio()
    {
        return DBO::getInstance()->query("
			SELECT a.*
			FROM sw_station_audio_cnx AS b
			LEFT JOIN sw_audio AS a ON (a.id = b.audio_id)
			WHERE b.station_id = " . DBO::getInstance()->quote($this->id, \PDO::PARAM_STR) . "
		")->fetchAll(\PDO::FETCH_OBJ);
    }

    public function hasAudio()
    {
        $audio = $this->audio();

        if (!empty($audio)) {
            return true;
        }

        return false;
    }

    public function title() {
        $title = strip_tags($this->title);
        $title = stripslashes($title);

        return $title;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'title' => utf8_encode($this->title()),
            'station_name' => $this->station_name,
            'is_active' => $this->is_active,
            'modified' => $this->modified,
            'locations' => $this->locations(),
            'num_logs' => $this->numLogs(),
            'num_locations' => count($this->locations())
        );
    }
}