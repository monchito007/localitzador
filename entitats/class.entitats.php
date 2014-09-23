<?php
/*Modul 7: Desenvolupament web en entorn servidor          
 * 
 * Practica: Classe Grafic Base
 * Fitxer: class.entitats.php
 * 
 * Autor: Moisés Aguilar
 * Curs: 2012/2013
 * 
 * Descripció: 
 * Pre: 
 * Post: 
 */
?>
<?php

define(KTipus, 0);
define(KMida, 1);
define(KNot_Nullable, 2);
define(KNot_Visible, 3);
define(KReadOnly, 4);
define(KDisplayText, 5);

class EntitatBase{
    
    //Constants que serviran per indicar el nom que necessitem.
    const KTipus = 0;
    const KMida = 1;
    const KNot_Nullable = 2; //1 si no pot ser null, 0 en cas contrari
    const KNot_Visible = 3;
    const KReadOnly = 4;
    const KDisplayText = 5;
    
    //ATRIBUTS
    
    //Nom de la Taula
    protected $nom_taula;
    
    //Camps de la taula base
    protected $camps =NULL ;
    
    protected $propietats = NULL;
    
    public function get_nom_taula(){
        
        return $this->nom_taula;
        
    }
    public function get_camps(){
        
        return $this->camps;
        
    }
    public function get_propietat_camps(){
        
        return $this->propietats;
        
    }
    
    protected function set_nom_taula($nom_taula){
        
        $this->nom_taula = $nom_taula;
        
    }
    protected function set_camps($camps){
        
        $this->camps = $camps;
        
    }
    
    protected function set_propietats($propietats){
        
        $this->propietats = $propietats;
        
    }
    
    //MÈTODES
    
    //Mètode que generarà el select command.
    public function get_select_cmd($where=NULL){
        
        $cmd = "SELECT * FROM ".$this->nom_taula;
        
        if(isset($where)){
            
            $cmd = $cmd . " WHERE " . $where;
            
        }
        
        return $cmd;
        
    }
    
    public function get_insert_cmd($values){
        
        //Afegim les cometes als valors de tipus string.
        foreach($this->propietats as $i => $valor){
            
            if($valor[KTipus]=='char'){
                
                $values[$i]="'".$values[$i]."'";
                
            }
            
        }
        
        $camps = $this->get_camps();
        
        //implode, join -> juntar elements d'un array en una cadena.
        $column_string = implode(",",$camps);
        
        $values_string = implode(",",$values);
        
        $cmd = "INSERT INTO ".$this->nom_taula." (".$column_string.") values(".$values_string.")";
        
        return $cmd;
        
    }
     
    
    public function get_update_cmd($values,$where=NULL){
        
        foreach ($this->propietats as $i => $valor) {
            
            //echo "valor->".$valor[$i]."<br>";
            //echo "i->".$i."<br>";
            
            if($valor[KTipus]=='char'){
                
                $values[$i] = $i."='".$values[$i]."'";
                
            }else{
                
                $values[$i] = $i."=".$values[$i];
                
            }
            
        }
        
        $values_string = implode(",",$values);
        
        $cmd = "UPDATE ".$this->nom_taula." SET ".$values_string;
        
        return $cmd;
        
    }
    
    public function get_delete_cmd($where=NULL){
        
        if(!isset($where)){
            
            //$cmd = $cmd = "DELETE FROM ".$this->nom_taula;
            
        }else{
            
            $cmd = $cmd = "DELETE FROM ".$this->nom_taula." WHERE ".$where;
            
        }
        
        return $cmd;
        
    }
    
}

class entUsuaris extends EntitatBase{
    
    public function __construct(){
        
        $this->set_nom_taula("usuaris");
        $this->set_camps(array("nom_usuari","clau","mail"));
        $this->set_propietats( array(
                
                "nom_usuari"=>array("char",20,0,0,0,"Nom d'usuari"),
                "clau"=>array("char",50,0,0,0,"Clau"),
                "mail"=>array("char",100,0,0,0,"Correu electrònic")
                
       ));
        
    }
    
}

class entUsuaris_no_reg extends EntitatBase{
    
    public function __construct(){
        
        $this->set_nom_taula("usuaris_no_reg");
        $this->set_camps(array("nom_usuari","clau","mail"));
        $this->set_propietats( array(
                
                "nom_usuari"=>array("char",20,0,0,0,"Nom d'usuari"),
                "clau"=>array("char",50,0,0,0,"Clau"),
                "mail"=>array("char",100,0,0,0,"Correu electrònic")
                
       ));
        
    }
    
    
}
class entLlocs extends EntitatBase{
    
    public function __construct(){
        
        $this->set_nom_taula("llocs");
        $this->set_camps(array("latitud","longitud","nom","carrer","direccio","municipios_id","provincias_id","regiones_id"));
        $this->set_propietats( array(
                
                "latitud"=>array("decimal",11.6,0,0,0,"Latitud"),
                "longitud"=>array("decimal",11.6,0,0,0,"Longitud"),
                "nom"=>array("char",150,0,0,0,"Nom"),
                "carrer"=>array("char",200,0,0,0,"Carrer"),
                "direccio"=>array("char",200,0,0,0,"Direccio"),
                "municipios_id"=>array("int",11,0,0,0,"Localitat"),
                "provincias_id"=>array("int",2,0,0,0,"Provincia"),
                "regiones_id"=>array("int",100,0,0,0,"Regió")
                
       ));
        
    }
}
class entRecomanacions extends EntitatBase{
    
    public function __construct(){
        
        $this->set_nom_taula("recomanacions");
        $this->set_camps(array("llocs_id","usuaris_id"));
        $this->set_propietats( array(
                
                "llocs_id"=>array("int",6,0,0,0,"ID Lloc"),
                "usuaris_id"=>array("int",6,0,0,0,"ID Usuari")
            
       ));
        
    }
}
class entComentaris extends EntitatBase{
    
    public function __construct(){
        
        $this->set_nom_taula("comentaris");
        $this->set_camps(array("llocs_id","usuaris_id","comentari"));
        $this->set_propietats( array(
                
                "llocs_id"=>array("int",6,0,0,0,"ID Lloc"),
                "usuaris_id"=>array("int",100,0,0,0,"ID Usuari"),
                "comentari"=>array("char",255,0,0,0,"Comentari")
            
       ));
        
    }
}
    
/*
$ebase = new entPaisos();

$eUsuaris = new entUsuaris();


$array_valors = array(
                
                "Pais"=>"Montserratí",
                "Capital"=>"Montserrat",
                "Moneda"=>"moneda pròpia",
                "Superficie"=>22,
                "Poblacion"=>100000,
                "Esperanza_de_vida"=>80,
                "Mortalidad_infantil"=>0,
                "PNB"=>0,
                "PNB_per_capita"=>0,
                "Lengua"=>"Qualsevol",
                "Religion"=>"Qualsevol",
                "Continente"=>"Europa");

$array_registre = array(
                
                "nom_usuari"=>"Monchito007",
                "clau"=>"12345",
                "mail"=>"monchito007@gmail.com");

echo $ebase->get_select_cmd();
echo "<br>";
echo $ebase->get_insert_cmd($array_valors);
echo "<br>";
echo $ebase->get_update_cmd($array_valors);
echo "<br>";
echo $ebase->get_delete_cmd();
echo "<br>";
echo $ebase->get_delete_cmd("id=1");

echo "<hr>";

echo $eUsuaris->get_select_cmd();
echo "<br>";
echo $eUsuaris->get_insert_cmd($array_registre);
echo "<br>";
echo $eUsuaris->get_update_cmd($array_registre);
echo "<br>";
echo $eUsuaris->get_delete_cmd();
echo "<br>";
echo $eUsuaris->get_delete_cmd("id=1");
*/
?>
