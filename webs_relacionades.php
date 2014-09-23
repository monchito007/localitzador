<?php
//Include de Funcions PHP
include("functions/functions.php");

$websearch = $_REQUEST['websearch'];

//api google
$api="AIzaSyCQt49pKh4iZvkowhRNHLXCXV5n8TaTPw0";

//http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=[q]&key=[key]&cx=[account]&rsz=large&userip=[userip]&start=[start]

    $url = "http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=".comprova_espais_html(utf8_decode($websearch))."&rsz=4&key=".$api;
    try {
    
        $body = file_get_contents($url);
        $json = json_decode($body);
    
    }  catch (Exception $e){
        
        echo 'Excepción capturada: ',  $e->getMessage(), "\n";

    }
    
    echo "<section id='noticies'>";
    
    echo "<h1>Webs relacionades</h1>";
    
    for($x=0;$x<count($json->responseData->results);$x++){
        
        echo "<p>";
        echo "<b>".utf8_decode($json->responseData->results[$x]->title)."</b>";
        echo "<br>";
        echo "<a href='".$json->responseData->results[$x]->url."' target=_BLANK>".$json->responseData->results[$x]->visibleUrl."</a>";
        echo "<br>";
        echo utf8_decode($json->responseData->results[$x]->content);
        echo "</p>";

    }
    
    echo "</section>";

?>
