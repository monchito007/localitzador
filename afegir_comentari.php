<?php
session_start();
include("entitats/class.connection.php");
include("entitats/class.entitats.php");
include("functions/functions.php");

//Obtenim les dades necessaries per afegir el comentari a la taula
$id_usuari = $_SESSION["id"];
$localitat = $_SESSION["localitat"];
$provincia = $_SESSION["provincia"];
$cAutonoma = $_SESSION["cAutonoma"];
$carrer = $_SESSION["carrer"];
//$direccio = $_SESSION["direccio"];
//$nom = $_SESSION["nom"];
//$lat_destino = $_SESSION["lat_destino"];
//$lng_destino = $_SESSION["lng_destino"];

$nom = $_REQUEST['nom'];
$lat_destino = $_REQUEST['lat'];
$lng_destino = $_REQUEST['lng'];
$direccio = $_REQUEST['direccio'];
$comentari = $_REQUEST["tinyeditor"];

//echo $comentari;

//phpinfo();

/*
$nom = $_REQUEST["nom"];
$direccio = $_REQUEST["direccio"];
$id_lloc = $_REQUEST["id_lloc"]; 
$lat = $_REQUEST["lat"]; 
$lng = $_REQUEST["lng"];
*/

//Modifiquem els apostrofs per a que no falli la sentencia SQL.
$localitat = str_replace ("'", "\'", $localitat);
$provincia = str_replace ("'", "\'", $provincia);
$cAutonoma = str_replace ("'", "\'", $cAutonoma);
$direccio = str_replace ("'", "\'", $direccio);
$carrer = str_replace ("'", "\'", $carrer);
$comentari = str_replace ("'", "\'", $comentari);

//Obrim una connexió en la BBDD.
$con = new connexio();

$error = "";

$con->obrirConnexio($error);

//Funció que comproba si un lloc ja està registrat en la BBDD i retornal la seva id o 0 si el resultat es False.
$registrat = comprova_llocs_registrats($con,$nom,$localitat);

//Si el lloc no existeix en la BBDD el guardem.
if($registrat==0){
    
    $array_direccio = array();
    
    $array_direccio = explode(", ", $direccio);
    
    $carrer = $array_direccio[0];
    
    //Obtenim el id ($conexió,$valor,$camp_taula,$nom_taula).
    $id_localitat = obtenir_id_BBDD($con,$localitat,"municipio","municipios");
    $id_provincia = obtenir_id_BBDD($con,$provincia,"provincia","provincias");
    $id_cAutonoma = obtenir_id_BBDD($con,$cAutonoma,"region","regiones");
    
    $array_lloc = array(
                
        "latitud"=>$lat_destino,
        "longitud"=>$lng_destino,
        "nom"=>$nom,
        "carrer"=>$carrer,
        "direccio"=>$direccio,
        "id_localitat"=>$id_localitat,
        "id_provincia"=>$id_provincia,
        "id_regio"=>$id_cAutonoma
        
    );
    
    afegir_lloc_BBDD($con,$array_lloc);
    
}

//Obtenim el ID del lloc
$id_lloc = obtenir_id_BBDD($con,str_replace("'", "\'", $nom),"nom","llocs");

//treiem els espais en blanc del comentari
$comentari = trim($comentari);

//Afegim el comentari en la BBDD
if(isset($_SESSION["id"])&&($comentari!='')){
//afegir_comentari_BBDD($con,$id_lloc,$_SESSION["id"],str_replace("'", "\'", $comentari));
afegir_comentari_BBDD($con,$id_lloc,$_SESSION["id"],$comentari);
}

//Tanquem la connexió
$con->tancarConnexio();

echo "<script type='text/javascript'>location.href = 'llistat_comentaris.php?lat=".$lat_destino."&lng=".$lng_destino."&nom=".$nom."&localitat=".$localitat."&direccio=".split_direccio2($direccio)."&id_lloc=".$id_lloc."';</script>";
//echo "<script type='text/javascript'>location.href = 'llistat_comentaris.php';</script>";
?>
