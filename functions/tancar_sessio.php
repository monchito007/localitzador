<?php
session_start();
unset($_SESSION["id"]);
//Segons la pàgina on venim reenviem 
    if($_SESSION['path']=='informacio.php'){
        
        echo "<script type='text/javascript'> location.href = '../".$_SESSION['path']."?lat_origen=".$_SESSION['lat_origen']."&lng_origen=".$_SESSION['lng_origen']."&lat_destino=".$_SESSION['lat_destino']."&lng_destino=".$_SESSION['lng_destino']."&nom=".str_replace("'", "%27", $_SESSION['nom'])."&direccio=".str_replace("'", "%27", $_SESSION['direccio'])."';</script>";
        
    }else if($_SESSION['path']=='home.php'){
        
        echo "<script type='text/javascript'> location.href = '../".$_SESSION['path']."?lat=".$_SESSION['lat_origen']."&lng=".$_SESSION['lat_origen']."';</script>";
        
    }else{
        
        echo "<script type='text/javascript'> location.href = '../".$_SESSION['path']."?lat=".$_SESSION['lat_origen']."&lng=".$_SESSION['lng_origen']."';</script>";
        
    }
?>
