<?php
session_start();
?>
<?php
/* Projecte Desenvolupament d'Aplicacions Web.
 * 
 * Fitxer: previsio_meteo.php
 * 
 * Autor: Moisés Aguilar Miranda
 * Curs: 2012/2013
 * 
 * Descripció: Fitxer amb la previsió meteorològica de forma extesa.
 */
?>
<?php
//Include Objecte API Yahoo Weather
include("functions/class.xml.parser.php");
include("functions/class.weather.php");
//Classe Location amb la que obtenim les dades de la localitat a través de les coordenades.
include("functions/class.location.php");
//Include de Funcions PHP
include("functions/functions.php");

//VARIABLES DE SESSIÓ
$latitud = $_SESSION["lat_origen"];
$longitud = $_SESSION["lng_origen"];
$woeid = $_SESSION["woeid"];

//Si hem obtingut el Woeid creem un objecte weather.
if(isset($woeid)){
    
    $weather = new weather($woeid, 3600, 'c');//Objecte Weather.
    
    $weather->parsecached();
    
}
//Si hem obtingut el nom de la localitat o les coordenades creem un objecte location
if(isset($latitud)&&(isset($longitud))){
    
    $classe_location_origen = new location();

    $coord_string = parse_coord($latitud,$longitud);
    
    $classe_location_origen->location_by_coord($coord_string);

}else if(isset($poblacio)){
    
    $classe_location_origen = new location();

    $classe_location_origen->location_by_name($poblacio);
    
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>Localitzador WEB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href='bootstrap/css/bootstrap.css' rel='stylesheet' type='text/css' media='screen' />
    <link href='css/style.css' rel='stylesheet' type='text/css' media='screen' />
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements --> 
    <!--[if lt IE 9]> 
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script> 
    <![endif]-->
    <link href='css/style.css' rel='stylesheet' type='text/css' media='screen' />
    <!-- Llibreria Javascript d'encriptació MD5 --> 
    <script src="js/md5-min.js"></script>
    
  </head>
  <body>
    <!-- Menú -->
    <div id="menu">
        <?php
            include("templates/menu.php");
        ?>
    </div>    
    <!-- Menú -->
    
    <!-- Contenidor -->
    <div class="container">
        
    <!-- Errors Inici de sessió -->
    <section id='div_msg_sessio'>
    <section id='msg_password'></section>
    <section id='missatges_form_sessio'></section>
    </section>
    <!-- Errors Inici de sessió -->
        
<!-- Main hero unit for a primary marketing message or call to action -->
<div class="hero-unit">
        <section id="div_form_registre">
            <h2>Registre</h2>
            <form id="form_registre" name="form_registre" action="entitats/registre_temporal.php" method="post">
                    <table border="0">
                        <tr>
                            <td>Nom d'usuari</td>
                            <td><input type="text" id="nom_usuari" name="nom_usuari" type="text"></td>
                            <td><section id="msg_usuari"></section></td>
                        </tr>
                        <tr>
                            <td>Correu</td>
                            <td><input type="text" id="correu" name="correu" type="text"></td>
                            <td><section id="msg_correu"></section></td>
                        </tr>
                        <tr>
                            <td>Contrasenya</td>
                            <td><input type="password" id="password1" name="password1"></td>
                            <td rowspan="2"><section id="msg_password"></section></td>
                        </tr>
                        <tr>
                            <td>Repeteix contrasenya</td>
                            <td><input type="password" id="password2" name="password2"></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="button" value="Envia dades" onclick="valida_dades()"></td>
                        </tr>
                        
                    </table>
                </form>
        </section>     

                
</div>


<!-- Main hero unit for a primary marketing message or call to action -->

<footer>
    <?php
        include("templates/footer.php");
    ?>
</footer>

        
        
    </div>
    <!-- Contenidor -->
      
          <!-- Placed at the end of the document so the pages load faster -->
    <script src="bootstrap/js/jquery.js"></script>
    <script src="bootstrap/js/bootstrap-transition.js"></script>
    <script src="bootstrap/js/bootstrap-alert.js"></script>
    <script src="bootstrap/js/bootstrap-modal.js"></script>
    <script src="bootstrap/js/bootstrap-dropdown.js"></script>
    <script src="bootstrap/js/bootstrap-scrollspy.js"></script>
    <script src="bootstrap/js/bootstrap-tab.js"></script>
    <script src="bootstrap/js/bootstrap-tooltip.js"></script>
    <script src="bootstrap/js/bootstrap-popover.js"></script>
    <script src="bootstrap/js/bootstrap-button.js"></script>
    <script src="bootstrap/js/bootstrap-collapse.js"></script>
    <script src="bootstrap/js/bootstrap-carousel.js"></script>
    <script src="bootstrap/js/bootstrap-typeahead.js"></script>
    
  
  </body>
  
</html>

        <script>
            
            //Funció per validar el nom d'usuari.
            function valida_nom_usuari(){
                
               if(document.form_registre.nom_usuari.value.length<5){
                    
                    //alert("El nom d'usuari ha de tenir almenys 5 caracters");
                    document.getElementById("msg_usuari").innerHTML = "<b><i><font color='red'>*</font> El nom d'usuari ha de tenir almenys 5 caracters.</i></b>";
                    //document.form_registre.nom_usuari.value = "";				  
                    document.form_registre.nom_usuari.style.background="#FF0000";		  
                    document.form_registre.nom_usuari.style.fontWeight="bold";		  
                    document.form_registre.nom_usuari.focus();
                    
                    return false;
                    
                }
                
                document.getElementById("msg_usuari").innerHTML = "";
                document.form_registre.nom_usuari.style.background="#FFFFFF";		  
                document.form_registre.nom_usuari.style.fontWeight="normal";	
                return true;
                
            }
            
            //Funció per validar les contrasenyes.
            function valida_contrasenyes(){
                
                var clau1 = document.form_registre.password1.value;
                var clau2 = document.form_registre.password2.value;
                
                if((clau1.length<5)||(clau2.length<5)){
                    
                    document.getElementById("msg_password").innerHTML = "<b><i><font color='red'>*</font> Les contrasenyes han de tenir almenys 5 caracters.</i></b>";
                    document.form_registre.password1.style.background="#FF0000";
                    document.form_registre.password2.style.background="#FF0000";
                    document.form_registre.password1.value="";
                    document.form_registre.password2.value="";
                    document.form_registre.password1.focus();
                    
                    return false;
                    
                }else if(clau1!=clau2){
                    
                    document.getElementById("msg_password").innerHTML = "<b><i><font color='red'>*</font> Les contrasenyes no coincideixen.</i></b>";
                    document.form_registre.password1.style.background="#FF0000";
                    document.form_registre.password2.style.background="#FF0000";
                    document.form_registre.password1.value="";
                    document.form_registre.password2.value="";
                    document.form_registre.password1.focus();
                    
                    return false;
                    
                }
                
                document.getElementById("msg_password").innerHTML = "";
                document.form_registre.password1.style.background="#FFFFFF";
                document.form_registre.password2.style.background="#FFFFFF";
                return true;
                
            }
            
            function valida_correu(){
                
                var Cadena = document.form_registre.correu.value;
                var Punto = Cadena.substring(Cadena.lastIndexOf('.') + 1, Cadena.length)            // Cadena del .com  
                var Dominio = Cadena.substring(Cadena.lastIndexOf('@') + 1, Cadena.lastIndexOf('.'))    // Domini @lala.com  
                var Usuario = Cadena.substring(0, Cadena.lastIndexOf('@'))                  // Cadena lalala@  
                var Reserv = "@/º\"\'+*{}\\<>?¿[]áéíóú#·¡!^*;,:"                      // Lletres reservades 

                //variable que ens dirà si el correu es vàlid o no.
                valido = true;

                // verifica qie el Usuario no tenga un caracter especial  
                for (var Cont=0; Cont<Usuario.length; Cont++) {  
                    X = Usuario.substring(Cont,Cont+1)  
                    if (Reserv.indexOf(X)!=-1){valido = false;}
                }  

                // verifica qie el Punto no tenga un caracter especial  
                for (var Cont=0; Cont<Punto.length; Cont++) {  
                    X=Punto.substring(Cont,Cont+1)  
                    if (Reserv.indexOf(X)!=-1){valido = false;}
                }  

                // verifica qie el Dominio no tenga un caracter especial  
                for (var Cont=0; Cont<Dominio.length; Cont++) {  
                    X=Dominio.substring(Cont,Cont+1)  
                    if (Reserv.indexOf(X)!=-1){valido = false;}
                }  

                // Verifica la sintaxis básica.....  
                if (Punto.length<2 || Dominio <1 || Cadena.lastIndexOf('.')<0 || Cadena.lastIndexOf('@')<0 || Usuario<1) {  
                    valido = false;
                }  

                if(!valido){
                    
                    document.getElementById("msg_correu").innerHTML = "<b><i><font color='red'>*</font> Correu no vàlid.</i></b>";
                    document.form_registre.correu.style.background="#FF0000";		  
                    document.form_registre.correu.style.fontWeight="bold";		  
                    document.form_registre.correu.focus();
                    return false;
                    
                }
                
                document.getElementById("msg_correu").innerHTML = "";
                document.form_registre.correu.style.background="#FFFFFF";		  
                document.form_registre.correu.style.fontWeight="normal";	
                return true;
                
            }
            
            //Funció per validar les dades del formulari.
            function valida_dades(){
                
                //Validem el nom d'usuari
                var usuari = valida_nom_usuari();
                //Validem el correu
                var correu = valida_correu();
                //Validem les contrasenyes
                var contrasenyes = valida_contrasenyes();
                
                //Si totes les validacions son correctes encriptem la clau en MD5 i enviem el formulari.
                if((usuari)&&(correu)&&(contrasenyes)){
                    
                    //Encriptem la contrasenya en MD5.
                    var clau = document.form_registre.password1.value;

                    var clau_md5 = hex_md5(clau);

                    document.form_registre.password1.value = clau_md5;
                    document.form_registre.password2.value = clau_md5;
                    //alert(clau_md5);
                    document.form_registre.submit();
                    
                }
            }
            
    
        </script>