<?php 
session_start();
//Constant amb el nom de l'arxiu per llegir json i convertir a xml.
define(JSON_READ, "leer_json_desnivel.php");

//Include de Funcions PHP
include("functions/functions.php");

//Classe Location amb la que obtenim les dades de la localitat a través de les coordenades.
include("functions/class.location.php");

//VARIABLES DE SESSIÓ
$latitud = $_SESSION["lat"];
$longitud = $_SESSION["lng"];
$localitat = $_SESSION["localitat"];
$direccio = $_SESSION["direccio"];

//Obtenim les coordenades del destí
$lat_destino = $_REQUEST["lat_destino"];
$lng_destino = $_REQUEST["lng_destino"]; 
$nom_destinacio = $_REQUEST["nom"];

//Creem un objecte location per a l'origen
    
    //Obtenim un string amb les coordenades.
    $coord = parse_coord($latitud ,$longitud);
    
    //Crear objecte Location
    $classe_location_origen = new location();
    
    //Li passem les coordenades.
    $classe_location_origen->location_by_coord($coord);


//Creem un objecte location per al destí
    
    //Obtenim un string amb les coordenades.
    $coord = parse_coord($lat_destino ,$lng_destino);
    
    //Crear objecte Location
    $classe_location_desti = new location();
    
    //Li passem les coordenades.
    $classe_location_desti->location_by_coord($coord);

//Calculem la quantitat de samples necessaria.
$samples = obtenir_samples_distancia(distance_numeric($latitud, $longitud, $lat_destino, $lng_destino, "K"));

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Gràfica de Desnivell</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <!--Funció per llegir XML downloadUrl(); -->
        <script type="text/javascript" src="js/util.js"></script>
        <!-- Google Chart Tools -->
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    </head>
    <script>
        google.load("visualization", "1", {packages:["corechart"]});
        function initialize() {
    
            var lat = "<?php echo $lat_destino; ?>";
            var lng = "<?php echo $lng_destino; ?>";
            
            //Funció per llegir XML
            downloadUrl('leer_json_desnivel.php?lat_destino='+lat+'&lng_destino='+lng, function(data){
                
                var samplers = data.documentElement.getElementsByTagName("sampler");
                var datos_grafica = new Array();
                
                datos_grafica[0] = ["Distància","Desnivell"]; 

                //Recorrem l'XML
                for (var i = 0; i < samplers.length; i++) {
                    
                    //Obtenim les dades de l'XML
                    var distancia = parseFloat(samplers[i].getAttribute("resolution")*i);
                    var altitud = parseFloat(samplers[i].getAttribute("elevation"));
                    
                    //Arrodonim la distància a 2 decimals.
                    distancia = Math.round(distancia*100)/100;
                    
                    //Guardem l'array amb les dades per la gràfica
                    datos_grafica[i+1]=[distancia,altitud];

                }
                
                //Funció per dibuixar la gràfica.
                drawChart(datos_grafica);
                
            });
        }
        
        function drawChart(datos_grafica) {
          
        //Obtenim les altituds màxima i minima per determinar les dimensions del gràfic.
        var altitud_maxima = <?php echo arrodonir_dos_decimals(altitud_maxima($latitud,$longitud,$lat_destino,$lng_destino,$samples)); ?>;
        var altitud_minima = <?php echo arrodonir_dos_decimals(altitud_minima($latitud,$longitud,$lat_destino,$lng_destino,$samples)); ?>;
        
        var data = google.visualization.arrayToDataTable(datos_grafica);

        var options = {
          title: 'Gràfica de desnivell del recorregut',
          hAxis: {title: 'Distància (metres)',  titleTextStyle: {color: 'black'}},
          vAxis: {title: 'Altura (metres)',  titleTextStyle: {color: 'black'}, minValue: altitud_minima, maxValue: altitud_maxima}
          
        };

        //var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(data, options);
        
        }
        
        window.onload = initialize;
    </script>
    <body>
        <div id="chart_div" style="width: 100%; height: 600px;"></div>
        <div id="info_origen">
            <h2>Origen</h2>
            
            <?php
                echo "<b>Direcció:</b> ".utf8_decode($classe_location_origen->direccio);
                echo "<br>";
                echo "<b>Localitat:</b> ".utf8_decode($classe_location_origen->localitat);
                echo "<br>";
                echo "<b>Província:</b> ".utf8_decode($classe_location_origen->provincia);
                echo "<br>";
                echo "<b>Altitud:</b> ".arrodonir_dos_decimals($classe_location_origen->altitud)." m";
                
            ?>
        </div>
        <div id="info_recorregut">
            <h3>Recorregut</h3>
            
            <?php
                echo "<b>Distància:</b> ".arrodonir_dos_decimals(distance_numeric($latitud, $longitud, $lat_destino, $lng_destino, "K"))." Km";
                echo "<br>";
                echo "<b>Desnivell positiu acumulat:</b> ".arrodonir_dos_decimals(desnivell_positiu($latitud,$longitud,$lat_destino,$lng_destino,$samples))." m";
                echo "<br>";
                echo "<b>Desnivell negatiu acumulat:</b> ".arrodonir_dos_decimals(desnivell_negatiu($latitud,$longitud,$lat_destino,$lng_destino,$samples))." m";
                echo "<br>";
                echo "<b>Altitud màxima:</b> ".arrodonir_dos_decimals(altitud_maxima($latitud,$longitud,$lat_destino,$lng_destino,$samples))." m";
                echo "<br>";
                echo "<b>Altitud mínima:</b> ".arrodonir_dos_decimals(altitud_minima($latitud,$longitud,$lat_destino,$lng_destino,$samples))." m";
                echo "<br>";
                echo "<b>Percentatge desnivell mig:</b> ".arrodonir_dos_decimals(angle_desnivell_mig($latitud,$longitud,$lat_destino,$lng_destino,$samples))." º";
            ?>
        </div>
        <div id="info_desti">
            <h2>Destí</h2>
            
            <?php
                echo "<b>".strtoupper($nom_destinacio)."</b>";
                echo "<br>";
                echo "<b>Direcció:</b> ".utf8_decode($classe_location_desti->direccio);
                echo "<br>";
                echo "<b>Localitat:</b> ".utf8_decode($classe_location_desti->localitat);
                echo "<br>";
                echo "<b>Província:</b> ".utf8_decode($classe_location_desti->provincia);
                echo "<br>";
                echo "<b>Altitud:</b> ".arrodonir_dos_decimals($classe_location_desti->altitud)." m";
                
            ?>
        </div>
        
    </body>
</html>
