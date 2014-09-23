<?php
//JSON Municipios
//Script per llegir un arxiu CSV (municipios.csv) amb la llista de poblacions 
//i tornar un arxiu JSON per poder llegir-lo amb YUI
?>
<?php

$csv_municipios = "municipios.csv";

$file = fopen($csv_municipios,'r');

$x = 0;

while(!feof($file) )
{
    $str = fgets($file);
    
    $str = str_replace(';', '', $str);
    
    $response['municipios'][$x] = array(
    
        'name'=>  utf8_encode($str),
    
    );
    
    $x++;
    
}
fclose($file);

header('Content-type: application/json');

echo json_encode($response);
?>