<?php

class Index {
    const ROOT = "../restaurantes";
    const INDEX_FOLDER = "../restaurantes/json";
    const INDEX_PATH = "../restaurantes/index";
    
    static function exists() {
        return is_dir(self::INDEX_PATH);
    }
    static function create() {
        umask(0);
        mkdir(self::INDEX_PATH, 0777, true);
        shell_exec('cd .. ; ./criaColecao.sh; ./indexaColecao.sh');
    }
    static function search($query) {
        $resp =	shell_exec("cd .. ; ./buscaColecao.sh '$query'");
	$filenames = explode("\n", $resp);
        return $filenames;
    }
}