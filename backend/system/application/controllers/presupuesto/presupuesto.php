<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
class presupuesto extends validaciones {
	
	function presupuesto(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		//$this->flongpres  =strlen(trim($this->formatopres));
		//$this->formatoestru=$this->datasis->traevalor('FORMATOESTRU');
		//$this->flongestru  =strlen(trim($this->formatoestru));  
	}
	
	function  index(){
		redirect("presupuesto/presupuesto/filteredgrid");
	}
	
	function filteredgrid(){
		//$this->datasis->modulo_id(101,1);
		
		$this->rapyd->load("datafilter","datagrid");
		
		$filter = new DataFilter("","presupuesto");
		$filter->db->select(array("REPLACE(CONCAT(codigoadm,codigopres),'.','') codigo2","CONCAT(codigoadm,'.',codigopres) codigo","codigoadm","codigopres","tipo","denominacion","asignacion"));
		
		$filter->tipo = new inputField("Fuente de Financiamiento", "tipo");
		$filter->tipo->size=20;
		
		$filter->codigoadm = new inputField("Estructura Administrativa", "codigoadm");
		$filter->codigoadm->size=20;
		
		$filter->codigopres = new inputField("C&oacute;digo Presupuesto", "codigopres");
		$filter->codigopres->size=20;
		
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=20;
		$filter->codigo->db_name="CONCAT(codigoadm,'.',codigopres)";
		
		$filter->denominacion = new inputField("Denominaci&oacute;n", "denominacion");
		$filter->denominacion->size=40;
		
		$filter->asignacion = new inputField("Asignaci&oacute;n", "asignacion");
		$filter->asignacion->size=20;
		$filter->asignacion->oper='=';
		$filter->asignacion->clause='where';
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		$uri = anchor('presupuesto/presupuesto/dataedit/show/<#codigoadm#>/<#tipo#>/<#codigopres#>','<#tipo#><#codigoadm#><#codigopres#>');
		
		$grid = new DataGrid("Lista de Sectores");
		
		$grid->order_by("codigoadm","asc");
		$grid->per_page = 20;
		
		$grid->column(""                      ,$uri              ,"align='left'");
		$grid->column_orderby("F. Financiamiento"     ,"tipo"         ,"tipo"         ,"align='left'");
		$grid->column_orderby("Estructura Adm."       ,"codigoadm"    ,"codigoadm"    ,"align='left'");
		$grid->column_orderby("C&oacute;digo Presup." ,"codigopres"   ,"codigopres"   ,"align='left'");
		$grid->column_orderby("Denominacion"          ,"denominacion" ,"denominacion" ,"align='left'");
		$grid->column_orderby("Asignaci&oacute;n"     ,"asignacion"   ,"asignacion"   ,"align='right'");
		///$grid->column("Aumento"               ,"aumento"      ,"align='left'");
		///$grid->column("Disminuci&oacute;n"    ,"disminucion"  ,"align='left'");
		//$grid->column("Comprometido"          ,"comprometido" ,"align='left'");
		///$grid->column("Causado"               ,"causado"      ,"align='left'");
		///$grid->column("Opago"                 ,"opago"        ,"align='left'");
		///$grid->column("Pagado"                ,"pagado"       ,"align='left'");
		
		$grid->add("presupuesto/presupuesto/dataedit/create");
		
		$grid->build();
		
		//echo $grid->db->last_query();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script']  = script("jquery.js")."\n";
		$data['title']   = 'Presupuesto';
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit($action=''){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("dataedit2");
		$this->load->helper('form');
		
		$link =site_url('presupuesto/ppla/autocompleteppla');
		$script="
		$('.inputnum').numeric('.');
		$(document).ready(function() {
			$('#codigopres').setMask('9.99.99.99.99.999');
			$('#codigopres').focus();
			$.post('$link',{ partida:'' },function(data){
				datos=jQuery.parseJSON(data);
				
				$('#codigopres').autocomplete({
					delay: 0,
					minLength: 4,
					source: datos,
					focus: function( event, ui ) {
						
						$( '#denominacion').val( ui.item.denominacion );
						return false;
					},
					select: function( event, ui ) {
						$( '#codigopres').val( ui.item.codigo );
						$( '#denominacion').val( ui.item.denominacion );
						$('#asignacion').focus();
						return false;
					}
				})
				.data( 'autocomplete' )._renderItem = function( ul, item ) {
					return $( '<li></li>' )
					.data( 'item.autocomplete', item )
					.append( '<a>' +item.codigo+'-'+ item.denominacion + '</a>' )
					.appendTo( ul );
				};
			});
		});
		";
		
		$edit = new DataEdit2("Presupuesto","presupuesto");
		$edit->back_url = "presupuesto/presupuesto";
		$edit->post_process('update'  ,'_post');
		$edit->post_process('insert'  ,'_post');
		$edit->script($script,'create');
		$edit->script($script,'modify');
		
		$edit->codigoadm = new dropdownField("Estructura Administrativa","codigoadm");
		$edit->codigoadm->options("SELECT codigo,CONCAT_WS(' ',codigo,denominacion) FROM estruadm WHERE LENGTH(codigo)=(SELECT LENGTH(valor) FROM valores WHERE nombre='FORMATOESTRU') ORDER BY codigo");
		//$edit->codigoadm->mode = "autohide";
		$edit->codigoadm->rule ="required";
		$edit->codigoadm->style ="width:500px;";
		
		$edit->tipo = new dropdownField("Fuente de Financiamiento","tipo");
		$edit->tipo->options("SELECT fondo, fondo AS val FROM fondo ORDER BY fondo desc");
		$edit->tipo-> style='width:150px;';
		//$edit->tipo->mode = "autohide";
		$edit->tipo->rule ="required";
		
		$edit->codigopres = new inputField("C&oacute;digo Presupuesto", "codigopres");
		$edit->codigopres->db_name = "codigopres";
		$edit->codigopres->size=20;
		//$edit->codigopres->mode = "autohide";
		$edit->codigopres->rule ="required";
		$edit->codigopres->autocomplete=false;
		
		$edit->denominacion = new textareaField("Denominacion", "denominacion");
		$edit->denominacion->rows=2;
		$edit->denominacion->cols=70;
		$edit->denominacion->rule ="required";
		 
		if($this->datasis->puede(302)){
		    $edit->asignacion = new inputField("Asignaci&oacute;n", "asignacion");
		    $edit->asignacion->size=20;
		    $edit->asignacion->css_class='inputnum';
		    $edit->asignacion->rule="numeric";
		    //$edit->asignacion->mode = "autohide";
		    //$edit->asignacion->when = array("show");
		}
		
		$edit->gasinv = new dropdownField("Tipo","gasinv");
		$edit->gasinv->option("G","GASTO");
		$edit->gasinv->option("I","INVERSION");
		$edit->gasinv->rule ="required";
		$edit->gasinv->style ="width:500px;";
    
		$edit->aumento = new inputField("Aumento", "aumento");
		$edit->aumento->size=20;
		$edit->aumento->mode = "autohide";
		$edit->aumento->when = array("show");
    
		$edit->disminucion = new inputField("Disminuci&oacute;n", "disminucion");
		$edit->disminucion->size=20;
		$edit->disminucion->mode = "autohide";
		$edit->disminucion->when = array("show");
		 
		$edit->comprometido = new inputField("Comprometido", "comprometido");
		$edit->comprometido->size=20;
		$edit->comprometido->mode = "autohide";
		$edit->comprometido->when = array("show");
		 
		$edit->causado = new inputField("Causado", "causado");
		$edit->causado->size=20;
		$edit->causado->mode = "autohide";
		$edit->causado->when = array("show");
		 
		$edit->pagado = new inputField("Pagado", "pagado");
		$edit->pagado->size=20;
		$edit->pagado->mode = "autohide";
		$edit->pagado->when = array("show");
		
		
		
		$bs='';
		
		if($action=='show' || $action=='modify'){
			$codigoadm = $edit->getval('codigoadm');
			$tipo      = $edit->getval('tipo');
			
			$hidden = array('codigoadm' => $codigoadm, 'tipo' => $tipo);
			
			$bs = form_open('presupuesto/presupuesto/dataedit/create','',$hidden);
			$bs.= form_submit('mysubmit', 'Agregar Similar');
			$bs.= form_close();			
		}
		
		
		//$action = "javascript:window.location='" .site_url('presupuesto/presupuesto/dataedit/'.$estruadm.'/'.$fondo.'/create'). "'";
		//$edit->button_status("btn_agregar",'Agregar Similar',$action,"TL","show");
		
		//$edit->_button_container["TL"][]=$bs;
				
		$edit->buttons("add","modify","save", "undo","delete", "back");
		$edit->build();
		
		$style='
		.ui-autocomplete {
		  max-height: 250px;
		  overflow-y: auto;
		  max-width: 600px;
		}
		 html.ui-autocomplete {
		  height: 250px;
		  width: 600px;
		}
		';
		
		$data['content'] = $bs.$edit->output;
		$data["style"]   = $style;
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').script("plugins/jquery.numeric.pack.js").$this->rapyd->get_head();
		$data['title']   = ' Presupuesto ';
		$this->load->view('view_ventanas', $data);
	}
	
	function filteredgrid2(){
		//$this->datasis->modulo_id(101,1);
		
		$this->rapyd->load("datafilter","datagrid");
		
		$filter = new DataFilter("","presupuesto");
		$filter->db->select(array("REPLACE(CONCAT(codigoadm,codigopres),'.','') codigo2","CONCAT(codigoadm,'.',codigopres) codigo","codigoadm","codigopres","tipo","denominacion","asignacion"));
		
		$filter->tipo = new inputField("Fuente de Financiamiento", "tipo");
		$filter->tipo->size=20;
		
		$filter->codigoadm = new inputField("Estructura Administrativa", "codigoadm");
		$filter->codigoadm->size=20;
		
		$filter->codigopres = new inputField("C&oacute;digo Presupuesto", "codigopres");
		$filter->codigopres->size=20;
		
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=20;
		$filter->codigo->db_name="CONCAT(codigoadm,'.',codigopres)";
		
		$filter->denominacion = new inputField("Denominaci&oacute;n", "denominacion");
		$filter->denominacion->size=40;
		
		$filter->asignacion = new inputField("Asignaci&oacute;n", "asignacion");
		$filter->asignacion->size=20;
		$filter->asignacion->oper='=';
		$filter->asignacion->clause='where';
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		function fondo($valor,$codigo){
			$input = new inputField2('Title', 'F'.$valor.'_'.$codigo);
			$input->status     = 'create';
			$input->size       =10;
			$input->insertValue=$valor;
			$input->style      ="height:18px;width:100px; padding:0px;margin:0px";
			$input->build();
			return $input->output;
		}
		
		function codigo($valor,$codigo){
			$input = new inputField2('Title', 'C'.$valor.'_'.$codigo);
			$input->status     = 'create';
			$input->size       =20;
			$input->insertValue=$codigo;
			$input->style      ="height:18px;width:200px; padding:0px;margin:0px";
			$input->build();
			return $input->output;
		}
		
		function denominacion($tipo,$codigo,$valor){
			$input = new inputField2('Title', 'D'.$tipo.'_'.$codigo);
			$input->status     = 'create';
			$input->size       =50;
			$input->insertValue=$valor;
			$input->style      ="height:18px;width:100%; padding:0px;margin:0px";
			$input->build();
			return $input->output;
		}
		
		function asignacion($tipo,$codigo,$valor){
			$input = new inputField2('Title', 'A'.$tipo.'_'.$codigo);
			$input->status     = 'create';
			$input->size       =10;
			$input->insertValue=$valor;
			$input->css_class  ='inputnum';
			$input->style      ="height:18px;width:100%; padding:0px;margin:0px";
			$input->build();
			return $input->output;
		}
		
		$grid = new DataGrid("Asignaci&oacute;n Presupuestaria");
		$grid->use_function('fondo','codigo','denominacion','asignacion');
		$grid->order_by("codigoadm","asc");
		$grid->per_page = 20;

		$grid->column("F. Financia."                 ,"<fondo><#tipo#>|<#codigo#>|</fondo>"                                ,"align='left' ");
		$grid->column("C&oacute;digo Presupuestario" ,"<codigo><#tipo#>|<#codigo#>|</codigo>"                              ,"align='left' ");
		$grid->column("Denominaci&oacute;n"          ,"<denominacion><#tipo#>|<#codigo#>|<#denominacion#></denominacion>"  ,"align='left' margin='0px' padding='0px' cellpadding='0px' cellspacing='0px' width='100%' height='2px' ");
		$grid->column("Asignaci&oacute;n"            ,"<asignacion><#tipo#>|<#codigo#>|<#asignacion#></asignacion>"        ,"align='right'");
		
		$grid->build();
		
		//echo $grid->db->last_query();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script']  = script("jquery.js")."\n";
		$data['title']   = 'Formulaci&oacute;n de Presupuesto';
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _post($do){
		$codigoadm  = $do->get('codigoadm' );
		$tipo       = $do->get('tipo'      );
		$codigopres = $do->get('codigopres');
		$codigoadm  = $this->db->escape($codigoadm);
		$tipo       = $this->db->escape($tipo     );
		
		//$codigos = explode('.',$codigopres);
		//$max     = count($codigos);
		//
		//$i=0;
		//for($i=0 ; $i<$max-1;$i++){
		//	$temp = array();
		//	$j=0;
		//	for($j=0 ; $j<=$i;$j++){
		//		$temp[$j]=$codigos[$j];
		//	}
		//	$c = $this->db->escape(implode('.',$temp));
		//	$query = "INSERT IGNORE INTO presupuesto (`codigoadm`,`tipo`,`codigopres`) values ($codigoadm,$tipo,$c)";
		//	$this->db->simple_query($query);
		//}
		//$c = $this->db->escape($this->datasis->traevalor('PARTIDAIVA'));
		////$d = $this->db->escape($this->datasis->dameval("SELECT denominacion FROM ppla WHERE codigo=$c"));
		//$query = "INSERT IGNORE INTO presupuesto (`codigoadm`,`tipo`,`codigopres`,`denominacion`) values ($codigoadm,$tipo,$c,'IMPUESTO AL VALOR AGREGADO')";
		//$this->db->simple_query($query);
	}

	function arregla(){
		$result = $this->db->query("SELECT codigoadm,tipo,codigopres FROM presupuesto");
		foreach($result->result() as $row){
			
			$codigoadm  = $this->db->escape($row->codigoadm);
			$tipo       = $this->db->escape($row->tipo     );
			$codigopres = $row->codigopres;
			
			$codigos = explode('.',$codigopres);
			$max     = count($codigos);
			
			$i=0;
			for($i=0 ; $i<$max-1;$i++){
				$temp = array();
				$j=0;
				for($j=0 ; $j<=$i;$j++){
					$temp[$j]=$codigos[$j];
				}
				$c = $this->db->escape(implode('.',$temp));
				$query = "INSERT IGNORE INTO presupuesto (`codigoadm`,`tipo`,`codigopres`) values ($codigoadm,$tipo,$c)";
				$this->db->simple_query($query);
			}
			$c = $this->db->escape($this->datasis->traevalor('PARTIDAIVA'));
			$query = "INSERT IGNORE INTO presupuesto (`codigoadm`,`tipo`,`codigopres`) values ($codigoadm,$tipo,$c)";
			$this->db->simple_query($query);
		}
		$this->db->query("CALL sp_presupuestonivelar()");
		$this->db->query("CALL sp_presucalct()");
	}
	
	
	function get_tipo(){
		$codigoadm=$this->input->post('codigoadm');
		$codigoadm=$this->db->escape($codigoadm);
		if($codigoadm!==false){
			$query=$this->db->query("SELECT tipo  FROM presupuesto WHERE codigoadm=$codigoadm GROUP BY  tipo");
			if($query){
			if($query->num_rows()>1)echo "<option value=''>Seleccionar</option>";
				if($query->num_rows()>0){					
					foreach($query->result() AS $fila ){
						echo "<option value='".$fila->tipo."'>".$fila->tipo."</option>";
					}
				}else{
					echo "<option value=''>No hay registros disponibles</option>";
				}
			}
		}
	}
	
	
	function get_estruadm(){
		$tipo=$this->input->post('tipo');
		if($tipo!==false){
			$query=$this->db->query("SELECT b.codigo,b.denominacion  FROM estruadm b JOIN presupuesto a ON b.codigo=a.codigoadm WHERE a.tipo='$tipo' GROUP BY  b.codigo");
			if($query){
				if($query->num_rows()>0){
					echo "<option value=''>Seleccionar</option>";
					foreach($query->result() AS $fila ){
						echo "<option value='".$fila->codigo."'>".$fila->codigo.' '.$fila->denominacion."</option>";
					}
				}else{
					echo "<option value=''>No hay registros disponibles</option>";
				}
			}
		}
	}
  
	function get_estrupres(){
		$tipo=$this->db->escape($this->input->post('tipo'));
		$estruadm=$this->db->escape($this->input->post('codigoadm'));
		$ff=explode('.',$this->formatopres);
		if(count($ff)>2)
			$cana=strlen($ff[0].$ff[1])+1;
		else
			$cana=strlen($ff[0])+1;
		if($tipo!==false AND $estruadm!==false){
			$query=$this->db->query("SELECT b.codigo,b.denominacion  FROM presupuesto AS a JOIN ppla AS b ON b.codigo=a.codigopres WHERE a.tipo=$tipo AND a.codigoadm=$estruadm AND LENGTH(a.codigopres)<=$cana GROUP BY  b.codigo");
			if($query){
				if($query->num_rows()>0){
					echo "<option value=''>Seleccionar</option>";
					foreach($query->result() AS $fila ){
						echo "<option value='".$fila->codigo."'>".$fila->codigo.' '.$fila->denominacion."</option>";
					}
				}else{
					echo "<option value=''>No hay registros disponibles</option>";
				}
			}
		}
	}
	
	function get_estruprest(){
		$tipo=$this->db->escape($this->input->post('tipo'));
		$estruadm=$this->db->escape($this->input->post('codigoadm'));
		//$ff=explode('.',$this->formatopres);
		//if(count($ff)>2)
		//	$cana=strlen($ff[0].$ff[1])+1;
		//else
		//	$cana=strlen($ff[0])+1;
			
		if($tipo!==false AND $estruadm!==false){
			$query=$this->db->query("SELECT b.codigo,b.denominacion  FROM presupuesto AS a JOIN ppla AS b ON b.codigo=a.codigopres WHERE a.tipo=$tipo AND a.codigoadm=$estruadm AND b.movimiento='S' GROUP BY  b.codigo");
			if($query){
				if($query->num_rows()>0){
					echo "<option value=''>Seleccionar</option>";
					foreach($query->result() AS $fila ){
						echo "<option value='".$fila->codigo."'>".$fila->codigo.' '.$fila->denominacion."</option>";
					}
				}else{
					echo "<option value=''>No hay registros disponibles</option>";
				}
			}
		}
	}
	
	function auto_presupuesto($campo,$cod=FALSE,$fondo=FALSE,$codigoadm = FALSE){
	  		
		if($cod!==false){
			$mSQL="SELECT $campo FROM presupuesto WHERE $campo LIKE '$cod%' ";
			if($fondo!==FALSE)
				$mSQL.=" AND tipo = ".$this->db->escape($fondo);
			if($codigoadm!==FALSE)
				$mSQL.=" AND codigoadm = ".$this->db->escape($codigoadm);
			
			$query=$this->db->query($mSQL);
			if($query->num_rows() > 0){
				foreach($query->result() AS $row){
					echo $row->$campo."\n";
				}
			}
		}
	}
	
	
	function auto_presaldo3($cod=FALSE,$codigoadm=''){
		//if($cod!==false){
		  $codigoadm=$this->input->post('codigoadm');
      $fondo=$this->input->post('fondo');
		  $this->db->query('UPDATE valores SET valor="'.$codigoadm.'" where  nombre="ARMAS"');
      
      if($cod!==false){
  			$mSQL="SELECT codigo AS c1,denominacion AS c2 FROM v_presaldo WHERE movimiento = 'S' AND codigopres LIKE '$cod%'";//
  			if(!empty($codigoadm))
  				$mSQL.=" AND codigoadm = ".$this->db->escape($codigoadm);
  			
  			if(!empty($fondo))
  				$mSQL.=" AND fondo = ".$this->db->escape($fondo);
  			
  			$query=$this->db->query($mSQL);
        
        
  			$salida = '';
  			if($query->num_rows() > 0){
  				foreach($query->result() AS $row){
  					$salida.=$row->c1.'|'.$row->c2.",";
  					$salida.=$row->$campo.",";
  				}
  			}
  			echo $salida;
		  }
	}
	
	function auto_presaldo($codigoadm='',$fondo=''){//,
		//if($cod!==false){
			$mSQL="SELECT codigo AS c1,denominacion AS c2 FROM v_presaldo WHERE saldo > 0 AND movimiento = 'S'";
			if(!empty($codigoadm) && $codigoadm!='null')
				$mSQL.=" AND codigoadm = ".$this->db->escape($codigoadm);
			
			if(!empty($fondo) && $fondo!='null')
				$mSQL.=" AND fondo = ".$this->db->escape($fondo);
			
			$query=$this->db->query($mSQL);
			$salida = '';
			if($query->num_rows() > 0){
				foreach($query->result() AS $row){
					echo $salida.=$row->c1.'|'.$row->c2."\n";
				}
			}
			//echo $salida;
			
		//}
	}
	
	function auto_presaldo2(){
		//if($cod!==false){
			$mSQL="SELECT codigo AS c1,denominacion AS c2 FROM v_presaldo WHERE saldo > 0 AND movimiento = 'S'";
						
			$query=$this->db->query($mSQL);
			$salida = '';
			if($query->num_rows() > 0){
				foreach($query->result() AS $row){
					$salida.=$row->c1.'|'.$row->c2."\n";
				}
			}
			echo $salida;
		//}
	}
	
	function autocompletecompleto(){
		$query  ="SELECT CONCAT(codigoadm,'.',codigopres) codigop,tipo fondo ,CONCAT(codigoadm,'.',codigopres) label,denominacion FROM presupuesto";
			    $mSQL   = $this->db->query($query);
			    $arreglo= $mSQL->result_array($query);
			    foreach($arreglo as $key=>$value)
				    foreach($value as $key2=>$value2) 
				    $arreglo[$key][$key2] = ($value2);
				     
		echo json_encode($arreglo);
	}

	function calcula(){
	    $this->db->query("call sp_view_pres()");
	}

	function instalar(){
		$query="ALTER TABLE `presupuesto`  ADD COLUMN `uejecutora` VARCHAR(4) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `presupuesto` CHANGE COLUMN `uejecutora` `uejecutora` VARCHAR(8) NULL DEFAULT NULL AFTER `movimiento`";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `presupuesto` ADD COLUMN `gasinv` CHAR(1) NULL DEFAULT 'G'";
		$this->db->simple_query($query);
	}
}
?>
