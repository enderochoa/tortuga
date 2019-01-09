<?php require_once(BASEPATH.'application/controllers/validaciones.php');
//concepto
class Conc extends validaciones{
  
  var $url="nomina/conc";

	function conc(){
		parent::Controller(); 
		$this->load->library("rapyd");
		define ("THISFILE",   APPPATH."controllers/nomina". $this->uri->segment(2).EXT);
		$this->load->library('pnomina');
	}

	function index(){
		$this->datasis->modulo_id(49,1);
		redirect("nomina/conc/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("", 'conc');
		
		$filter->concepto = new inputField("Concepto", "concepto");
		$filter->concepto->size = 5;
		
		$filter->descrip  = new inputField("Descripci&oacute;n", "descrip");
		
		$filter->formula  = new inputField("Formula", "formula");
		
		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('nomina/conc/dataedit/show/<raencode><#concepto#></raencode>','<#concepto#>');

		$grid = new DataGrid("");
		$grid->order_by("concepto","asc");
		$grid->per_page = 20;

		$grid->column_orderby("Concepto"          ,$uri     ,"concepto");
		$grid->column_orderby("Tipo"              ,"tipo"   ,"tipo"   );
		$grid->column_orderby("Descripci&oacute;n","descrip","descrip","align='left'NOWRAP");
		$grid->column_orderby("Tipoa"             ,"tipoa"  ,"tipoa");
		//$grid->column("F&oacute;rmula","formula");
		
		$grid->add("nomina/conc/dataedit/create");
		$grid->build();
	
		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script'] = script("jquery.js")."\n";
		$data['title']   = "Conceptos";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function getctade($tipoa=NULL){
		$this->rapyd->load("fields");
		$uadministra = new dropdownField("ctade", "ctade");
		$uadministra->status = "modify";
		$uadministra->style ="width:400px;";
		//echo 'de nuevo:'.$tipoa;
		if ($tipoa!==false){
		if($tipoa=='P'){
					$uadministra->options("SELECT proveed,nombre FROM sprv ORDER BY proveed");
			}else{
				if($tipoa=='G'){
					$uadministra->options("SELECT codigo,descrip FROM mgas ORDER BY codigo");
				}else{
					$uadministra->options("SELECT cliente,nombre FROM sprv ORDER BY cliente");
				}
			}
		}else{
 				$uadministra->option("Seleccione un opcion");
		}
		$uadministra->build(); 
		echo $uadministra->output;
	}
	
	function dataedit(){
		$this->rapyd->load("dataobject","dataedit2");
			
		$qformato=$this->qformato=$this->datasis->formato_cpla();
		
		$modbus=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'<#i#>'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
			'p_uri'=>array(4=>'<#i#>')
		);
		
		$mPPLA=array(
			'tabla'   =>'ppla',
			'columnas'=>array(
				'codigo'      =>'C&oacute;digo',
				'denominacion'=>'Denominaci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
			'retornar'=>array('codigo'=>'codigopres'),
			'titulo'  =>'Buscar Cuenta'
		);
		
		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'rif' =>'RIF',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'RIF'),
				'retornar'=>array('proveed'=>'cod_prov', 'nombre'=>'nombrep' ),
				'script'=>array('cal_total()'),
				'titulo'  =>'Buscar Beneficiario');
			
		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");
		$bPPLA    =$this->datasis->modbus($mPPLA );
		$bcuenta  =$this->datasis->p_modbus($modbus ,'cuenta');
		$bcontra  =$this->datasis->p_modbus($modbus ,'contra');
		
    $link8=site_url($this->url.'/sugerir/');
    $script ='
    function sugerir(){
      $.ajax({
        url: "'.$link8.'",
        success: function(msg){
          if(msg){
            $("#concepto").val(msg);
          }
          else{
            alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
          }
        }
      });
    }

		$(function(){
			$("#codigoadm").change(function(){
				$.post("'.site_url('presupuesto/presupuesto/get_tipo').'",{ codigoadm:$("#codigoadm").val() },function(data){
					$("#fondo").html(data);
	 			});
 			});
		});
    ';
    
		$do = new DataObject("conc");
		//$do->pointer('sprv' ,'sprv.proveed=conc.cod_prov','sprv.nombre as nombrep','LEFT');
		//esta comentado porque da problemas
		
		
		
		$edit = new DataEdit2("Conceptos", $do);
		$edit->back_url = site_url("nomina/conc/filteredgrid");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
    
		$edit->script($script, "create");
		$edit->script($script, "modify");
			    
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->concepto = new inputField("Concepto", "concepto");
		$edit->concepto->rule = "required|callback_chexiste";
		$edit->concepto->dbname = "conc.concepto";
		$edit->concepto->mode = "autohide";
		$edit->concepto->maxlength= 4;
		$edit->concepto->size = 7;
		$edit->concepto->append($sugerir);
		
		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->style ="width:100px;";
		$edit->tipo->option("","");
		$edit->tipo->options(array("A"=> "Asignaci&oacute;n","O"=>"Otros","D"=> "Deducci&oacute;n"));
		
		$edit->descrip =  new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->size =45;
		$edit->descrip->maxlength=35;
		$edit->descrip->rule = "strtoupper|required";
		
		$edit->grupo = new inputField("Grupo", "grupo");
		$edit->grupo->size =7;
		$edit->grupo->maxlength=4;
		
		$edit->encab1 = new inputField("Encabezado 1", "encab1");
		$edit->encab1->size = 22;
		$edit->encab1->maxlength=12;
		
		$edit->encab2 =   new inputField("Encabezado 2&nbsp;", "encab2");
		$edit->encab2->size = 22;
		$edit->encab2->maxlength=12;
				
		$edit->formula = new textareaField("F&oacute;rmula","formula");
		$edit->formula->rows = 4;
		$edit->formula->cols=90;
		$edit->formula->rule='callback_formulacheck';
		
		$edit->codigoadm = new dropdownField("Estructura Administrativa","codigoadm");
		$edit->codigoadm->option("","Seleccione");
		$edit->codigoadm->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		
		$edit->fondo = new dropdownField("Fondo", "fondo");
		$edit->fondo->style="width:300px;";
		$estadmin=$edit->getval('codigoadm');
		if($estadmin!==false){
			$edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto WHERE codigoadm='$estadmin' GROUP BY tipo");
		}else{
			$edit->fondo->option("","Seleccione una estructura administrativa primero");
		}
		
		$edit->codigopres = new inputField("Partida", "codigopres");
		//$edit->codigopres->rule='required';//callback_repetido|
		$edit->codigopres->size=20;
		$edit->codigopres->append($bPPLA);
		
		$edit->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$edit->cod_prov->size     = 6;		
		//$edit->cod_prov->rule     = "required";
		$edit->cod_prov->append($bSPRV);
		//$edit->cod_prov->readonly=true;
		
		$edit->nombrep = new inputField("Nombre Beneficiario", 'nombrep');
		$edit->nombrep->size = 50;
		$edit->nombrep->readonly = true;
		$edit->nombrep->pointer = true;
		$edit->nombrep->in       = "cod_prov";
    
		//$edit->ordinal = new inputField("Ordinal", "ordinal");
		//$edit->ordinal->rule     ='callback_ordinal';
		//$edit->ordinal->db_name  ='ordinal';
		//$edit->ordinal->maxlength=3;
		//$edit->ordinal->size     =5;
		
//		$edit->cuenta = new inputField("Debe", "cuenta");
//		$edit->cuenta->size =19;
//		$edit->cuenta->maxlength=15;
//		$edit->cuenta->group="Enlase Contable";
//		$edit->cuenta->rule='callback_chcuentac';
//		$edit->cuenta->append($bcuenta);
//		
//		$edit->contra =  new inputField("Haber", "contra"); 
//		$edit->contra->size = 19;   
//		$edit->contra->maxlength=15;
//		$edit->contra->group="Enlase Contable";
//		$edit->contra->rule='callback_chcuentac';
//		$edit->contra->append($bcontra);
		
		$edit->tipoa = new dropdownField("Usa Partida", "tipoa");
		$edit->tipoa->style ="width:100px;";
		$edit->tipoa->options(array("A"=> "Automatico","C"=>"Concepto","P"=>"Persona"));
		
			
			
		//$edit->tipoa = new dropdownField ("Deudor ", "tipoa");  
		//$edit->tipoa->style ="width:100px;";
		//$edit->tipoa->option(" "," "); 
		//$edit->tipoa->option("G","Gasto");    
		//$edit->tipoa->option("C","Cliente");  
		//$edit->tipoa->option("P","Proveedor");
		//$edit->tipoa->group="Enlase Administrativo";
		//$edit->tipoa->onchange = "get_ctade();";

		//$edit->ctade = new dropdownField("ctade", "ctade");
		//$edit->ctade->style ="width:400px;";
		//$edit->ctade->group="Enlase Administrativo";
		//if($edit->_status=='modify'){
		//	$tipoa  =$edit->getval("tipoa");
		//	if($tipoa=='P'){
		//			$edit->ctade->options("SELECT proveed,nombre FROM sprv ORDER BY proveed");
		//	}else{
		//		if($tipoa=='G'){
		//			$edit->ctade->options("SELECT codigo,descrip FROM mgas ORDER BY codigo");
		//		}else{
		//			$edit->ctade->options("SELECT cliente,nombre FROM sprv ORDER BY cliente");
		//		}
		//	}
		//}else{
		//	$edit->ctade->option("","Seleccione una Deudor");
		//}
			  
		//$edit->tipod = new dropdownField ("Acreedor", "tipod");
		//$edit->tipod->style ="width:100px;";
		//$edit->tipod->option(" "," "); 
		//$edit->tipod->option("G","Gasto");
		//$edit->tipod->option("C","Cliente");
		//$edit->tipod->option("P","Proveedor");
		//$edit->tipod->onchange = "get_ctaac();";
		//$edit->tipod->group="Enlase Administrativo";
		
		//$edit->ctaac =   new dropdownField("ctaac", "ctaac"); 
		//$edit->ctaac->style ="width:400px;";     
		//$edit->ctaac->group="Enlase Administrativo";
		//if($edit->_status=='modify'){
		//	$tipod  =$edit->getval("tipod");
		//	if($tipod=='P'){
		//			$edit->ctaac->options("SELECT proveed,nombre FROM sprv ORDER BY proveed");
		//	}else{
		//		if($tipod=='G'){
		//			$edit->ctaac->options("SELECT codigo,descrip FROM mgas ORDER BY codigo");
		//		}else{
		//			$edit->ctaac->options("SELECT cliente,nombre FROM sprv ORDER BY cliente");
		//		}
		//	}
		//}else{
		//	$edit->ctaac->option("","Seleccione un Acreedor");
		//}
		
		$edit->aplica =   new dropdownField("Aplica para liquidacion", "liquida"); 
		$edit->aplica->style ="width:50px;";     
		$edit->aplica->option("S","S");
		$edit->aplica->option("N","N"); 
    			
		$edit->buttons("add","modify", "save", "undo", "back","delete");
		$edit->build();
			
		$data['content'] = $edit->output;           
		$data['title']   = "Conceptos";        
		$data["head"]    = $this->rapyd->get_head();                                                                                         
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}
	
	function ordinal($ordinal){
		if(strlen($codigo!=3)){
			$this->validation->set_message('ordinal',"El ordinal debe contener tres(3) digitos");
			return FALSE;
		}else {
  			return TRUE;
		}
	}

	function _post_insert($do){
		$codigo=$do->get('concepto');
		$nombre=$do->get('descrip');
		logusu('conc',"CONCEPTO $codigo NOMBRE  $nombre CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('concepto');
		$nombre=$do->get('descrip');
		logusu('conc',"CONCEPTO $codigo NOMBRE  $nombre  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('concepto');
		$nombre=$do->get('descrip');
		logusu('conc',"CONCEPTO $codigo NOMBRE  $nombre  ELIMINADO ");
	}

	function chexiste($codigo){
		$codigo=$this->input->post('concepto');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM conc WHERE concepto='$codigo'");
		if ($chek > 0){
			$nombre=$this->datasis->dameval("SELECT descrip FROM conc WHERE concepto='$codigo'");
			$this->validation->set_message('chexiste',"El concepto $codigo nombre $nombre ya existe");
			return FALSE;
		}else {
  			return TRUE;
		}
		
	}

	function sugerir(){
	  $ultimo=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN conc ON LPAD(concepto,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND concepto IS NULL LIMIT 1");
	  echo $ultimo;
	}
	
	function formulacheck($formula){
		$this->pnomina->MONTO =1;
		$valor=$this->pnomina->evalform($formula);
		//if(!($valor>=0 || $valor<0)){
		if($valor===false){
			$this->validation->set_message('formulacheck',"ERROR. La formula es Invalida");
			return FALSE;
		}
	}

	function instalar(){
		$mSQL="ALTER TABLE conc ADD PRIMARY KEY (concepto);";
		$this->db->simple_query($mSQL);	
		$mSQL="ALTER TABLE `conc` ADD COLUMN `codigopres` VARCHAR(25) NULL  AFTER `cod_prov`";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `conc` ADD COLUMN `codigoadm` VARCHAR(25) NULL  AFTER `codigopres` ";
		$this->db->simple_query($mSQL);	
		$mSQL="ALTER TABLE `conc` ADD COLUMN `fondo` VARCHAR(25) NULL  AFTER `codigoadm` ";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `conc`  ADD COLUMN `salarial` CHAR(1) NULL DEFAULT 'N'";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `conc`  CHANGE COLUMN `descrip` `descrip` VARCHAR(100) NULL DEFAULT NUL";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `conc` CHANGE COLUMN `formula` `formula` TEXT NULL DEFAULT NULL ";
		$this->db->simple_query($mSQL);
		
	}
}
?>
