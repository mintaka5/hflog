<?php

/**
 * Miscellaneous utility functions
 *
 * @author cjwalsh
 * @copyright Christopher Walsh 2010
 * @package
 * @name Util
 *
 */
class Util
{
    /**
     * Debug output
     *
     * @param mixed $data
     * @param boolean $die kill after dumping
     * @param boolean $dump use var_dump or print_r
     * @access public
     */
    public static function debug($data, $die = false, $dump = true)
    {
        print "<pre>";
        if ($dump == true) {
            var_dump($data);
        } else {
            print_r($data);
        }
        print "</pre>";

        if ($die === true) {
            die();
        }
    }

    /**
     * @param mixed $data
     * @param bool|false $text whether or not to output binary or plain text
     */
    public static function json($data, $text = false)
    {
        $ary = array("status" => "ok", "result" => $data);

        if (!$data) {
            $ary = array("status" => "fail", "result" => array());
        }

        if ($text == false) {
            header("Content-Type: application/json");
        } else {
            header("Content-Type: text/json");
        }

        if (function_exists("json_encode")) {
            echo json_encode($ary);
        } else {
            $json = new Services_JSON();
            echo $json->encode($ary);
        }

        return;
    }

    /**
     * Replaces any parameter placeholders in a query with the value of that
     * parameter. Useful for debugging. Assumes anonymous parameters from
     * $params are are in the same order as specified in $query
     *
     * @param string $query The sql query with parameter placeholders
     * @param array $params The array of substitution parameters
     * @return string The interpolated query
     */
    public static function interpolateQuery($query, $params)
    {
        $keys = array();

        // build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:' . $key . '/';
            } else {
                $keys[] = '/[?]/';
            }
        }

        $query = preg_replace($keys, $params, $query, 1, $count);

        //trigger_error('replaced '.$count.' keys');

        return $query;
    }

    public static function triMonthToNum($dateStr)
    {
        $months['jan'] = 1;
        $months['feb'] = 2;
        $months['mar'] = 3;
        $months['apr'] = 4;
        $months['may'] = 5;
        $months['jun'] = 6;
        $months['jul'] = 7;
        $months['aug'] = 8;
        $months['sep'] = 9;
        $months['oct'] = 10;
        $months['nov'] = 11;
        $months['dec'] = 12;

        $dateStr = strtolower($dateStr);

        return $months[$dateStr];
    }

    public static function randomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public static function getClean($get)
    {
        if (!is_array($get)) {
            $get = htmlentities($get, ENT_QUOTES, 'UTF-8');
        } else {
            foreach ($get as $key => $val) {
                $get[$key] = self::getClean($val);
            }
        }

        return $get;
    }

    public static function throwUnauthorized($msg = 'You are not authorized.')
    {
        header('HTTP/1.0 401 Unauthorized');
        exit($msg);
    }

    public static function camelCase($str, array $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }
}