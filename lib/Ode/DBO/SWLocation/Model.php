<?php
namespace Ode\DBO\SWLocation;

use Ode\DBO;
use Ode\DBO\Language;
use Ode\DBO\SWLocation;
use Ode\DBO\SWStation;
use Ode\Geo\LatLng;
use Ode\DBO\Hflog\SWLocation as LogLocation;
use Ode\Utils\Time;

class Model implements \JsonSerializable
{
    public $id;
    public $station_id;
    public $lat;
    public $lng;
    public $azimuth;
    public $site;
    public $start_utc;
    public $end_utc;
    public $days;
    public $frequency;
    public $power;
    public $lang_iso;
    public $is_active;
    public $modified;

    public function __construct()
    {}

    public function site() {
        $site = stripslashes($this->site);
        $site = strip_tags($site);

        return $site;
    }

    public function station()
    {
        return SWStation::getOneById($this->station_id);
    }

    public function coordinates()
    {
        if (!empty($this->lat) || !empty($this->lng)) {
            return new LatLng($this->lat, $this->lng);
        }

        return false;
    }

    public function times()
    {
        $str = "";

        if($this->hasStartTime()) {
            $start = Time::dateTimeFromTime($this->start_utc);
            $str .= $start->format('H:i');
        }

        if($this->hasEndTime()) {
            $end = Time::dateTimeFromTime($this->end_utc);
            $str .= ' to ';
            $str .= $end->format('H:i');
        }

        return $str;
    }

    private function hasStartTime() {
        try {
            Time::dateTimeFromTime($this->start_utc);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    private function hasEndTime() {
        try {
            Time::dateTimeFromTime($this->end_utc);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function language()
    {
        return Language::getOneByISO($this->lang_iso);
    }

    public function freq($default = "n/a")
    {
        return $this->frequency($default);
    }

    public function frequency($default = "n/a")
    {
        if ($this->frequency <= 0) {
            return $default;
        }

        return number_format($this->frequency, 2, ".", "");
    }

    public function numLogs()
    {
        $count = DBO::getInstance()->query('
            SELECT COUNT(*)
            FROM ' . DBO\Hflog::TABLE_NAME . ' AS a
            LEFT JOIN ' . LogLocation::TABLE_NAME . ' AS b ON (b.hflog_id = a.id)
            LEFT JOIN ' . SWLocation::TABLE_NAME . ' AS c ON (c.id = b.sw_loc_id)
            WHERE c.id = ' . DBO::getInstance()->quote($this->id, \PDO::PARAM_INT) . '
        ')->fetchColumn();

        return $count;
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
        return [
            'id' => $this->id,
            'azimuth' => $this->azimuth,
            'days' => $this->days,
            'times' => $this->times(),
            'start_utc' => $this->start_utc,
            'end_utc' => $this->end_utc,
            'frequency' => $this->frequency(),
            'is_active' => $this->is_active,
            'language' => $this->language(),
            'station_id' => $this->station_id,
            'station_title' => $this->station()->title,
            'coordinates' => $this->coordinates(),
            'site' => utf8_encode($this->site()),
            'num_logs' => $this->numLogs()
        ];
    }
}