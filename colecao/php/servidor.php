<?php
	# É necessário adicionar permissão de escrita à pasta colecao
	$haveDir = is_dir("../restaurantes");

	if(!$haveDir){
		umask(0);
		mkdir('../restaurantes', 0777, true);
		shell_exec('cd .. ; ./criaColecao.sh; ./indexaColecao.sh');
	}

	if($_POST['parametro'] == "distancia"){
		$q = "endereco";
	}
	else if($_POST['parametro'] == "preco"){
		$q = "preco";
	}
	else{
		$q = "nota";
	}

	if($haveDir){
		$dir = dir("../restaurantes");
		$JSON = '{ "restaurantes" : [';

		$i = 0;
		while($arq = "../restaurantes/".$dir->read()){
			if(ereg("\.txt", $arq)){
				$fp = fopen($arq, 'r');
				if(!$fp){
					echo "ERRO ao abrir o arquivo ". $arq ."<br/>";
				} else{
					$JSON .= utf8_encode(fread($fp, filesize($arq)));
					fclose($fp);
				}
				$i++;
				if($i == 5) break;
				else $JSON .= ', ';
			}
		}
		$dir->close();
		$JSON .= ']}';
	
		echo $JSON;
	}

?>