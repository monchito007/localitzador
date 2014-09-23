<?php
session_start();
?>
<?php
/* Projecte Desenvolupament d'Aplicacions Web.
 * 
 * Fitxer: previsio_meteo.php
 * 
 * Autor: Moisés Aguilar Miranda
 * Curs: 2012/2013
 * 
 * Descripció: Fitxer amb la previsió meteorològica de forma extesa.
 */
?>
<?php
//Include Objecte API Yahoo Weather
include("functions/class.xml.parser.php");
include("functions/class.weather.php");
//Classe Location amb la que obtenim les dades de la localitat a través de les coordenades.
include("functions/class.location.php");
//Include de Funcions PHP
include("functions/functions.php");

//VARIABLES DE SESSIÓ
$latitud = $_SESSION["lat_origen"];
$longitud = $_SESSION["lng_origen"];
$poblacio = $_SESSION["localitat"];
$direccio = $_SESSION["direccio"];
$woeid = $_SESSION["woeid"];

//Si hem obtingut el Woeid creem un objecte weather.
if(isset($woeid)){
    
    $weather = new weather($woeid, 3600, 'c');//Objecte Weather.
    
    $weather->parsecached();
    
}
//Si hem obtingut el nom de la localitat o les coordenades creem un objecte location
if(isset($latitud)&&(isset($longitud))){
    
    $classe_location = new location();

    $coord_string = parse_coord($latitud,$longitud);
    
    $classe_location->location_by_coord($coord_string);

}else if(isset($poblacio)){
    
    $classe_location = new location();

    $classe_location->location_by_name($poblacio);
    
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>Localitzador WEB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href='bootstrap/css/bootstrap.css' rel='stylesheet' type='text/css' media='screen' />
    <link href='css/style.css' rel='stylesheet' type='text/css' media='screen' />
    <!-- Llibreries YUI 3 -->
    <script src="http://yui.yahooapis.com/3.8.0/build/yui/yui-min.js"></script>
    <!-- Google Maps API -->
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
    <!--Funció per llegir XML downloadUrl(); -->
    <script type="text/javascript" src="js/util.js"></script>
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements --> 
    <!--[if lt IE 9]> 
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script> 
    <![endif]--> 

<link href='css/style.css' rel='stylesheet' type='text/css' media='screen' />
  </head>
  <body>
    <!-- Menú -->
    <div id="menu">
        <?php
            include("templates/menu.php");
        ?>
    </div>    
    <!-- Menú -->
    
    <!-- Contenidor -->
    <div class="container">
        
    <!-- Errors Inici de sessió -->
    <section id='div_msg_sessio'>
    <section id='msg_password'></section>
    <section id='missatges_form_sessio'></section>
    </section>
    <!-- Errors Inici de sessió -->
        
<!-- Main hero unit for a primary marketing message or call to action -->
<div class="hero-unit">
        <section id="previsio_meteo">
            <?php
                if(isset($woeid)){
                    //parsejem l'xml de l'api de yahoo weather.
                    $weather->parsecached(); // => RECOMMENDED!
                        // ------------------- 
                        // OUTPUT
                        // -------------------
                        //print "<font face=Arial size=2><b>";
                        // VARIOUS
                        print "<h1>Previsió Meteorològica ".utf8_decode($poblacio)."</h1>";
                        print "<hr>";
                        //print "title: $weather->title<br>";        // Yahoo! Weather - Santiago, CI
                        //print "<h3>Ciutat: $weather->city</h3>";         // Santiago

                        //print "yahoolink: $weather->yahoolink<br>";    // http://us.rd.yahoo.com/dailynews/rss/weather/Santiago__CI/*http://xml.weather.yahoo.com/forecast/CIXX0020_c.html
                        // ACTUAL SITUATION
                        
                        echo "<div id='div_previsio'>";
                        
                        print "<h2>Previsió Actual</h2>";
                        
                       
                        echo "<div id='div_info_previsio'>";
                        
                        print "<p>Temp: $weather->acttemp $weather->unit_temperature</p>";      // 16
                        print "<p>Humitat: $weather->humidity %</p>";
                        print "<p>Altitud: ". arrodonir_dos_decimals($classe_location->altitud) ." m</p>";
                        print "<p>Visibilitat: $weather->visibility $weather->unit_distance</p>";
                        print "<p>Presió Atmosfèrica: $weather->pressure $weather->unit_pressure</p>";
                        print "<p>Velocitat del vent: $weather->wind_speed $weather->unit_speed</p>";
                        print "<p>Temperatura del vent: $weather->wind_chill $weather->unit_temperature</p>";
                        //print "<h3>Última Actualització: $weather->acttime</h3>";      // Wed, 26 Oct 2005 2:00 pm CLDT
                        //print "<h3>imagurl: $weather->imageurl</h3>";     // http://us.i1.yimg.com/us.yimg.com/i/us/nws/th/main_142b.gif
                        //print "<h3>actcode: $weather->actcode</h3>";
                        print "<p>Previsió: $weather->acttext</p>";      // Partly Cloudy
                        //print "<h3>Codi imatge: $weather->actcode</h3>";//image 
                        
                        print "<p>Sortida del sol: $weather->sunrise</p>";      // 6:49 am
                        print "<p>Posta de sol: $weather->sunset</p>";       // 08:05 pm
                        echo "</div>";
                        
                        echo "<div id='div_imatge_previsio'>";
                        print "<img src='images/weather/$weather->actcode.png'>";//image 
                        echo "</div>";
                        
                        echo "</div>";
                        
                        print "<hr>";

                        // Day Forecast day 1
                        
                        echo "<div id='div_previsio_2'>";
                        
                        print "<h2>Previsió avui</h2>";
                        
                        echo "<div id='div_info_previsio'>";
                        
                        print "<p>Dia:     $weather->fore_day1_day</p>";     // Wed
                        print "<p>Data:    $weather->fore_day1_date</p>";    // 26 Oct 2005
                        print "<p>Mínim °C:  $weather->fore_day1_tlow</p>";    // 8
                        print "<p>Máxim °C: $weather->fore_day1_thigh</p>";   // 19
                        print "<p>Previsió:    $weather->fore_day1_text</p>";    // Partly Cloudy
                        //print "<h3>imgcode: $weather->fore_day1_imgcode</h3>"; // 29=Image for partly cloudy
                        //print "<h3>Codi imatge: $weather->fore_day1_imgcode</h3>";//image 
                        
                        echo "</div>";
                        
                        echo "<div id='div_imatge_previsio'>";
                        print "<img src='images/weather/$weather->fore_day1_imgcode.png'>";//image 
                        echo "</div>";
                        
                        echo "</div>";
                        print "<hr>";

                        // Day Forecast day 2
                        echo "<div id='div_previsio_2'>";
                        
                        print "<h2>Previsió demà</h2>";
                        
                        echo "<div id='div_info_previsio'>";
                        
                        print "<p>Dia:     $weather->fore_day2_day</p>";     // Wed
                        print "<p>Data:    $weather->fore_day2_date</p>";    // 26 Oct 2005
                        print "<p>Mínim °C:  $weather->fore_day2_tlow</p>";    // 8
                        print "<p>Máxim °C: $weather->fore_day2_thigh</p>";   // 19
                        print "<p>Previsió:    $weather->fore_day2_text</p>";    // Partly Cloudy
                        //print "<h3>imgcode: $weather->fore_day2_imgcode</h3>"; // 29=Image for partly cloudy
                        //print "<h3>Codi imatge: $weather->fore_day2_imgcode</h3>";//image 
                        
                        echo "</div>";
                        
                        echo "<div id='div_imatge_previsio'>";
                        print "<img src='images/weather/$weather->fore_day2_imgcode.png'>";//image
                        echo "</div>";
                        
                        echo "</div>";

                        print "<hr>";


                } 
            ?>
        </section>     

                
</div>


<!-- Main hero unit for a primary marketing message or call to action -->

<footer>
    <?php
        include("templates/footer.php");
    ?>
</footer>

        
        
    </div>
    <!-- Contenidor -->
      
          <!-- Placed at the end of the document so the pages load faster -->
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
    
  
  </body>
  
</html>