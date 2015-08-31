<?php

class Index {
    const ROOT = "/var/www/TCC/colecao/restaurantes";
    const INDEX_FOLDER = "/var/www/TCC/colecao/restaurantes/json";
    const INDEX_PATH = "/var/www/TCC/colecao/restaurantes/index";
    
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

        $resposta = array();
        foreach ($filenames as $value) {
            if (preg_match("/[A-z\.\/]+\.json$/", $value,$expr)) {
                $resposta[] = $expr[0];
            }
        }
        return $resposta;
    }
}