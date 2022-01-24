<?php
namespace Ode\Utils;

class Time {
    /**
     * @param string $time required format '00:00:00'
     * @return \DateTime
     */
    public static function dateTimeFromTime($time, $timezone = 'UTC') {
        if(!preg_match("/^\d{2}:\d{2}:\d{2}$/", $time)) {
            throw new \Exception('Invalid time string was provided. ' . strval($time), E_ERROR);
        }

        $dt = new \DateTime('now', new \DateTimeZone($timezone));
        $dt->setTime(substr($time, 0, 2), substr($time, 4, 2), substr($time, 6));

        return $dt;
    }
}