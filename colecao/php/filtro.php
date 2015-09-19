<?php
	/** 
	*	Obs.:
	*		É necessário adicionar permissão de escrita à pasta colecao
	*/

	include_once 'Index.php';

        function str_to_parameter($str) {
            switch ($str) {
                case "endereco":
                    return Index::DISTANCE_PARAM;
                case "preco":
                    return Index::PRICE_PARAM;
                case "nota":
                    return Index::GRADE_PARAM;
                default:
                    return false;
            }
        }
	$_parametros = filter_input(INPUT_POST,"parametro",FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
        
        $parametros = array();
	if (!empty($_parametros)) {
            foreach($_parametros as $_parametro) {
                $parametro = str_to_parameter($_parametro);
                if($parametro) {
                    $parametros[] = $parametro;
                }
            }
	}
        
	$query = filter_input(INPUT_POST,"query");
	$lat = filter_input(INPUT_POST,"lat",FILTER_VALIDATE_FLOAT);
	$lng = filter_input(INPUT_POST,"lng",FILTER_VALIDATE_FLOAT);
        
        $geoCoordenates = array($lat,$lng);
	if (!Index::exists()) {
		Index::create();
	}
	$filenames = Index::search($query,$geoCoordenates,$parametros);
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
