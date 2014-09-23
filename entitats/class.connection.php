<?php

/*--------------------------------------------------------------------------------------
 *      M�dul 7: Desenvolupament Web en Entorn Servidor
 *             
 *             Activitat: Acc�s a bases de dades: BasesDades.php
 *      
 *      Autor: Maribel Alcoba Laranjinha
 *             Curs: 2012/2013
 *             
 *      Descripci�: Classe per accedir a bases de dades
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
	 *	Constructor, crea una nova inst�ncia de connexi� a la base de dades.
	 * Tamb� crea una inst�ncia de la connexi� (inicialitza), per� no obre la connexi� a la base de dades
	 * @param string server -> adre�a del servidor de base de dades
	 * @param string usuari -> usuari de base de dades
	 * @param string password ->password d'acc�s a la base de dades
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
	  *m�tode p�blic obrirConnexio. Obre realment la connexi� a la base de dades.
	  * @param string error Out, error de connexi� en cas de fallar aquesta.
	  * @return boolean true si la connexi� s'ha obert correctament. false en cas contrari. 
	  */
	 public function obrirConnexio(&$error){
		 
		$bret=false;
		 /*Si no hem tancat la connexi�, no hem d'intentar obrir-la de nou*/
		if(!$this->isOpen){
			$this->con=mysqli_init();
			if(!$this->con){
				 throw new Exception('Error en inicialitzar la connexi� (mysqli_init)');
			}
			else{
				$bret=$this->con->real_connect($this->host,$this->usuari,$this->password,$this->bdname);
                                //echo "<br> La connexi� ha estat oberta correctament";
                                
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
	  *  M�tode p�blic tancarConnexi�. Comprova si la connexi� est� oberta, en cas afirmatiu, tanca la connexi�
	  * @return boolean: true si s'ha pogut tancar la connexi�, false en cas contrari.
	  */
	 public function tancarConnexio(){
		 $bret=false;
		 if($this->isOpen) {
			 
			 $bret=$this->con->close();
			 if ($bret) $this->isOpen=false;	
			 
		 }
                 //echo "<br> S'ha tancat la connexi� correctament";
		 return $bret;
	 
	}
	
	/*
	 * M�tode p�blic executarConsulta. Si troba que la connexi� a la base de dades
	 * no est� oberta, obre la connexi�, executa la consulta que es passa per par�metre.
	 * Aquesta consulta ha d'estar convenientment escapada.
	 * Nom�s executa consultes �niques. Retorna un array amb tots els resultats de la consulta
	 * */
	public function executarConsulta($query, &$error){
		$error="";
		$bObrir=$this->isOpen;
		$ret=NULL;
		if (!$this->isOpen){
			$bObrir=$this->obrirConnexio($error);
		}
		if ($bObrir){ //Hem pogut obrir la connexi� amb �xit. Executem la consulta SQL
			if( $this->con->real_query($query)){ //Si executem la consulta i ha anat b�
				$resultatConsulta=$this->con->use_result(); //Accedim a les dades de la consulta
				
                                if($resultatConsulta){
                                    
                                    $ret=array();
                                
                                    while($row=$resultatConsulta->fetch_array(MYSQLI_ASSOC)){
                                            array_push($ret,$row); //Afegim totes les files retornades a l'array de sortida
                                    }
                                    $resultatConsulta->free();
                                    
                                }
                                
			}
			else{//S'ha produ�t algun error i la consulta no ha tornat res...
							
				 $error='Error Query '.$this->con->sqlstate ;

			}
			
		}
		return $ret;
	}
	
		
	/*
	 *	Funci� escapaCadena, serveix per substituir car�cters "conflictius" amb la base de dades
	 * abans de fer l'insert o l'update a la base de dades. 
	 */
	public function escapaCadena($cadena){
		$bObrir=$this->isOpen;
		$ret=NULL;
		if (!$this->isOpen){
			$bObrir=$this->obrirConnexio($error);
		}
		if ($bObrir){ //Hem pogut obrir la connexi� amb �xit. Executem la consulta SQL
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

