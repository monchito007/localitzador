<?php
include("/var/www/html/localitzadorweb/entitats/class.connection.php");
//include_once realpath(dirname(__FILE__)."../entitats/class.connection.php");  

//Constants per obtenir les dades de la direcció.
define(KEstabliment, 0);
define(KNum_direccio, 1);
define(KDireccio, 2);
define(KLocalitat, 3);
define(KCodi_postal, 3);


class location
{
    
    // ATRIBUTS
    var $localitat;     //Localitat
    var $latitud;       //Latitud
    var $longitud;      //Longitud
    var $woeid;         //Identificador de la població per l'API de Yahoo.
    var $provincia;     //Provincia
    var $cAutonoma;     //Comunitat Autonoma
    var $direccio;      //Direcció
    var $codi_postal;   //Codi Postal
    var $altitud;       //Altitud
    var $coord_string;   //Coordenades en format string.
    //Llistes per a multiples localitats.
    var $llista_localitats = array();   //Llista de dades per a multiples poblacions
    var $llista_lat = array();        //Llista de latituds per a multiples poblacions
    var $llista_lng = array();        //Llista de longituds per a multiples poblacions
    
    
    //Key Yahoo
    var $key_yahoo="ji1OqanV34Ecxczzy5egf0YXElOncKSUBx0EUc2.GK_F8RHKAVCGXR7rH9YZBP_MMQ--";
    
    //Url per obtenir el woeid a través de coordenades amb l'API de yahoo. 
    //var $url_coord="http://where.yahooapis.com/geocode?gflags=R&format=json&location=";
    var $url_coord="http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=";
    
    
    //Url per obtenir les coordenades a través el nom de la població amb l'API de yahoo.
    var $url_name="http://query.yahooapis.com/v1/public/yql?format=json&diagnostics=true&q=";
    
    //URL per obtenir l'altitud de les coordenades amb l'API d'Elevacio de Google.
    var $url_elevation = "http://maps.googleapis.com/maps/api/elevation/json?sensor=true&locations=";
    
    // METODES
    
    //Metode per retornar les coordenades en format String
    function get_coord(){
        
        return $this->latitud.",".$this->longitud;
        
    }
    
    //Metode per retornar el woeid de la localitat.
    function get_woeid(){
        
        return $this->woeid;
        
    }
    
    //metode per substituir apostrofs per a sentencies sql
    function sql_apostrofs($phrase){
        
        $phrase = str_replace ("'", "\'", $phrase);
        
        return $phrase;
        
    }
    
    //Metode que obté el nom de la localitat i busca les coordenades d'aquesta.
    function location_by_name($localitat){
        
        //Guardem el nom de la localitat per buscar-lo.
        $this->localitat = utf8_encode($localitat);
        //Obtenim les coordenades de la localitat
        $this->obtenir_coordenades();
        
        //echo "<br>coordenades->".$this->latitud.",".$this->longitud;
        
        //Guardem l'string de les coordenades.
        $this->coord_string = $this->get_coord();
        //Obtenim l'altitud.
        $this->obtenir_altitud();
        //Cridem al mètode per obtenir les dades de la població.
        $this->obtenir_dades_localitat();
        
    }
    
    //Metode que separa coordenades en format string.
    function converir_string_coord($coord){
        
        //Array per obtenir les coordenades.
        $array_coord = array();
        //Separem l'string en format latitud,longitud.
        $array_coord = explode(",", $coord);
        //Obtenim les coordenades de la localitat
        $this->latitud = $array_coord[0];
        $this->longitud = $array_coord[1];
        
    }
    
    
    
    //Metode per obtenir les dades mitjançant les coordenades en format string.
    function location_by_coord($coord){
        
        //Guardem l'string de les coordenades.
        $this->coord_string = $coord;
        //Separem l'string de coordenades.
        $this->converir_string_coord($coord);
        //Obtenim l'altitud.
        $this->obtenir_altitud();
        //Obtenim totes les altres dades de la localitat.
        $this->obtenir_dades_localitat();
                
    }
    
    //Metode per obtenir l'altitud a través de l'api d'elecació de google.
    function obtenir_altitud(){
        
        try{
            
            //Formem la URL completament.
            //$url_elevation = "http://maps.googleapis.com/maps/api/elevation/json?sensor=true&locations=".parse_coord($lat,$lng);
            $url_elevation = $this->url_elevation.$this->coord_string;
            
            //Obtenim les dades de la URL en format JSON.
            $string_json = file_get_contents($url_elevation);

            //Convertim l'string en format JSON per poder llegir-lo.
            $json = json_decode($string_json,true);

            //Obtenim l'altitud de la localitat.
            $this->altitud = $json['results'][0]['elevation'];
            
        }catch(Exception $e){
            
            echo "<h3 class='lead'>No s'ha pogut obtenir l'altitud</h3>";
            
        }
        
        
    }
    
    //Metode per convertir l'string de la població a format d'URL amigable.
    function convertir_string_a_html($phrase){
        
        $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'à', 'è', 'ì', 'ò', 'ù', "'");

        $repl = array('a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u', "\'");

        $phrase = str_replace ($find, $repl, $phrase);
        
        return $phrase;
    }
    
    //Metode per obtenir les coordenades
    function obtenir_coordenades(){
        
        //Parsejem el nom de la localitat per introduir-lo a la URL.
        $localitat_html = $this->convertir_string_a_html($this->localitat);
        
        //Query per obtenir les dades de la localitat
        $query = "select * from geo.places where text='$localitat_html'";
        
        //Substituïm els espais en blanc per %20
        $query = $cadena = str_replace(" ","%20",$query);
        
        //Formem la URL completament.
        //$url_query = "http://query.yahooapis.com/v1/public/yql?format=json&diagnostics=true&q=".$query;
        $url_query = $this->url_name.$query;
        
        //echo $url_query;
        try{
            //Obtenim les dades de la URL en format JSON.
            $string_json = file_get_contents($url_query);

            //Convertim l'string en format JSON per poder llegir-lo.
            $json = json_decode($string_json,true);

            //Obtenim les coordenades de la localitat.
            $this->latitud = $json['query']['results']['place']['centroid']['latitude'];
            $this->longitud = $json['query']['results']['place']['centroid']['longitude'];

            //Si obtenim més d'un resultat agafem el que sigui d'españa i sinó el primer.
            if(!isset($this->latitud)&&(!isset($this->longitud))){

                //Obtenim les coordenades de la primera localitat.
                $this->latitud = $json['query']['results']['place'][0]['centroid']['latitude'];
                $this->longitud = $json['query']['results']['place'][0]['centroid']['longitude'];

                //Creem una llista amb les possibles localitats
                for($x=0;$x<count($json['query']['results']['place']);$x++){

                    //Emmagatzemem la informació de les localitats
                    $nom = $json['query']['results']['place'][$x]['name'];
                    $nom .= " ".$json['query']['results']['place'][$x]['country']['content'];
                    $nom .= " ".$json['query']['results']['place'][$x]['admin1']['content'];
                    $nom .= " ".$json['query']['results']['place'][$x]['placeTypeName']['content'];
                    //$nom .= " ".$json['query']['results']['place'][$x]['centroid']['latitude'];
                    //$nom .= " ".$json['query']['results']['place'][$x]['centroid']['longitude'];

                    //Emmagatzemem les coordenades de les localitats
                    $lat = $json['query']['results']['place'][$x]['centroid']['latitude'];
                    $lng = $json['query']['results']['place'][$x]['centroid']['longitude'];

                    //Guardem les dades en les llistes de la classe.
                    $this->llista_localitats[$x] = $nom;   
                    $this->llista_lat[$x] = $lat;
                    $this->llista_lng[$x] = $lng;

                }
            }
    
        }catch(Exception $e){
            
            echo "<h3 class='lead'>Error obtenint les dades de geolocalització.</h3>";
            
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
    //Funció per obtenir noms de localitats que contenen els dos idiomes Ej -> Moreda de Álava / Moreda Araba a Moreda de Álava.
    function sub_localitat($phrase){
    
        $array_parts = array();

        $array_parts = explode(" / ", $phrase);

        return $array_parts[0];
    
    }
    
    //Metode per obtenir el woeid de la localitat.
    function obtenir_dades_localitat(){
        
        //URL per obtenir les dades de la localitat a través de les coordenades.
        //$url_location = "http://where.yahooapis.com/geocode?gflags=R&format=json&location=$this->latitud,$this->longitud";
        $url_location = $this->url_coord.$this->coord_string;
        
        //echo "<br>URL Location -> ".$url_location;
        try{
            //Obtenim les dades de la URL en format JSON.
            $string_json = file_get_contents($url_location);

            //Convertim l'string en format JSON per poder llegir-lo.
            $json = json_decode($string_json,true);

            //Obtenim les dades de la localitat 
            for($x=0;$x<count($json['results'][0]['address_components']);$x++){

                    //Agafem la direccó
                    if(($x==0)&&(!$json['results'][0]['address_components'][$x]['types'][0])){

                        $this->direccio = $json['results'][0]['address_components'][$x]['long_name'];

                    }
                    if($json['results'][0]['address_components'][$x]['types'][0]=='street_number'){

                        $numero = $json['results'][0]['address_components'][$x]['long_name'];

                    }
                    if($json['results'][0]['address_components'][$x]['types'][0]=='route'){

                        $this->direccio = $json['results'][0]['address_components'][$x]['long_name'].", ".$numero;

                    }
                    //Agafem la Localitat
                    if($json['results'][0]['address_components'][$x]['types'][0]=='locality'){

                        $this->localitat = $json['results'][0]['address_components'][$x]['long_name'];

                    }
                    //Agafem la Provincia
                    if($json['results'][0]['address_components'][$x]['types'][0]=='administrative_area_level_2'){

                        $this->provincia = $json['results'][0]['address_components'][$x]['long_name'];

                    }
                    //Agafem el Codi Postal
                    if($json['results'][0]['address_components'][$x]['types'][0]=='postal_code'){

                        $this->codi_postal = $json['results'][0]['address_components'][$x]['long_name'];

                    }

            }
        
        //Obtenim lla provincia, la comunitat autonoma i el WOEID de la BBDD 
        
        //Obtenim el nom de la localitat
        $localitat = utf8_decode($this->localitat);
        //Query per obtenir la Comunitat Autonoma de la localitat.
        //$query = "SELECT c.region FROM municipios as a,provincias as b, regiones as c WHERE a.id_provincia=b.id AND b.id_region=c.id AND a.municipio like '".$localitat."%'";
        //Query per obtenir la Comunitat Autonoma de la provincia.
        
        //Si la localitat té articles la modifiquem.
        if(strpos($localitat," (")){
    
             $localitat= $this->sub_articles($localitat);
    
        }
        //Si la localitat té el nom en dos idiomes agafem el primer
        if(strpos($localitat,"/")){
    
             $localitat= $this->sub_localitat($localitat);
    
        }
        
        //Si el nom de la població conté articles afegim % a la cerca en la BBDD
        $query = "SELECT a.municipio,c.region, b.provincia, d.woeid FROM municipios AS a, provincias AS b, regiones AS c, woeid AS d WHERE a.provincias_id=b.id  AND b.regiones_id=c.id AND a.id = d.municipios_id AND a.municipio LIKE '".$this->sql_apostrofs($localitat)."'";
        
        //Creem un objecte connexió
        $con = new connexio();
        //String per capturar l'error
        $error = "";
        //Obrim la connexió
        $con->obrirConnexio($error);
        //Executem la consulta a la base de dades.
        $res = $con->executarConsulta($query,$error);
        //Obtenim les dades de l'array i les guardem en les propietats de la classe.
        
        //Si no hi han resultats potser és perqué la població portaba article.
        if(!$res){
            
            //Si el nom de la població conté articles afegim % a la cerca en la BBDD
            $query = "SELECT a.municipio,c.region, b.provincia, d.woeid FROM municipios AS a, provincias AS b, regiones AS c, woeid AS d WHERE a.provincias_id=b.id  AND b.regiones_id=c.id AND a.id = d.municipios_id AND a.municipio LIKE '%".$this->sql_apostrofs($localitat)."'";
            //Executem la consulta a la base de dades.
            $res = $con->executarConsulta($query,$error);
            
        }
        
        
        //if(strtolower($res[$x]['municipio'])==strtolower($localitat)){
        //if(strpos(strtolower($res[$x]['municipio']),strtolower($localitat))){
            
            $this->localitat = $res[0]['municipio'];
            //if(!isset($this->cAutonoma)){
                    
            $this->cAutonoma = $res[0]['region'];
                    
            //}
            //if(!isset($this->provincia)){
                    
            $this->provincia = $res[0]['provincia'];
                    
            //}
                
            //$this->localitat = $res[0]['municipio'];
            $this->woeid = $res[0]['woeid'];
            
            
        //}    
        
        //Si no es troba la localitat a la BBDD busquem la regió a partir de la provincia
        if (!$res){
            
            $query = "SELECT c.region, b.provincia FROM provincias AS b, regiones AS c WHERE b.regiones_id=c.id AND b.provincia LIKE '".$this->provincia."'";
            
            //Executem la consulta a la base de dades.
            $res = $con->executarConsulta($query,$error);
            
            $this->cAutonoma = $res[0]['region'];
            
        }
        
        //Tanquem la connexió.
        $con->tancarConnexio();
        
        /*
        echo "<br>cAutonoma -> ".utf8_decode($this->cAutonoma)."<br>";
        echo "<br>provincia -> ".utf8_decode($this->provincia)."<br>";
        echo "<br>Direccio -> ".utf8_decode($this->direccio)."<br>";
        echo "<br>codi_postal -> ".utf8_decode($this->codi_postal)."<br>";
        echo "<br>localitat -> ".utf8_decode($this->localitat)."<br>";
        echo "<br>lat -> ".utf8_decode($this->latitud)."<br>";
        echo "<br>lng -> ".utf8_decode($this->longitud)."<br>";
        echo "<br>Altitud -> ".utf8_decode($this->altitud)."<br>";
        echo "<br>Woeid -> ".utf8_decode($this->woeid)."<br>";
        echo "<br>coord_string -> ".utf8_decode($this->coord_string)."<br>";
        */
        
        
    }catch(Exception $e){
        
        echo "<h3 class='lead'>Error obtenint les dades de geolocalització.</h3>";
        
    }
        
    }
    
}
/*

$string = "l'hospitalet";
$string2 = "Olesa de Montserrat";


$coord = "41.545613,1.891494";

//Crear objecte Location
$classe_location = new location();
$classe_location2 = new location();
*/
//$classe_location->location_by_name($string2);
//$classe_location->location_by_coord($coord);
//$classe_location2->location_by_name($string);
//$classe_location->location_by_coord($coord);

/*

echo "<br>Localitat -> ".utf8_decode($classe_location->localitat);
echo "<br>Provincia -> ".utf8_decode($classe_location->provincia);
echo "<br>Direcció -> ".utf8_decode($classe_location->direccio);
echo "<br>Codi Postal -> ".$classe_location->codi_postal;
echo "<br>Altitud -> ".$classe_location->altitud;
echo "<br>woeid: ".$classe_location->woeid;

echo "<br>coord -> ".$classe_location->get_coord();

*/
?>
