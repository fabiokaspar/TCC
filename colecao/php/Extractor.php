<?php

require_once 'InvalidFileException.php';

class Extractor {
    private $path;
    private $text;
    
    public function __construct($path) {
        $this->path = $path;
        $this->extractText();
    }
    
    private function extractText() {
        $pure_text = file_get_contents($this->path);
        $text = mb_convert_encoding($pure_text,"UTF-8");
        if(empty($text)) {
            throw new InvalidFileException($this->path);
        }
        $text = $this->getInfo("/\<\!\-{2}TITLE\-{2}\>(.+?)\<\!\-{2}\/LOCATIONS\-{2}\>/s",$text);
        if(empty($text)) {
            throw new InvalidFileException($this->path);
            
        }
        $this->text = $text;
    }
    
    public function getText() {
        return $this->text;
    }
    
    public function getFilenameWithoutExtension() {
        return preg_replace("/([\w_]+)\.(\w+)/i", '${1}', $this->path);
    }
    
    public function getName() {
        return $this->getInfo('/<td id="eventName">([\w\s-_\.&\'"]+)<\/td>/u');
    }
    
    public function getGrade() {
        return $this->getInfo("/alt\=\"(\w+)\"/u");
    }
    
    public function getSummary() {
        $summary_text = $this->getInfo('/<td id="summary"[\s\w=\"]*>(.+?)<\/td>/s');
        $summary = array();
        $summary['descricao'] = $this->getInfo("/(.*?)<p>/",$summary_text);
        $summary['preco'] = $this->getInfo("/<b>Preço médio<\/b>:\s?(.+)<br\/?>/i",$summary_text);
        $summary['cozinha'] = $this->getInfo("/<b>Tipo\s+de\s+cozinha:<\/b>\s*([^<]+)\s*<br>/i",$summary_text);
        return $summary;
    }
    
    public function getLocalInfo() {
        $response = array();
        $serviceInfos = $this->getAllInfo('/<div class="serviceInfo">(.+?)<\/div>/s');    
        foreach($serviceInfos as $serviceInfo) {
            $local = array();
            $localInfo = $this->getInfo('/<td class="localInfo" colspan="2">\n(.+?)<\/td>/s',$serviceInfo);
            if($localInfo) {
                $infos = $this->getInfos('/(.+?)Telefone:\s?(\d{4}\-\d{4})\.?<br\/?>(.*)/s',$localInfo);
                $local['endereco'] = $infos[1];
                $local['telefone'] = $infos[2];      
                $local['info'] = $infos[3];      
                $local['funcionamento'] = $this->getInfo("/<b>Quando<\/b>(.+?)\/table>/s",$serviceInfo);
                $response[] = $local;
            }            
        }
        return $response;
    }
    
    private function getInfo($regex,$text=NULL) {
        $matches = $this->getInfos($regex,$text);
        return ($matches)?$matches[1]:false;
    }
    
    private function getInfos($regex,$text=NULL) {
        $text = (empty($text))?$this->text:$text;
        if(preg_match($regex, $text, $matches)) {
            return $matches;
        }
        return false;
    }
    
    private function getAllInfo($regex,$text=NULL) {
        $matches = $this->getAllInfos($regex,$text);
        return ($matches)?$matches[1]:false;
    }
    
    private function getAllInfos($regex,$text=NULL) {
        $text = (empty($text))?$this->text:$text;
        if(preg_match_all($regex, $text, $matches)) {
            return $matches;
        }
        return false;
    }
}