<?php
	# É necessário adicionar permissão de escrita à pasta colecao
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

	#echo "<b>Parâmetro relevante: " .$q. "</b><br><br>";
	#echo shell_exec('cd ..; ./buscaColecao.sh '. $q);

	if($haveDir){
		$dir = dir("../restaurantes");
		$array = array();

		$i = 0;
		while($arq = "../restaurantes/".$dir->read()){
			//echo "<a href='../restaurantes/".$arq."'>".$arq."</a><br/>";
			 //echo $arq. "<br/>";
			if(ereg("\.txt", $arq)){
				$fp = fopen($arq, 'r');
				if(!$fp){
					echo "ERRO ao abrir o arquivo ". $arq ."<br/>";
				} else{
					$conteudo = utf8_encode(fread($fp, filesize($arq)));
					$array[$arq] = $conteudo;
					//echo "<b>Arquivo = ".$arq."</b>:<br/><br/>".$conteudo;
					fclose($fp);
				}
				$i++;
				if($i == 10) break;
			}
		}
		$dir->close();
		foreach($array as $key => $value){
			echo "<b>Nome: ". $key .". Conteudo: </b><br><br>".$value."<br/><br/>";
		}
	}

?>