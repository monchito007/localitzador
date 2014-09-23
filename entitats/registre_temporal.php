<?php
//*******************************************************************************************
      
        $nom_usuari = $_REQUEST["nom_usuari"];
        $correu = $_REQUEST["correu"];
        $contrasenya = $_REQUEST["password1"];
/*
        echo "usuari -> ".$nom_usuari;
        echo "<br>";
        echo "clave -> ".$correu;
        echo "<br>";
        echo "correo -> ".$contrasenya;
        echo "<br>";
*/
 

//*******************************************************************************************

include("class.connection.php");
include("class.entitats.php");
//include("../functions/phpmailer.inc.php");
require_once '../functions/swiftmailer/lib/swift_required.php';

//define("FITXER_VALIDACIO", "http://panovisio.net/projecte/functions/confirmacio_registre.php");
define("FITXER_VALIDACIO", "https://localitzadorweb.tk/functions/confirmacio_registre.php");

//Funció per comprovar si un usuari ja està registrat en la BBDD
function comprova_usuaris_registrats($con,$nom_usuari){
    
    //Boleà per determinar si l'usuari ja està registrat. True si està en la BBDD, i False si no està. 
    $trobat = false;
    
    //String per obtenir el missatge d'error.
    $error="";
    
    //Formem les querys per comprovar l'usuari i el correu en la BBDD
    $query = "SELECT * FROM usuaris WHERE nom_usuari = '".$nom_usuari."'";
    
    //Comprovem l'usuari
    $res = $con->executarConsulta($query,$error);
    if($res){$trobat = true;}
    
    return $trobat;
    
}

//Funció per comprovar si el correu ja està registrat en la BBDD
function comprova_correus_registrats($con,$correu){
    
    //Boleà per determinar si l'usuari ja està registrat. True si està en la BBDD, i False si no està. 
    $trobat = false;
    
    //String per obtenir el missatge d'error.
    $error="";
    
    //Formem les querys per comprovar l'usuari i el correu en la BBDD
    $query = "SELECT * FROM usuaris WHERE mail = '".$correu."'";
    
    //Comprovem el correu.
    $res = $con->executarConsulta($query,$error);
    if($res){$trobat = true;}
    
    return $trobat;
    
}

//Funció per guardar les dades de l'usuari en la taula temporal "usuaris_no_reg"
function afegir_usuaris_no_reg($con,$nom_usuari,$contrasenya,$correu){
    
    //Creem una instancia de l'entitat "usuaris_no_reg".
    $eUsuaris_no_reg = new entUsuaris_no_reg;
    
    //Formem l'array de dades per insertar dins la taula.
    $array_registre = array(
                
        "nom_usuari"=>$nom_usuari,
        "clau"=>$contrasenya,
        "mail"=>$correu
    );
    
    //Formem la sentencia SQL per insertar dades
    $query = $eUsuaris_no_reg->get_insert_cmd($array_registre);
    
    //String per obtenir el missatge d'error
    $error = "";
    
    //Executem la sentencia SQL
    $con->executarConsulta($query,$error);
    
    return;
    
}

//Funció per enviar un correu electrònic de confirmació del registre a l'usuari.
function envia_correu_validacio($nom_usuari,$contrasenya,$correu){

    //require_once 'lib/swift_required.php';

    // Create the Transport
    $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
    ->setUsername('monchito007@gmail.com')
    ->setPassword('enter_your_password_here')
    ;
    
    // Create the Mailer using your created Transport
    $mailer = Swift_Mailer::newInstance($transport);

    // Create a message
    $message = Swift_Message::newInstance('Email de confirmació LOCALITZADOR WEB')
    ->setFrom(array('monchito007@gmail.com' => 'Moisés Aguilar'))
    ->setTo(array($correu, 'other@domain.org' => 'A name'))
    ->setBody('<html><head><title>Email de confirmació LOCALITZADOR WEB</title></head><body><h1>LOCALITZADOR WEB</h1><p><b>Benvinguts al Localitzador WEB</b>.<br><br> Per finalitzar el registre feu click en el següent enllaç: <br><br><a href="'.FITXER_VALIDACIO.'?clau='.$contrasenya.'">Confirmar registre Localitzador WEB</a><br><br><b>Tècnic Web. monchito007@gmail.com</b></p></body></html>')
    ;

    // Send the message
    $result = $mailer->send($message);

    if($result){
        return true;
    }else{
        return false;
    }
    
    /*
    
    //Creem un objecte PHPmailer per enviar el correu electrònic.
    $class_phpmailer = new phpmailer();

    //Afegim el correu i el nom d'usuari.
    $class_phpmailer->AddAddress($correu,$nom_usuari);

    //Afegim el cos del correu electrònic
    $class_phpmailer->Body = "<html><head><title>Email de confirmació LOCALITZADOR WEB</title></head><body><h1>LOCALITZADOR WEB</h1><p><b>Benvinguts al Localitzador WEB</b>.<br><br> Per finalitzar el registre feu click en el següent enllaç: <br><br><a href='".FITXER_VALIDACIO."?clau=".$contrasenya."'>Confirmar registre Localitzador WEB</a><br><br><b>Tècnic Web. monchito007@gmail.com</b></p></body></html>";
    
    //Enviem el correu electrònic a l'usuari.
    $class_phpmailer->Send();
    
    echo "<br>Correu enviat amb èxit";
    echo "<script type='text/javascript'> alert('S´ha enviat un correu de validació a la direcció ".$correu.".'); </script>";
    echo "<script type='text/javascript'> location.href = '../index_prueba.php';</script>";
    
    if(!$mail->Send())
    {
        echo "Se ha producido un error al enviar el correo.";

        echo "Mailer Error: " . $mail->ErrorInfo;

        exit;

    }else{
        echo 'mail enviado correctamente';
    }
    
    return;
    */
}

//Creem un objecte connexió
$con = new connexio();

$error = "";

//Iniciem una connexió 
$con->obrirConnexio($error);

//Creem una instància a l'entitat de la taula 'usuaris'.
$eUsuaris = new entUsuaris();

//Comprovem si l'usuari o el correu existeixen en la BBDD.
$existeix_usuari = comprova_usuaris_registrats($con,$nom_usuari);//true -> existeix, false -> no existeix.
$existeix_correu = comprova_correus_registrats($con,$correu);//true -> existeix, false -> no existeix.
//Si existeixen les dades mostrem un missatge per pantalla i reenviem al formulari de registre.
if(($existeix_usuari)&&($existeix_correu)){echo "<script type='text/javascript'> alert('L´usuari ".$nom_usuari." i el correu ".$correu." ja existeixen en la base de dades. '); location.href = '../form_registre.php';</script>";}
if(($existeix_usuari)&&(!$existeix_correu)){echo "<script type='text/javascript'> alert('L´usuari ".$nom_usuari." ja existeix en la base de dades.'); location.href = '../form_registre.php';</script>";}
if((!$existeix_usuari)&&($existeix_correu)){echo "<script type='text/javascript'> alert('El correu ".$correu." ja existeix en la base de dades.'); location.href = '../form_registre.php';</script>";}

//Si l'usuari no existeix en la BBDD l'afegim en la Taula d'usuaris temporal, i li enviem un correu electrònic per confirmar el registre.
if((!$existeix_usuari)&&(!$existeix_correu)){
    
    //Enviem un correu de confirmació a l'usuari
    if(envia_correu_validacio($nom_usuari,$contrasenya,$correu)){
        //envia_correu_validacio($nom_usuari,$contrasenya,$correu);
        //Afegim l'usuari en la taula temporal.
        afegir_usuaris_no_reg($con,$nom_usuari,$contrasenya,$correu);
        echo "Correu de confirmació enviat a ".$correu.", revisi el correu brossa.<br><br>Gràcies per registrar-te al LocalitzadorWEB.";
        
    }
}
//Tanquem la connexió.
$con->tancarConnexio();

//Funció per redirigir a la pagina principal passat un interval de 5 segons.
echo "<script type='text/javascript'> function redireccionarPagina(){window.location = 'http://localitzadorweb.tk';}setTimeout('redireccionarPagina()', 5000);</script>";
    

?>
