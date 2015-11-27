<?php
include('metodos.php');

class Reglas extends Metodos {
	
	var $html =null;
	
	function Reglas(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(601,1);
		//$this->rapyd->set_connection('supermer');
		//$this->load->database('supermer',TRUE);
		$this->html = new Html();
	}
	
	function index() {
		$this->rapyd->load("datagrid","dataform");
		$fecha=$this->uri->segment(4);
		$form = new DataForm();  
		$form->title('Fecha para la ejecuci&oacute;n');
		$form->fecha = new dateonlyField("Fecha", "fecha","d/m/Y");
		$form->fecha->size = 10;
		$form->fecha->insertValue = ($fecha ? $fecha : date("Ymd"));
		$form->fecha->append("<input type='hidden' name='modulo' id='modulo'>");
		$form->build_form();
		
		$link ="<a href='#' onclick='verregla(\"<#modulo#>\")'>Ver Detalle</a>";
		$link2="<a href='#' onclick='ejecutar(\"<#modulo#>\")'>Ejecutar</a>";
		$grid = new DataGrid();
		$grid->db->select('modulo, descripcion, (SELECT COUNT(*) FROM reglascont a WHERE a.modulo=b.modulo) as cant');
		$grid->db->from('reglascont b');
		$grid->db->where('regla','1');
		$grid->db->groupby('modulo');
		$grid->db->orderby('modulo,regla');
		$grid->column("Modulo"     , "modulo"     );
		$grid->column("Descripcion", "descripcion");
		$grid->column("Reglas"     , "cant",'align="center"');
		$grid->column(''           , $link ,'align="center"');
		$grid->column(''           , $link2,'align="center"');
		//$grid->column(''           , "Explicar",'align="center"');

		$grid->build();
		
		$data['script']="<script type='text/javascript'>
		function ejecutar(modulo) {
			document.getElementById('modulo').value=modulo;
			document.getElementById('df1').action='".site_url('contabilidad/reglas/ejecutar')."';
			document.getElementById('df1').submit();
		}
		function verregla(modulo) {
			document.getElementById('modulo').value=modulo;
			document.getElementById('df1').action='".site_url('contabilidad/reglas/detalle')."';
			document.getElementById('df1').submit();
		}
		</script>";
		
		$data['content'] =$form->output.$grid->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   ='Reglas de Contabilidad';
		$this->load->view('view_ventanas', $data);
	}
	
	function detalle() {
		$this->rapyd->load("datagrid","dataform");
		$modulo=($this->uri->segment(4) ? $this->uri->segment(4) : $this->input->post('modulo'));
		if(!$modulo) redirect('/contabilidad/reglas');
		
		$form = new DataForm('contabilidad/reglas/ejecutar');  
		$form->title('Fecha para la ejecuci&oacute;n');
		$form->fecha = new dateonlyField("Fecha", "fecha","d/m/Y");
		$form->fecha->size = 10;
		$form->fecha->insertValue =($this->input->post('fecha') ? $this->input->post('fecha') : date("Ymd"));
		$form->fecha->append("<input type='hidden' name='modulo' value='$modulo' id='modulo'>");
		$form->submit = new submitField("Ejecutar","btn_submit");
		$form->submit->in='fecha';
		$form->build_form();
		
		$link =anchor('/contabilidad/reglas/dataedit/<#modulo#>/show/<#modulo#>/<#regla#>','Ver regla');
		$link2=anchor('/contabilidad/reglas/duplicar/<#modulo#>/<#regla#>','Duplicar');
		$action = "javascript:window.location='" . site_url('contabilidad/reglas') . "'";
		$grid = new DataGrid();
		$grid->add("contabilidad/reglas/dataedit/$modulo/create",'Agregar Regla');
		$grid->button('cancelar', RAPYD_BUTTON_BACK, $action);
		$grid->db->select('modulo, regla, tabla, descripcion,condicion');
		$grid->db->from('`reglascont`');
		$grid->db->where('modulo',$modulo);
		$grid->db->orderby('modulo,tabla,regla');
		$grid->column("Modulo"     , "modulo"     );
		$grid->column("Regla"      , "regla"      );
		$grid->column("Tabla"      , "tabla"      );
		$grid->column("Descripcion", "descripcion");
		//$grid->column("Condicion", "condicion");
		
		$grid->column(''           , $link ,'align="center"');
		$grid->column(''           , $link2,'align="center"');
		$grid->build();
		
		$data['content'] =$form->output.$grid->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   ="Detalle de regla $modulo";
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		$this->rapyd->load("dataedit");
		$modulo=($this->uri->segment(4) ? $this->uri->segment(4) : $this->input->post('modulo'));
		$uri=$this->rapyd->uri->get("show");

		$edit = new DataEdit("Reglas Contabilidad","reglascont");
		$edit->back_url = 'contabilidad/reglas/detalle/'.$modulo;
		$edit->modulo = new inputField("Modulo", "modulo");
		$edit->modulo->value=$modulo;
		$edit->modulo->rule= "required";
		//$edit->modulo->mode="autohide";
		$edit->modulo->maxlength=20;
		$edit->modulo->size=5;
		
		$edit->regla = new inputField("Regla", "regla");
		$edit->regla->rule= "required";
		$edit->regla->value=$this->_rdisponible($modulo);
		$edit->regla->maxlength=3;
		$edit->regla->size=4;
		
		$edit->descripcion = new inputField("Descripci&oacute;n", "descripcion");
//		$edit->descripcion->maxlength=40;
		
		$edit->tabla = new dropdownField("Tabla", "tabla");
		$edit->tabla->option("ITCASI","ITCASI");  
		$edit->tabla->option("CASI","CASI");  
	
		$edit->control = new inputField("Control", "control");
		$edit->control->maxlength=40;
    
		//$edit->control = new dropdownField("Control", "control");
		//$edit->control->option("transac","transac");  
		//$edit->control->option("fecha","fecha");  
		
		$edit->origen = new textareaField("Tabla Or&iacute;gen", "origen");
		
		$edit->condicion = new textareaField("Condiciones", "condicion");

		$edit->agrupar = new textareaField("Agrupar", "agrupar");

		$edit->concepto = new textareaField("Conceptos", "concepto");
		
		$edit->fecha = new textareaField("Fecha", "fecha");

		$edit->comprob = new textareaField("Comprobante", "comprob");
		$edit->origen->cols =$edit->condicion->cols =$edit->agrupar->cols =$edit->concepto->cols =$edit->fecha->cols = $edit->comprob->cols = 90;  
		$edit->origen->rows =$edit->condicion->rows =$edit->agrupar->rows =$edit->concepto->rows =$edit->fecha->rows =$edit->comprob->rows = 2;
		$edit->origen->maxlength=$edit->condicion->maxlength=$edit->agrupar->maxlength=$edit->concepto->maxlength=$edit->fecha->maxlength=$edit->comprob->maxlength=255;

		$edit->cuenta = new textareaField("Cuenta", "cuenta");
		$edit->cuenta->cols = 90;  
		$edit->cuenta->rows = 2;
		$edit->cuenta->maxlength=255;

		$edit->referen = new textareaField("Referencia", "referen");
		$edit->referen->cols = 90;  
		$edit->referen->rows = 2;
		$edit->referen->maxlength=255;

		$edit->debe = new textareaField("Debe", "debe");
		$edit->debe->cols = 90;  
		$edit->debe->rows = 2;
		$edit->debe->maxlength=255;

		$edit->haber = new textareaField("Haber", "haber");
		$edit->haber->cols = 90;  
		$edit->haber->rows = 2;
		$edit->haber->maxlength=255;
		
		$edit->havin = new textareaField("Teniendo en Cuenta (having)", "havin");
		$edit->havin->cols = 90;  
		$edit->havin->rows = 2;

		$edit->ccosto = new textareaField("Centro de Costo", "ccosto");
		$edit->ccosto->cols = 90;  
		$edit->ccosto->rows = 2;
		$edit->ccosto->maxlength=255;

		$edit->sucursal = new textareaField("Sucursal", "sucursal");
		$edit->sucursal->cols = 90;  
		$edit->sucursal->rows = 2;
		$edit->sucursal->maxlength=255;
		
		$edit->mbanc_id = new textareaField("Ref. Movimiento Banacario", "mbanc_id");
		$edit->mbanc_id->cols = 90;  
		$edit->mbanc_id->rows = 2;
		

		if ($this->uri->segment(4)==="1")
			$edit->buttons("modify", "save", "undo", "back");
		else 
			$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] =$edit->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   ="Editar regla";

		$this->load->view('view_ventanas', $data);
	}

	function ejecutar() {
		$this->rapyd->load("datagrid2","fields");

		function dif($a,$b){
			return number_format($a-$b,2,',','.');
		}

		$modulo = $_POST['modulo'];
		$mFECHA = date("Ymd",timestampFromInputDate($_POST['fecha'], 'd/m/Y'));
		$mTABLA  =$this->datasis->dameval("SELECT origen  FROM reglascont WHERE modulo='$modulo' AND regla=1 ");
		$mCONTROL=$this->datasis->dameval("SELECT control FROM reglascont WHERE modulo='$modulo' AND regla=1 ");
		$action = "javascript:window.location='" . site_url("contabilidad/reglas/index/$mFECHA") . "'";
		$data['content']='';
		$pos=stripos($mCONTROL,'YEAR_MONTH');
		if($pos!==FALSE){
		  $mFECHA='extract(year_month from '.$mFECHA.')';
		}else{
			$pos=0;
			$pos=stripos($mCONTROL,'MONTH');
			if($pos!==FALSE){
			  $mFECHA='MONTH('.$mFECHA.')';
			}
		}
		
		$query=$this->db->query("SELECT $mCONTROL FROM $mTABLA WHERE $mCONTROL=$mFECHA GROUP BY $mCONTROL ");

		foreach ($query->result_array() as $fila){
			$aregla = $this->_hace_regla($modulo, $mCONTROL, $fila[$mCONTROL]);
			//echo '<pre>';print_r($aregla);'</pre>';
			$encab=$encab_titu=$pivote=array();
			//Construye los encabezados
			print_r($aregla);
			foreach ($aregla['casi'] as $mSQL){

				$casi_query=$this->db->query($mSQL);
				if ($casi_query->num_rows() > 0){
					$row = $casi_query->row();
					$encab[$row->comprob]=array();
					$encab_titu[$row->comprob]='<b>Comprobante:</b> '.$row->comprob.' <b>Fecha:</b> '.date("d/m/Y",timestampFromInputDate($row->fecha, 'Y-m-d')).' <b>Concepto:</b> '.$row->concepto;
				}
			}
			//echo $query;
			//Construye la data de los encabezados
			foreach ($aregla['itcasi'] as $mSQL){

				$itcasi_query=$this->db->query($mSQL);
				$acumulador=array(0,0);
				if ($itcasi_query->num_rows() > 0){
					foreach ($itcasi_query->result()  as $row){
						$pivote['origen']  =$row->clave;
						$pivote['cuenta']  =$row->cuenta;
						$pivote['referen'] =$row->referen;
						$pivote['concepto']=$row->concepto;
						$pivote['debe']    =$row->debe;
						$pivote['haber']   =$row->haber;
						$pivote['sucursal']=$row->sucursal;
						$pivote['ccosto']  =$row->ccosto;
						$encab[$row->comprob][]=$pivote;
						//$acumulador[0]+=$row->debe;
						//$acumulador[1]+=$row->haber;
					}
					$pivote['origen']=$pivote['cuenta']=$pivote['referen']=$pivote['concepto']=$pivote['sucursal']='';
					$pivote['debe']  =number_format($acumulador[0],2,',','.');
					$pivote['haber'] =number_format($acumulador[1],2,',','.');
					$pivote['diferencia']=number_format($acumulador[0]-$acumulador[1],2,',','.');
					//$encab[$row->comprob][]=$pivote;
				}

			}   
			foreach ($encab  as $comprob=>$tabla){
					if (array_key_exists($comprob, $encab_titu))
						$titulo=$encab_titu[$comprob];
					else
						$titulo='HUERFANO';
				$grid = new DataGrid2($titulo,$tabla);
				$grid->per_page=count($tabla);
				$grid->use_function('dif');

				$grid->column('Or&iacute;gen', 'origen');
				$grid->column('Cuenta'    , 'cuenta'  );
				$grid->column('Referencia', 'referen' );
				$grid->column('Concepto'  , 'concepto');
				$grid->column("Debe","<nformat><#debe#></nformat>","align=right");
				$grid->column("Haber","<nformat><#haber#></nformat>","align=right");
				//$grid->column("Diferencia" , "<dif><#debe#>|<#haber#></dif>",'align=right');
				$grid->column('Sucursal'  , 'sucursal','align=right');
				$grid->column('C. Costo'  , 'ccosto'  ,'align=right');
				
				$grid->totalizar('debe','haber');
				$grid->build();
				//echo $grid->db->last_query();
				$data['content'] .=$grid->output;

			}
	
		}
		
		$data['content'] .= $this->html->button('regresa', RAPYD_BUTTON_BACK, $action, "button", "button");
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   ="Ejecuci&oacute;n de la regla $modulo";
		$this->load->view('view_ventanas', $data);
	}
	
	function duplicar(){
		$modulo=$this->uri->segment(4);
		$regla =$this->uri->segment(5);
		$dispon=$this->_rdisponible($modulo);
		$mSQL  ="INSERT INTO `reglascont` (modulo,regla,tabla,descripcion,fecha,comprob,origen,condicion,agrupar,cuenta,referen,concepto,debe,haber,ccosto,sucursal,fuente,control,mbanc_id,auxiliar) SELECT modulo, '$dispon',tabla,descripcion,fecha,comprob,origen,condicion,agrupar,cuenta,referen,concepto,debe,haber,ccosto,sucursal,fuente,control,mbanc_id,auxiliar FROM `reglascont` WHERE modulo='$modulo' AND regla='$regla'";
		$this->db->query($mSQL);
		redirect('/contabilidad/reglas/detalle/'.$modulo);
		//if($modulo AND $regla ) redirect('/contabilidad/reglas/detalle');
		
	}
	function _rdisponible($modulo=''){
		if(empty($modulo)) return FALSE;
		$query = $this->db->query("SELECT regla FROM `reglascont` WHERE modulo='$modulo'");
		$i=0;
		foreach ($query->result() as $row){ $i++;
			if ($row->regla!=$i) return $i;
		}return $i+1;
	}
  
  function instalar(){
	  $query="ALTER TABLE `reglascont` CHANGE COLUMN `condicion` `condicion` TEXT NULL DEFAULT NULL";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `reglascont`  ADD COLUMN `mbanc_id` TEXT NULL DEFAULT NULL";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `reglascont` ADD COLUMN `auxiliar` MEDIUMTEXT NULL ";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `reglascont` 	ADD COLUMN `havin` TEXT NULL AFTER `auxiliar`";
	  $this->db->simple_query($query);
  }
}
?>
