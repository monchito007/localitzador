<?php
//FUNCIONS

function arrodonir_dos_decimals($valor) { 
   
    $float_redondeado=round($valor * 100) / 100; 
    
    return $float_redondeado; 
    
}

//Funció que substitueix els espais en blanc d'una cadena per simbols de sumar +
function comprova_espais($phrase){
    
    for($i=0;$i<strlen($phrase);$i++){
	if($phrase[$i] == " "){
                $phrase[$i] = "+";		
	}
    }
    return $phrase;
}

//Funció que substitueix els espais en blanc d'una cadena per simbols de sumar %20
function comprova_espais_html($phrase){
    
    $phrase = str_replace (" ", "%20", $phrase);
        
    return $phrase;
    
}

//Funció per convertir una distància en format string a numèric. ej -> "2.2 km", "22 m"
function convertir_distancia_metres2($dist){
    
    $array_dist = array();
    
    //separem l'string
    $array_dist = explode(" ", $dist);
    
    //Obtenim la distancia
    $dist = $dist_array[0]*1000;
    
    //Si son Kilometres convertim la unitat a metres.
    if(($array_dist[1]=="km")||($array_dist[1]=="kms")){
        
        $dist = $dist_array[0]*1000;
        
    }
    
    return $dist;
    
}

//Funció per convertir les unitats de distància obtingudes
function unitats_distancia($dist,$unit){
    
    //Pasem la distància al Sistema Internacional de Mesura
    if ($unit=="K"){
        if ($dist<1){
            $res = intval($dist*1000)." Metres";
        }else if (($dist>1)&&($dist<2)){
            $res = intval($dist)." Km";
        }else{
            $res = intval($dist)." Kms";
        }
    }
    
    //Pasem la distància al Sistema Mètric Americà
    if ($unit=="M"){
        if ($dist<1){
            $res = intval($dist*1000)." Yards";
        }else if (($dist>1)&&($dist<2)){
            $res = intval($dist)." Mile";
        }else{
            $res = intval($dist)." Miles";
        }
    }
    
    return $res;
    
}

//Funció que calcula la distància entre 2 coordenades.
function distance($lat1, $lon1, $lat2, $lon2, $unit) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);
    
    if ($unit == "K") {
        //return ($miles * 1.609344);
        return unitats_distancia(($miles * 1.609344),$unit);
    }else if ($unit == "M"){
        //return ($miles * 0.8684);
        return unitats_distancia(($miles * 0.8684),$unit);
    }else{
        return $miles;
    }

}

//Funció que calcula la distància entre 2 coordenades.
function distance_numeric($lat1, $lon1, $lat2, $lon2, $unit) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);
    
    /*
    echo "<br>miles->".$miles;
    echo "<br>lat1->".$lat1;
    echo "<br>lon1->".$lon1;
    echo "<br>lat2->".$lat2;
    echo "<br>lon2->".$lon2;
    echo "<br>unit->".$unit;
    */
    
    if ($unit == "K") {
        
        return ($miles * 1.609344);
        
    }else if ($unit == "M"){
        
        return ($miles * 0.8684);
        
    }else if ($unit == "metres"){
        
        return (($miles * 1.609344)*100);
        
    }

}

//Funció que converteix text a minÃºscules i junta les dos paraules de cerca obtingudes i forma la direccio de la cerca
function url_cerca($place,$location,$provincia,$cAutonoma){
    
    $place = strtolower($place);
    $location = strtolower($location);
    $cAutonoma = strtolower($cAutonoma);
    $provincia = strtolower($provincia);
    
    //$place = convertir_string_a_html($place);
    //$location = convertir_string_a_html($location);
    //$cAutonoma = convertir_string_a_html($cAutonoma);
    //$provincia = convertir_string_a_html($provincia);
    
    $place = comprova_espais($place);
    $location = comprova_espais($location);
    $cAutonoma = comprova_espais($cAutonoma);
    $provincia = comprova_espais($provincia);
    
    return $place."+".$location."+".$provincia."+".$cAutonoma;
     
}

//Funció per parsejar les coordenades i poder afegir-les a la url de cerca.
function parse_coord($lat,$lng){
    
    return (string)($lat.",".$lng);
    
}
//Funció que calcula la distància entre 2 coordenades API Google Directions.
function distance_api_google_directions($lat1, $lon1, $lat2, $lon2, $unit, $mode) {
    
    if ($unit == "K") {
        $units = 'metric';
    }else{
        $units = 'imperial';
    }
    if ($mode == "D") {
        $mode = 'driving';
    }else{
        $mode = 'walking';
    }
    
    $url_ruta = "http://maps.googleapis.com/maps/api/directions/json?origin=$lat1,$lon1&destination=$lat2,$lon2&sensor=true&mode=".$mode."&mode=$units";
    $str_datos = file_get_contents($url_ruta);
    $datos = json_decode($str_datos,true);
    
    //Si hem trobat la distancia la retornem, sinó la calculem en linea recta. 
    if($datos['routes'][0]['legs'][0]['distance']['text']){
        
        return $datos['routes'][0]['legs'][0]['distance']['text'];
    
    }else{
        
        return distance($lat1, $lon1, $lat2, $lon2, $unit);
        
    }
    
    

}

//http://maps.googleapis.com/maps/api/directions/json?origin=41.54517,1.891337&destination=41.593088,1.838234&sensor=true&mode=driving&language=ca

//Funció per parsejar XML's
function parseToXML($htmlStr) 
{ 
$xmlStr=str_replace('<','&lt;',$htmlStr); 
$xmlStr=str_replace('>','&gt;',$xmlStr); 
$xmlStr=str_replace('"','&quot;',$xmlStr); 
$xmlStr=str_replace("'",'&apos;',$xmlStr); 
$xmlStr=str_replace("&",'&amp;',$xmlStr); 
return $xmlStr; 
}

//Funció per substituir apostrofs per a executar sentencies sql
function sql_apostrofs($phrase){
        
    $phrase = str_replace ("'", "\'", $phrase);
        
    return $phrase;
        
    
}

//Funció per calcular el nombre de samplers a utilitzar en la grafica de desnivell
function obtenir_samples_distancia($dist){
    
    $distance = explode(" ", $dist);
    
    //Convertim el valor a enter
    $distance[0] = intval($distance[0]);
    
    //Nombre inicial de samples
    $samples=0;
    
    if($distance[1]=="km"){
        
        if($distance[0]==0){$distance[0]*100;}
        
        $samples = $distance[0]*200;
        
    }
    
    if($samples<3){$samples=20;}
    
    if($samples>100){$samples=100;}
    
    return $samples;
    
}

//Funció per calcular el desnivell positu entre 2 punts
function desnivell_positiu($lat_origen,$lng_origen,$lat_destino,$lng_destino,$samples){
    
    //url per obtenir l'elevació entre dos punts
    $url_elevation = "http://maps.googleapis.com/maps/api/elevation/json?path=".$lat_origen.",".$lng_origen."|".$lat_destino.",".$lng_destino."&samples=".$samples."&sensor=true";
    
    //Llegim la url de l'arxiu JSON
    $str_datos = file_get_contents($url_elevation);
    $datos = json_decode($str_datos,true);
    
    $desnivell = 0;
    
    // Recorrem l'array obtingut i creem l'arxiu XML
    for($x=1;$x<count($datos['results']);$x++){
        
        $elevacio1 = $datos['results'][$x-1]['elevation'];
        $elevacio2 = $datos['results'][$x]['elevation'];
        
        if($elevacio1<$elevacio2){
            $desnivell = $desnivell+($elevacio2-$elevacio1);
        }
        
    }
    
    return $desnivell;
    
}
//Funció per calcular el desnivell negatiu entre 2 punts
function desnivell_negatiu($lat_origen,$lng_origen,$lat_destino,$lng_destino,$samples){
    
    //url per obtenir l'elevació entre dos punts
    $url_elevation = "http://maps.googleapis.com/maps/api/elevation/json?path=".$lat_origen.",".$lng_origen."|".$lat_destino.",".$lng_destino."&samples=".$samples."&sensor=true";
    
    //Llegim la url de l'arxiu JSON
    $str_datos = file_get_contents($url_elevation);
    $datos = json_decode($str_datos,true);
    
    $desnivell = 0;
    
    // Recorrem l'array obtingut i creem l'arxiu XML
    for($x=1;$x<count($datos['results']);$x++){
        
        $elevacio1 = $datos['results'][$x-1]['elevation'];
        $elevacio2 = $datos['results'][$x]['elevation'];
        
        if($elevacio1>$elevacio2){
            $desnivell = $desnivell+($elevacio2-$elevacio1);
        }
        
    }
    
    return $desnivell;
    
}

//Funció per buscar el punt mes alt entre 2 punts
function altitud_maxima($lat_origen,$lng_origen,$lat_destino,$lng_destino,$samples){
    
    //url per obtenir l'elevació entre dos punts
    $url_elevation = "http://maps.googleapis.com/maps/api/elevation/json?path=".$lat_origen.",".$lng_origen."|".$lat_destino.",".$lng_destino."&samples=".$samples."&sensor=true";
    
    //Llegim la url de l'arxiu JSON
    $str_datos = file_get_contents($url_elevation);
    $datos = json_decode($str_datos,true);
    
    $limit = $datos['results'][0]['elevation'];
    
    // Recorrem l'array obtingut i creem l'arxiu XML
    for($x=1;$x<count($datos['results']);$x++){
        
        $elevacio = $datos['results'][$x]['elevation'];
        
        if($limit<$elevacio){
            
            $limit = $elevacio;
            
        }
        
    }
    
    return $limit;
    
}
//Funció per buscar el punt mes baix entre 2 punts
function altitud_minima($lat_origen,$lng_origen,$lat_destino,$lng_destino,$samples){
    
    //url per obtenir l'elevació entre dos punts
    $url_elevation = "http://maps.googleapis.com/maps/api/elevation/json?path=".$lat_origen.",".$lng_origen."|".$lat_destino.",".$lng_destino."&samples=".$samples."&sensor=true";
    
    //Llegim la url de l'arxiu JSON
    $str_datos = file_get_contents($url_elevation);
    $datos = json_decode($str_datos,true);
    
    $limit = $datos['results'][0]['elevation'];
    
    // Recorrem l'array obtingut i creem l'arxiu XML
    for($x=1;$x<count($datos['results']);$x++){
        
        $elevacio = $datos['results'][$x]['elevation'];
        
        if($limit>$elevacio){
            
            $limit = $elevacio;
            
        }
        
    }
    
    return $limit;
    
}

//Funció per calcular l'angle de desnivell mig entre 2 punts
function angle_desnivell_mig($lat_origen,$lng_origen,$lat_destino,$lng_destino,$samples){
    
    //Array per guardar els angles.
    $llista_percentatges = array();
    
    //url per obtenir l'elevació entre dos punts
    $url_elevation = "http://maps.googleapis.com/maps/api/elevation/json?path=".$lat_origen.",".$lng_origen."|".$lat_destino.",".$lng_destino."&samples=".$samples."&sensor=true";
    
    //Llegim la url de l'arxiu JSON
    $str_datos = file_get_contents($url_elevation);
    $datos = json_decode($str_datos,true);
    
    // Recorrem l'array obtingut i creem l'arxiu XML
    for($x=1;$x<count($datos['results']);$x++){
        
        //Obtenim la elevació entre el primer i el segon punt.
        $elevacio1 = $datos['results'][$x-1]['elevation'];
        $elevacio2 = $datos['results'][$x]['elevation'];
        
        //Obtenim la elevació entre el primer i el segon punt.
        $lat1 = $datos['results'][$x-1]['location']['lat'];
        $lng1 = $datos['results'][$x-1]['location']['lng'];
        $lat2 = $datos['results'][$x]['location']['lat'];
        $lng2 = $datos['results'][$x]['location']['lng'];
        
        //Calculem la X i la Y
        $distancia = (distance_numeric($lat1, $lng1, $lat2, $lng2, "K")*100);
        $elevacio = $elevacio2-$elevacio1;
        
        //Guardem la llista d'angles
        $llista_percentatges[$x-1]=(($elevacio/$distancia)*100);
        
    }
    
    $suma_angles = 0;
    
    for($x=0;$x<count($llista_percentatges);$x++){
        
        //echo "<br>desnivell ".$x." -> ".$llista_percentatges[$x];
        
        $suma_angles += $llista_percentatges[$x];
        
    }
    
    return $suma_angles/$samples;
    
}

//Funció per convertir un string a format d'URL amigable.
 function convertir_string_a_html($phrase){
        
        $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'à', 'è', 'ì', 'ò', 'ù', "'");

        $repl = array('a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u', "\'");

        $phrase = str_replace ($find, $repl, $phrase);
        
        $find = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'À', 'È', 'Ì', 'Ò', 'Ù','Ü');

        $repl = array('a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u','u');

        $phrase = str_replace ($find, $repl, $phrase);
        
        return $phrase;
}

//Funció que comproba si un lloc ja està registrat en la BBDD i retornal la seva id o 0 si el resultat es False.
function comprova_llocs_registrats($con,$nom,$localitat){
    
    $id = 0;
    
    //String per obtenir el missatge d'error.
    $error="";
    
    try{
        //Formem la query per comprovar el nom del lloc i la localitat en la BBDD
        $query = "SELECT a.id FROM llocs as a, municipios as b WHERE a.nom='".$nom."' AND b.municipio='".$localitat."' AND a.municipios_id=b.id";

        //echo $query;

        //Executem la consulta
        $res = $con->executarConsulta($query,$error);

        if($res){

            $id = $res[0]['id'];

        }

        return $id;
        
    }catch (Exception $e){
        
        echo "<p>Error comprobant el lloc a la llista de llocs registrats.</p>";
        
    }
        
        
}

//Funció per modificar les poblacions amb articles ej->Hospitalet de Llobregat (L\') a L'Hospitalet de Llobregat.
function sub_articles($phrase){
    
        $array_parts = array();

        $array_parts = explode(" (", $phrase);

        $array_parts[1] = str_replace(")", "", $array_parts[1]);

        $conte_apostrof = strpos($array_parts[1],"'");

        if($conte_apostrof){

            $phrase = $array_parts[1]."".$array_parts[0];

        }else{

            $phrase = $array_parts[1]." ".$array_parts[0];

        }

        return $phrase;
    
}
//Funció per modificar les poblacions amb articles ej->Hospitalet de Llobregat (L\') a Hospitalet de Llobregat.
function sub_articles2($phrase){
    
        $array_parts = array();

        $array_parts = explode(" (", $phrase);

        $array_parts[1] = str_replace(")", "", $array_parts[1]);

        $phrase = $array_parts[0];

        return $phrase;
    
}
//Funció per obtenir id's de la BBDD
//Obtenim el id ($conexió,$valor,$camp_taula,$nom_taula)
function obtenir_id_BBDD($con,$valor,$camp,$nom_taula){
    
    //echo "<br>".$valor;
    //echo "<br>".$camp;
    //echo "<br>".$nom_taula;
    
    
    try{
        
        //Comprobem si té articles i formem el nom de la localitat de forma correcta
        if(strpos($valor," (")){

            $valor = sub_articles($valor);

        }

        //Substituim els apostrofs per a que no falli la sentencia SQL
        $valor = str_replace ("'", "\'", $valor);

        
        $id = 0;

        //String per obtenir el missatge d'error.
        $error="";

        //Formem la query per obtenir la id de la BBDD
        //$query = "SELECT id FROM ".$nom_taula." WHERE ".$camp." like '".$valor."'";
        $query = "SELECT id FROM ".$nom_taula." WHERE ".$camp." like '".$valor."'";

        //echo $query;
        
        //Executem la consulta
        $res = $con->executarConsulta($query,$error);

        if($res){

            $id = $res[0]['id'];

        }

        return $id;
        
    }catch(Exception $e){}
}
//Funció per obtenir id's de la BBDD a través de coordenades
//Obtenim el id ($conexió,$valor,$camp_taula,$nom_taula)
function obtenir_id_lloc_BBDD($con,$lat,$lng){
    
    $id = 0;
    
    //String per obtenir el missatge d'error.
    $error="";
    
    try{
        //Formem la query per obtenir la id de la BBDD
        $query = "SELECT id FROM llocs WHERE latitud=".$lat." AND longitud=".$lng;

        //Executem la consulta
        $res = $con->executarConsulta($query,$error);

        if($res){

            $id = $res[0]['id'];

        }

        return $id;
    
    }catch(Exception $e){
        
        echo "<p>Error obtenint el id del lloc.<p>";
        
    }
        
}

//Funció per afegir llocs en la BBDD
function afegir_lloc_BBDD($con,$array_lloc){
    
    //Creem un objecte de l'entitat llocs
    $eLlocs = new entLlocs();

    //Formem la Query
    $query = $eLlocs->get_insert_cmd($array_lloc);
    
    //echo $query;
    
    //echo $query;
    $error = "";
    
    //Executem la consulta
    $con->executarConsulta($query,$error);
    
}

//Funció per comprobar si un lloc està recomanat o no, si es false torna 0, si es true torna el numero de vegades que ha sigut recomanat.
function lloc_recomanat($nom_lloc){
    
    $con = new connexio();
    
    //String per obtenir el missatge d'error.
    $error="";
    
    //Formem la query per obtenir la id de la BBDD
    $query = "SELECT count(llocs_id) as 'num_recomanacions' FROM recomanacions as a, llocs as b WHERE  a.llocs_id=b.id AND b.nom = '".sql_apostrofs($nom_lloc)."'";
    
    $num_recomanacions = 0;
    
    //Executem la consulta
    $res = $con->executarConsulta($query,$error);
    
    if($res){
        
        $num_recomanacions = $res[0]['num_recomanacions'];
        
    }
    
    $con->tancarConnexio();
    
    return $num_recomanacions;
    
}

function afegir_recomanacio_BBDD($con,$id_lloc,$id_usuari){
    
    //Creem un objecte de l'entitat Recomanacions
    $eRecomanacions = new entRecomanacions();
    
    try{
    
    //Comprobem que les id's no siguin 0.
    if(($id_lloc!=0)&&($id_usuari!=0)){
        
        //Former l'array de dades.
        $array_recomanacio = array(

            "id_lloc"=>$id_lloc,
            "id_usuari"=>$id_usuari

        );
        
        //Formem la query
        $query = $eRecomanacions->get_insert_cmd($array_recomanacio);

        $error = "";

        //Executem la cosulta.
        $con->executarConsulta($query,$error);
        
    }
    
    }catch(Exception $e){
        
        echo "<p>Error afegint la recomanació en la base de dades.</p>";
        
    }
    
    
}
function afegir_comentari_BBDD($con,$id_lloc,$id_usuari,$comentari){
    
    //Creem un objecte de l'entitat Recomanacions
    $eComentaris = new entComentaris();
    
    //Former l'array de dades.
    $array_comentari = array(
                
        "id_lloc"=>$id_lloc,
        "id_usuari"=>$id_usuari,
        "comentari"=>$comentari
        
    );
    
    //Formem la query
    $query = $eComentaris->get_insert_cmd($array_comentari);
    
    //echo $query;
    
    $error = "";
    try{
        
        //Executem la cosulta.
        $con->executarConsulta($query,$error);
    
    }catch(Exception $e){
        
        echo "<p>Error afegint el comentari en la base de dades.</p>";
        
    }
    
}
//Funció per separar les parts d'una direcció en format string.
function split_direccio($direccio){
    
    $array_direccio = array();
    
    $array_direccio = explode(", ", $direccio);
    
    //return $array_direccio[0].", ".$array_direccio[1];
    return $array_direccio[0];
    
}
//Funció per separar les parts d'una direcció en format string.
function split_direccio2($direccio){
    
    $array_direccio = array();
    
    $array_direccio = explode(", ", $direccio);
    
    //return $array_direccio[0].", ".$array_direccio[1];
    return $array_direccio[0].", ".$array_direccio[1];
    
}

 //Funció per dibuixar el menú
function menu(){
            
    $file = "templates/menu.php";
    $len_file = filesize($file);
    $file_menu = fopen("templates/menu.php", "r");
    $contingut = fread($file_menu,$len_file);
    fclose($file_menu);
    print_r($contingut); 
            
}

//Funció per convertir una distància en format string a numèric. ej -> "2.2 km", "22 m"
function convertir_distancia_metres($distance){
    
    $dades_distancia = array();
    $dades_num = array();
    
    //Separem les dades en un array. (ej. 0,8 Km , 333 m)
    $dades_distancia = explode(" ",$distance,2);
     
    if($dades_distancia[1]=="m"){
        
        return $dades_distancia[0]*1;
        
    }else{
        
        $dades_num = explode(",",$dades_distancia[0],2);
        
        return ($dades_num[0]*1000 + $dades_num[1]*100);
        
    }
    
}
//Funció per calcular el desnivell entre dos punts utilitzant el teorema del sinus.
function calcula_desnivell($y_inicial,$y_final,$x){
    
    //Calculem la diferència d'altitud.
    $y = ($y_final-$y_inicial);
    
    //Distancia X
    $x = number_format($x, 2);
    
    //return $y/$x;
    
    return atan($y/$x);
    
}

//Funció per enviar les dades de les cerques en format JSON al Webservice.
function webservice_json($cerca,$municipios_id,$provincias_id,$regiones_id){
    
    $url =  "http://174.129.192.167/webservice/set_values/"; 
    
    //Modifiquem els apostrofs per a que la sentencia SQL no falli
    $cerca = sql_apostrofs($cerca);
    $municipios_id = sql_apostrofs($municipios_id);
    $provincias_id = sql_apostrofs($provincias_id);
    $regiones_id = sql_apostrofs($regiones_id);
    
    if(($cerca)&&($municipios_id)&&($provincias_id)&&($regiones_id)){
    
        $parametros_post = array(
            "cerca" => $cerca,
            "municipios_id" => $municipios_id,
            "provincias_id" => $provincias_id,
            "regiones_id" => $regiones_id,
        );

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                //'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                //'content' => http_build_query($data),
                'content' => json_encode($parametros_post),
            ),
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        //var_dump($result);
    
    }
    
}


?>
