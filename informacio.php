<?php 
session_start();
//Variable de sessió de la pàgina actual.
$_SESSION['path']='informacio.php';
//Constant amb el nom de l'arxiu per llegir json i convertir a xml.
define(JSON_READ, "leer_json_desnivel.php");

//Include de Funcions PHP
include("functions/functions.php");
//Classe Location amb la que obtenim les dades de la localitat a través de les coordenades.
include("functions/class.location.php");
//Include Objecte API Yahoo Weather
include("functions/class.xml.parser.php");
include("functions/class.weather.php");

//Google Analytics
include("functions/google_analytics");

//Si obtenim les dades d'origen, les guardem
//Guardem les dades obtingudes de la localitat i la cerca.
$lng_origen = $_SESSION["lng_origen"];
$lat_origen = $_SESSION["lat_origen"];

if(isset($_REQUEST["lat_origen"])){
    $_SESSION["lat_origen"] = $_REQUEST["lat_origen"];
    $lat_origen = $_SESSION["lat_origen"];
    }
if(isset($_REQUEST["lng_origen"])){
    $_SESSION["lng_origen"] = $_REQUEST["lng_origen"];
    $lng_origen = $_SESSION["lng_origen"];
    }

//VARIABLES DE SESSIÓ
$localitat = $_SESSION["localitat"];
$direccio_origen = $_SESSION["direccio"];

//echo "origen-> ".$lat_origen.",".$lng_origen;
//echo "origen-> ".$_SESSION['lat'] .",".$_SESSION['lng'];


//Obtenim les coordenades del destí
if(isset($_REQUEST["lat_destino"])){
    $_SESSION["lat_destino"] = $_REQUEST["lat_destino"];
    $lat_destino=$_SESSION["lat_destino"];
    }
if(isset($_REQUEST["lng_destino"])){
    $_SESSION["lng_destino"] = $_REQUEST["lng_destino"];
    $lng_destino=$_SESSION["lng_destino"];
    }
if(isset($_REQUEST["nom"])){
    $_SESSION["nom"] = $_REQUEST["nom"];
    $nom_destinacio=$_SESSION["nom"];
    }
if(isset($_REQUEST["direccio"])){
    $_SESSION["direccio"] = $_REQUEST["direccio"];
    $direccio_desti=$_SESSION["direccio"];
    }

//Guardem les dades de l'establiment en variables de sessió.
/*
if(isset($_SESSION["lat_destino"])){$lat_destino=$_SESSION["lat_destino"];}
if(isset($_SESSION["lng_destino"])){$lng_destino=$_SESSION["lng_destino"];}
if(isset($_SESSION["nom"])){$nom_destinacio=$_SESSION["nom"];}
if(isset($_SESSION["direccio"])){$direccio_desti=$_SESSION["direccio"];}
*/




//echo "destino-> ".$lat_destino.",".$lng_destino;
//echo "<br>";

/*
echo "distance -> ".distance_api_google_directions($lat_origen, $lng_origen, $lat_destino, $lng_destino, "K","D");
echo "<br>coordenadas destino -> ".$lat_destino.",".$lng_destino;
echo "<br>coordenadas origen -> ".$lat_origen.",".$lng_origen;
*/

//Creem un objecte location per a l'origen
    
    //Obtenim un string amb les coordenades.
    $coord = parse_coord($_SESSION['lat_origen'],$_SESSION['lng_origen']);
    
    //echo "<br>coord_origen -> ".parse_coord($_SESSION['lat_origen'],$_SESSION['lng_origen']);
    
    //Crear objecte Location
    $classe_location_origen = new location();
    
    //Li passem les coordenades.
    $classe_location_origen->location_by_coord($coord);

    //Creem un Objecte Weather.
    $weather = new weather($classe_location_origen->woeid, 3600, 'c');//Objecte Weather.
    
    //Parsegem les dades del caché.
    $weather->parsecached();

//Creem un objecte location per al destí
    
    //Obtenim un string amb les coordenades.
    $coord = parse_coord($_SESSION['lat_destino'],$_SESSION['lng_destino']);
    
    //echo "<br>coord_destino -> ".parse_coord($_SESSION['lat_destino'],$_SESSION['lng_destino']);
    
    //Crear objecte Location
    $classe_location_desti = new location();
    
    //Li passem les coordenades.
    $classe_location_desti->location_by_coord($coord);
    
    //Guardem el carrer i la direccio del destí en les variables de sessio
    $_SESSION['direccio'] = utf8_decode($classe_location_desti->direccio);
    $_SESSION['carrer'] = split_direccio($direccio_desti);

//Calculem la quantitat de samples necessaria.
$samples = obtenir_samples_distancia(distance_numeric($lat_origen, $lng_origen, $lat_destino, $lng_destino, "K"));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>Mostrar Rutas</title>
        <!-- Llibreries JQuery -->
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/jquery.custom.min.js"></script>
        <!--<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true&language=ca"></script>-->
        <!-- Google Maps API -->
        <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
        <!-- Google Chart Tools -->
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <!--Funció per llegir XML downloadUrl(); -->
        <script type="text/javascript" src="js/util.js"></script>
        
        <!-- Bootstrap -->
        <link href='bootstrap/css/bootstrap.css' rel='stylesheet' type='text/css' media='screen' />
        <link href='bootstrap/css/bootstrap.css' rel='stylesheet' type='text/css' media='screen' />
        <link href='bootstrap/css/bootstrap-responsive.css' rel='stylesheet' type='text/css' media='screen' />
        <link href='css/style.css' rel='stylesheet' type='text/css' media='screen' />
        
<script type="text/javascript">

//window.onload = getDirections;

</script>
    </head>
    <body>
    
    <!-- Menú -->
    <div id="menu">
        <?php
            include("templates/menu.php");
        ?>
    </div>    
    <!-- Menú -->
        
    <div class="row-fluid">
    <!-- Contenidor -->
    <div class="container-fluid">
     
    
        <?php //echo "origen-> ".$_SESSION['lat_origen'] .",".$_SESSION['lng_origen']; ?>
        <?php //echo "origen-> ".$_SESSION['lat_destino'] .",".$_SESSION['lng_destino']; ?>
        
    <!-- Errors Inici de sessió -->
    <section id='div_msg_sessio'>
    <section id='msg_password'></section>
    <section id='missatges_form_sessio'></section>
    </section>
    <!-- Errors Inici de sessió -->
        
       <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
    
     <!-- Titol Establiment -->
     <?php
        echo "<div class='span12'>";
        echo "<h1 class='lead'>".$nom_destinacio."</h1>";
        echo "<h3>".utf8_decode($classe_location_desti->direccio).".</h3>";
        echo "<h3>".$classe_location_desti->localitat.", ".$classe_location_desti->provincia.", ".$classe_location_desti->cAutonoma.".</h3>";
        echo "</div>";
     ?>
     
    
        <div class="span12">
            <?php
            //Iframe recomanar
            echo "<section id='recomanar'>";
            //echo 'recomanar_lloc.php?lat='.$lat_destino.'&lng='.$lng_destino.'&nom='.urlencode($nom_destinacio).'&localitat='.$localitat.'&direccio='.urlencode($direccio_desti);
            echo '<iframe name="frame_recomanacions" src="recomanar_lloc.php?lat='.$lat_destino.'&lng='.$lng_destino.'&nom='.urlencode($nom_destinacio).'&localitat='.$localitat.'&direccio='.urlencode($direccio_desti).'"  frameborder="0" class="span12" height="320px"></iframe>';
            echo "</section>";
            ?>
        </div>
        
    
    
    <!-- Info Recorregut --> 
    <section class='span12'>
        
        <div id="info_origen" class='span4'>
            <h3>Origen</h3>
            
            <?php
                echo "<b>Direcció:</b> ".utf8_decode($classe_location_origen->direccio);
                echo "<br>";
                echo "<b>Localitat:</b> ".$classe_location_origen->localitat;
                echo "<br>";
                echo "<b>Província:</b> ".$classe_location_origen->provincia;
                echo "<br>";
                echo "<b>Altitud:</b> ".arrodonir_dos_decimals($classe_location_origen->altitud)." m";
                //echo "<br>".$classe_location_origen->latitud.",".$classe_location_origen->longitud.",".$classe_location_desti->latitud.",".$classe_location_desti->longitud."<br>";
                
            ?>
            
        </div>
        
        <div id="info_recorregut" class='span4'>
            <h3>Informació del recorregut</h3>
            <p><b>Carregant...</b></p>
            <?php
            /*
                echo "<b>Distància:</b> ".arrodonir_dos_decimals(distance_numeric($classe_location_origen->latitud,$classe_location_origen->longitud, $classe_location_desti->latitud, $classe_location_desti->longitud, "K"))." Km";
                echo "<br>";
                echo "<b>Desnivell positiu acumulat:</b> ".arrodonir_dos_decimals(desnivell_positiu($classe_location_origen->latitud,$classe_location_origen->longitud, $classe_location_desti->latitud, $classe_location_desti->longitud,$samples))." m";
                echo "<br>";
                echo "<b>Desnivell negatiu acumulat:</b> ".arrodonir_dos_decimals(desnivell_negatiu($classe_location_origen->latitud,$classe_location_origen->longitud, $classe_location_desti->latitud, $classe_location_desti->longitud,$samples))." m";
                echo "<br>";
                echo "<b>Altitud màxima:</b> ".arrodonir_dos_decimals(altitud_maxima($classe_location_origen->latitud,$classe_location_origen->longitud, $classe_location_desti->latitud, $classe_location_desti->longitud,$samples))." m";
                echo "<br>";
                echo "<b>Altitud mínima:</b> ".arrodonir_dos_decimals(altitud_minima($classe_location_origen->latitud,$classe_location_origen->longitud, $classe_location_desti->latitud, $classe_location_desti->longitud,$samples))." m";
                echo "<br>";
                //echo "<b>Percentatge desnivell mig:</b> ".arrodonir_dos_decimals(angle_desnivell_mig($classe_location_origen->latitud,$classe_location_origen->longitud, $classe_location_desti->latitud, $classe_location_desti->longitud,$samples))." º";
             
             */
            ?>
        </div>
        
        <section id='info_lloc' class='span4'>

            <?php 
            echo "<h3>Destinació (".$nom_destinacio.")</h3>";
            echo "<p>";
            echo "<b>Direcció:</b> ".utf8_decode($classe_location_desti->direccio);
            echo "<br>";
            echo "<b>Carrer:</b> ".split_direccio($direccio_desti);
            echo "<br>";
            echo "<b>Localitat:</b> ".$classe_location_desti->localitat;
            echo "<br>";
            echo "<b>Província:</b> ".$classe_location_desti->provincia;
            echo "<br>";
            echo "<b>Distància Automòbil:</b> ".distance_api_google_directions($classe_location_origen->latitud,$classe_location_origen->longitud, $classe_location_desti->latitud, $classe_location_desti->longitud, "K","D");
            echo "<br>";
            echo "<b>Distància Caminant:</b> ".distance_api_google_directions($classe_location_origen->latitud,$classe_location_origen->longitud, $classe_location_desti->latitud, $classe_location_desti->longitud, "K","W");
            echo "<br>";
            echo "<b>Altitud:</b> ".arrodonir_dos_decimals($classe_location_desti->altitud)." m";
            echo "</p>";
            ?>
        </section>
    
        
    </section>
    <!-- Final Info Recorregut -->
     
        <!-- Tipus de Viatge -->
        <div id="div_tipus_viatge" class="span12 center">
            <p>
            <b>Tipus de viatge</b>
            <br>
            <select id="travelMode" class="routeOptions" >
                <option value="DRIVING" selected="selected">Automòbil</option>
                <option value="WALKING">Caminant</option>
                <!--<option value="BICYCLING">En Bicicleta (Solo disponible en EEUU)</option>-->
            </select>
            </p>
        </div>
    
    <!-- Gràfica -->
    <div id="chart_div" class='span12'></div>
          
    
        
        <div id="map_canvas" class='span12'></div>
        
        <div id="directions_panel" class='span12'></div>
        <!-- Iframe Formulari Inici de Sessió -->
        <?php
        //Iframe comentaris
        echo '<iframe name="frame_comentaris" src="llistat_comentaris.php?lat='.$lat_destino.'&lng='.$lng_destino.'&nom='.urlencode($nom_destinacio).'&localitat='.$localitat.'&direccio='.urlencode($direccio_desti).'" rows="100,*" frameborder="0" width="100%" height="1000px"></iframe>';
    
        echo "<iframe src='webs_relacionades.php?websearch=".$nom_destinacio."' rows='100,*' frameborder='0' width='100%' height='500px'></iframe>";
        ?>
                
      </div>
      <!-- Main hero unit for a primary marketing message or call to action -->

      <hr>

      <footer>
          <?php
            include("templates/footer.php");
          ?>
      </footer>

        
        
    </div>
    <!-- Contenidor -->
     </div>
    <!-- Row Fluid -->
          <!-- Placed at the end of the document so the pages load faster -->
   <!--
    <script src="bootstrap/js/jquery.js"></script>
    <script src="bootstrap/js/bootstrap-transition.js"></script>
    <script src="bootstrap/js/bootstrap-alert.js"></script>
    <script src="bootstrap/js/bootstrap-modal.js"></script>
    <script src="bootstrap/js/bootstrap-dropdown.js"></script>
    <script src="bootstrap/js/bootstrap-scrollspy.js"></script>
    <script src="bootstrap/js/bootstrap-tab.js"></script>
    <script src="bootstrap/js/bootstrap-tooltip.js"></script>
    <script src="bootstrap/js/bootstrap-popover.js"></script>
    <script src="bootstrap/js/bootstrap-button.js"></script>
    <script src="bootstrap/js/bootstrap-collapse.js"></script>
    <script src="bootstrap/js/bootstrap-carousel.js"></script>
    <script src="bootstrap/js/bootstrap-typeahead.js"></script>
    -->
<script type='text/javascript'>
            google.load("visualization", "1", {packages:["corechart"]});
        
            var map = null;
            var directionsDisplay = null;
            var directionsService = null;
            var init_directions = false;
            var myLatlng = new google.maps.LatLng(<?php echo parse_coord($classe_location_origen->latitud,$classe_location_origen->longitud); ?>);
            var start = "<?php echo parse_coord($classe_location_origen->latitud,$classe_location_origen->longitud); ?>";
            var end = "<?php echo parse_coord($classe_location_desti->latitud,$classe_location_desti->longitud); ?>";
            var trabel_mode = "";

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
                //Llamamos a la función para cargar la ruta a través de leer_json_rutas.php (JSONP->XML)
                //getDirections();
                
                
	}
        
        function getDirections(){
            //var start = $('#start').val();
            //var end = $('#end').val();
            //Guardem el tipus de viatge
            trabel_mode = $('#travelMode').val();
            
            if(!start || !end){
                alert("Start and End addresses are required");
                return;
            }
            
            var request = {
                origin: start,
		destination: end,
		travelMode: google.maps.DirectionsTravelMode[$('#travelMode').val()],
		//unitSystem: google.maps.DirectionsUnitSystem[$('#unitSystem').val()],
		unitSystem: google.maps.DirectionsUnitSystem['METRIC'],
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
            
            DibuixaGrafic();
            
	}
        
        function DibuixaGrafic(){
            
            var lat = <?php echo $classe_location_origen->latitud; ?>;
            var lng = <?php echo $classe_location_origen->longitud; ?>;
            var lat_destino = <?php echo $classe_location_desti->latitud; ?>;
            var lng_destino = <?php echo $classe_location_desti->longitud; ?>;
            
            //alert(lat,lng);
            
            var array_steps = new Array();
            var datos_grafica = new Array();
            
        try{
            //Funció per llegir XML
            //downloadUrl('leer_json_rutas.php?lat_destino='+lat+'&lng_destino='+lng+'&travel_mode='+$('#travelMode').val(), function(data){
            downloadUrl('leer_json_rutas.php?lat_origen='+lat+'&lng_origen='+lng+'&lat_destino='+lat_destino+'&lng_destino='+lng_destino+'&travel_mode='+$('#travelMode').val(), function(data){
                
                var steps = data.documentElement.getElementsByTagName("step");
                var distancia = 0;
                
                datos_grafica[0] = ["Distància","Desnivell"]; 

                //Recorrem l'XML
                for (var i = 0; i < steps.length; i++) {
                    
                    array_steps[i] = new google.maps.LatLng(steps[i].getAttribute("lat"), steps[i].getAttribute("lng"));
                    
                    //Obtenim les dades de l'XML
                    distancia = distancia + parseFloat(steps[i].getAttribute("metres"));
                    var altitud = parseFloat(steps[i].getAttribute("altitud"));
                    
                    //Arrodonim la distància a 2 decimals.
                    distancia = Math.round(distancia*100)/100;
                    
                    //Guardem l'array amb les dades per la gràfica
                    datos_grafica[i+1]=[distancia,altitud];
                    //datos_grafica[i]=[distancia,altitud];

                }
                
                //Funció per dibuixar la gràfica.
                drawChart(datos_grafica);
                //Funció per dibuixar les dades del recorregut.
                infoRecorregut(steps);
                
                
            });
            }catch(err){
                console.log("Obtenint dades d'altitud");
            }    
}        
        
        function infoRecorregut(steps){
            
            var altitud_maxima = parseFloat(steps[0].getAttribute("altitud"));
            var altitud_minima = parseFloat(steps[0].getAttribute("altitud"));
            var distancia_total = parseFloat(steps[0].getAttribute("distancia_total"));
            var desnivell_positiu = 0;
            var desnivell_negatiu = 0;
            var duracio = steps[0].getAttribute("duracio");
            var array_angles = new Array();
            
            //alert(distancia_total);
            
            //Si tenim resultats actualitzem les dades.
            if (!isNaN(distancia_total)){
                
                //Recorrem l'XML
                for (var i = 1; i < steps.length; i++) {

                    //Calculem l'altitud maxima
                    if(parseFloat(steps[i].getAttribute("altitud"))>altitud_maxima){altitud_maxima=parseFloat(steps[i].getAttribute("altitud"));}
                    //Calculem l'altitud minima
                    if(parseFloat(steps[i].getAttribute("altitud"))<altitud_minima){altitud_minima=parseFloat(steps[i].getAttribute("altitud"));}
                    //Calculem el desnivell positiu
                    if(parseFloat(steps[i].getAttribute("altitud"))>parseFloat(steps[i-1].getAttribute("altitud"))){desnivell_positiu = desnivell_positiu + (parseFloat(steps[i].getAttribute("altitud"))-parseFloat(steps[i-1].getAttribute("altitud")));}
                    //Calculem el desnivell negatiu
                    if(parseFloat(steps[i].getAttribute("altitud"))<parseFloat(steps[i-1].getAttribute("altitud"))){desnivell_negatiu = desnivell_negatiu + (parseFloat(steps[i].getAttribute("altitud"))-parseFloat(steps[i-1].getAttribute("altitud")));}
                    //Calculem l'angle de desnivell

                    var distancia_real = parseFloat(steps[i].getAttribute("metres"));
                    var altura = parseFloat(steps[i].getAttribute("altitud"))-parseFloat(steps[i-1].getAttribute("altitud"));
                    var distancia_x = Math.sqrt(Math.pow(distancia_real,2)-Math.pow(altura,2));
                    //alert(distancia_x);
                    array_angles[i-1] = altura*100/distancia_x;
                    //alert(array_angles[i-1]+"%");

                }

                //Arrodonim les dades a 2 decimals
                desnivell_positiu=Math.round(desnivell_positiu*10)/10;
                desnivell_negatiu=Math.round(desnivell_negatiu*10)/10;

                //Afegim les dades a la secció
                document.getElementById("info_recorregut").innerHTML = "";
                document.getElementById("info_recorregut").innerHTML += "<h3>Informació del recorregut</h3>";
                document.getElementById("info_recorregut").innerHTML += "<b>Distància:</b> "+distancia_total+" m";
                document.getElementById("info_recorregut").innerHTML += "<br>";
                document.getElementById("info_recorregut").innerHTML += "<b>Duració:</b> "+duracio;
                document.getElementById("info_recorregut").innerHTML += "<br>";
                document.getElementById("info_recorregut").innerHTML += "<b>Desnivell positiu acumulat:</b> "+desnivell_positiu+" m";
                document.getElementById("info_recorregut").innerHTML += "<br>";
                document.getElementById("info_recorregut").innerHTML += "<b>Desnivell negatiu acumulat:</b> "+desnivell_negatiu+" m";
                document.getElementById("info_recorregut").innerHTML += "<br>";
                document.getElementById("info_recorregut").innerHTML += "<b>Altitud màxima:</b> "+altitud_maxima+" m";
                document.getElementById("info_recorregut").innerHTML += "<br>";
                document.getElementById("info_recorregut").innerHTML += "<b>Altitud mínima:</b> "+altitud_minima+" m";
                //document.getElementById("info_recorregut").innerHTML = "<br>";
            }
            
        }
        
        function drawChart(datos_grafica) {
          
            //Obtenim les altituds màxima i minima per determinar les dimensions del gràfic.
            var altitud_maxima = <?php echo arrodonir_dos_decimals(altitud_maxima($lat_origen,$lng_origen,$lat_destino,$lng_destino,$samples)); ?>;
            var altitud_minima = <?php echo arrodonir_dos_decimals(altitud_minima($lat_origen,$lng_origen,$lat_destino,$lng_destino,$samples)); ?>;

            //alert('entra');

            var data = google.visualization.arrayToDataTable(datos_grafica);
            
            //Afegim les columnes
            //data.addColumn('number', 'Distància (metres)');
            //data.addColumn('number', 'Altura (metres)');
            
            var travel_mode = "";
            
            //Comentari Grafica
            if(trabel_mode=='WALKING'){travel_mode = "Caminant";}
            if(trabel_mode=='DRIVING'){travel_mode = "Automòbil";}

            var options = {
                title: 'Gràfica de desnivell del recorregut ('+travel_mode+')',
                hAxis: {title: 'Distància (metres)',  titleTextStyle: {color: 'black'}},
                vAxis: {title: 'Altura (metres)',  titleTextStyle: {color: 'black'}, minValue: altitud_minima, maxValue: altitud_maxima}
            };

            //var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        
        }

	
        
        //----------------------------------------------------------------------------------
       
       
       //window.onload = initialize;
       
       $(document).ready(function() {
	    initialize();
            getDirections();
            $('.routeOptions').on('change', function(){ getDirections(); });
            //gmaps_ads();
	});
        
       
        
        
</script>
<script type="text/javascript">
        //$('#language').on('click', function(){ getDirections(); });
	//$('.routeOptions').on('change', function(){ getDirections(); });
</script>
    <!--<link href='css/style.css' rel='stylesheet' type='text/css' media='screen' />-->
    </body>
</html>