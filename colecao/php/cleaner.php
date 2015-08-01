<?php

include_once 'Geocoder.php';

set_error_handler('exceptions_error_handler');
function exceptions_error_handler($severity, $message, $filename, $lineno) {
  if (error_reporting() == 0) {
    return;
  }
  if (error_reporting() & $severity) {
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
  }
}

$filename = $argv[1];
echo "$filename";

$text = utf8_encode(file_get_contents($filename));
if(!$text) {
    echo " - ERRO\n";
    exit(0);
}
$args = array();


if(preg_match("/\<\!\-{2}TITLE\-{2}\>(.+?)\<\!\-{2}\/LOCATIONS\-{2}\>/s", $text, $matches_g)) {
    $text = $matches_g[1];
    if(preg_match("/\<td id\=\"eventName\"\>([\\p{L}\\s]+)\<\/td\>/u",$text,$matches)) {
        $args['nome'] = $matches[1];
    }
    if(preg_match("/alt\=\"(\w+)\"/s",$text,$matches)) {
        $args['nota'] = $matches[1];
    }
    if(preg_match_all("/\<td class\=\"localInfo\" colspan\=\"2\"\>\n(.+?)Telefone:\s?(\d{4}\-\d{4})/s",$text,$matches)) {
        $enderecos = array_map(function($v) {
            return ereg_replace("-", ",", $v).", São Paulo";
        },$matches[1]);
        $args['local'] = array();
        foreach($enderecos as $i => $endereco) {
            $args['local'][$i] = array('endereco'=>$endereco);
            try {
                echo "\n$endereco";
//                $json = Geocoder::getJSON($endereco);
//                $api_info = json_decode($json)->{'results'}[0];
//                $location = $api_info->{'geometry'}->{'location'};
//                $args['local'][$i]['latlon'] = array($location->{"lat"},$location->{"lng"});
                $args['local'][$i]['telefone'] = $matches[2][$i];
            } catch (Exception $e) {
                echo " - ERRO DE API\n";
                unset($args['local'][$i]);
            }
        }
    }
    if(preg_match("/(R[$]\s?\d+\,\d{2})/",$text,$matches)) {
        $args['preco'] = $matches[1];
    }
    $args['link'] = $argv[2];
    
    $f1 = trim(strip_tags($text));
    $f2 = trim($f1,"\x0A\n");
    $args['conteudo'] = $f2;

    $saida = fopen($filename,"w");
    $json = json_encode($args);
    fwrite($saida,$json);
    echo " - OK";
} else {
    echo " - ERRO";
}
echo "\n";
exit(0);

