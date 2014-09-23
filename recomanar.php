<?php
session_start();

include("entitats/class.connection.php");
include("entitats/class.entitats.php");
include("functions/functions.php");

$id_usuari = $_SESSION["id"];
$provincia = $_SESSION["provincia"];
$cAutonoma = $_SESSION["cAutonoma"];

$lat = $_REQUEST["lat"]; 
$lng = $_REQUEST["lng"];
$nom = $_REQUEST["nom"];
$localitat = $_REQUEST["localitat"];
$direccio = $_REQUEST["direccio"];

//Modifiquem els apostrofs per a que no falli la sentencia SQL.
$nom = str_replace ("'", "\'", $nom);
$direccio = str_replace ("'", "\'", $direccio);

//$localitat = str_replace ("'", "\'", $localitat);
//$provincia = str_replace ("'", "\'", $provincia);
//$cAutonoma = str_replace ("'", "\'", $cAutonoma);
/*
echo "<br>".$nom;
echo "<br>".$direccio;
echo "<br>".$localitat;
echo "<br>".$provincia;
echo "<br>".$cAutonoma;
*/
//Obrim una connexió en la BBDD.
$con = new connexio();

$error = "";

$con->obrirConnexio($error);

//Funció que comproba si un lloc ja està registrat en la BBDD i retornal la seva id o 0 si el resultat es False.
$registrat = comprova_llocs_registrats($con,$nom,$localitat);

//echo "registrat -> ".$registrat;

//Si el lloc no existeix en la BBDD el guardem.
if($registrat==0){
    
    $array_direccio = array();
    
    $array_direccio = explode(", ", $direccio);
    
    $carrer = $array_direccio[0];
    
    //Obtenim el id ($conexió,$valor,$camp_taula,$nom_taula).
    $id_localitat = obtenir_id_BBDD($con,$localitat,"municipio","municipios");
    $id_provincia = obtenir_id_BBDD($con,$provincia,"provincia","provincias");
    $id_cAutonoma = obtenir_id_BBDD($con,$cAutonoma,"region","regiones");
    
    /*
    echo $id_localitat;
    echo $id_provincia;
    echo $id_cAutonoma;
    */
    
    $array_lloc = array(
                
        "latitud"=>$lat,
        "longitud"=>$lng,
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
//$id_lloc = obtenir_id_BBDD($con,str_replace("'", "\'", $nom),"nom","llocs");
$id_lloc = obtenir_id_lloc_BBDD($con,$lat,$lng);
//echo $id_lloc;

if(isset($_SESSION["id"])){
//Afegim la recomanació en la BBDD
afegir_recomanacio_BBDD($con,$id_lloc,$_SESSION["id"]);
}


//Tanquem la connexió
$con->tancarConnexio();

/*
        $nom = str_replace ("\'", "%27", $nom);
        $localitat = str_replace ("\'", "%27", $localitat);
        $direccio = str_replace ("\'", "%27", $direccio);
*/
        $nom = str_replace ("\'", "'", $nom);
        $localitat = str_replace ("\'", "'", $localitat);
        $direccio = str_replace ("\'", "'", $direccio);
        
        $nom = str_replace ("'", "%27", $nom);
        $localitat = str_replace ("'", "%27", $localitat);
        $direccio = str_replace ("'", "%27", $direccio);

        //echo 'recomanar_lloc.php?lat='.$lat.'&lng='.$lng.'&nom='.$nom.'&localitat='.$localitat.'&direccio='.$direccio;
        
echo "<script type='text/javascript'>location.href = 'recomanar_lloc.php?lat=".$lat."&lng=".$lng."&nom=".$nom."&localitat=".$localitat."&direccio=".$direccio."'</script>";
?>
