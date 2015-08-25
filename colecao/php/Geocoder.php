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
    
    static public function getLatLng($endereco) {
        $json = Geocoder::getJSON($endereco);
        $results = json_decode($json)->{'results'};
        foreach($results as $result) {
            $location = $result->{'geometry'}->{'location'};
            $lat = $location->{"lat"};
            $lng = $location->{"lng"};
            if(self::equals($lat,-23.5505199) && self::equals($lng,-46.6333094)) {
                continue;
            }
            if($lng < -46.85 || $lng > -46.35) {
                continue;
            }
            if($lat < -23.75 || $lat > -23.45) {
                continue;
            }
            return array($lat,$lng);
        }
        return false;
    }
    
    static private function equals($a,$b,$e=0.000001) {
        return (($a + $e > $b) && ($a - $e < $b));
    }
}