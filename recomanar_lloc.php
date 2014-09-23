<?php
session_start();

include("entitats/class.connection.php");
include("functions/functions.php");

$id_usuari = $_SESSION["id"];
$provincia = $_SESSION["provincia"];
$cAutonoma = $_SESSION["cAutonoma"];


//Guardem les dades en variables de sessió per no perdre els valors
//Lat
if(isset($_REQUEST["lat"])){
    $lat = $_REQUEST["lat"];
    $_SESSION["lat_desti"] = $_REQUEST["lat"];
}else{
    $lat = $_SESSION["lat_desti"];
}
//Lng
if(isset($_REQUEST["lng"])){
    $lng = $_REQUEST["lng"];
    $_SESSION["lng_desti"] = $_REQUEST["lng"];
}else{
    $lng = $_SESSION["lng_desti"];
}
//Nom
if(isset($_REQUEST["nom"])){
    $nom = $_REQUEST["nom"];
    $_SESSION["nom_desti"] = $_REQUEST["nom"];
}else{
    $nom = $_SESSION["nom_desti"];
}
//Direccio
if(isset($_REQUEST["direccio"])){
    $direccio = $_REQUEST["direccio"];
    $_SESSION["direccio_desti"] = $_REQUEST["direccio"];
}else{
    $direccio = $_SESSION["direccio_desti"];
}
//Localitat
if(isset($_REQUEST["localitat"])){
    $localitat = $_REQUEST["localitat"];
    $_SESSION["localitat_desti"] = $_REQUEST["localitat"];
}else{
    $localitat = $_SESSION["localitat_desti"];
}

//Obrim una connexió en la BBDD.
$con = new connexio();

$error = "";

$con->obrirConnexio($error);

//Boleà per saber si l'usuari ja ha recomanat
$recomanat_per_usuari = false;

$id_lloc = 0;

//Obtenim el id del lloc si esta en la BBDD($conexió,$valor_where,$camp_taula,$nom_taula)
//$id_lloc = obtenir_id_BBDD($con,$direccio,"direccio","llocs");
$id_lloc = obtenir_id_BBDD($con,$nom,"nom","llocs");

/*
echo "<br>id_lloc->".$id_lloc;
echo "<br>direccio->".$direccio;
*/
//Si hem obtingut el id del lloc comprobem el nuumero de vegades que el lloc ha sigut recomanat 
//i si ha sigut recomanat per l'usuari.
if($id_lloc>0){
    
    $error = "";
    
    //Query per saber si l'usuari ha recomanat el lloc
    $query = "SELECT usuaris_id FROM recomanacions WHERE llocs_id='".$id_lloc."' AND usuaris_id='".$id_usuari."'";
    
    $res = $con->executarConsulta($query,$error);
    
    if($res){
        
        //Comprobem si el lloc ha sigut recomanat per l'usuari
        $recomanat_per_usuari = true;
        
    }
}

/*
echo "<br>id_lloc -> ".$id_lloc;
if($recomanat_per_usuari){echo "recomanat";}
else{echo "no recomanat";}
*/

//Tanquem la connexió
$con->tancarConnexio();

?>
<!-- Bootstrap -->
<link href='bootstrap/css/bootstrap.css' rel='stylesheet' type='text/css' media='screen' />
<link href='bootstrap/css/bootstrap.css' rel='stylesheet' type='text/css' media='screen' />
<link href='bootstrap/css/bootstrap-responsive.css' rel='stylesheet' type='text/css' media='screen' />
<link href='css/style.css' rel='stylesheet' type='text/css' media='screen' />
<body style="background-color: #eeeeee;height: 75px">
<div class="row-fluid">
<?php

//Comprobem les vegades que ha sigut recomanat el lloc
$num_recomanacions = lloc_recomanat($nom);

//echo "<br>num_recomanacions->".$num_recomanacions;

echo "<section class='container-fluid'>";
echo "<section id='recomanar' class='hero-unit'>";
echo "<section class='row-fluid'>";
echo "<section class='span12'>";

//Si l'usuari no està validat mostrem un missatge informatiu amb enllaç al registre
if(!isset($_SESSION['id'])){
    
    
        if($num_recomanacions>1){
            echo "<img src='images/icons/star_enabled.png'>";
            echo "<p>Lloc recomanat ".$num_recomanacions." vegades.</p>";

        }else if($num_recomanacions==1){
            echo "<img src='images/icons/star_enabled.png'>";
            echo "<p>Lloc recomanat ".$num_recomanacions." vegada.</p>";
            
        }else if($num_recomanacions==0){
            echo "<img src='images/icons/star_disabled.png'>";
            echo "<h3>Aquest lloc encara no ha sigut recomanat</h3>";
        }
        echo "<p>Registra't per poder recomanar.</p>";
   
    
}else if(isset($_SESSION['id'])&&($recomanat_per_usuari == false)){
    
        //Modifiquem els apostrofs per a que no falli la URL.
        $nom = str_replace ("'", "%27", $nom);
        $localitat = str_replace ("'", "%27", $localitat);
        $direccio = str_replace ("'", "%27", $direccio);
        $direccio = sub_articles2($direccio);
    
    
        //Numero de Recomanacions del lloc
        if($num_recomanacions>1){
            echo "<img src='images/icons/star_enabled.png'>";
            echo "<p>Lloc recomanat ".$num_recomanacions." vegades.</p>";

        }else if($num_recomanacions==1){
            echo "<img src='images/icons/star_enabled.png'>";
            echo "<p>Lloc recomanat ".$num_recomanacions." vegada.</p>";
            
        }else if($num_recomanacions==0){
            echo "<img src='images/icons/star_disabled.png'>";
            echo "<p>Aquest lloc encara no ha sigut recomanat</p>";
        }
        echo "<p><a href='recomanar.php?lat=".$lat."&lng=".$lng."&nom=".$nom."&localitat=".$localitat."&direccio=".$direccio."'>Recomanar</p>";
        
        
}else if($recomanat_per_usuari == true){
            
        if($num_recomanacions>1){
            echo "<img src='images/icons/star_enabled.png'>";
            echo "<p>Lloc recomanat ".$num_recomanacions." vegades.</p>";

        }else if($num_recomanacions==1){
            echo "<img src='images/icons/star_enabled.png'>";
            echo "<p>Lloc recomanat ".$num_recomanacions." vegada.</p>";
            
        }else if($num_recomanacions==0){
            echo "<img src='images/icons/star_disabled.png'>";
            echo "<p>Aquest lloc encara no ha sigut recomanat</p>";
        }
            
} 
    
    echo "</section>";
    echo "</section>";
    echo "</section>";
    echo "</section>";
    
    


?>
</div>
</body>