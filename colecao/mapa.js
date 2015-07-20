var map;
var mapProp;
var origem = undefined;
var destino = undefined;
var dist;
var calculeRota;
var renderRota;

google.maps.event.addDomListener(window, 'load', initialize);

function initialize() {	  
	inicializarMapa();
	navigator.geolocation.getCurrentPosition(getGeolocation, errorGeolocation);
}
		
function inicializarMapa(){
	mapProp = {
		center:new google.maps.LatLng(-23.55112,-46.63438),
		zoom:11
	};

	map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
}

function getGeolocation(position){
	origem = new google.maps.Marker({
   		position: new google.maps.LatLng(position.coords.latitude,position.coords.longitude),
   	});

	map.setCenter(origem.position);
	//origem.setMap(map);

	marcaDestino();
	desenhaRota();
	calculaDistancia(origem.getPosition(), destino.getPosition());
}

function errorGeolocation(error){
  if(error.code != error.PERMISSION_DENIED)
    alert("Falha ao buscar sua geolocalização");
}

function marcaDestino(){
	destino = new google.maps.Marker({
   		position: new google.maps.LatLng(-23.40,-45.10)
   	});

	//destino.setMap(map);
}

function desenhaRota(){
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

	console.log(rota);
	rota.setMap(map);  

	if(origem != undefined && destino != undefined){
		calculeRota = new google.maps.DirectionsService();
		renderRota = new google.maps.DirectionsRenderer();
		renderRota.setMap(map);
		
		var request = {
			origin: origem.position,
			destination: destino.position,
			travelMode: google.maps.DirectionsTravelMode.DRIVING
		};

		calculeRota.route(request, function(response, status){
			if(status == google.maps.DirectionsStatus.OK){
				renderRota.setDirections(response);
			}
		});
	}
}

function calculaDistancia(origLatLng, destLatLng){
	dist = google.maps.geometry.spherical.computeDistanceBetween(origLatLng, destLatLng);
	
	if(dist != 0){
		dist = dist.toFixed();

		if(dist >= 1000){
			dist /= 1000;
			alert("distancia = "+ dist +"km");
		}
		else if(dist > 0){
			alert("distancia = "+ dist +"m");
		}
		else alert("menos de 1m");
	}
	else{
		alert("0m");
	}
}
