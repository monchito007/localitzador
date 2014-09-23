<?php 
session_start();
//Include de Funcions PHP
include("functions/functions.php");

//VARIABLES DE SESSIÓ
//Guardem les dades obtingudes de la localitat i la cerca.
$lat_origen = $_SESSION["lat"];
$lng_origen = $_SESSION["lng"];
//echo $lat_origen.",".$lng_origen;

$lat_destino = $_REQUEST["lat_destino"];
$lng_destino = $_REQUEST["lng_destino"]; 

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Projecte - Mostrar Rutas</title>
        <!-- Llibreries JQuery -->
        <script type="text/javascript" src="js/jquery.js"></script>
        <!--<script type="text/javascript" src="js/jquery.min.js"></script>-->
        <script type="text/javascript" src="js/jquery.custom.min.js"></script>
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true"></script>
        <script type='text/javascript'>
            
            var map = null;
            var directionsDisplay = null;
            var directionsService = null;
            var init_directions = false;
            var myLatlng = new google.maps.LatLng(<?php echo parse_coord($lat_origen,$lng_origen); ?>);
            var start = "<?php echo parse_coord($lat_origen,$lng_origen); ?>";
            var end = "<?php echo parse_coord($lat_destino,$lng_destino); ?>";

	function initialize(){
	    
            var rendererOptions = {
                draggable: true
            };

            
            var myOptions = {
	        zoom: 4,
	        center: myLatlng,
                draggable: true,
	        mapTypeId: google.maps.MapTypeId.ROADMAP
	    };
                map = new google.maps.Map($("#map_canvas").get(0), myOptions);
		directionsDisplay = new google.maps.DirectionsRenderer(rendererOptions);
		directionsService = new google.maps.DirectionsService();
                //Llamamos a la función para cargar la ruta.
                //getDirections();
                
	}

	function getDirections(){
            //var start = $('#start').val();
            //var end = $('#end').val();
                
            if(!start || !end){
                alert("Start and End addresses are required");
                return;
            }
            
            var request = {
                origin: start,
		destination: end,
		travelMode: google.maps.DirectionsTravelMode[$('#travelMode').val()],
		unitSystem: google.maps.DirectionsUnitSystem[$('#unitSystem').val()],
		provideRouteAlternatives: true
	    };
            directionsService.route(request, function(response, status) {
	    if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setMap(map);
	        directionsDisplay.setPanel($("#directions_panel").get(0));
	        directionsDisplay.setDirections(response);
            }else{
                alert("There is no directions available between these two points");
	    }
	    });
	}
        
	$(document).ready(function() {
	    initialize();
            getDirections();
            gmaps_ads();
	});        
        </script>
    <!--<link href='css/style.css' rel='stylesheet' type='text/css' media='screen' />-->
    <style>
        #map_canvas{
            
            width: 800px;
            height: 600px;
            
        }
        
    </style>
    </head>
    <body>
    <header>Rutas</header>
        <div id="map_canvas"></div>
        <!--
        <div class="column width1 first">
        Origen <input type="text" id="start" placeholder="address or coordinates" />
	<br/>
        Destino <input type="text" id="end" placeholder="address or coordinates" />
	</div>
        -->
        <div class="column width1">
        <p>
        Tipo de Viaje
	<select id="travelMode" class="routeOptions" >
            <option value="DRIVING">En Auto</option>
            <option value="WALKING" selected="selected">Caminando</option>
            <option value="BICYCLING">En Bicicleta (Solo disponible en EEUU)</option>
      	</select>
        </p>
        <p>
        Unidades de medida
      	<select id="unitSystem" class="routeOptions">
            <option value="METRIC" selected="selected">MÃ©trico</option>
            <option value="IMPERIAL">Imperial</option>
      	</select>
        </p>
        </div>
        
        <div class="first">
        <!--<p class="button"><a href="javascript:void(0)" id="search" class="send" >Buscar Ruta</a></p>-->
        </div>
        <div id="directions_panel"></div>
        <script>
        //$('#search').on('click', function(){ getDirections(); });
	$('.routeOptions').on('change', function(){ getDirections(); });
        </script>
    </body>
</html>