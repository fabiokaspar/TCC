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
        #echo "resp =>\n".$resp;
        $filenames = explode("\n", $resp);
        //echo " \n\nLista:\n".$resp;
        
        #$resp = array();
        $final = array();
        foreach ($filenames as $value) {
            if (ereg("[A-z\.\/]+\.json$", $value, $expr)) {
                $final[] = $expr[0];
                #echo ">".$expr[0]."\n";
            }
        }

        #return $filenames;
        return $final;
    }
}