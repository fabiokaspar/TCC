<?php

class Geocoder {
    
    const KEY = "AIzaSyC0RkcZShUjR9HW8rcuYuu-kltiFxe0VUA";
    const SENSOR = "false";
    const URL = "https://maps.googleapis.com/maps/api/geocode/json";
    
    static public function getJSON($endereco) {
        $opcoes=array();
        $opcoes['address'] = ereg_replace(" ", "+", $endereco);
        $opcoes['key'] = self::KEY;
        $opcoes['sensor'] = self::SENSOR;
        $opcoes2 = array_map(function ($v, $k) { return $k . '=' . $v; }, $opcoes, array_keys($opcoes));
        $url = self::URL."?".implode("&",$opcoes2);
        $json = file_get_contents($url);
        return $json;
    }
    
}