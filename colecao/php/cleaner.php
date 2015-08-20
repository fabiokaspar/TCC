<?php
header('Content-type: text/html; charset=utf-8');

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
function clear($str) {
    $str = strip_tags($str);
    $str = trim(preg_replace("/\s+/", " ", $str));
    return $str;
}
function finish($status) {
    global $input_filename, $totalFiles, $filesGenerated;
    $status_str = ($status)?" OK ":"ERRO";
    $status_clr = ($status)?"1;34":"1;31";
    if($totalFiles > 0) {
        $cnt_address = $totalFiles;
        $err_address = $filesGenerated;    
        $msg_clr = ($status)?"1;34":"1;31";
        $msg = "(\033[{$msg_clr}m$err_address\033[0m/\033[1;34m$cnt_address\033[0m)";
    } else {
        $msg = "file?";
    }
    echo "\t[\033[{$status_clr}m$status_str\033[0m] $msg $input_filename\n";
    exit(0);
}
function extractLatLng($results) {
    foreach($results as $result) {
        $location = $result->{'geometry'}->{'location'};
        $lat = $location->{"lat"};
        $lng = $location->{"lng"};
        if(equals($lat,-23.5505199) && equals($lng,-46.6333094)) {
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
    throw new Exception("Error.", 0, null);
}
function equals($a,$b,$e=0.000001) {
    return (($a + $e > $b) && ($a - $e < $b));
}

$totalFiles = 0;
$filesGenerated = 0;

$input_filename = $argv[1];
$pure_text = file_get_contents($input_filename);
$text = mb_convert_encoding($pure_text,"UTF-8");
$filenameWithoutExt = preg_replace("/([\w_]+)\.(\w+)/i", '${1}', $input_filename);
if(!$text) {
    finish(false);
}

$args = array();
$ok = true;
if(preg_match("/\<\!\-{2}TITLE\-{2}\>(.+?)\<\!\-{2}\/LOCATIONS\-{2}\>/s", $text, $matches_g)) {
    $text = $matches_g[1];
    if(preg_match('/<td id="eventName">([\w\s-_\.&\'"]+)<\/td>/u',$text,$matches)) {
        $args['nome'] = $matches[1];
    } else {
        finish(false);
    }
    $args['link'] = $argv[2];
    if(preg_match("/alt\=\"(\w+)\"/u",$text,$matches)) {
        $args['nota'] = $matches[1];
    }
    if(preg_match('/<td id="summary"([\s\w=\"])*>(.+?)<\/td>/s',$text,$sm_matches)) {
        $summary = $sm_matches[2];
        if(preg_match("/(.*?)<p>/",$summary,$matches)) {
            $args['descricao'] = $matches[1];
        }
        if(preg_match("/<b>Preço médio<\/b>:\s?(.+)<br\/?>/i",$summary,$matches)) {
            $args['preco'] = $matches[1];
        }
        if(preg_match("/<b>Tipo\s+de\s+cozinha:<\/b>\s*([^<]+)\s*<br>/i",$summary,$matches)) {
            $args['cozinha'] = $matches[1];
        }   
    }
    if(preg_match_all('/<div class="serviceInfo">(.+?)<\/div>/s',$text,$matches)) {
        $totalFiles = count($matches[1]);
        $filesGenerated = 0;
        foreach ($matches[1] as $i => $serviceInfo) {
            if(preg_match('/<td class="localInfo" colspan="2">\n(.+?)<\/td>/s',$serviceInfo,$si_matches)) {
                $localInfo = $si_matches[1];
                if(preg_match('/(.+?)Telefone:\s?(\d{4}\-\d{4})\.?<br\/?>(.*)/s',$localInfo,$li_matches)) {
                    $endereco = $li_matches[1];
                } else {
                    continue;
                }
                $endereco = trim($endereco);
                $endereco = trim($endereco,".");
                $endereco = preg_replace("/\s-/", ",", $endereco);    
                $endereco_banco = "$endereco, São Paulo, Brasil";
                $endereco_busca = preg_replace("/^([^,]+,[^,]+).+$/", '${1}', $endereco).", São Paulo, Brasil";    
                $args['local'] = array();
                $args['local']['endereco'] = $endereco_banco;
                $args['local']['telefone'] = $li_matches[2];      
                $args['local']['localInfo'] = clear($li_matches[3]);      
                if(preg_match("/<b>Quando<\/b>(.+?)\/table>/s",$serviceInfo,$qnd_matches)) {
                    $funcionamento = clear($qnd_matches[1]);
                    $args['local']['funcionamento'] = $funcionamento;
                }
                $json = Geocoder::getJSON($endereco_busca);
                try {
                    $api_info = json_decode($json)->{'results'};
                    $args['local']['latlon'] = extractLatLng($api_info);  
                    
                    $json_options = JSON_UNESCAPED_UNICODE 
                        + JSON_UNESCAPED_SLASHES
                        + JSON_PRETTY_PRINT;
                    $json_output = json_encode($args,$json_options);
                    $id = ($i>0)?"($i)":"";
                    $output_filename = "$filenameWithoutExt$id.json"; 
                    file_put_contents($output_filename, $json_output);  
                    
                    $filesGenerated++;
                } catch (Exception $e) {
                    $ok = false;    
                }       
                unset($args['local']);
            }
        }
    }   
    finish($ok);
}
finish(false);

