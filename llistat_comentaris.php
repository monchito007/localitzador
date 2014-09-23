<?php
session_start();

include("entitats/class.connection.php");
include("functions/functions.php");

//Obtenim les dades de l'establiment de les variables de sessió.
$id_usuari = $_SESSION["id"];
//$localitat = $_SESSION["localitat"];
$provincia = $_SESSION["provincia"];
$cAutonoma = $_SESSION["cAutonoma"];
//$lat_destino = $_SESSION["lat_destino"];
//$lng_destino = $_SESSION["lng_destino"]; 
//$nom_destinacio = $_SESSION["nom"];
//$direccio_desti = $_SESSION["direccio"];

$lat_destino = $_REQUEST["lat"]; 
$lng_destino = $_REQUEST["lng"];
$nom_destinacio = $_REQUEST["nom"];
$direccio_desti = $_REQUEST["direccio"];
$localitat = $_REQUEST["localitat"];
if($_REQUEST["id_lloc"]){
    
    $id_lloc=$_REQUEST["id_lloc"];
    
}else{
    
    $id_lloc = 0;
    
}

//Obrim una connexió en la BBDD.
$con = new connexio();

$error = "";

$con->obrirConnexio($error);

//Boleà per saber si l'usuari ja ha recomanat
$recomanat = false;



if($id_lloc==0){
//Obtenim el id del lloc si esta en la BBDD($conexió,$valor_where,$camp_taula,$nom_taula)
$id_lloc = obtenir_id_lloc_BBDD($con,$lat_destino,$lng_destino);
}
$error = "";
//Query per mostrar els resultats de comentaris amb la data formatada.
$query = "SELECT b.nom_usuari as usuari,concat(ELT(date_format(a.data_hora,'%w')+1, 'Diumenge','Dilluns','Dimarts','Dimecres','Dijous','Divendres','Dissabte'), ', ' ,DAY(a.data_hora),' de ',ELT(date_format(a.data_hora,'%c'),'Gener','Febrer','Març','Abril','Maig','Juny','Juliol','Agost','Setembre','Octubre','Novembre','Desembre'), ' del ' ,YEAR(a.data_hora),'. ',date_format(a.data_hora,'%k:%i:%s')) as dia,comentari FROM comentaris as a, usuaris as b WHERE a.llocs_id=".$id_lloc." AND a.usuaris_id=b.id ORDER BY a.data_hora ASC";

//Executem la consulta
$res = $con->executarConsulta($query,$error);

//Tanquem la connexió
$con->tancarConnexio();

/*
echo "<html>";
echo "<head>";
echo "<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>";
echo "<title>Llistat Comentaris<title>";
echo "</head>";
echo "<body>";
*/
?>
<!-- Llibreries Tiny Editor -->
<link rel="stylesheet" href="widgets/tinyeditor/tinyeditor.css">
<!-- Tiny Editor -->
<script src="widgets/tinyeditor/tiny.editor.packed.js"></script>
<?php

//Capa per llistar el comentaris
echo "<section id='comentaris'>";
echo "<h2>Comentaris</h2>";
if($res){
    
    for($x=0;$x<count($res);$x++){
        
        echo "<b>".$res[$x]['usuari']."</b>";
        echo "<br>";
        echo "<i>".$res[$x]['dia']."</i>";
        echo "<br>";
        echo $res[$x]['comentari'];
        echo "<br><br>";
        
    }   
}else{
    
    echo "<h3>No hi han comentaris. Sigues el primer!!</h3>";
    
}
echo "</section>";
echo "<hr>";
//Capa del formulari per afegir comentaris
echo "<section id='comentar' class='span12'>";

if(isset($_SESSION['id'])){
    
    echo "<form name='form_comentaris' action='afegir_comentari.php' method='GET'>";
    echo "Afegeix un comentari sobre aquest lloc";
    echo "<br>";
    
    echo "<input type='hidden' id='id_lloc' value=".$id_lloc.">";
    echo "<input type='hidden' id='nom' name='nom' value='".$nom_destinacio."'>";
    echo '<input type="hidden" id="direccio" name="direccio" value="'.split_direccio2($direccio_desti).'">';
    echo "<input type='hidden' id='lat' name='lat' value=".$lat_destino.">";
    echo "<input type='hidden' id='lng' name='lng' value=".$lng_destino.">";
    
    echo "<textarea id='tinyeditor' name='tinyeditor' rows='10' cols='40' maxlenght='266'></textarea>";
    echo "<br>";
    echo "<br>";
    //echo "<input type='submit' value='envia'>";
    //echo "<button onclick='envia_comentari();'>Envia</button>";
    echo "<button onclick='comentari.post();'>Envia</button>";
    echo "</form>";
    
    
}else{
    
    echo "<h3>Has d'estar registrat per poder comentar</h3>";
    
}


/*
echo "</body>";
echo "</html>";
*/
echo "</section>";
?>
<script type="text/javascript">

function envia_comentari(){
    
    document.getElementById("form_comentaris").submit();
    
}

</script>
<script src="widgets/tinyeditor/tiny.editor.packed.js"></script>
<script>
var comentari = new TINY.editor.edit('editor', {
	id: 'tinyeditor',
	width: 584,
	height: 175,
	cssclass: 'tinyeditor',
	controlclass: 'tinyeditor-control',
	rowclass: 'tinyeditor-header',
	dividerclass: 'tinyeditor-divider',
	controls: ['bold', 'italic', 'underline', 'strikethrough', '|', 'subscript', 'superscript', '|',
		'orderedlist', 'unorderedlist', '|', 'outdent', 'indent', '|', 'leftalign',
		'centeralign', 'rightalign', 'blockjustify', '|', 'unformat', '|', 'undo', 'redo', 'n',
		'font', 'size', 'style', '|', 'image', 'hr', 'link', 'unlink', '|', 'print'],
	footer: true,
	fonts: ['Verdana','Arial','Georgia','Trebuchet MS'],
	xhtml: true,
	cssfile: 'widgets/tinyeditor/tinyeditor.css',
	bodyid: 'editor',
	footerclass: 'tinyeditor-footer',
	//toggle: {text: 'source', activetext: 'wysiwyg', cssclass: 'toggle'},
	toggle: {text: 'source', cssclass: 'toggle'}
	//resize: {cssclass: 'resize'}
});
</script>


