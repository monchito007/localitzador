<?php 
session_start();
//include("../entitats/class.connection.php");
//include("../entitats/class.entitats.php");

//FunciÛ per obtenir el nom d'usuari de la BBDD.
function obtenir_nom_usuari_BBDD($con,$id){
    
    //String per obtenir el missatge d'error.
    $error="";
    
    //Formem la query per obtenir les dades de l'usuari.
    $query_registre = "SELECT nom_usuari FROM usuaris WHERE id=".$id;
    
    //Comprovem la clau.
    $res = $con->executarConsulta($query_registre,$error);
    
    //Obtenim les dades de l'array en variables separades.
    return $res[0]['nom_usuari'];
    
}

//Obtenim l'id de l'usuari
if($_SESSION["id"]){
    
    $id=$_SESSION["id"];
    
    //Creem un objecte connexiÛ
    $con = new connexio();

    $error = "";

    //Iniciem una connexiÛ 
    $con->obrirConnexio($error);
    
    //obtenim el nom d'usuari.
    $nom_usuari = obtenir_nom_usuari_BBDD($con,$id);
    
    $_SESSION["nom_usuari"]=$nom_usuari;
    
    //Tanquem la connexiÛ.
    $con->tancarConnexio();
    
 }

?>
<script type="text/javascript" src="js/md5-min.js"></script>
<script type="text/javascript">
            
            //Funci√≥ per validar el nom d'usuari.
            function valida_nom_usuari(){
                
               document.getElementById("missatges_form_sessio").innerHTML = "";
               
               if(document.getElementById("nom_usuari").value.length<8){
                    
                    //alert("El nom d'usuari ha de tenir almenys 5 caracters");
                    document.getElementById("missatges_form_sessio").innerHTML += "<b><font color='red'>*</font> El nom d'usuari ha de tenir almenys 8 caracters.</b>";
                    //document.form_registre.nom_usuari.value = "";				  
                    document.getElementById("nom_usuari").style.background="#D8D8D8";		  
                    document.getElementById("nom_usuari").style.fontWeight="bold";		  
                    document.getElementById("nom_usuari").focus();
                    
                    return false;
                    
                }
                
                document.getElementById("missatges_form_sessio").innerHTML += "";
                document.getElementById("nom_usuari").style.background="#FFFFFF";		  
                document.getElementById("nom_usuari").style.fontWeight="normal";	
                return true;
                
            }
            
            //Funci√≥ per validar les contrasenyes.
            function valida_contrasenyes(){
                
                if(document.getElementById("password1").value.length<5){
                    
                    document.getElementById("missatges_form_sessio").innerHTML += "<br><b><font color='red'>*</font> La contrasenya ha de tenir almenys 5 caracters.</b>";
                    document.getElementById("password1").style.background="#D8D8D8";
                    document.getElementById("password1").value="";
                    document.getElementById("password1").focus();
                    
                    return false;
                    
                }
                
                document.getElementById("password1").style.background="#FFFFFF";
                
                return true;
                
            }
            
            //Funci√≥ per validar les dades del formulari.
            function valida_dades(){
                
                //Validem el nom d'usuari
                var usuari = valida_nom_usuari();
                //Validem la contrasenya
                var contrasenya = valida_contrasenyes();
                
                //Si totes les validacions son correctes encriptem la clau en MD5 i enviem el formulari.
                if((usuari)&&(contrasenya)){
                    
                    //Encriptem la contrasenya en MD5.
                    var clau = document.getElementById("password1").value;

                    var clau_md5 = hex_md5(clau);

                    document.getElementById("password1").value = clau_md5;
                    //alert(clau_md5);
                    document.getElementById("form_inicia_sessio").submit();
                    
                }
            }
            
    
        </script>
<?php

if(!$_SESSION["id"]){

    echo "<form id='form_inicia_sessio' action='functions/confirmacio_inici_sessio.php' method='post' class='navbar-form pull-right'>";
    echo "<input type='text' id='nom_usuari' name='nom_usuari' class='span2' placeholder='Usuari'>";
    echo "<input type='password' id='password1' name='password1' class='span2' placeholder='Clau'>";
    echo "<br>";
    echo "<input class='btn btn-primary btn-small' type='button' value='Inicia Sessio' onclick='valida_dades()' />";
    echo "</form>";
    echo "<br>";
    
    echo "<button id='btn_registre' class='btn btn-primary btn-small' onclick=location.href='form_registre.php';>Registra't</button>";
    

}else{

    echo "<form>";
    echo "Benvingut ".ucfirst($_SESSION["nom_usuari"]);
    echo "<br>";
    echo "<a href='functions/tancar_sessio.php'>Sortir</a>";
    echo "</form>";

}
?>