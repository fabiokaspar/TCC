<?php

include_once 'DB.php';

$posicao = filter_input(INPUT_POST, "posicao", FILTER_VALIDATE_INT);

if($posicao < 0) {
    exit;
}
$bd = new DB();
$bd->insereEscolha($posicao);

