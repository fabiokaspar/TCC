var map;
var mapProp;
var origem = undefined;
var direcaoRota;
var mostraRota;

// TODO: Adicionar mais do que 5 markers
/*
var restaurantes = [
	{nome:"D", lat:-23.51583, lon: -46.65721, preco: "40,00", qualid: 3, distancia: {texto: "0", valor: 0}, marker: undefined},
	{nome:"B", lat:-23.50811, lon: -46.70064, preco: "75,00", qualid: 1, distancia: {texto: "0", valor: 0}, marker: undefined},
	{nome:"E", lat:-23.52779, lon: -46.64451, preco: "30,00", qualid: 4, distancia: {texto: "0", valor: 0}, marker: undefined},
	{nome:"C", lat:-23.51126, lon: -46.67936, preco: "55,00", qualid: 5, distancia: {texto: "0", valor: 0}, marker: undefined},
	{nome:"A", lat:-23.49473, lon: -46.70906, preco: "70,00", qualid: 2, distancia: {texto: "0", valor: 0}, marker: undefined}
]; 
*/

google.maps.event.addDomListener(window, 'load', initialize);

function initialize() {
	mapProp = {
		center:new google.maps.LatLng(-23.55112,-46.63438),
		zoom:11
	};

	map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
	direcaoRota = new google.maps.DirectionsService();
	mostraRota = new google.maps.DirectionsRenderer();

	navigator.geolocation.getCurrentPosition(getGeolocation, errorGeolocation);
	clienteRequisicao();
}

function clienteRequisicao(){
  	$('#ajax_form').submit(function(){
  		var dados = $(this).serialize();
  		$.ajax({
			type: "POST",
			url: "php/servidor.php",
			data: dados,
			dataType: "text",
			success: function(response){
				$('#listaRestaurantes').html('');
				$('#listaRestaurantes').append(response);
				//alert(response);
				console.log(response);
			},
			error: function(err){
				alert("ERRO!");
				console.log(err);
			}		
		});
		return false;
  	});
}

function getGeolocation(position){
	origem = new google.maps.Marker({
   		position: new google.maps.LatLng(position.coords.latitude,position.coords.longitude),
   		title: "onde estou!",
   		draggable: true
   	});

	map.setCenter(origem.position);
	origem.setMap(map);

	var infowindow = new google.maps.InfoWindow({
		content: "origem"
	});

	infowindow.open(map,origem);

	$("#btnPesquisar").click(function(){
		//clienteRequisicao();
		origem.setMap(map);
		mostraRota.setMap(null);
		main();
	});
}

function errorGeolocation(error){
  if(error.code != error.PERMISSION_DENIED)
    alert("Falha ao buscar sua geolocalização");
}

function main(){
	var rotaMarcada = false;

	if(origem != undefined){
		for(var i = 0; i < 5; i++){
			marcaDestino(i);
			calculaDistanciaEuclidiana(origem.position, restaurantes[i].marker.position, restaurantes[i].nome, i);
			// desenhaRotaEuclidiana(origem, restaurantes[i].marker);
		}

		if($("#inptLocalizacao").prop("checked")) restaurantes.sort(compareDist);
		else if($("#inptPreco").prop("checked")) restaurantes.sort(comparePreco);
		else if($("#inptQualidade").prop("checked")) restaurantes.sort(compareQualid);
	
		mostraListaRestaurante();

		$("#btnRota").click(function(){
			for(var i = 0; i < restaurantes.length; i++){
				if($("#restaurante"+i).prop("checked")){ 
					reapareceMarkers();
					escondeMarkers(i);
					rotaMarcada = true;
					mostraRota.setMap(null);
					desenhaRota(origem, restaurantes[i].marker);
				}
			}
			if(!rotaMarcada){
				alert("Por favor, escolha uma das rotas!");
			}
		});
	}
}

function marcaDestino(index){
	var destino = new google.maps.Marker({
		position: new google.maps.LatLng(restaurantes[index].lat,restaurantes[index].lon),
		title: restaurantes[index].nome,
		icon: "images/restaurant-24@2x.png"
	});
	
	destino.setMap(map);

	var infowindow = new google.maps.InfoWindow({
		content: restaurantes[index].nome
	});

	//infowindow.open(map,destino);		

	restaurantes[index].marker = destino;
}

// cuidado: a distancia é euclidiana e pouco conveniente
function calculaDistanciaEuclidiana(origLatLng, destLatLng, nomeDestino, index){
	var dist = google.maps.geometry.spherical.computeDistanceBetween(origLatLng, destLatLng);
	
	if(dist != 0){
		dist = dist.toFixed();

		if(dist >= 1000){
			dist /= 1000;

			restaurantes[index].distancia.texto = dist +" km";
		}
		else if(dist > 0){
			restaurantes[index].distancia.texto = dist +" m";
		}
		else{ 
			restaurantes[index].distancia.texto = "menos de 1 m";
		}
	}
	else{
		restaurantes[index].distancia.texto = "0 m";
	}

	restaurantes[index].distancia.valor = dist;
}

function compareDist(a,b) {
	if (a.distancia.valor < b.distancia.valor) return -1;
	if (a.distancia.valor > b.distancia.valor) return 1;
  	
  	return 0;
}

function comparePreco(a,b) {
	if (a.preco < b.preco) return -1;
	if (a.preco > b.preco) return 1;
  	
  	return 0;
}

function compareQualid(a,b) {
	if (a.qualid < b.qualid) return -1;
	if (a.qualid > b.qualid) return 1;
  	
  	return 0;
}

function desenhaRota(origem, destino){
	mostraRota.setMap(map);
	
	var request = {
		origin: origem.position,
		destination: destino.position,
		travelMode: google.maps.DirectionsTravelMode.DRIVING
	};

	direcaoRota.route(request, function(response, status){
		if(status == google.maps.DirectionsStatus.OK){
			mostraRota.setDirections(response);
			console.log("Tempo de percurso estimado: "+response.routes[0].legs[0].duration.text);
			console.log("Distancia total: "+response.routes[0].legs[0].distance.text);
		}
	});
}

function mostraListaRestaurante(){
	var lista = "<br><b>Lista Restaurantes por Distância</b><br>";
		lista += "<mark> Escolha um dos estabelecimentos para calcular rota:</mark><form>"

	if($("#inptQualidade").prop("checked")){
		for (var i = restaurantes.length-1; i >=0; i--) {
	    	lista += "<p class='restauranteInfo'><input id='restaurante"+i+"' type='radio' name='1'> restaurante " + restaurantes[i].nome + "<br>";
	    	lista += " Preco: " + restaurantes[i].preco + "<br>";
	    	lista += " Qualidade: " + restaurantes[i].qualid + " estrelas <br>";
	    	lista += " <b>Distância: " + restaurantes[i].distancia.texto + "</b></p>";
	  	}
	}
	else{
		for (var i = 0; i < restaurantes.length; i++) {
	    	lista += "<p class='restauranteInfo'><input id='restaurante"+i+"' type='radio' name='1'> restaurante " + restaurantes[i].nome + "<br>";
	    	lista += " Preco: " + restaurantes[i].preco + "<br>";
	    	lista += " Qualidade: " + restaurantes[i].qualid + "<br>";
	    	lista += " <b>Distância: " + restaurantes[i].distancia.texto + "</b></p>";
	  	}
	}

  	lista += "</form>";
  	lista += "<input type='button' id='btnRota' value='Criar rota até o local marcado'>";
  	
	$("#listaRestaurantes").html(lista);
}

function escondeMarkers(index){
	restaurantes[index].marker.setMap(null);

	origem.setMap(null);
}

function reapareceMarkers(){
	for(var i = 0; i < 5; i++){
		restaurantes[i].marker.setMap(map);
	}
}

// função em desuso
/*
function desenhaRotaEuclidiana(origem, destino){
	var coordenadas = [
 	    origem.position,
 		destino.position    
 	];

	var rota = new google.maps.Polyline({
		path: coordenadas,
		geodesic: true,
		strokeColor: '#FF0000',
		strokeOpacity: 1.0,
		strokeWeight: 2
	});

	rota.setMap(map);
} */