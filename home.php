<?php
/* Projecte Desenvolupament d'Aplicacions Web.
 * 
 * Fitxer: index.php
 * 
 * Autor: Moisés Aguilar Miranda
 * Curs: 2012/2013
 * 
 * Descripció: Fitxer d'inici del localitzador Web.
 */
?>
<?php
//Iniciem les variables de sessió.
session_start();
//Variable de sessió de la pàgina actual.
$_SESSION['path']='home.php';
//Includes
//Classe Location amb la que obtenim les dades de la localitat a través de les coordenades.
include("functions/class.location.php");
//Include Objecte API Yahoo Weather
include("functions/class.xml.parser.php");
include("functions/class.weather.php");
//Arxiu de funcions amb PHP
include("functions/functions.php");
//Connexió a la base de dades.
//include("entitats/class.connection.php");
//include("entitats/class.entitats.php");
//Constant amb el nom del fitxer.
define("FITXER", "home.php");

//Obtenim les coordenades
if(($_REQUEST["lat"]!="")&&($_REQUEST['lng']!="")&&((!isset($_REQUEST["nova_localitat"])))){
    
    //Guardem les coordenades en variables de sessió.
    $_SESSION["lat_origen"] = $_REQUEST["lat"];
    $_SESSION["lng_origen"] = $_REQUEST["lng"];
    
    //Obtenim les coordenades.
    $lat = $_REQUEST["lat"];
    $lng = $_REQUEST["lng"];
    
    //Obtenim un string amb les coordenades.
    $coord = parse_coord($lat,$lng);
    
    //Crear objecte Location
    $classe_location_origen = new location();
    
    //Li passem les coordenades.
    $classe_location_origen->location_by_coord($coord);
    
    //Obtenim el WOEID de la població
    $woeid = $classe_location_origen->woeid;
    
    //Obtenim el nom de la localitat i la direccio.
    $_SESSION["localitat"] = $classe_location_origen->localitat;
    $_SESSION["cAutonoma"] = $classe_location_origen->cAutonoma;
    $_SESSION["provincia"] = $classe_location_origen->provincia;
    $_SESSION["direccio"] = $classe_location_origen->direccio;
    $_SESSION["woeid"] = $classe_location_origen->woeid;
    
    //Creem un Objecte Weather.
    $weather = new weather($woeid, 3600, 'c');//Objecte Weather.
    
    //Parsegem les dades del caché.
    $weather->parsecached();
 
}
//Obtenim el nom de la localitat
if(isset($_REQUEST["nova_localitat"])&&($_REQUEST["nova_localitat"]!="")){
    
    //Guardem el nom de la localitat
    $localitat = $_REQUEST["nova_localitat"];
    
    //Crear objecte Location
    $classe_location_origen = new location();
    
    //Li passem les coordenades.
    $classe_location_origen->location_by_name($localitat);
    
    //Obtenim el WOEID de la població
    $woeid = $classe_location_origen->woeid;
    
    //Guardem les coordenades en variables de sessió.
    $_SESSION["lat_origen"] = $classe_location_origen->latitud;
    $_SESSION["lng_origen"] = $classe_location_origen->longitud;
    $_SESSION["localitat"] = $classe_location_origen->localitat;
    $_SESSION["direccio"] = $classe_location_origen->direccio;
    $_SESSION["provincia"] = $classe_location_origen->provincia;
    $_SESSION["cAutonoma"] = $classe_location_origen->cAutonoma;
    $_SESSION["woeid"] = $classe_location_origen->woeid;
    
    //Creem un Objecte Weather.
    $weather = new weather($woeid, 0, 'c');//Objecte Weather.
    
    //Parsegem les dades del caché.
    $weather->parsecached();
    
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta charset="iso-8859-1">
    <title>Localitzador WEB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href='bootstrap/css/bootstrap.css' rel='stylesheet' type='text/css' media='screen' />
    <link href='bootstrap/css/bootstrap.css' rel='stylesheet' type='text/css' media='screen' />
    <link href='bootstrap/css/bootstrap-responsive.css' rel='stylesheet' type='text/css' media='screen' />
    <link href='css/style.css' rel='stylesheet' type='text/css' media='screen' />
    <!-- Llibreries YUI 3 -->
    <!--<script src="http://yui.yahooapis.com/3.8.0/build/yui/yui-min.js"></script>-->
    <script src="yui3/build/yui/yui-min.js"></script>
    <script type="text/javascript">
    
        //Connexions locals YUI 3
        var YUI_ONLINE_CONF = {},
        YUI_OFFLINE_CONF = {
            base: "yui3/build/",
            combine:0,
            groups: {
                gallery: {
                    base:'yui3-gallery/build/',
                    patterns:  { 'gallery-': {} }
                }
            }
        },
        ONLINE = false; 
        CURRENT_CONF = (ONLINE) ? YUI_ONLINE_CONF : YUI_OFFLINE_CONF;
    
    </script>
    <!-- Google Maps API -->
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true"></script>
    <!-- Llibreries JQuery -->
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.js"></script>
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements --> 
    <!--[if lt IE 9]> 
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script> 
    <![endif]--> 

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
      <div class="hero-unit-fluid" id="principal">
          <div class="row-fluid">
                <?php
                if($weather){
                    echo "<section id='div_localitat' class='span12'>";
                        
                        if($classe_location_origen->localitat){
                            
                            echo "<h3 class='lead'>Buscar a:</h3>";
                            echo "<h3 >".$classe_location_origen->localitat.", ".$classe_location_origen->provincia.", ".$classe_location_origen->cAutonoma."</h1>";
                            echo "<h3 class='lead'>".utf8_decode($classe_location_origen->direccio)."</h3>";
                            
                        }else{
                            
                            echo "<h1 class='lead'>No s'ha trobat cap localitat</h1>";
                            echo "<br>";
                            
                        }
                        echo "<p>Altitud ".arrodonir_dos_decimals($classe_location_origen->altitud)." m</p>";
                        
                    echo "</section>";
                    
                    echo "<section id='div_form_cerca' class='span12'>";
                    
                        echo "<form id='form_buscar' method='post' action='cerca_google_maps.php' >";
                        echo "<input type='text' id='cerca' name='cerca' data-provide='typeahead' autofocus='autofocus' placeholder='Pizza, Farmacia, Hotel, Taller mecánico...'>";
                        echo "<br>";
                        echo "<input class='btn btn-primary .btn-large' type='submit' value='Buscar Establiments'></form>";
                        echo "</form>";
                        echo "<form>";
                        echo "<a href='index.php'><button id='boto_localitzar' class='btn btn-primary'>Localitzar Posició</button></a>";
                        echo "</form>";
                        
                        
                    echo "</section>";
                        
                    }else{
                        //Coloquem el form per a que no falli el javascript en l'onload.
                        echo "<form id='form_buscar' method='post' action='cerca_google_maps.php' >";
                        echo "</form>";
                        
                        echo "<section class='span12 center'>";
                        echo "<h3 class='lead'>Carregant posició..<h3>";
                        echo "</section>";
                        
                        echo "<section id='div_loading' class='span12'>";
                        //echo "<h3 class='label label-important'>Localitzant la posició..<h3>";
                        
                        echo "<div class='span4'>";
                        echo "</div>";
                        echo "<div class='progress-bar blue stripes span4'>";
                        echo "<span></span>";
                        echo "</div>";
                        echo "<div class='span4'>";
                        echo "</div>";
                       
                        echo "</section>";
                        
                    }
                    echo "<input type='hidden' id='coord' name='coord' value='".$classe_location_origen->latitud."'>";
                    ?>
          </div>
        
          
     <!-- Contenidor desplegable -->     
    <div class="container-fluid">
     <div class="accordion" id="accordion2">
            <div class="accordion-group">
              <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                  Selecciona una ubicació manualment
                </a>
              </div>
              <div id="collapseOne" class="accordion-body collapse" style="height: 0px; ">
                <div class="accordion-inner">
                    
                  <!-- Modifica Localitat en YUI -->
                <div id="div_yui_api_google" class="yui3-skin-sam"> <!-- You need this skin class -->
                    <p>
                        <label for="ac-input">Escriu la direcció de la teva ubicació.</label>
                        <input id="ac-input" type="text" data-provide="typeahead"/>
                    </p>
                </div>
                <!-- Modifica Localitat en YUI -->
                </div>
              </div>
            </div>
            <div class="accordion-group">
              <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
                 Selecciona una localitat de la base de dades manualment
                </a>
              </div>
              <div id="collapseTwo" class="accordion-body collapse">
                <div class="accordion-inner">
                    
                  <!-- Modifica Localitat a través de la BBDD -->
                <div id="div_yui_api_google" class="yui3-skin-sam"> <!-- You need this skin class -->
                    <p>
                    <form id="localitat" method="post">
                        <label for="ac-input">Escriu el nom de la localitat que vulguis seleccionar.</label>
                        <input type="text" id="nova_localitat" name="nova_localitat" data-provide="typeahead">
                        <br>
                        <input class='btn btn-primary .btn-large' type='submit' value='Modifica'>
                    </form>
                    </p>
                </div>
                <!-- Modifica Localitat a través de la BBDD -->
                
                </div>
              </div>
            </div>
            <div class="accordion-group">
              <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">
                  Veure mapa local
                </a>
              </div>
              <div id="collapseThree" class="accordion-body collapse">
                <div class="accordion-inner">
                    
                    <!-- Mapa Ubicació -->
                    <div id="div_mapa_local">
                    <div id="infoPanel">
                        <p>Arrossega el marcador per canviar la ubicació</p>
                        <div id="mapCanvas"></div>
                    <div id="markerStatus"></div>
                    <b>Coordenades:</b>
                    <div id="info"></div>
                    <b>Direcció ubicació:</b>
                    <div id="address"></div>
                    </div>
                    
                    </div>
                    <!-- Mapa Ubicació -->
                    
                </div>
              </div>
            </div>
            <div class="accordion-group">
              <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseFour">
                  Informació
                </a>
              </div>
              <div id="collapseFour" class="accordion-body collapse">
                <div class="accordion-inner">
                    
                    <!-- Informació -->
                    <ul>
                        <li>No es garanteixen cerques precises fora de l'estat espanyol.</li>
                        <li>Si no es troba cap resultat en la localitat seleccionada, es mostrarán resultats de les poblacions més properes.</li>
                    </ul>
                    <!-- Informació -->
                    
                </div>
              </div>
            </div>
         
         
         
          </div>
            
    </div>
     <div class="container-fluid">
         <div class="row-fluid">
    <!-- Contenidor desplegable -->
    <?php
        if(isset($classe_location_origen->localitat)){
        //Llistat de Llocs Recomanats
        
        //Creem un objecte connexió
        $con = new connexio();
        $error = "";
        //Iniciem una connexió 
        $con->obrirConnexio($error);
        //Query per llistar els llocs recomanats d'aquesta localitat.
        $query = "SELECT count(c.id) as num, a.id,a.latitud,a.longitud,a.nom,a.carrer,a.direccio,b.municipio ";
        $query .= "FROM llocs as a, municipios as b,recomanacions as c ";
        $query .= "WHERE b.municipio like '".str_replace("'", "\'", $classe_location_origen->localitat)."' AND c.llocs_id=a.id AND b.id=a.municipios_id group by a.id ORDER BY count(c.id) DESC";
    
        //Comprovem el correu.
        $res = $con->executarConsulta($query,$error);
        
        //Tanquem la connexió.
        $con->tancarConnexio();
        
        echo "<section id='recomanacions' class='span12'>";
        echo "<h2>Recomanacions ".$classe_location_origen->localitat."</h2>";
        
        if($res){
            
            for($x=0;$x<count($res);$x++){
                
                //Condicional per donar estil a la llista.
                if($x%2==0){echo "<div class='span12 list'>";}
                else{echo "<div class='span12'>";}
                
                echo "<div class='span6'>";
                echo "<a href='informacio.php?lat_destino=".$res[$x]['latitud']."&lng_destino=".$res[$x]['longitud']."&nom=".comprova_espais_html(str_replace ("'", "%27", $res[$x]['nom']))."&direccio=".comprova_espais_html(str_replace ("'", "%27", $res[$x]['direccio']))."'>";
                echo "<img src='images/icons/star_icon.gif'/>";
                echo "<b>".$res[$x]['nom']."</b><br>".split_direccio($res[$x]['direccio']).", ".$res[$x]['municipio'];
                echo "</a>";
                echo "</div>";
                
                echo "<div class='span3 offset3'>";
                echo "Recomanacions: ".$res[$x]['num'];
                echo "</div>";
                echo "</div>";
                
            }
            
        }
        else{echo "<b class='lead'>No hi han resultats</b>";}
        
        echo "</section>";
        }
        ?>
        </div>
                
      
      <!-- Main hero unit for a primary marketing message or call to action -->
      <hr>
      <footer class="span12">
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
    document.getElementById('form_buscar').onsubmit = function(e){
        
        var valor = document.getElementById('cerca').value;
        
        valor = valor.trim();
        
        if (valor==""){
            
            document.getElementById('cerca').focus();
            return false;
            
        }
        
    };
</script>
<script type="text/javascript">
            //Variable constant per al nom del fitxer.
            var fitxer = "home.php";
            var latitud;
            var longitud;
            
            function localitzacio(position){
                
                //Obtenim les coordenades.
                latitud = position.coords.latitude;
                longitud = position.coords.longitude;
                
                //Recarreguem la pàgina amb les dades per treballar-les amb PHP
                if (document.getElementById("coord").value==""){
                    
                    window.location.href = fitxer+"?lat="+latitud+"&lng="+longitud;
                    
                }
                
            }
            
            //Funció que obté la localització actual a través de l'API de Geolocal·lització.
            //Geolocation API Specification. http://dev.w3.org/geo/api/spec-source.html
            function obtenir_localitzacio(){
               
                if (navigator.geolocation){
                    
                    navigator.geolocation.getCurrentPosition(localitzacio,displayError);
                    
                }else{
                    
                    alert('Tu navegador no soporta la API de geolocalizacion');
                    navigator.geolocation.getCurrentPosition(localitzacio,displayError);
                
                }
                
                
            }
            
            function localitzacio_manual(position){
                
                //Obtenim les coordenades.
                latitud = position.coords.latitude;
                longitud = position.coords.longitude;
                
                //Recarreguem la pàgina amb les dades per treballar-les amb PHP
                window.location.href = fitxer+"?lat="+latitud+"&lng="+longitud;
                
            }
            
            //Funció que obté la localització actual a través de l'API de Geolocal·lització.
            //Geolocation API Specification. http://dev.w3.org/geo/api/spec-source.html
            function obtenir_localitzacio_manual(){
               
                if (navigator.geolocation){
                    
                    navigator.geolocation.getCurrentPosition(localitzacio_manual,displayError);
                    
                }else{
                    
                    alert('Tu navegador no soporta la API de geolocalizacion');
                    navigator.geolocation.getCurrentPosition(localitzacio_manual,displayError);
                
                }
            }
            
            function displayError(error){
                
			var errors = { 
                            
				1: 'Permiso denegado',
				2: 'Posicion no disponible',
				3: 'Timeout'
                                
			};
                        
			alert("Error: " + errors[error.code]);
                        
                        document.getElementById['div_loading'].innerHTML = "<h3 class='lead'>Selecciona una ubicació manualment.</h3>";
                        
            }
            
        window.onload = obtenir_localitzacio;
        //window.document.getElementById("boto_localitzar").onclick = obtenir_localitzacio_manual;
        
        
</script>
<script>
YUI(CURRENT_CONF).use("autocomplete", 'autocomplete-filters', "autocomplete-highlighters", function (Y) {
    
  Y.one('#nova_localitat').plug(Y.Plugin.AutoComplete, {
    resultFilters    : 'phraseMatch',  
    resultHighlighter: 'phraseMatch',
    resultListLocator: 'municipios',
    resultTextLocator: 'name',
    maxResults: 10,
    source: 'json_municipios.php'
    
  });
});
</script>
<script>
//Script en YUI per obtenir les coordenades d'una direcció i recarregar la pàgina.    
YUI().use('autocomplete', function (Y) {
    var acNode = Y.one('#ac-input');

    acNode.plug(Y.Plugin.AutoComplete, {
        // Highlight the first result of the list.
        activateFirstItem: true,

        // The list of the results contains up to 10 results.
        maxResults: 10,

        // To display the suggestions, the minimum of typed chars is five.
        minQueryLength: 5,

        // Number of milliseconds to wait after user input before triggering a
        // `query` event. This is useful to throttle queries to a remote data
        // source.
        queryDelay: 500,

        // Handling the list of results is mandatory, because the service can be
        // unavailable, can return an error, one result, or an array of results.
        // However `resultListLocator` needs to always return an array.
        resultListLocator: function (response) {
            // Makes sure an array is returned even on an error.
            if (response.error) {
                return [];
            }

            var query = response.query.results.json,
                addresses;

            if (query.status !== 'OK') {
                return [];
            }

            // Grab the actual addresses from the YQL query.
            addresses = query.results;

            // Makes sure an array is always returned.
            return addresses.length > 0 ? addresses : [addresses];
        },

        // When an item is selected, the value of the field indicated in the
        // `resultTextLocator` is displayed in the input field.
        resultTextLocator: 'formatted_address',

        // {query} placeholder is encoded, but to handle the spaces correctly,
        // the query is has to be encoded again:
        //
        // "my address" -> "my%2520address" // OK => {request}
        // "my address" -> "my%20address"   // OK => {query}
        requestTemplate: function (query) {
            return encodeURI(query);
        },

        // {request} placeholder, instead of the {query} one, this will insert
        // the `requestTemplate` value instead of the raw `query` value for
        // cases where you actually want a double-encoded (or customized) query.
        source: 'SELECT * FROM json WHERE ' +
                    'url="http://maps.googleapis.com/maps/api/geocode/json?' +
                        'sensor=false&' +
                        'address={request}"',

        // Automatically adjust the width of the dropdown list.
        width: 'auto'
    });

    // Adjust the width of the input container.
    acNode.ac.after('resultsChange', function () {
        var newWidth = this.get('boundingBox').get('offsetWidth');
        acNode.setStyle('width', Math.max(newWidth, 300));
    });

    // Fill the `lat` and `lng` fields when the user selects an item.
    acNode.ac.on('select', function (e) {
        var location = e.result.raw.geometry.location;

        //Y.one('#locationLat').set('text', location.lat);
        //Y.one('#locationLng').set('text', location.lng);
        
        //Carreguem la pàgina amb les noves coordenades
        window.location.href = fitxer+"?lat="+location.lat+"&lng="+location.lng;
        
    });
});

</script>
<script type="text/javascript">
var geocoder = new google.maps.Geocoder();

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
}

function updateMarkerStatus(str) {
  document.getElementById('markerStatus').innerHTML = str;
}

function updateMarkerPosition(latLng) {
  document.getElementById('info').innerHTML = [
    latLng.lat(),
    latLng.lng()
  ].join(', ');
  
  latitud = latLng.lat();
  longitud = latLng.lng();
  
}

function updateMarkerAddress(str) {
  document.getElementById('address').innerHTML = str;
}

function initialize() {
    
  var latLng = new google.maps.LatLng(<?php echo parse_coord($classe_location_origen->latitud,$classe_location_origen->longitud); ?>);
  var map = new google.maps.Map(document.getElementById('mapCanvas'), {
    zoom: 15,
    center: latLng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });

  
  // Update current position info.
  updateMarkerPosition(latLng);
  geocodePosition(latLng);
  
    var marker = new google.maps.Marker({
    position: latLng,
    title: 'Estas aquí',
    map: map,
    draggable: true,
    icon:'images/markers/blu-circle.png'
    });
  
  // Add dragging event listeners.
  google.maps.event.addListener(marker, 'dragstart', function() {
    updateMarkerAddress('Dragging...');    
  });
  
  google.maps.event.addListener(marker, 'drag', function() {
    updateMarkerStatus('Canviant ubicació...');
    updateMarkerPosition(marker.getPosition());
  });
  
  google.maps.event.addListener(marker, 'dragend', function() {
    updateMarkerStatus('Ubicació canviada. Reiniciant pàgina...');
    geocodePosition(marker.getPosition());
    
    //Recarreguem la pàgina amb les dades per treballar-les amb PHP
    window.location.href = fitxer+"?lat="+latitud+"&lng="+longitud;
    
  });
}

// Onload handler to fire off the app.
if ((typeof latitud != 'undefined')&&(typeof longitud != 'undefined')) {
  google.maps.event.addDomListener(window, 'load', initialize);
}
window.onload=initialize;

</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-55087290-1', 'auto');
  ga('send', 'pageview');

</script>