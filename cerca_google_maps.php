<?php
session_start();
//Variable de sessió de la pàgina actual.
$_SESSION['path']='cerca_google_maps.php';
?>
<?php
/* Projecte Desenvolupament d'Aplicacions Web.
 * 
 * Fitxer: cerques_google_maps.php
 * 
 * Autor: Moisés Aguilar Miranda
 * Curs: 2012/2013
 * 
 * Descripció: Fitxer de cerques amb google places api.
 */
?>
<?php 
//Constant amb el nom de l'arxiu per llegir json i convertir a xml.
define(JSON_READ, "leer_json.php");

//Include de Funcions PHP
include("functions/functions.php");

//Include Objecte API Yahoo Weather
include("functions/class.xml.parser.php");
include("functions/class.weather.php");
include("functions/class.location.php");

//Include de Funcions PHP
//include("entitats/class.connection.php");
include("entitats/class.entitats.php");


//VARIABLES DE SESSIÓ
$lat_origen = $_SESSION["lat_origen"];
$lng_origen = $_SESSION["lng_origen"];
$localitat = utf8_decode($_SESSION["localitat"]);
$provincia = utf8_decode($_SESSION["provincia"]);
$cAutonoma = utf8_decode($_SESSION["cAutonoma"]);
$direccio = utf8_decode($_SESSION["direccio"]);



//Creem un objecte Location
    //Obtenim un string amb les coordenades.
    $coord = parse_coord($_SESSION['lat_origen'],$_SESSION['lng_origen']);
    
    //Crear objecte Location
    $classe_location_origen = new location();
    
    //Li passem les coordenades.
    $classe_location_origen->location_by_coord($coord);
    
//Creem un Objecte Weather.
$weather = new weather($classe_location_origen->woeid, 3600, 'c');//Objecte Weather.
//Parsegem les dades del caché.
$weather->parsecached();
    
//Alliberem les dades de l'establiment destí.
//unset($_SESSION["lat_destino"]);
//unset($_SESSION["lng_destino"]); 
//unset($_SESSION["nom"]);
//unset($_SESSION["direccio"]);

//Guardem les dades per fer la cerca.
if(isset($_REQUEST["cerca"])){
    $_SESSION["cerca"] = $_REQUEST["cerca"];
    $cerca = $_SESSION["cerca"];
}
//Si hem fet una nova cerca substituim l'string de cerca.
if(isset($_REQUEST["nova_cerca"])){
    $cerca=$_REQUEST["nova_cerca"];
    $_SESSION["cerca"]=$cerca;
}

if((isset($cerca))&&($cerca!="")){
    //Formem la URL per enviar a leer_json.php i generar l'XML
    //$cerca_formatada = url_cerca($cerca,$localitat,$provincia,utf8_encode($cAutonoma));
    $cerca_formatada = url_cerca($cerca,$classe_location_origen->localitat,$classe_location_origen->provincia,$classe_location_origen->cAutonoma);

    //Guardem la cerca formatada en la variable de sessió.    
    $_SESSION["cerca_formatada"] = $cerca_formatada;
}
echo "cerca_formatada->".$_SESSION["cerca_formatada"];

/*
echo "<br>";
echo "http://localhost:8080/PROJECTE_DAW/leer_json.php?&lat_origen=".$latitud."&lng_origen=".$longitud;
*/

//INICI CODI PHP

//Si les coordenades han sigut obtingudes formem la cadena per situar el mapa en aquesta posició
if((isset($lat_origen))&&(isset($lng_origen))){
    
    //Parsegem les coordenades en format string.
    $coordenades = parse_coord($lat_origen,$lng_origen);
    
}

//unset($_REQUEST["cerca"]);


?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>Localitzador WEB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href='bootstrap/css/bootstrap.css' rel='stylesheet' type='text/css' media='screen' />
    <link href='bootstrap/css/bootstrap-responsive.css' rel='stylesheet' type='text/css' media='screen' />
    <!-- Estils -->
    <link href='css/style.css' rel='stylesheet' type='text/css' media='screen' />
    <!-- Llibreries YUI 3 -->
    <!--<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.8.0/build/cssreset/reset-min.css">-->
    <!--<script type="text/javascript" src="http://yui.yahooapis.com/3.8.0/build/yui/yui-min.js"></script>-->
    <!-- Google Maps API -->
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
    <!--Funció per llegir XML downloadUrl(); -->
    <script type="text/javascript" src="js/util.js"></script>
    <!-- jQuery Mobile 1.3.1 -->
    <!--<script type="text/javascript" src="js/jquery.mobile.min.js"></script>-->
    
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
      <div class="hero-unit" id="principal">
          <div class="row-fluid">
      
         <!-- Resultats -->
        <div id="info_cerca" class="span12">
            <h3 class="lead">Resultats</h3>
            <?php echo "<h1 class='lead'>".ucfirst($_SESSION["cerca"])." (".$classe_location_origen->localitat.")</h1>"; ?>
        </div>
        
        <div id="map_canvas_cerca" class="span12"></div>
        
        <div id="infoPanel" class="span12">
        
        <div id="markerStatus" class="span12"></div>
            <b>Coordenades:</b>
            <div id="info" class="span12"></div>
            <br>
            <b>Direcció ubicació:</b>
            <div id="address" class="span12"></div>
        </div>
        <div id="cerca_google_maps" class="span12">
        <h2>Realitzar una nova cerca</h2>    
        <form id="form_cerca_gmaps" name="form_cerca_gmaps" method="post" action="cerca_google_maps.php">
                
            
            <input type="text" id="nova_cerca" name="nova_cerca" data-provide="typeahead">
            <br>
            <input class='btn btn-primary .btn-large' type='submit' value='Modifica'>
                
        </form>
            
        </div>
        
          
        <div id='div_table_results' class="span12"></div>
        <div id='establiments' class="span12"></div>
        
</div>
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
<script>
    //Funció per prevenir l'enviament del formulari amb el camp de cerca buit.
    document.getElementById('form_cerca_gmaps').onsubmit = function(e){
        
        var valor = document.getElementById('nova_cerca').value;
        
        valor = valor.trim();
        
        if (valor==""){
            
            document.getElementById('nova_cerca').focus();
            return false;
            
        }
        
    };
</script>
<!--Funció per Obtenir les coordenades locals -->
<script type="text/javascript">
  
  //Variable global map (mapa Google Maps V3).
  var map;
  var geocoder = new google.maps.Geocoder();
  var lat_origen = <?php echo $lat_origen; ?>;
  var lng_origen = <?php echo $lng_origen; ?>;
  var infowindow;
  var markers = Array();

    function geocodePosition(pos) {
        geocoder.geocode({
            latLng: pos
        }, function(responses) {
            if (responses && responses.length > 0) {
            updateMarkerAddress(responses[0].formatted_address);
            } else {
            updateMarkerAddress('Cannot determine address at this location.');
            }
        });
        
        //Actualitzem la Llista de resultats.
        var nova_posicio = false;
        
        //Si la latitud o la longitud son diferents es una nova posició
        if((pos.lat()!=lat_origen)||(pos.lng()!=lng_origen)){nova_posicio=true;}
        
        //Si la posició es diferent la guardem.
        //Guardem la nova posicio d'origen.
        if(pos.lat()!=lat_origen){lat_origen = pos.lat();}
        if(pos.lng()!=lng_origen){lng_origen = pos.lng();}
        
        //Si la latitud o la longitud son diferents carreguem l'XML
        //Funció per llegir l'XML generat i llistar els resultats en un div.
        if(nova_posicio==true){
            Llegir_XML();
            //Taula_YUI();
        }
        
    }

    

    function updateMarkerPosition(latLng) {
        document.getElementById('info').innerHTML = [
            latLng.lat(),
            latLng.lng()
        ].join(', ');
        
        
    }

    function updateMarkerAddress(str) {
        document.getElementById('address').innerHTML = str;
    }
  
  //Funció per obtenir la lletra majuscula d'un codi numèric
  function convertir_num_letra(num){
    
    //Pasem l'ID a numèric.
    num = parseInt(num);
    
    //Distancia fins la lletra A en ASCII.
    var distance = 64;
    
    //Calcul de num ASCII.
    var ascii_num = num+distance;
    
    //Convertim el num en un codi ASCII. 
    var letra = String.fromCharCode(ascii_num);
            
    return letra;
    //return (num+distance);
      
  }
  //Funció per crear el link a la pàgina d'informació.
  function crear_link_informacio(lat,lng,nom,direccio){
      
      //return 'informacio.php?lat_destino='+lat+'&lng_destino='+lng+'&nom='+nom+'&direccio='+direccio;
      return 'informacio.php?lat_origen='+lat_origen+'&lng_origen='+lng_origen+'&lat_destino='+lat+'&lng_destino='+lng+'&nom='+nom+'&direccio='+direccio;
      
  }
  
  //Funció per crear la llista de establiments.
  function crearLista(nom,direccio,distancia,lat,lng,id,cont){
    
    //Convertim el numero de id en la seva lletra corresponent
    var lletra = convertir_num_letra(id);
    
    //Modifiquem els apostrofs en format URL.
    //direccio = direccio.replace("'","%27");
    //nom = nom.replace("'","%27");
    
    //Link a la pàgina d'informació.
    var link_informacio = crear_link_informacio(lat,lng,nom,direccio);
    
    link_informacio = link_informacio.replace("'","%27");
    
    if(cont%2==0){
    //Afegim el link al contingut
        var llista_elem = "<div class='span12 list'><a href='"+link_informacio+"'><p><img src='images/markers/"+lletra+"-lv.png'><b>"+nom+"</b><br>"+direccio+"<br></p>Dist: "+distancia+" Km<a></div>";
    }
    else{
        var llista_elem = "<div class='span12'><a href='"+link_informacio+"'><p><img src='images/markers/"+lletra+"-lv.png'><b>"+nom+"</b><br>"+direccio+"<br></p>Dist: "+distancia+" Km<a></div>";
    }
    
    
    //Afegim el contingut al div.
    document.getElementById("establiments").innerHTML += llista_elem;
    
    return;
    
  }
  //Funció per crear els marcadors al mapa.
  function createMarker(name,direccio,latlng,lat,lng,id) {
    
    //Convertim el id en la seva lletra corresponent
    var lletra = convertir_num_letra(id);
    
    
    
    //Creem el marcador
    var marker = new google.maps.Marker({
            position: latlng, 
            map: map, 
            title: name,
            icon:'images/markers/'+lletra+'.png'
    });
    
    
    
    //Activem l'event click del marcador per a que mostri la finestra d'informació del lloc.
    google.maps.event.addListener(marker, "click", function() {
        
        //Link a la pàgina d'informació.
        var link_informacio = crear_link_informacio(lat,lng,name,direccio);
        
        link_informacio = link_informacio.replace("'","%27");
        
      if(infowindow){ 
          
            infowindow.close();
            infowindow = new google.maps.InfoWindow({ content: "<b>"+name+"</b><br>"+direccio+"<br>"+"<a href='"+link_informacio+"'>Mostra informació</a>" });
            infowindow.open(map, marker);
            
      }else{
          
          infowindow = new google.maps.InfoWindow({ content: "<b>"+name+"</b><br>"+direccio+"<br>"+"<a href='"+link_informacio+"'>Mostra informació</a>" });
          infowindow.open(map, marker);
          
      }
      
    });
    
    return marker;
    
  }
    
    //Funció que inicialitza el mapa, els marcadors i el llistat de resultats.
    function initialize() {
    
    //Guardem la posició acutal
    var myLatlng = new google.maps.LatLng(<?php echo $coordenades; ?>);
    
    //Opcions del mapa
    var myOptions = {
      zoom: 14,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    
    //Creem l'objecte Mapa.
    map = new google.maps.Map(document.getElementById("map_canvas_cerca"), myOptions);
    
    //Creem el punt per a la posició local.
    var marker = new google.maps.Marker({
        position: myLatlng,
        title: "<?php echo utf8_decode($direccio); ?>",
        map: map,
        title: 'Ets aquí',
        draggable: true,
        icon:'images/markers/blu-circle.png'
    });
    
    //Funció per llegir l'XML generat i llistar els resultats en un div.
    Llegir_XML();
    
    // Update current position info.
    updateMarkerPosition(myLatlng);
    geocodePosition(myLatlng);

    // Add dragging event listeners.
    google.maps.event.addListener(marker, 'dragstart', function() {
        updateMarkerAddress('Modificant posició...');
    });

    google.maps.event.addListener(marker, 'drag', function() {
        
        updateMarkerPosition(marker.getPosition());
    });

    google.maps.event.addListener(marker, 'dragend', function() {
        
        geocodePosition(marker.getPosition());
        //Funció per llegir l'XML generat i llistar els resultats en un div.
        Llegir_XML();
    });
    
    
}
    function Llegir_XML(){
    
    //Funció que obté les dades dels llocs a través d'un arxiu XML.
    //downloadUrl(<?php //echo "'".JSON_READ."'"; ?>, function(data) {
    //alert('leer_json.php?&lat_origen='+lat_origen+'&lng_origen='+lng_origen);
    
    try{
    
    downloadUrl('leer_json.php?&lat_origen='+lat_origen+'&lng_origen='+lng_origen, function(data) {
      
      //Obtenim les marcadors.
      var markers = data.documentElement.getElementsByTagName("marker");
      
      var latlng = Array();
      
      //Reiniciem la llista.
      document.getElementById("establiments").innerHTML = "";
      
      if(markers.length==0){
          
          document.getElementById("establiments").innerHTML = "No s'ha trobat cap resultat.";
          
      }
      
      
      //Recorrem l'array, obtenim les dades i creem els marcadors i la llista de resultats.
      for (var i = 0; i < markers.length; i++) {
          
          
          
          
          //alert(i);
          
          //Obtenim els resultats.
          var id = markers[i].getAttribute("id");
          var lat = markers[i].getAttribute("lat");
          var lng = markers[i].getAttribute("lng");
          var name = markers[i].getAttribute("name");
          var direccio = markers[i].getAttribute("direccio");
          var distancia = markers[i].getAttribute("distance");
          
          //alert(name+","+direccio+","+distancia+","+lat+","+lng+","+id);
          
          //Creem la posició per al marcador
          latlng[i] = new google.maps.LatLng(lat,lng);
          //Creem el marcador
          markers[i] = createMarker(name, direccio, latlng[i],lat,lng,id);
          //Afegim el lloc a la llista
          crearLista(name, direccio,distancia,lat,lng,id,i);
          
          
       }
       
     });
  
    }catch(error) {
        console.info("No s'han trobat resultats en la cerca.");
    }
  
 }
 
window.onload=initialize;
</script>

