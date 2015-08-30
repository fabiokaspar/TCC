var map;
var mapProp;
var origem = undefined;
var direcaoRota;
var mostraRota;
var numResultados;
var restaurantesArray = []; 
var destinoRota = undefined;

google.maps.event.addDomListener(window, 'load', initialize);

function initialize() {
	mapProp = {
		center:new google.maps.LatLng(-23.55112,-46.63438),
		zoom:11
	};

	map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
	direcaoRota = new google.maps.DirectionsService();
	mostraRota = new google.maps.DirectionsRenderer({
      suppressMarkers: true
  	});
    //numResultados = 9;
	navigator.geolocation.getCurrentPosition(getGeolocation, errorGeolocation);
}

function clienteRequisicao() {
  	var dados = $(this).serialize();
		if (origem != undefined){
			dados += '&lat=';
			dados += origem.position.G;
			dados += '&lng=';
			dados += origem.position.K;
		}
		$.ajax({
			type: "POST",
			url: "php/filtro.php",
			data: dados,
			dataType: "text",
			success: function(response){
				try {
					//console.log(response)
                    var obj = JSON.parse(response);
                    $("div#listaRestaurantes").html("");
                    clearMarkers();
                    restaurantesArray = [];
                    numResultados = obj.restaurantes.length; 
                    if(obj.restaurantes.length > 0) {
                        for(var i = 0; i < obj.restaurantes.length; i++){
                                var restaurante = obj.restaurantes[i];
                                var p = $("<p></p>").html((i+1)+" - "+restaurante.nome);
                                $("div#listaRestaurantes").append(p);
                                //console.log(restaurante.nome);
                                restaurante.distancia = {};
                                restaurantesArray.push(restaurante);					
                        }
                        main();
                    }
				} catch(err) {
					console.log("catch");
					printError(err);
				}
			},
			error: function(err){
				console.log("error");
				printError(err);
			}		
		});
		return false;
}

function printError(err) {
    console.log(err);
    //var erroString = "<b>-- Erro --</b><br/>"+err;
    var erroString = "Ocorreu algum erro. Tente novamente."; 
    $("#mensagem").fadeOut(250).html(erroString).fadeIn(250);
    setTimeout(function() {
    $("#mensagem").fadeOut(1000);
    },2000);
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

	$('#ajax_form').submit(function(e) {
		e.preventDefault();
		if (destinoRota != undefined) {
     		destinoRota.setMap(null);	
  		}
		mostraRota.setMap(null);
		origem.setMap(map);

		clienteRequisicao.call(this);
		return false;
	});
}

function errorGeolocation(error){
  if(error.code != error.PERMISSION_DENIED)
    alert("Falha ao buscar sua geolocalização");
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
    //var max = (restaurantesArray.length < numResultados)?restaurantesArray.length:numResultados;
    for (var i = 0; i < restaurantesArray.length; i++) {
        restaurantesArray[i].marker.setVisible(false);
        restaurantesArray[i].marker = null;
    }
}

function main(){
	var rotaMarcada = false;
	if(origem != undefined){
		for(var i = 0; i < numResultados; i++){
			marcaDestino(i);
            var latlng = new google.maps.LatLng(restaurantesArray[i].latlon[0],restaurantesArray[i].latlon[1]);
			calculaDistanciaEuclidiana(origem.position, latlng, restaurantesArray[i].nome, i);
			//desenhaRotaEuclidiana(origem, restaurantesArray[i].marker);
		}

//		if($("#inptLocalizacao").prop("checked")) restaurantesArray.sort(compareDist);
//		else if($("#inptPreco").prop("checked")) restaurantesArray.sort(comparePreco);
//		else if($("#inptQualidade").prop("checked")) restaurantesArray.sort(compareQualid);
	
		mostraListaRestaurante();

        setTimeout(function() {
                    $(".labels").mouseenter(function() {
                        var id = $(this).data("id");
                        $(".labels[data-id="+id+"]").addClass("hover");
                    }).mouseleave(function() {
                        var id = $(this).data("id");
                        $(".labels[data-id="+id+"]").removeClass("hover");
                    }); 
                    $("#googleMap .labels").click(function() {
                        var id = $(this).data("id");
                        $("#listaRestaurantes .labels[data-id="+id+"]").
                                siblings("input").click();
                    });
                    $("#listaRestaurantes input").change(function() {
                        $(".labels").removeClass("selecionado");
                        var id = $(this).siblings(".labels").data("id");
                        $(".labels[data-id="+id+"]").addClass("selecionado");
                    });
                },400);
		$("#btnRota").click(function(){
			for(var i = 0; i < restaurantesArray.length; i++){
				if($("#restaurante"+i).prop("checked")){ 
					reapareceMarkers();
					escondeMarkers(i);
					rotaMarcada = true;
					mostraRota.setMap(null);
					desenhaRota(origem, restaurantesArray[i].marker);
				}
			}
			if(!rotaMarcada){
				alert("Por favor, escolha uma das rotas!");
			}
		});
	}
}

function marcaDestino(index){
	var destino = new MarkerWithLabel({
		position: new google.maps.LatLng(restaurantesArray[index].latlon[0],restaurantesArray[index].latlon[1]),
		title: restaurantesArray[index].nome,
		icon: "images/restaurant-24@2x.png",
                labelContent: ""+(index+1),
                labelClass: "labels", // the CSS class for the label
                labelStyle: {"text-color" : "red"}
	});
	
	destino.setMap(map);

	/*var infowindow = new google.maps.InfoWindow({
		content: restaurantesArray[index].nome
	});

	infowindow.open(map,destino);		
*/
	restaurantesArray[index].marker = destino;
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
			if (destinoRota != undefined) {
     			destinoRota.setVisible(false);
     			destinoRota.infowindow.close();	
  			}
  			console.log(response)
			mostraMarkersDaRota(response, destino);
			//console.log("Tempo de percurso estimado: "+response.routes[0].legs[0].duration.text);
			//console.log("Distancia total: "+response.routes[0].legs[0].distance.text);
		}
	});
}

function mostraMarkersDaRota(directionResult, destino) {
  destinoRota =  new google.maps.Marker({
   		position: directionResult.request.destination,
   		title: 'destino'
  });

  destinoRota.infowindow = new google.maps.InfoWindow({
  	content: destino.title
  });

  destinoRota.infowindow.open(map,destinoRota);	

  origem.setMap(map);
  destinoRota.setMap(map);
}

function mostraListaRestaurante(){
	var lista = "";

        for (var i = 0; i < numResultados; i++) {
            var userVisibleIndex = (i+1);
            lista += "<label class='restauranteInfo'><input id='restaurante"+i+"' type='radio' name='1'>";
            lista += "<div class='labels' data-id='"+userVisibleIndex+"'>"+userVisibleIndex+"</div>";
            lista += "<a href='"+restaurantesArray[i].link+"' target='_blank'>" + restaurantesArray[i].nome + "</a><br>";
            lista += " Preco: " + restaurantesArray[i].preco + "<br>";
            lista += " Qualidade: " + restaurantesArray[i].nota + "<br>";
            lista += " <b>Distância: " + restaurantesArray[i].distancia.texto + "</b></label>";
        }
  	
	$("#listaRestaurantes").html(lista);
        $("form#restaurantes").show();
}

function escondeMarkers(index){
	restaurantesArray[index].marker.setMap(null);
	origem.setMap(null);
}

function reapareceMarkers(){
	for(var i = 0; i < numResultados; i++){
		restaurantesArray[i].marker.setMap(map);
	}
}

// cuidado: a distancia é euclidiana e pouco conveniente
function calculaDistanciaEuclidiana(origLatLng, destLatLng, nomeDestino, index){
	var dist = google.maps.geometry.spherical.computeDistanceBetween(origLatLng, destLatLng);	
	if(dist != 0){
		dist = dist.toFixed();
		if(dist >= 1000){
			dist /= 1000;
			restaurantesArray[index].distancia.texto = dist +" km";
		}
		else if(dist > 0){
			restaurantesArray[index].distancia.texto = dist +" m";
		}
		else{ 
			restaurantesArray[index].distancia.texto = "menos de 1 m";
		}
	}
	else{
		restaurantesArray[index].distancia.texto = "0 m";
	}
	restaurantesArray[index].distancia.valor = dist;
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
