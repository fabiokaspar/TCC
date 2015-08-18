<?php
	# É necessário adicionar permissão de escrita à pasta colecao
	if ($_POST['parametro'] == "distancia") {
		$p = "endereco";
	}
	else if ($_POST['parametro'] == "preco") {
		$p = "preco";
	}
	else {
		$p = "nota";
	}
	$q = $_POST['query'];
	
	$q = '"'.$q.'"';
	
	$haveDir = is_dir("../restaurantes");

	if (!$haveDir) {
		umask(0);
		mkdir('../restaurantes', 0777, true);
		shell_exec('cd .. ; ./criaColecao.sh; ./indexaColecao.sh');
	}

	$resp =	shell_exec("cd .. ; ./buscaColecao.sh ".$q);
	//echo $resp;
	
	$array = explode("\n", $resp);
	$final = array();

	foreach ($array as $value) {	
		if (ereg("[A-z\.\/]+\.json$", $value, $expr)) { 
			//echo $expr[0]."\n";
			$final[] = $expr[0];
		}
	}
	$tam = count($final);

	$dir = dir("../restaurantes");
	$JSON = '{ "restaurantes" : [';
	
	while ($nome = $dir->read()) {
		if (ereg("[A-z\.\/]+\.json$", $nome)) {
			if (in_array("./restaurantes/".$nome, $final)) {
				$arquivo = "../restaurantes/".$nome;
				$content = utf8_encode(file_get_contents($arquivo));
				$JSON .= $content;
				$JSON .= ",\n\n";			
			}
		}
	}

	$JSON = rtrim($JSON, ",..\n");
	$JSON .= ']}';
	$dir->close();
	
	echo $JSON;
	//echo "latitude = ". $_POST['lat'];
	//echo "\nlongitude = ". $_POST['lng'];

?>
