<?php
class Common extends controller {
	function get_depto(){//usado por sinv
    	$mSQL=$this->db->query("SELECT depto, descrip as valor FROM dpto WHERE tipo='I' ORDER BY valor");
    	echo "<option value=''></option>";
    	if($mSQL){
    		foreach($mSQL->result() AS $fila ){
    			echo "<option value='".$fila->depto."'>".$fila->valor."</option>";
    		}
    	}
	}
	
	function add_depto(){//usado por sinv
		if(isset($_POST['valor'])){
			$valor=$_POST['valor'];
			$existe=$this->datasis->dameval("SELECT COUNT(descrip) FROM dpto WHERE descrip='$valor'");
			if($existe>0){
				echo "Y.a-Existe";
			}else{
				$depto=$this->sug('dpto');
				$agrego=$this->db->query("INSERT INTO dpto (depto,tipo,descrip) VALUES ('$depto','I','$valor')");
				if($agrego)echo $depto;
					else echo "N.o-SeAgrego";
			}
		}
	}
	
	function add_linea()//usado por sinv
	{
		if(isset($_POST['valor']) && isset($_POST['valor2'])){
			$valor=$_POST['valor'];
			$valor2=$_POST['valor2'];
			$existe=$this->datasis->dameval("SELECT COUNT(descrip) FROM line WHERE descrip='$valor' AND depto='$valor2'");
			if($existe>0){
				echo "Y.a-Existe";
			}else{
				$linea=$this->sug('line');
				$agrego=$this->db->query("INSERT INTO line (linea,depto,descrip) VALUES ('$linea','$valor2','$valor')");
				if($agrego)echo $linea;
					else echo "N.o-SeAgrego";
			}
		}
	}
	
	function get_linea(){//usado por sinv
		echo "<option value=''>Seleccione un Departamento</option>";
	    $depto=$this->input->post('depto');
	    if(!empty($depto)){
	    	$mSQL=$this->db->query("SELECT linea,descrip FROM line WHERE depto ='$depto'");
	    	if($mSQL){
	    		foreach($mSQL->result() AS $fila ){
	    			echo "<option value='".$fila->linea."'>".$fila->descrip."</option>";
	    		}
	    	}
	    }
	}
	
	function get_grupo(){//usado por sinv
    $linea=$this->input->post('linea');
   	if(!empty($linea)){
   		$mSQL=$this->db->query("SELECT grupo,nom_grup FROM grup WHERE linea ='$linea'");
   		if($mSQL){
   			echo "<option value=''>Seleccione una L&iacute;nea</option>";
   			foreach($mSQL->result() AS $fila ){
   				echo "<option value='".$fila->grupo."'>".$fila->nom_grup."</option>";
   			}
   		}
   	}else{
   		echo "<option value=''>Seleccione una L&iacute;nea primero</option>";
   	}
	}
	
	function add_grupo()//usado por sinv
	{
		if(isset($_POST['valor']) && isset($_POST['valor2']) && isset($_POST['valor3'])){
			$valor=$_POST['valor'];
			$valor2=$_POST['valor2'];
			$valor3=$_POST['valor3'];			
			$existe=$this->datasis->dameval("SELECT COUNT(nom_grup) FROM grup WHERE nom_grup='$valor' AND linea='$valor2' AND depto='$valor3'");
			if($existe>0){
				echo "Y.a-Existe";
			}else{				
				$grupo=$this->sug('grup');
				$agrego=$this->db->query("INSERT INTO grup (grupo,linea,nom_grup,tipo,depto) VALUES ('$grupo','$valor2','$valor','I','$valor3')");
				if($agrego)echo $grupo;
					else echo "N.o-SeAgrego";
			}
		}
	}
	
	function get_marca(){//usado por sinv
    	$mSQL=$this->db->query("SELECT marca as codigo, marca FROM marc ORDER BY marca");
    	if($mSQL){
    		foreach($mSQL->result() AS $fila ){
    			echo "<option value='".$fila->codigo."'>".$fila->marca."</option>";
    		}
    	}
	}
	
	function add_marc()//usado por sinv
	{
		if(isset($_POST['valor'])){
			$valor=$_POST['valor'];
			$agrego=$this->db->query("INSERT INTO marc (marca) VALUES ('$valor')ON DUPLICATE KEY UPDATE marca='$valor'");
			if($agrego)echo "s.i";
		}
	}
	
	function get_unidad(){//usado por sinv
    	$mSQL=$this->db->query("SELECT unidades, unidades as valor FROM unidad ORDER BY unidades");
    	echo "<option value=''></option>";
    	if($mSQL){
    		foreach($mSQL->result() AS $fila ){
    			echo "<option value='".$fila->unidades."'>".$fila->valor."</option>";
    		}
    	}
	}
	
	function add_unidad()//usado por sinv
	{
		if(isset($_POST['valor'])){
			$valor=$_POST['valor'];
			$agrego=$this->db->query("INSERT INTO unidad (unidades) VALUES ('$valor')ON DUPLICATE KEY UPDATE unidades='$valor'");
			if($agrego)echo "s.i";
		}
	}
	
	function sugerir_dpto(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN dpbi ON LPAD(codigo,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND codigo IS NULL LIMIT 1");
		echo $ultimo;		
	}
	
	function sugerir_grup(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN grup ON LPAD(grupo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND grupo IS NULL LIMIT 1");
		echo $ultimo;
	}
	
	function sugerir_line(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN line ON LPAD(linea,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND linea IS NULL LIMIT 1");
		echo $ultimo;
	}
	
	function sug($tabla=''){		
		if($tabla=='dpto'){
			$valor=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN dpto ON LPAD(depto,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND depto IS NULL LIMIT 1");
		}elseif($tabla=='line'){
				$valor=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN line ON LPAD(linea,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND linea IS NULL LIMIT 1");
		}elseif($tabla=='grup'){
				$valor=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN grup ON LPAD(grupo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND grupo IS NULL LIMIT 1");
		}
		return $valor;
	}
}
?>