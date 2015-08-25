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
	$lat = filter_input(INPUT_POST,"lat",FILTER_VALIDATE_INT);
	$lng = filter_input(INPUT_POST,"lng",FILTER_VALIDATE_INT);
        
	if (!Index::exists()) {
		Index::create();
	}
	$filenames = Index::search($query);
        
	$dir = Index::INDEX_FOLDER;
	$restaurantes = array();
	foreach($filenames as $filename) {
		$path = "$dir/$filename";
		if(empty($filename) || !file_exists($path)) {
			continue;
		}
		$content = json_decode(file_get_contents($path),TRUE);
		$restaurantes[] = $content;
	}
	$pre_JSON = array("restaurantes"=>$restaurantes);
	$JSON = json_encode($pre_JSON);
	
	echo $JSON;
?>
