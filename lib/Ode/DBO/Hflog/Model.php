<?php
namespace Ode\DBO\Hflog;

use Ode\DBO;
use Ode\DBO\User;
use Ode\Geo\LatLng;
use Ode\DBO\Hflog\SWLocation as LogLocation;
use Ode\DBO\Hflog\Status as LogStatus;
use Ode\Geo\Util;

class Model implements \JsonSerializable
{
    public $id;
    public $frequency;
    public $mode;
    public $description;
    public $time_on;
    public $time_off;
    public $lat;
    public $lng;
    public $user_id;
    public $submitted;

    public function __construct()
    {
        $this->description = stripslashes($this->description);
    }

    public function user()
    {
        return User::getOneById($this->user_id);
    }

    public function description()
    {
        $desc = stripslashes($this->description);
        $desc = strip_tags($desc);

        return $desc;
    }

    public function coordinates()
    {
        if (!empty($this->lat) || !empty($this->lng)) {
            return new LatLng((float)$this->lat, (float)$this->lng);
        }

        return false;
    }

    public function location()
    {
        return LogLocation::getOneByHflog($this->id);
    }

    public function hasLocation()
    {
        $loc = LogLocation::getOneByHflog($this->id);

        if ($loc != false) return true;

        return false;
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

    public function audio()
    {
        return DBO::getInstance()->query("
			SELECT " . DBO\SWAudio::COLUMNS . "
			FROM " . DBO\Hflog\Audio\Cnx::TABLE_NAME . " AS b
			LEFT JOIN " . DBO\SWAudio::TABLE_NAME . " AS a ON (a.id = b.audio_id)
			WHERE b.log_id = " . DBO::getInstance()->quote($this->id, \PDO::PARAM_STR) . "
		")->fetchAll(\PDO::FETCH_OBJ);
    }

    public function html()
    {
        $html = $this->frequency() . ' ' . $this->mode;
        $html .= '<br />';
        $html .= date('D. H:i', strtotime($this->time_on)) . ' UTC';

        return $html;
    }

    public function isUsers($userId)
    {
        if ($this->user_id === $userId) {
            return true;
        }

        return false;
    }

    public function status()
    {
        $s = Status::getOneByLog($this->id);

        if ($s instanceof DBO\Hflog\Status\Model) {
            return $s->status;
        }

        return false;
    }

    public function isBroadcasting(\DateTime $start, \DateTime $end) {
        $logDate = new \DateTime($this->submitted);

        if($logDate->getTimestamp() >= $start->getTimestamp() && $logDate->getTimestamp() <= $end->getTimestamp()) {
            return true;
        }

        return false;
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
            'frequency' => $this->frequency(),
            'mode' => $this->mode,
            'description' => utf8_encode($this->description()),
            'time_on' => $this->time_on,
            'time_off' => $this->time_off,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'user_id' => $this->user_id,
            'submitted' => $this->submitted,
            'location' => ($this->hasLocation()) ? $this->location()->location() : null,
            'html' => $this->html()
        );
    }
}
