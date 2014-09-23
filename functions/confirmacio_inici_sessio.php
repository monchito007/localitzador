<?php
//Iniciem les variables de sessió
session_start();

//Obtenim les dades del form_inicia_sessio.php
$nom_usuari = $_REQUEST['nom_usuari'];
$clau = $_REQUEST['password1'];

include("../entitats/class.connection.php");
include("../entitats/class.entitats.php");

//Funció per comprovar el registre de l'usuari a la BBDD.
function comprova_usuari_BBDD($con,$nom_usuari,$clau){
    
    $id=0;
    
    //String per obtenir el missatge d'error.
    $error="";
    
    //Formem la query per comprovar l'usuari.
    $query_clau = "SELECT id FROM usuaris WHERE nom_usuari='".$nom_usuari."' AND clau='".$clau."'";
    
    //Executem la consulta en la base de dades l'usuaris.
    $res = $con->executarConsulta($query_clau,$error);
    
    //Si s'ha trobat l'usuari obtenim el seu id.
    if($res){
        
        $id = $res[0]['id'];
        
    }
    
    return $id;
    
}

//Creem un objecte connexió
$con = new connexio();

$error = "";

//Iniciem una connexió 
$con->obrirConnexio($error);

$id_usuari = comprova_usuari_BBDD($con,$nom_usuari,$clau);

if($id_usuari){
    
    $_SESSION["id"]=$id_usuari;
    //Funció per redirigir a la pagina principal passat un interval de 5 segons.
    //echo "<script type='text/javascript'> alert('Registre Correcte'); location.href = '../index.php';</script>";
    //echo "<script type='text/javascript'> location.href = '../index.php?lat=".$_SESSION['lat']."&lng=".$_SESSION['lng']."';</script>";
    
    //Segons la pàgina on venim reenviem 
    
    if($_SESSION['path']=='informacio.php'){
        
        echo "<script type='text/javascript'> location.href = '../".$_SESSION['path']."?lat_origen=".$_SESSION['lat_origen']."&lng_origen=".$_SESSION['lng_origen']."&lat_destino=".$_SESSION['lat_destino']."&lng_destino=".$_SESSION['lng_destino']."&nom=".str_replace("'", "%27", $_SESSION['nom'])."&direccio=".str_replace("'", "%27", $_SESSION['direccio'])."';</script>";
        
    }else if($_SESSION['path']=='home.php'){
        
        echo "<script type='text/javascript'> location.href = '../".$_SESSION['path']."?lat=".$_SESSION['lat_origen']."&lng=".$_SESSION['lng_origen']."';</script>";
        
    }else{
        
        echo "<script type='text/javascript'> location.href = '../".$_SESSION['path']."?lat=".$_SESSION['lat_origen']."&lng=".$_SESSION['lng_origen']."';</script>";
        
    }
    
    
}else{
    echo "<script type='text/javascript'> alert('Registre Incorrecte'); location.href = '../".$_SESSION['path']."?lat=".$_SESSION['lat_origen']."&lng=".$_SESSION['lng_origen']."';</script>";
    }

//Tanquem la connexió.
$con->tancarConnexio();



?>
