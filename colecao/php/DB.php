<?php

class DB {
    
    private $BANCO_DE_DADOS;
    private $USUARIO;
    private $SENHA;
    private $HOST;
    private $conexao;
    
    public function __construct() {
        $this->BANCO_DE_DADOS = "looking_for";
        $this->USUARIO = "root";
        $this->SENHA = "MY5PhWDfDkekQ4GtAeCJRuHfv2Z7qcuc";
        $this->HOST = "localhost";
        $this->conectar();
    }

    public function __destruct() {
        $this->desconectar();
    }

    private function conectar() {
        $this->conexao = mysql_connect($this->HOST, $this->USUARIO, $this->SENHA, true) or trigger_error(mysql_error(), E_USER_ERROR);
        mysql_select_db($this->BANCO_DE_DADOS, $this->conexao);
    }

    // Fecha a conexao com o banco
    private function desconectar() {
        if($this->conexao !== NULL) {
            mysql_close($this->conexao);
        }
    }
    
    public function executaQuery($sqlQuery, $ignorarErros = false) {
        //echo "$sqlQuery<br>";
        if ($ignorarErros) {
            $queryResult = mysql_query($sqlQuery, $this->conexao);
        } else {
            $queryResult = mysql_query($sqlQuery, $this->conexao) or trigger_error(mysql_error($this->conexao),E_USER_ERROR);
        }
        return $queryResult;
    }
    
    public function insereHistoricoDeBusca($texto, $params=NULL) {
        $columns = array("texto");
        $values = array("'$texto'");
        if(!empty($params) && is_array($params)) {
            $param_value = 0;
            foreach($params as $param) {
                $columns[] = $param;
                $values[] = ++$param_value;
            }
        }
        $columns = implode(",", $columns);
        $values = implode(",", $values);
        $query = <<<Q
                INSERT INTO buscas ($columns)
                VALUES ($values)
Q;
        return $this->executaQuery($query);
    }
    
    public function insereEscolha($posicao) {
        $query = <<<Q
                INSERT INTO escolhas (posicao)
                VALUES ($posicao)
Q;
        return $this->executaQuery($query);
    }
}
