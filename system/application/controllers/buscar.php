<?php

class Buscar extends Controller
{
	// Tabla a consultar
	var $tabla;
	// Columnas array('Campo de la tabla'=>'Titulo de la columna')
	var $columnas;
	// Filtro de busqueda array('Campo de la tabla'=>'Titulo del campo','Campo de la tabla'=>array('Valor desde', 'Valor hasta'))
	var $filtro;
	// Valores a retornar array('Campo de la tabla'=>'Id del objeto que recibe')
	var $retornar;
	// Titulo
	var $titulo;
	// Usar varibles proveniente del uri en en este formato array(segmento=>'<#xxxx#>'), Ej array( 3=>'<#i#>')
	//por ahora solo definido para los campo de retorno
	var $p_uri=false;
	//Where adicional para la consulta
	var $where='';
	//Funciones javasrip que se ejecutaran en el targer despues del paso
	var $script=array();
	//parametros para los join en las consultas
	var $join=array();
	//Parametros para agupar
	var $groupby='';
	var $order_by=null;


	function Buscar(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function index(){
		$this->rapyd->load("datafilter2","datagrid");
		$this->_db2prop();
		
		$join=false;
		//extrae las varibles provenientes de las uris	
		if ($this->p_uri){
			$uris=array();
			foreach($this->p_uri as $segment=>$nombre){
				$valor=$this->uri->segment($segment);
				//if($valor==false){
				//	echo 'entre';
				//	$this->p_uri=false;
				//	break;	
				//}
				$uris[$nombre]=$valor;
			}
		}
		
		//Filtro
		$codigo=$this->filtro;
		$mSQL="SHOW FIELDS FROM $this->tabla WHERE Field IN ('".implode('\',\'',array_keys($this->filtro)).'\')';
		//echo $mSQL;
		$query = $this->db->query($mSQL);
				
		$prev= array_merge(array_keys($this->columnas), array_keys($this->retornar));
		$prev= array_unique($prev);     
		foreach($prev AS $ddata){
				//$ddata=$this->tabla.'.'.$ddata;
				$ddata=$ddata;
				$select[]=$ddata;
		}

		$filter = new DataFilter2("Parametros de B&uacute;squeda");
		
		$filter->db->select($select);
		$filter->db->from($this->tabla);
		
		if (!empty($this->groupby)) $filter->db->groupby($this->tabla.'.'.$this->groupby);
		
		if (count($this->join)>0){
			if(is_array($this->join)){
				foreach($this->join as $row){
					if (count($row)==3){  
						$join=true;
						$filter->db->join($row[0],$row[1],$row[2]);
					}
				}
			}
		}elseif (count($this->join)==3){  
			$join=true;
			$filter->db->join($this->join[0],$this->join[1],$this->join[2]);
		}
			
		foreach ($query->result() as $fila){
			$campo=$fila->Field;
			$titulo=$this->filtro[$campo];
			if(strncasecmp ($fila->Type,'date', 4)==0){
				if(is_array ($titulo)){
					$filter->$campo = new dateField($titulo[0],$campo,"Y/m/d");
					$filter->$campo->clause="where";
					$filter->$campo->operator=">=";
					$campo2=$campo.'2';
					$filter->$campo2 = new dateField($titulo[1],$campo2,"Y/m/d");
					$filter->$campo2->db_name=$this->tabla.'.'.$campo;
					$filter->$campo2->clause="where";
					$filter->$campo2->operator="<=";
				}else{
					$filter->$campo = new dateField($titulo,$campo,"Y/m/d");
					$filter->$campo->clause="where";
					$filter->$campo->operator="=";
				}
			}else{
				if(is_array ($titulo)){
					$filter->$campo = new inputField($titulo[0],$campo);
					$filter->$campo->clause="where";
					$filter->$campo->operator=">=";
					$campo2=$campo.'2';
					$filter->$campo2 = new inputField($titulo[1],$campo2);
					$filter->$campo2->db_name=$this->tabla.'.'.$campo;
					$filter->$campo2->db_name=$campo;
					$filter->$campo2->clause="where";
					$filter->$campo2->operator="<=";
				}else{ //
					$nobj=$campo.'_CDROPDOWN';
					$filter->$nobj = new dropdownField($titulo, "$nobj");
					$filter->$nobj->clause='';
					$filter->$nobj->style='width:120px';
					$filter->$nobj->option('both'  ,'Contiene');
					$filter->$nobj->option('after' ,'Comienza con');
					$filter->$nobj->option('before','Termina con' );
					$side=$filter->getval($nobj);
					$filter->$campo = new inputField($titulo,$campo);
					$filter->$campo->in=$nobj;
					if($side!==FALSE){
						$filter->$campo->like_side=$side;
					}
				}
			}
			$filter->$campo->db_name=$this->tabla.'.'.$campo;
		}
		
		if (!empty($this->where)) {
			if(isset($uris)){
				$valores=array_values($uris);
				for($i=0;$i<count($valores);$i++)
					$valores[$i]=$this->db->escape($valores[$i]);
				$where=str_replace(array_keys($uris),$valores,$this->where);
			}else{
				$where=$this->where;
			}
			$filter->db->where($where);
		};
		$filter->buttons("reset","search");
		$filter->build();
	
		//Tabla
		function j_escape($parr){
			$search[] = '\''; $replace[] = '\'+String.fromCharCode(39)+\'';
			$search[] = '"';  $replace[] = '\'+String.fromCharCode(34)+\'';
			$search[] = "\n"; $replace[] = '\\n';
			$search[] = "\r"; $replace[] = '\\r';

			$pattern = str_replace($search, $replace, $parr);
			return '\''.$pattern.'\'';
		}
		
		$link='<j_escape><#'.implode("#></j_escape>,<j_escape><#",array_keys($this->retornar)).'#></j_escape>';
		$link = "javascript:pasar($link);";
		$grid = new DataGrid("Resultados");
		$grid->use_function('j_escape');
		$grid->per_page = 20;
		if($this->order_by){
			$this->order_by;
			$grid->order_by($this->order_by,' ');
		}
		$i=0;
		foreach ($this->columnas as $campo => $titulo){
			$cp1=strrchr($campo, '.');
			if ($cp1) $campo=str_replace('.','',$cp1);
			if ($i==0){
				if(!$this->order_by)
				$grid->order_by($this->tabla.'.'.$campo);
				$grid->column_orderby($titulo,"<a href=\"$link\"><#$campo#></a>", $this->tabla.'.'.$campo);
			}elseif ($i==1)
				$grid->column($titulo,$campo);
			else
				$grid->column($titulo,$campo);
			$i++;
		} $grid->build();
		
		$i=0; $pjs1='';$pjs2='';
		foreach ($this->retornar as $campo => $id){
			if ($this->p_uri)
				$id = str_replace(array_keys($uris),array_values($uris),$id);
			if($i==0) $pjs1.="p$i";
			else      $pjs1.=",p$i";
			$pjs2.="window.opener.document.getElementById('$id').value = p$i;\n";
			$i++;
		}
		
		$jscript ="<SCRIPT LANGUAGE=\"JavaScript\">\n";
		$jscript.="function pasar($pjs1) {\n";
		$jscript.=" if (window.opener && !window.opener.closed){\n";
		$jscript.=$pjs2;
		$jscript.="   window.close();\n";
		foreach($this->script AS $funcion){
			$funcion = (isset($uris) ) ? str_replace(array_keys($uris),array_values($uris),$funcion) : $funcion;
			$jscript.=" window.opener.$funcion;\n";
		}
		$jscript.="}\n}\n</SCRIPT>";
		
		
		memowrite($grid->db->last_query(),'modbus2');
		$data["crud"]   = $filter->output . $grid->output;
		$data["titulo"] = '';
		$data['encab']=$this->titulo;
		$content["content"]   = $this->load->view('rapyd/crud', $data, true);
		$content["rapyd_head"]= $jscript.$this->rapyd->get_head();
		$content["code"]  = '';
		//$content["titulo"]=$this->titulo;
		$content["lista"] = "";
		
		$this->load->view('rapyd/modbus', $content);
	
		//echo $filter->db->last_query();
	}
	function _sess2prop(){
		$id = $this->uri->segment(3);	
		$arreglo=$this->session->flashdata('modbus');
		//echo '<pre>';print_r($this->session->userdata);echo '</pre>';
		//echo 'ARREGLO <pre>';print_r($arreglo);echo '</pre>';
		

		if($arreglo==FALSE or !array_key_exists($id, $arreglo)){
			echo '<pre>';print_r($this->session->userdata);echo '</pre>';
			exit("Error: No se han definido los parametros: $id");
		}
		//$modbus=$this->session->flashdata('modbus'.$id);
		$modbus=$arreglo[$id];
		$this->tabla   =$modbus['tabla'];
		$this->columnas=$modbus['columnas'];
		$this->filtro  =$modbus['filtro'];
		$this->retornar=$modbus['retornar'];
	  $this->titulo  =$modbus['titulo'];
	}	
	
	function _db2prop(){
		$id = $this->uri->segment(3);
		$mSQL="SELECT parametros FROM modbus WHERE id='$id'";
		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			$row = $query->row();
			$modbus=unserialize($row->parametros);
			$this->tabla   =$modbus['tabla'];
			$this->columnas=$modbus['columnas'];
			$this->filtro  =$modbus['filtro'];
			$this->retornar=$modbus['retornar'];
	  	$this->titulo  =$modbus['titulo'];
	  	if (isset($modbus['p_uri']))   $this->p_uri     =$modbus['p_uri'];
	  	if (isset($modbus['where']))   $this->where     =$modbus['where'];
	  	if (isset($modbus['script']))  $this->script    =$modbus['script'];
	  	if (isset($modbus['join']))    $this->join      =$modbus['join'];
	  	if (isset($modbus['groupby'])) $this->groupby   =$modbus['groupby'];
		if (isset($modbus['order_by'])) $this->order_by =$modbus['order_by'];
		}else{
			exit();
		}
		//echo '<pre>';print_r($this->session->userdata);echo '</pre>';
	}
	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `modbus` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `uri` varchar(50) NOT NULL default '',
		  `idm` varchar(50) NOT NULL default '',
		  `parametros` text,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1745 DEFAULT CHARSET=utf8";

		$this->db->simple_query($mSQL);
	}
}
?>
