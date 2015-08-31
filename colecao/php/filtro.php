<?php
	/** 
	*	Obs.:
	*		É necessário adicionar permissão de escrita à pasta colecao
	*/

	include_once 'Index.php';

	$parametro = filter_input(INPUT_POST,"parametro");
	if (!$parametro) {
		$parametro = "nota";
	}
	$query = filter_input(INPUT_POST,"query");
	$lat = filter_input(INPUT_POST,"lat",FILTER_VALIDATE_FLOAT);
	$lng = filter_input(INPUT_POST,"lng",FILTER_VALIDATE_FLOAT);
        
        $geoCoordenates = array($lat,$lng);
	if (!Index::exists()) {
		Index::create();
	}
	$filenames = Index::search($query,$geoCoordenates);
	$dir = Index::INDEX_FOLDER;
	$restaurantes = array();
	
	// procura na lista filenames
	foreach($filenames as $infos) {
                $filename = $infos['json'];
		$path = "$dir/$filename";
		if(empty($path) || !file_exists($path)) {
			continue;
		}
		$content = json_decode(file_get_contents($path),TRUE);
                $content['distancia'] = array("texto"=>$infos['distance']."km","valor"=>$infos['distance']);
		$restaurantes[] = $content;
	}

	$pre_JSON = array("restaurantes"=>$restaurantes);
	$JSON = json_encode($pre_JSON);
	
	echo $JSON;
?>
