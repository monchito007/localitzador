<?php

/*--------------------------------------------------------------------------------------
 *      Mòdul 7: Desenvolupament Web en Entorn Servidor
 *             
 *             Activitat: Accés a bases de dades: BasesDades.php
 *      
 *      Autor: Maribel Alcoba Laranjinha
 *             Curs: 2012/2013
 *             
 *      Descripció: Classe per accedir a bases de dades
 *             Pre:  
 *             Post:
 *      
 */
 class connexio{
	 private $con=NULL;
	 private $host = NULL;
	 private $usuari = NULL;
	 private $password=NULL;
	 private $bdname=NULL;
	 private $isOpen=false;
	 
	 private $threadId=NULL;
	 
	 public function setHost($server){
		 if (isset($server)){
			$this->host = $server;
		 }
		 else{
			$this->host = "localhost";
		 }
	 }
	 
	 public function setUsuari($usuari){
		 
                if (isset($usuari)){
                    
                    $this->usuari = $usuari;
		 
                 }else{
                    
                     $this->usuari = "root";
		 
                }
                
	 }

	 public function setPassword($password){
		 if (isset($password)){
			 $this->password = $password;
		 }
		 else
		 {
			 $this->password = "12345";

		 }
	 }

	 public function setBD($bd){
		 if (isset($bd)){
			 $this->bdname = $bd;
		 }
		 else
		 {
			 $this->bdname = "localitzador";
		 }
	 }

	/*
	 *	Constructor, crea una nova instància de connexió a la base de dades.
	 * També crea una instància de la connexió (inicialitza), però no obre la connexió a la base de dades
	 * @param string server -> adreça del servidor de base de dades
	 * @param string usuari -> usuari de base de dades
	 * @param string password ->password d'accés a la base de dades
	 * @param string nombd	-> nom de la base de dades que volem atacar
	 * 
	 */
	 public function __construct($server=NULL,$usuari=NULL,$password=NULL,$nombd=NULL){
		 $this->setHost($server);
		 $this->setUsuari($usuari);
		 $this->setPassword($password);
		 $this->setBD($nombd);

		 
	 }
	 
	 /*
	  *mètode públic obrirConnexio. Obre realment la connexió a la base de dades.
	  * @param string error Out, error de connexió en cas de fallar aquesta.
	  * @return boolean true si la connexió s'ha obert correctament. false en cas contrari. 
	  */
	 public function obrirConnexio(&$error){
		 
		$bret=false;
		 /*Si no hem tancat la connexió, no hem d'intentar obrir-la de nou*/
		if(!$this->isOpen){
			$this->con=mysqli_init();
			if(!$this->con){
				 throw new Exception('Error en inicialitzar la connexió (mysqli_init)');
			}
			else{
				$bret=$this->con->real_connect($this->host,$this->usuari,$this->password,$this->bdname);
                                //echo "<br> La connexió ha estat oberta correctament";
                                
				if(!$bret){
					throw new Exception('Connect Error ('.$this->con->connect_errno.') '.$this->con->connect_error);
				}
				else{
				 $this->isOpen=true;
				}
			}
		 		 
		 }
		 return $bret;
	 }
	 
	 /*
	  *  Mètode públic tancarConnexió. Comprova si la connexió està oberta, en cas afirmatiu, tanca la connexió
	  * @return boolean: true si s'ha pogut tancar la connexió, false en cas contrari.
	  */
	 public function tancarConnexio(){
		 $bret=false;
		 if($this->isOpen) {
			 
			 $bret=$this->con->close();
			 if ($bret) $this->isOpen=false;	
			 
		 }
                 //echo "<br> S'ha tancat la connexió correctament";
		 return $bret;
	 
	}
	
	/*
	 * Mètode públic executarConsulta. Si troba que la connexió a la base de dades
	 * no està oberta, obre la connexió, executa la consulta que es passa per paràmetre.
	 * Aquesta consulta ha d'estar convenientment escapada.
	 * Només executa consultes úniques. Retorna un array amb tots els resultats de la consulta
	 * */
	public function executarConsulta($query, &$error){
		$error="";
		$bObrir=$this->isOpen;
		$ret=NULL;
		if (!$this->isOpen){
			$bObrir=$this->obrirConnexio($error);
		}
		if ($bObrir){ //Hem pogut obrir la connexió amb èxit. Executem la consulta SQL
			if( $this->con->real_query($query)){ //Si executem la consulta i ha anat bé
				$resultatConsulta=$this->con->use_result(); //Accedim a les dades de la consulta
				
                                if($resultatConsulta){
                                    
                                    $ret=array();
                                
                                    while($row=$resultatConsulta->fetch_array(MYSQLI_ASSOC)){
                                            array_push($ret,$row); //Afegim totes les files retornades a l'array de sortida
                                    }
                                    $resultatConsulta->free();
                                    
                                }
                                
			}
			else{//S'ha produït algun error i la consulta no ha tornat res...
							
				 $error='Error Query '.$this->con->sqlstate ;

			}
			
		}
		return $ret;
	}
	
		
	/*
	 *	Funció escapaCadena, serveix per substituir caràcters "conflictius" amb la base de dades
	 * abans de fer l'insert o l'update a la base de dades. 
	 */
	public function escapaCadena($cadena){
		$bObrir=$this->isOpen;
		$ret=NULL;
		if (!$this->isOpen){
			$bObrir=$this->obrirConnexio($error);
		}
		if ($bObrir){ //Hem pogut obrir la connexió amb èxit. Executem la consulta SQL
			$ret=$this->con->real_escape_string($cadena);
		}
		return $ret;
		
		
	}
}
 
 
?>
<?php
/*
$con = new connexio();

$error = "";

$con->obrirConnexio($error);

$con->tancarConnexio();
*/

?>

