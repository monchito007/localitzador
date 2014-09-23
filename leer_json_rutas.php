<?php
session_start();
//Include de Funcions PHP
include("functions/functions.php");


//VARIABLES DE SESSIÓ
//$lat_origen = $_SESSION["lat_origen"];
//$lng_origen = $_SESSION["lng_origen"];
$localitat = $_SESSION["localitat"];
$direccio = $_SESSION["direccio"];

//Obtenim les coordenades del destí
$lat_destino = $_REQUEST["lat_destino"];
$lng_destino = $_REQUEST["lng_destino"]; 
$lat_origen = $_REQUEST["lat_origen"];
$lng_origen = $_REQUEST["lng_origen"]; 
$travel_mode = $_REQUEST["travel_mode"];
/*
if(isset($_SESSION["lat_destino"])){
    
    $lat_destino = $_SESSION["lat_destino"];
    
}
if(isset($_SESSION["lng_destino"])){
    
    $lng_destino = $_SESSION["lng_destino"];
    
}
if(isset($_SESSION["lat_origen"])){
    
    $lat_origen = $_SESSION["lat_origen"];
    
}
if(isset($_SESSION["lng_origen"])){
    
    $lng_origen = $_SESSION["lng_origen"];
    
}
*/
/*
echo $lat_destino.",".$lng_destino;
echo "<br>";
echo $lat_origen.",".$lng_origen;
*/
//api google
$api="AIzaSyCQt49pKh4iZvkowhRNHLXCXV5n8TaTPw0";

//url per cercar una ruta a google directions.
//$url_ruta = "http://maps.googleapis.com/maps/api/directions/json?origin=41.54517,1.891337&destination=41.593088,1.838234&sensor=true&mode=driving&language=ca";
$url_ruta = "http://maps.googleapis.com/maps/api/directions/json?origin=".parse_coord($lat_origen,$lng_origen)."&destination=".parse_coord($lat_destino,$lng_destino)."&sensor=false&mode=".strtolower($travel_mode);
//$url_ruta = "http://maps.googleapis.com/maps/api/directions/json?origin=".$latitud.",".$longitud."&destination=".$lat_destino.",".$lng_destino."&sensor=false&mode=driving";
// Lee el fichero en una variable,
// y convierte su contenido a una estructura de datos
$str_datos = file_get_contents($url_ruta);
$datos = json_decode($str_datos,true);


/*
echo '<information>';
echo '<info ';
echo 'distancia="' . $datos['routes'][0]['legs'][0]['distance']['text'] . '" ';
echo 'duracio="' . $datos['routes'][0]['legs'][0]['duration']['text'] . '" ';
echo 'direccio_inici="' . $datos['routes'][0]['legs'][0]['start_address'] . '" ';
echo 'direccio_desti="' . $datos['routes'][0]['legs'][0]['end_address'] . '" ';
echo '/>';
echo '</information>';
*/


//Creem un XML amb les dades.

header("Content-type: text/xml");


// Start XML file, echo parent node
echo '<steps>';

//$array_json = array();

// Recorrem l'array obtingut i creem l'arxiu XML
for($x=0;$x<count($datos['routes'][0]['legs'][0]['steps']);$x++){

    //Obtenim les coordenades de cada punt.
    $lat_indicacio = $datos['routes'][0]['legs'][0]['steps'][$x]['end_location']['lat'];
    $lng_indicacio = $datos['routes'][0]['legs'][0]['steps'][$x]['end_location']['lng'];

    $url_elevation = "http://maps.googleapis.com/maps/api/elevation/json?locations=".parse_coord($lat_indicacio,$lng_indicacio)."&sensor=true";

    // Lee el fichero en una variable,
    // y convierte su contenido a una estructura de datos
    $str_datos_elevation = file_get_contents($url_elevation);
    $datos_elevation = json_decode($str_datos_elevation,true);

    //Obtenim l'altitud i dexem només dos decimals.
    $altitud = number_format($datos_elevation['results'][0]['elevation'],2)*1;
    
    //echo $str_datos_elevation;
    
    /*
    $array_json['steps'][$x]['metres']=convertir_distancia_metres($datos['routes'][0]['legs'][0]['steps'][$x]['distance']['text']);
    $array_json['steps'][$x]['altitud']=$altitud;
    $array_json['steps'][$x]['distancia']=$datos['routes'][0]['legs'][0]['steps'][$x]['distance']['text'];
    $array_json['steps'][$x]['lat']=$datos['routes'][0]['legs'][0]['steps'][$x]['end_location']['lat'];
    $array_json['steps'][$x]['lng']=$datos['routes'][0]['legs'][0]['steps'][$x]['end_location']['lng'];
    $array_json['steps'][$x]['temps']=$datos['routes'][0]['legs'][0]['steps'][$x]['duration']['text'];
    $array_json['steps'][$x]['desnivell']=calcula_desnivell($altitud_anterior,$altitud,convertir_distancia_metres($datos['routes'][0]['legs'][0]['steps'][$x]['distance']['text']));
    $array_json['steps'][$x]['duracio']=$datos['routes'][0]['legs'][0]['duration']['text'];
    $array_json['steps'][$x]['distancia_total']=$datos['routes'][0]['legs'][0]['distance']['text'];
    */
    
    
      
    // ADD TO XML DOCUMENT NODE
    echo '<step ';
    echo 'metres="' .convertir_distancia_metres($datos['routes'][0]['legs'][0]['steps'][$x]['distance']['text']) . '" ';
    echo 'distancia="' . $datos['routes'][0]['legs'][0]['steps'][$x]['distance']['text'] . '" ';
    echo 'lat="' . $datos['routes'][0]['legs'][0]['steps'][$x]['end_location']['lat'] . '" ';
    echo 'lng="' . $datos['routes'][0]['legs'][0]['steps'][$x]['end_location']['lng'] . '" ';
    echo 'altitud="' . $altitud . '" ';
    $indicacio = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $datos['routes'][0]['legs'][0]['steps'][$x]['html_instructions']);
    $indicacio = str_replace ("<", "", $indicacio);
    //echo 'incicacio="' . $indicacio . '" ';
    echo 'temps="' . $datos['routes'][0]['legs'][0]['steps'][$x]['duration']['text'] . '" ';
    echo 'desnivell="' . calcula_desnivell($altitud_anterior,$altitud,convertir_distancia_metres($datos['routes'][0]['legs'][0]['steps'][$x]['distance']['text'])) . '" ';
    echo 'duracio="' . $datos['routes'][0]['legs'][0]['duration']['text'] . '" ';
    echo 'distancia_total="' . convertir_distancia_metres($datos['routes'][0]['legs'][0]['distance']['text']) . '" ';
    echo '/>';
    //}
  
  $altitud_anterior = $altitud;
  
}

//header('Content-type: application/json');

//echo json_encode($array_json);

// End XML file
echo '</steps>';







?>