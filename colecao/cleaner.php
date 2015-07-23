<?php

$filename = $argv[1];
echo "$filename";

$text = file_get_contents($filename);
if(!$text) {
    echo " - ERRO\n";
    exit(0);
}
$args = array();


if(preg_match("/\<\!\-{2}TITLE\-{2}\>(.+?)\<\!\-{2}\/LOCATIONS\-{2}\>/s", $text, $matches_g)) {
    $text = $matches_g[1];
    if(preg_match("/alt\=\"(\w+)\"/s",$text,$matches)) {
        $args['nota'] = $matches[1];
    }
    if(preg_match_all("/\<td class\=\"localInfo\" colspan\=\"2\"\>\n(.+?)Telefone/s",$text,$matches)) {
        $args['endereco'] = $matches[1];
    }
    if(preg_match("/(R[$]\s?\d+\,\d{2})/",$text,$matches)) {
        $args['preco'] = $matches[1];
    }
    $args['link'] = $argv[2];
    
    $saida = fopen("final_".$filename,"w");
    foreach($args as $i => $e) {
        if(is_array($e)) {
            foreach($e as $j => $f) {
                fwrite($saida,"$i = $f\n");
            }
        } else {
            fwrite($saida,"$i = $e\n");
        }
    }
    $f1 = trim(strip_tags($text));
    $f2 = trim(strip_tags($f1),"\x0A");
    fwrite($saida,"\n".$f2);
    echo " - OK";
} else {
    echo " - ERRO";
}
echo "\n";
exit(0);

