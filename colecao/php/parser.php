<?php
header('Content-type: text/html; charset=utf-8');

require_once 'Geocoder.php';
require_once 'Extractor.php';
require_once 'InvalidFileException.php';
require_once 'InvalidInfoException.php';

set_error_handler('exceptions_error_handler');
function exceptions_error_handler($severity, $message, $filename, $lineno) {
    if (error_reporting() == 0) {
        return;
    }
    if (error_reporting() & $severity) {
        //throw new ErrorException($message, 0, $severity, $filename, $lineno);
        echo "\n\n\t\t ERROR in file $filename at line $lineno:\n\t\t\t\" $message \"\n\n";
    }
}
function clear($str) {
    $str1 = strip_tags($str);
    $str2 = trim(preg_replace("/\s+/", " ", $str1));
    return $str2;
}
function clearAddress($address) {
    $address1 = trim($address);
    $address2 = trim($address1,".");
    $address3 = preg_replace("/\s-/", ",", $address2);  
    return $address3;
}
function finish($status, $input_filename, $_msg=false) {
    global $totalFiles, $filesGenerated;
    $status_str = ($status)?" OK ":"ERRO";
    $status_clr = ($status)?"1;34":"1;31";
    if($totalFiles > 0) {
        $cnt_address = $totalFiles;
        $err_address = $filesGenerated;    
        $msg_clr = ($status)?"1;34":"1;31";
        $msg = "(\033[{$msg_clr}m$err_address\033[0m/\033[1;34m$cnt_address\033[0m)";
    } else {
        $msg = (empty($_msg))?"file?":$_msg;
    }
    echo "[\033[{$status_clr}m$status_str\033[0m] $msg $input_filename\n";
}


$json_options = JSON_UNESCAPED_UNICODE 
                + JSON_UNESCAPED_SLASHES
                + JSON_PRETTY_PRINT;

$dir_sources = $argv[1];
$dir_links = $argv[2];
$dir_json = $argv[3];

$totalFiles_g = 0;
$filesGenerated_g = 0;
$erros = 0;

$dir = new DirectoryIterator($dir_sources);
$basenames = array();
foreach ($dir as $fileinfo) {
    if ($fileinfo->isDot() || $fileinfo->isDir()) {
        continue;
    }
    $basenames[] = $fileinfo->getBasename(".html");
}
asort($basenames);
foreach ($basenames as $basename) {
    $filename = "$basename.html";
    $filepath = "$dir_sources/$filename";
    $totalFiles = 0;
    $filesGenerated = 0;
    $args = array();
    $ok = true;
    try {
        $extractor = new Extractor($filepath);
        $text = $extractor->getText();

        $args['nome'] = $extractor->getName();
        if(empty($args['nome'])) {
            finish(false,$filename);
            $erros++;
            continue;
        }
        $args['link'] = trim(file_get_contents("$dir_links/$basename.txt"));
        $args['nota'] = $extractor->getGrade();
        $summary = $extractor->getSummary();
        $args['descricao'] = $summary['descricao'];
        $args['preco'] = $summary['preco'];
        $args['cozinha'] = $summary['cozinha'];

        $locais = $extractor->getLocalInfo();
    } catch (Exception $e) {
        finish(false,$filename,$e);
        $erros++;
        continue;
    }
    if(!empty($locais)) {
        $totalFiles = count($locais);
        $filesGenerated = 0;
        foreach ($locais as $i => $localInfo) {
            $endereco = clearAddress($localInfo['endereco']);  
            $endereco_banco = "$endereco, São Paulo, Brasil";
            $endereco_busca = preg_replace("/^([^,]+,[^,]+).+$/", '${1}', $endereco).", São Paulo, Brasil";    

            $local = array();
            $local['endereco'] = $endereco_banco;
            $local['endereco_busca'] = $endereco_busca;
            $local['telefone'] = $localInfo['telefone'];      
            $local['localInfo'] = clear($localInfo['info']);      
            $local['funcionamento'] = clear($localInfo['funcionamento']);
            $latlon = Geocoder::getLatLng($endereco_busca);
            if($latlon) {
                $local['latlon'] = $latlon;  
                $json = array_merge($args,$local);
                $json_output = json_encode($json,$json_options);
                $id = ($i>0)?"($i)":"";
                file_put_contents("$dir_json/$basename$id.json", $json_output);  
                $filesGenerated++;
            } else {
                $ok = false;    
            }       
            unset($local);
        }
    }   
    $totalFiles_g += $totalFiles;
    $filesGenerated_g += $filesGenerated;
    $erros += ($totalFiles - $filesGenerated);
    finish($ok,$filename);
}
echo "\n\nArquivos gerados:$filesGenerated_g";
echo "\n\nArquivos que deveriam ser gerados:$totalFiles_g";
echo "\n\nTotal de erros:$erros";
echo "\n\n";
