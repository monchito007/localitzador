<?php

$clau = $_REQUEST['clau'];

include("../entitats/class.connection.php");
include("../entitats/class.entitats.php");

//Funció per comprovar si la clau es correcta.
function comprova_clau_registre($con,$clau){
    
    //Boleà per determinar si la clau es correcta. True si està en la BBDD, i False si no està. 
    $trobat = false;
    
    //String per obtenir el missatge d'error.
    $error="";
    
    //Formem la query per comprovar la clau.
    $query_clau = "SELECT MAX(id) as 'id',nom_usuari,clau,mail FROM usuaris_no_reg WHERE clau = '".$clau."'";
    
    //Comprovem la clau.
    $res_clau = $con->executarConsulta($query_clau,$error);
    
    //Si hi han resultats marquem la variable com a True
    if($res_clau){$trobat = true;}
    
    return $trobat;
    
}

function finalitza_registre($con,$clau){
    
    //String per obtenir el missatge d'error.
    $error="";
    
    //Formem la query per obtenir les dades de l'usuari.
    $query_registre = "SELECT MAX(id) as 'id',nom_usuari,clau,mail FROM usuaris_no_reg WHERE clau = '".$clau."'";
    
    //Comprovem la clau.
    $res_registre = $con->executarConsulta($query_registre,$error);
    
    //Obtenim les dades de l'array en variables separades.
    $id_temp = $res_registre[0]['id'];
    $nom_usuari = $res_registre[0]['nom_usuari'];
    $clau = $res_registre[0]['clau'];
    $mail = $res_registre[0]['mail'];
    
    //Creem una instancia de l'entitat "usuaris_no_reg".
    $eUsuaris = new entUsuaris;
    //Creem una instancia de l'entitat "usuaris_no_reg".
    $eUsuaris_no_reg = new entUsuaris_no_reg;
    
    //Formem l'array de dades per insertar dins la taula.
    $array_registre = array(
                
        "nom_usuari"=>$nom_usuari,
        "clau"=>$clau,
        "mail"=>$mail
    );
    
    //Formem la sentencia SQL per insertar dades
    $query = $eUsuaris->get_insert_cmd($array_registre);
    
    //echo $query;
    
    //String per obtenir el missatge d'error
    $error = "";
    
    //Guardem les dades de registre de l'usuari.
    $con->executarConsulta($query,$error);
    
    $where = "clau='".$clau."' AND id=".$id_temp;
    
    //Formem la sentencia SQL per insertar dades
    $query = $eUsuaris_no_reg->get_delete_cmd($where);
    
    //echo $query;
    
    //Eliminem les dades del registre temporal de l'usuari.
    $con->executarConsulta($query,$error);
    
    
}

//echo "http://localhost/PROJECTE_DAW/functions/confirmacio_registre.php?clau=f8e0607bc7fd72077f5f84326126c868";

//Creem un objecte connexió
$con = new connexio();

$error = "";

//Iniciem una connexió 
$con->obrirConnexio($error);

//Funcions

//Comprovem la clau del registre
if (comprova_clau_registre($con,$clau)){
    
    //Finalitzem el registre.
    finalitza_registre($con,$clau);
    
    //Missatge de registre correcte.
    echo '<br><b>Registre realitzat amb èxit.</b>';
    echo '<br>Seràs redirigit en 5 segons al següent <a href="http://localitzadorweb.tk">enllaç</a>.';
    //Funció per redirigir a la pagina principal passat un interval de 5 segons.
    echo "<script type='text/javascript'> function redireccionarPagina(){window.location = 'https://localitzadorweb.tk';}setTimeout('redireccionarPagina()', 5000);</script>";
    
}

//Tanquem la connexió.
$con->tancarConnexio();

?>
