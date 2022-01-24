<?php
namespace Ode\DBO\SWStation;

class Entity extends Model {
    const MODIFIED_TIME_FORMAT = 'Y-m-d H:i:s';

    public function __construct($id = null, $station_name, $title, $is_active, \DateTime $modified)
    {
        $this->id = $id;
        $this->station_name = $station_name;
        $this->title = $title;
        $this->is_active = $is_active;
        $this->modified = $modified->format(self::MODIFIED_TIME_FORMAT);
    }
}