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
    global $input_filename, $args;
    $status_str = ($status)?" OK ":"ERRO";
    $status_clr = ($status)?"1;34":"1;31";
    if(key_exists('local', $args)) {
        $cnt_address = end(array_keys($args['local']))+1;
        $err_address = count($args['local']);    
        $msg_clr = ($status)?"1;34":"1;31";
        $msg = "(\033[{$msg_clr}m$err_address\033[0m/\033[1;34m$cnt_address\033[0m)";
    } else {
        $msg = "file?";
    }
    echo "\t[\033[{$status_clr}m$status_str\033[0m] $msg $input_filename\n";
    exit(0);
}

$input_filename = $argv[1];
$pure_text = file_get_contents($input_filename);
$text = mb_convert_encoding($pure_text,"UTF-8");
$output_filename= preg_replace("/([\w_]+)\.(\w+)/i", '${1}.json', $input_filename);
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
        $args['local'] = array();
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
                $endereco = preg_replace("/\s-/", ",", $endereco).", São Paulo";    
                $args['local'][$i] = array();
                $args['local'][$i]['endereco'] = $endereco;
                $args['local'][$i]['telefone'] = $li_matches[2];      
                $args['local'][$i]['localInfo'] = clear($li_matches[3]);      
                if(preg_match("/<b>Quando<\/b>(.+?)\/table>/s",$serviceInfo,$qnd_matches)) {
                    $funcionamento = clear($qnd_matches[1]);
                    $args['local'][$i]['funcionamento'] = $funcionamento;
                }
                $json = Geocoder::getJSON($endereco);
                if($json) {
                    $api_info = json_decode($json)->{'results'}[0];
                    $location = $api_info->{'geometry'}->{'location'};
                    $args['local'][$i]['latlon'] = array($location->{"lat"},$location->{"lng"});  
                } else {
                    unset($args['local'][$i]);
                    $ok = false;    
                }                
            }
        }
    }   
    $args['link'] = $argv[2];
    
    $json_options = JSON_UNESCAPED_UNICODE 
            + JSON_UNESCAPED_SLASHES
            + JSON_PRETTY_PRINT;
    $json = json_encode($args,$json_options);
    file_put_contents($output_filename, $json);
    finish($ok);
}
finish(false);

