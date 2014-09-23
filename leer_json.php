<?php
session_start();

//Include de Funcions PHP
//include("entitats/class.connection.php");
include("entitats/class.entitats.php");
include("functions/functions.php");
//Classe Location amb la que obtenim les dades de la localitat a través de les coordenades.
include("functions/class.location.php");

//VARIABLES DE SESSIÓ
if(isset($_REQUEST['lat_origen'])){$_SESSION["lat_origen"]=$_REQUEST['lat_origen'];}
if(isset($_REQUEST['lng_origen'])){$_SESSION["lng_origen"]=$_REQUEST['lng_origen'];}

$latitud = $_SESSION["lat_origen"];
$longitud = $_SESSION["lng_origen"];

$localitat = $_SESSION["localitat"];
$direccio = $_SESSION["direccio"];
$cerca_formatada = $_SESSION["cerca_formatada"];

$cerca_formatada = comprova_espais($cerca_formatada);
//api google
$api="AIzaSyCQt49pKh4iZvkowhRNHLXCXV5n8TaTPw0";
$api2="AIzaSyBuWrN29HsISI8gIKWwffUi_9EglCgRjOQ";

//Creem una classe location
//Obtenim un string amb les coordenades.
$coord = parse_coord($latitud,$longitud);
    
//Crear objecte Location
$classe_location_origen = new location();
    
//Li passem les coordenades.
$classe_location_origen->location_by_coord($coord);
    

//url per cercar a google places
$url_query = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=".convertir_string_a_html($cerca_formatada)."&sensor=true&key=".$api;
//$url_query = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=restaurant+Baga+catalunya&sensor=true&key=AIzaSyBuWrN29HsISI8gIKWwffUi_9EglCgRjOQ";

// Lee el fichero en una variable,
// y convierte su contenido a una estructura de datos
$str_datos = file_get_contents($url_query);
$datos = json_decode($str_datos,true);

/*
//Si hem obtingut resultats en la cerca enviem un JSON al Webservice per crear estadístiques.
if(count($datos)>0){
    
    try{
    
    //Obrim una connexió en la BBDD.
    $con = new connexio();

    $error = "";

    $con->obrirConnexio($error);
    
    $localitat = $classe_location_origen->localitat;
    $provincia = $classe_location_origen->provincia;
    $cAutonoma = $classe_location_origen->cAutonoma;

    //Obtenim els id ($conexió,$valor,$camp_taula,$nom_taula).
    $municipios_id = obtenir_id_BBDD($con,$localitat,"municipio","municipios");
    $provincias_id = obtenir_id_BBDD($con,$provincia,"provincia","provincias");
    $regiones_id = obtenir_id_BBDD($con,$cAutonoma,"region","regiones");
    
    //Obtenim la cerca realitzada
    $cerca = $_SESSION["cerca"];
    
    webservice_json($cerca,$municipios_id,$provincias_id,$regiones_id);
    }
    catch (Exception $e) {}
    
}
*/

//Impressió de l'array en forma d'XML.
header("Content-type: text/xml");

// Start XML file, echo parent node
echo '<markers>';


//Array ordre de resultats per distancia.
$array_distancia = array();
//Recorrem l'array per calcular les distancies i poder mostrar l'XML ordenat
for($x=0;$x<count($datos['results']);$x++){
    
    //Calculem la distància entre la posició actual i el lloc trobat
    $array_distancia['id'][$x] = $x;
    $array_distancia['lat'][$x] = $datos['results'][$x]['geometry']['location']['lat'];
    $array_distancia['lng'][$x] = $datos['results'][$x]['geometry']['location']['lng'];
    $distancia = arrodonir_dos_decimals(distance_numeric($latitud, $longitud, $array_distancia['lat'][$x] , $array_distancia['lng'][$x], "K"));
    $array_distancia['distancia'][$x]=$distancia;
    
    $nom = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $datos['results'][$x]['name']);
    $nom = str_replace('"', "'", $nom);
    $array_distancia['nom'][$x]=$nom;
    
    $array_distancia['direccio'][$x]=$datos['results'][$x]['formatted_address'];
    
}

asort($array_distancia['distancia']);


//Recorrem l'array per ordre de distancia ascendent
foreach ($array_distancia['distancia'] as $key => $val) {
    echo '<marker ';
    echo 'id="' . ($array_distancia['id'][$key]+1) . '" ';
    echo 'name="' .$array_distancia['nom'][$key]. '" ';
    echo 'direccio="' . $array_distancia['direccio'][$key] . '" ';
    echo 'lat="' . $array_distancia['lat'][$key] . '" ';
    echo 'lng="' . $array_distancia['lng'][$key] . '" ';
    echo 'distance="' . $array_distancia['distancia'][$key] . '" ';
    echo '/>';
}
// End XML file
echo '</markers>';




?>
