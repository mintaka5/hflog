<?php
namespace Ode\DBO\SWLocation;


use Ode\DBO\SWStation\Model as StationModel;
use Ode\Geo\LatLng;
use Ode\DBO\Language\Model as LanguageModel;

class Entity extends Model
{
    const TIME_FORMAT = 'H:i:s';

    public function __construct($id = null, StationModel $station, LatLng $coords = null, $azimuth = null, $site = null, \DateTime $startUtc = null, \DateTime $endUtc = null, $days = null, $frequency = null, $power = null, LanguageModel $language = null)
    {
        $this->id = $id;
        $this->station_id = $station->id;
        $this->lat = $coords->lat();
        $this->lng = $coords->lng();
        $this->azimuth = $azimuth;
        $this->site = $site;
        $this->start_utc = $startUtc->format(self::TIME_FORMAT);
        $this->end_utc = $endUtc->format(self::TIME_FORMAT);
        $this->days = $days;
        $this->frequency = $frequency;
        $this->power = $power;
        $this->lang_iso = $language->iso;
    }
}