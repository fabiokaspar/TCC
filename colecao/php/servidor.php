<?php
	# É necessário adicionar permissão de escrita à pasta colecao
 
	$path = '../../../lucene-5.1.0/core/lucene-core-5.1.0.jar';
	$path .= ':../../../lucene-5.1.0/queryparser/lucene-queryparser-5.1.0.jar';
	$path .= ':../../../lucene-5.1.0/demo/lucene-demo-5.1.0.jar';
	$path .= ':../../../lucene-5.1.0/analysis/common/lucene-analyzers-common-5.1.0.jar';

	$haveDir = is_dir("../restaurantes");

	if(!$haveDir){
		umask(0);
		mkdir('../restaurantes', 0777, true);
		shell_exec('cd .. ; ./criaColecao.sh; ./indexaColecao.sh');
	}
	else{
		//echo "Já existe a pasta restaurantes!<br>";
	}

	//echo "<br>parametro => ". $_POST["parametro"];
	//echo "<br>query => ". $_POST["query"];
	if($_POST['parametro'] == "distancia"){
		$q = "endereco";
	}
	else if($_POST['parametro'] == "preco"){
		$q = "preco";
	}
	else{
		$q = "nota";
	}

	echo "<b>Parametro relevante: " .$q. "</b><br><br>";
	echo shell_exec('java -cp '. $path.' org.apache.lucene.demo.SearchFiles -paging 50 -index ../restaurantes/index -query '. $q);	
?>