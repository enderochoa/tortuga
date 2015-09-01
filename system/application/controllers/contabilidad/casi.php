<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Casi extends Common {

	var $qformato;
	var $titp   = 'Asientos';
	var $tits   = 'Asiento';
	var $url    = 'contabilidad/casi/';

	function casi(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(135,1);
		$this->casivalidacpla=$this->datasis->traevalor('CASIVALIDACPLA','S','VALIDA CUENTA CONTABLE AL MODIFICAR EN CASI');
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("datafilter2","datagrid");

		$filter = new DataFilter2("");

		$filter->db->select(array("b.cuenta","a.comprob","a.fecha","a.origen","a.debe","a.haber","a.status","a.descrip","a.total","b.referen"));
		$filter->db->from("casi a");
		$filter->db->join("itcasi b" ,"a.comprob=b.comprob");
		$filter->db->groupby("a.comprob");

		$filter->comprob = new inputField("Comprobante", "comprob");
		$filter->comprob->size  =10;
		
		$filter->comprob->db_name="a.comprob";

		$filter->fechad = new dateonlyField("Desde", "fechad",'d/m/Y');
		$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause ="where";
		$filter->fechad->db_name =$filter->fechah->db_name="a.fecha";
		//$filter->fechad->insertValue = date("Y-m-d");
		//$filter->fechah->insertValue = date("Y-m-d");
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator=">=";
		$filter->fechah->operator="<=";

		//$filter->fecha = new dateonlyField("Fecha", "fecha");
		//$filter->fecha->size=12;

		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->db_name="a.descrip";
		
		$filter->descripd = new inputField("Concepto Detalle", "descripd");
		$filter->descripd->db_name="b.concepto";
		
		$filter->cuenta = new inputField("Cuenta", "cuenta");
		$filter->cuenta->db_name="b.cuenta";
		
		$filter->referen = new inputField("Refencia", "referen");
		$filter->referen->db_name="b.referen";

		$filter->status = new dropdownField("Status", "status");
		$filter->status->db_name="a.status";
		$filter->status->option("","Todos");
		$filter->status->option("C2","Actualizado");
		$filter->status->option("C1","Pendiente");

		$filter->vdes = new checkboxField("Ver solo asientos descuadrados","vdes",'S','N');
		$filter->vdes->insertValue='N';
		$filter->vdes->clause='';

		$filter->buttons("reset","search");

		$filter->build();
		$uri  = anchor($this->url.'dataedit/0/show/<raencode><#comprob#></raencode>','<#comprob#>');
		$uri2 = anchor($this->url.'dataedit/0/show/<#numero#>','<#numero#>');

		function sta($status){
			switch($status){
				case "C2":return "Cuadrado";break;
				case "C1":return "Pendiente";break;
			}
		}

		$grid = new DataGrid("");
		$vdes = $this->input->post('vdes');
		if($vdes)
		$grid->db->where('(debe-haber) <>',0);
		$grid->order_by("a.comprob","desc");
		$grid->per_page = 20;
		$grid->use_function('substr','str_pad');
		$grid->use_function('sta');


		$grid->column_orderby("Comprobante"      ,$uri                                            ,"comprob");
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"  ,"fecha"  ,"align='center'"      );
		$grid->column_orderby("Cuenta"           ,"cuenta"                                        ,"cuenta" ,"align='center'"      );
		$grid->column_orderby("Debe"             ,"<nformat><#debe#>|2|,|.</nformat>" ,"debe"   ,"align='right'"       );
		$grid->column_orderby("Haber"            ,"<nformat><#haber#>|2|,|.</nformat>","haber"  ,"align='right'"       );
		$grid->column_orderby("Pago"             ,"<nformat><#total#>|2|,|.</nformat>","total"  ,"align='right'"       );
		$grid->column_orderby("Estado"           ,"<sta><#status#></sta>"                       ,"status" ,"align='center'NOWRAP");
		$grid->column_orderby("Descripcion"      ,"descrip"                                     ,"descrip","align='left'  NOWRAP");

		//echo $grid->db->last_query();
		$grid->add($this->url."dataedit/create");
		$grid->build();

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script']  = script("jquery.js")."\n";
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($orden=0,$estado=null,$numero=null){
		
		$this->load->helper('form');
		//$this->datasis->modulo_id(101,1);

		//$formato=$this->datasis->dameval('SELECT format FROM cemp LIMIT 0,1');
		$formato=$this->datasis->traevalor('FORMATOPATRI');
		$len_for = strlen($formato);
 		$qformato='%';
 		for($i=1;$i<substr_count($formato, '.')+1;$i++) $qformato.='.%';
 			$this->qformato=$qformato;

		$modbus=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'denominacion'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'cuenta_<#i#>'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\" AND LENGTH(codigo) = $len_for ",// = $len_for
			'p_uri'=>array(4=>'<#i#>')
		);

		$button  = $this->datasis->p_modbus($modbus,'<#i#>' );

		$this->rapyd->load('dataobject','datadetails');

		$do = new DataObject("casi");
		$do->rel_one_to_many('itcasi', 'itcasi', array('comprob'=>'comprob'));
		$do->rel_pointer('itcasi','cpla' ,'itcasi.cuenta=cpla.codigo',"cpla.denominacion","LEFT");
		
		switch($orden){
			case 0 :{$do->order_by('itcasi','itcasi.id',' ');}
			break;
			case 1 :{$do->order_by('itcasi','itcasi.cuenta',' ');}
			break;
			case 2 :{
				$do->order_by('itcasi',"MID( `itcasi`.`concepto` ,1, 10 )",' ');
				$do->order_by('itcasi','itcasi.haber > 0',' ');
				$do->order_by('itcasi','itcasi.cuenta',' ');
				}
			break;
			case 3:{
				
				$do->order_by('itcasi','itcasi.debe+itcasi.haber',' ');
				$do->order_by('itcasi','itcasi.haber > 0',' ');
				$do->order_by('itcasi','itcasi.cuenta',' ');
				}
			break;
			case 4 :{$do->order_by('itcasi','itcasi.cuenta','desc');}
			case 5 :{$do->order_by('itcasi','itcasi.fecha','asc');}
			break;
		}
		
		$do->db->_escape_char='';
		$do->db->_protect_identifiers=false;
			
		if($numero && ($estado=='show' || $estado=='modify'))
			$do->load("$numero");

		$edit = new DataDetails($this->tits, $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('itcasi','Rubro <#o#>');

		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		//$edit->numero  = new inputField("Identificador", "numero");
		//$edit->numero->mode="autohide";
		//$edit->numero->when=array('show');
		
		$options=array(
			'0'=>'Ordenar',
			'1'=>"Cuenta Contable Ascendente",
			'4'=>"Cuenta Contable Descendente",
			'2'=>"Primeros 10 digitos del concepto, debe,haber,cuenta ",
			'3'=>"Ordenado por monto",
			'5'=>"Fecha"
			);
		
		$js = 'id="orden" onChange="ordenar();"';
		$orden = form_dropdown('orden',$options,0,$js);
		/*
		$edit->orden = new dropdownField("Ordenar por","orden");
		$edit->orden->option("0","Cuenta Contable Ascendente");
		$edit->orden->option("1","Primeros 10 digitos del concepto, debe,haber ");
		$edit->orden->status='create';
		$edit->orden->when=array('show','modify','create');
		*/

		$edit->comprob  = new inputField("Comprobante", "comprob");
		//$edit->comprob->mode ="autohide";
		$edit->comprob->rule ="trim|required|unique";
		//$edit->comprob->when=array('show','modify');

		$edit->fecha = new  dateonlyField("Fecha",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size        =12;
		$edit->fecha->rule        = 'required';

		$edit->descrip = new textAreaField("Descripci&oacute;n", 'descrip');
		$edit->descrip->cols = 100;
		$edit->descrip->rows = 3;

		$edit->debe  = new inputField2("Debe", "debe");
		$edit->debe->size = 12;
		$edit->debe->css_class='inputnum';
		$edit->debe->readonly=TRUE;

		$edit->haber = new inputField2("Haber", "haber");
		$edit->haber->size = 12;
		$edit->haber->css_class='inputnum';
		$edit->haber->readonly=TRUE;

		$edit->total = new inputField("Saldo", "total");
		$edit->total->size = 12;
		$edit->total->css_class='inputnum';
		$edit->total->readonly=TRUE;

		$edit->status = new dropdownField("Status", "status");
		$edit->status->style="width:110px";
		$edit->status->option("C1","Cuadrado");
		$edit->status->option("C2","Pendiente");

		$edit->itcuenta = new inputField("(<#o#>) Cuenta", "cuenta_<#i#>");
		$edit->itcuenta->rule         ='trim|required|callback_chcodigo';//|callback_itorden |callback_repetido|
		$edit->itcuenta->size         =20;
		$edit->itcuenta->db_name      ='cuenta';
		$edit->itcuenta->rel_id       ='itcasi';
		$edit->itcuenta->autocomplete =false;
		$edit->itcuenta->append($button);
		
		//$edit->itdenomi = new inputField("(<#o#>) Denominaci&oacute;n", "denominacion_<#i#>");
		//$edit->itdenomi->db_name ='denominacion';
		//$edit->itdenomi->rel_id  ='itcasi';
		////$edit->itdenomi->size    =20;
		//$edit->itdenomi->readonly=true;
		//$edit->itdenomi->pointer =true;

		$edit->itconcepto = new textareaField("Concepto", "concepto_<#i#>");
		$edit->itconcepto->db_name='concepto';
		$edit->itconcepto->rows   =2;
		$edit->itconcepto->cols   =30;
		$edit->itconcepto->rel_id   ='itcasi';
		
		$edit->itfecha = new dateOnlyField("(<#o#>) Fecha", "fecha_<#i#>");
		$edit->itfecha->db_name  = 'fecha';
		$edit->itfecha->rel_id   = 'itcasi';
		$edit->itfecha->size     =10;

		$edit->itreferencia = new inputField("Referencia", "referen_<#i#>");
		$edit->itreferencia->size=10;
		$edit->itreferencia->db_name='referen';
		$edit->itreferencia->maxlength=12;
		$edit->itreferencia->rel_id   ='itcasi';

		$edit->itdebe = new inputField("(<#o#>) Debe", "debe_<#i#>");
		$edit->itdebe->css_class= 'inputnum';
		$edit->itdebe->db_name  ='debe';
		$edit->itdebe->rel_id   ='itcasi';
		$edit->itdebe->rule     ='numeric';
		$edit->itdebe->onchange = 'cal_totald(<#i#>);';
		$edit->itdebe->size     =15;

		$edit->ithaber = new inputField("(<#o#>) Haber", "haber_<#i#>");
		$edit->ithaber->css_class= 'inputnum';
		$edit->ithaber->rule     = 'callback_positivo';
		$edit->ithaber->db_name  = 'haber';
		$edit->ithaber->rel_id   = 'itcasi';
		$edit->ithaber->onchange = 'cal_totalh(<#i#>);';
		$edit->ithaber->size     = 15;
		
		$edit->itorigen = new hiddenField("(<#o#>) Origen", "origen_<#i#>");
		$edit->itorigen->db_name  = 'origen';
		$edit->itorigen->rel_id   = 'itcasi';
		
		$status=$edit->get_from_dataobjetct('status');
		$fecha=$edit->get_from_dataobjetct('fecha');
		$error=$this->chcasise($fecha);
		if(empty($error)){
			if($status=='C1'){
				$action = "javascript:window.location='" .site_url($this->url.'/actualizar/'.$edit->rapyd->uri->get_edited_id()). "'";
				$edit->button_status("btn_status",'Cerrar Asiento',$action,"TR","show");
				$edit->buttons("modify","delete","save");
			}elseif($status=='C2'){
				$action = "javascript:window.location='" .site_url($this->url.'/reversar/'.$edit->rapyd->uri->get_edited_id()). "'";
				$edit->button_status("btn_rever",'Reversar',$action,"TR","show");
			}else{
				$edit->buttons("save");
			}
			$edit->buttons("modify","delete","save","add");
		}else{
			if($status=='C1'){
				$edit->buttons("delete");
			}
		}

		$edit->buttons("add_rel","undo","back","add");
		
		
		$edit->build();

		$smenu['link']   = barra_menu('198');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$conten['orden']   = $orden;
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_casi', $conten,true);
		//$data['content'] = $edit->output;
		$data['title']   = "$this->tits";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css');
		$this->load->view('view_ventanas', $data);
	}

	function chexiste($comprob){
		
		
		$codigo = $this->db->escape($comprob);
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM casi WHERE comprob=$codigo");
		if ($chek > 0){
		  $this->validation->set_message('chexiste',"El numero de comprobante ($codigo) ya existe");
		  return FALSE;
		}else {
		  return TRUE;
		}
	}

	function chcodigo($codigo){
		if($this->casivalidacpla=='S'){
			if (preg_match("/^[0-9]+(\.[0-9]+)*$/",$codigo)>0){
				$formato=$this->datasis->traevalor('FORMATOPATRI');
				$farr=explode('.',$formato);
				$carr=explode('.',$codigo);
				$max =count($carr);
				$mmac=count($farr);

				if($mmac == $max){
					for($i=0;$i<$max;$i++){
						if(strlen($farr[$i])!=strlen($carr[$i])){
							$this->validation->set_message('chcodigo',"La cuenta no coincide con el formato: $formato");
							return false;
						}
					}
				}else{
					$this->validation->set_message('chcodigo',"La cuenta no coincide con el formato: $formato");
					return false;
				}
				$pos=strrpos($codigo,'.');
				if($pos!==false){
					$str=substr($codigo,0,$pos);
					$cant=$this->datasis->dameval("SELECT COUNT(*) FROM cpla WHERE codigo='$str'");
					if($cant==0){
						$this->validation->set_message('chcodigo',"No existe la cuenta padre ($str) para registrar esa cuenta");
						return false;
					}
				}
			}else{
				$this->validation->set_message('chcodigo',"El c&oacute;digo parece tener formato invalido");
				return false;
			}
			return true;
		}else{
			return true;
		}
	}

	function _valida($do){
		$error  ='';
		$tdebe  =$thaber=0;
		$comprob=$do->get('comprob');
		$pk_comprob=$do->pk['comprob'];
		$fecha  =$do->get('fecha');
		if($pk_comprob!=$comprob){
			$origen=$do->get('origen');
			
			if(strpos($origen,'M_')===FALSE)
			$do->set('origen','M_'.$origen);	
		}
		
		$error.=$this->chcasise($fecha);
		
		if(strpos($comprob,' ')>0){
			$error.="El numero de comprobante no puede tener caracteres en blanco";
		}
		
		for($i=0;$i <  $do->count_rel('itcasi');$i++){
			$tdebe +=$debe  = $do->get_rel('itcasi','debe'  ,$i);
			$thaber+=$haber = $do->get_rel('itcasi','haber' ,$i);
			$cuenta         = $do->get_rel('itcasi','cuenta',$i);
			$origen         = $this->input->post('origen_',$i);
			
			if($pk_comprob!=$comprob && strpos($origen,'M_')===FALSE)
				$do->set_rel('itcasi','origen','M_'.$origen,$i);
			//else
			//	$do->set_rel('itcasi','origen',$origen,$i);

			//if(!(abs($debe-$haber)>0))
			//$error.="</br>El rubro $i de cuenta $cuenta , no puede ser cero";
		}
		
		$do->set('debe',$tdebe);
		$do->set('haber',$thaber);

		$do->set('status','C1');
		
		if(!empty($error)){
			$do->error_message_ar['pre_ins']="<div class='alert'>".$error."</div>";
			$do->error_message_ar['pre_upd']="<div class='alert'>".$error."</div>";
			return false;
		}else{
			$comprobe = $this->db->escape($do->pk['comprob']);
			$this->db->query("DELETE FROM itcasi WHERE comprob=$comprobe");
			//exit();
		}
	}

	function reversar($numero){
		$this->rapyd->load('dataobject');

		$do = new DataObject("casi");
		$do->load($numero);
		$error='';
		$fecha = $do->get('fecha');
		$error.= $this->chcasise($fecha);

		if(empty($error)){
			$do->set('status','C1');
			$do->save();
			
			$fecha = $do->get('fecha');
			$mes   =str_pad(date('m',strtotime($fecha)),2,'0',STR_PAD_LEFT);
			$this->cal_nrocomp($mes);
	
			logusu('CASI','Reverso Asiento Contable '.$numero);
			redirect($this->url."dataedit/show/$numero");
		}else{
			$data['content'] = "<div class='alert'>".$error."</div>".anchor($this->url."dataedit/show/$numero",'Regresar');
			$data['title']   = "$this->tits";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
			return false;
		}
	}

	function actualizar($numero){
		$this->rapyd->load('dataobject');

		$do = new DataObject("casi");
		$do->rel_one_to_many('itcasi', 'itcasi', array('comprob'=>'comprob'));
		$do->load($numero);
		$error ='';
		$fecha = $do->get('fecha');
		$error.= $this->chcasise($fecha);

		
		$tdebe=$thaber=0;
		for($i=0;$i <  $do->count_rel('itcasi');$i++){
			$cuenta      = $do->get_rel('itcasi','cuenta'  ,$i);
			$debe        = $do->get_rel('itcasi','debe'    ,$i);
			$haber       = $do->get_rel('itcasi','haber'   ,$i);

			$tdebe  +=$debe;
			$thaber +=$haber;

			if($debe !=0 && $haber!=0){
				$error.="<div class='alert'><p>Error en la cuenta $cuenta: Uno de los campos debe o haber debe contener un valor positivo y el otro cero(0)</p></div>";
			}
		}
		if($this->datasis->traevalor('CASIDESCUDRADO','N','PERMITE ACTURALIZAR UN ASIENTO DESCUADRADO') != 'S'){
			if(round($tdebe,2)!=round($thaber,2)){
				$error.="<div class='alert'><p>Asiento Descuadrado</p></div>";
			}
		}

		if(empty($error)){

			$do->set('debe'   ,$tdebe        );
			$do->set('haber'  ,$thaber       );
			$do->set('total'  ,$tdebe-$thaber);
			$do->set('status' ,'C2');
			$do->save();
		}

		if(empty($error)){
			$fecha = $do->get('fecha');
			$mes   =str_pad(date('m',strtotime($fecha)),2,'0',STR_PAD_LEFT);
			$this->cal_nrocomp($mes);
			logusu('CASI','Actualizo Asiento Contable '.$numero);
			redirect($this->url."dataedit/show/$numero");
		}else{
			$data['content'] = "<div class='alert'>".$error."</div>".anchor($this->url."dataedit/show/$numero",'Regresar');
			$data['title']   = "$this->tits";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
			return false;
		}
	}

	function cal_nrocomp($mes){
	  //echo "</br>";
		//$query = "UPDATE casi SET comprob='' WHERE status = 'C1' AND LPAD(EXTRACT(MONTH FROM fecha),2,'0')=$mes";
		//$query = $this->db->query($query);
		//$query = "SELECT numero,EXTRACT(MONTH FROM fecha) mes FROM casi WHERE (status = 'C2' AND LPAD(EXTRACT(MONTH FROM fecha),2,'0')=$mes)  ORDER BY fecha,numero";
		//$query = $this->db->query($query);
		//$query =$query->result();
		//
		//$c=1;
		//foreach($query AS $items){
		//	//echo "*".$items->mes;
		//	$query = "UPDATE casi SET comprob='".$mes.str_pad($c, 6 ,'0',STR_PAD_LEFT)."' WHERE numero=".$items->numero." AND LPAD(EXTRACT(MONTH FROM fecha),2,'0')=$mes ";
		//	$this->db->simple_query($query);
		//	$c++;
		//}
	}

	function _post_insert($do){
		$numero = $do->get('numero');

		logusu('casi',"Creo Asiento Contable Nro $numero");
		//redirect($this->url."actualizar/$numero");
	}

	function _post_update($do){
		$numero = $do->get('numero');

		logusu('casi'," Modifico Asiento Contable Nro $numero");

		//redirect($this->url."actualizar/$numero");
	}
	function _post_delete($do){
		$numero = $do->get('numero');
		logusu('casi'," Elimino Asiento Contable Nro $numero");
	}

	function cal_comprob(){
	  for($i=1;$i<=12;$i++){
	    echo str_pad($i, 2 ,'0',STR_PAD_LEFT);
	    $this->cal_nrocomp(str_pad($i, 2 ,'0',STR_PAD_LEFT));
	    }
	}

  function instalar(){
	 $query="CREATE TABLE `casi` (
			`numero` INT(11) NOT NULL AUTO_INCREMENT,
			`comprob` VARCHAR(45) NOT NULL,
			`fecha` DATE NOT NULL DEFAULT '0000-00-00',
			`descrip` VARCHAR(60) NULL DEFAULT NULL,
			`total` DECIMAL(19,2) NULL DEFAULT NULL,
			`debe` DECIMAL(19,2) NULL DEFAULT NULL,
			`haber` DECIMAL(19,2) NULL DEFAULT NULL,
			`status` CHAR(2) NULL DEFAULT 'C1',
			`tipo` VARCHAR(10) NULL DEFAULT '',
			`origen` VARCHAR(20) NULL DEFAULT NULL,
			`transac` VARCHAR(8) NULL DEFAULT NULL,
			`usuario` VARCHAR(4) NULL DEFAULT NULL,
			`estampa` DATE NULL DEFAULT NULL,
			`hora` VARCHAR(8) NULL DEFAULT NULL,
			PRIMARY KEY (`comprob`),
			INDEX `comprorigen` (`numero`, `origen`),
			INDEX `fecha` (`fecha`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1;
		"; 
		$this->db->simple_query($query);
		
		$query="CREATE TABLE `itcasi` (
			`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`fecha` DATE NOT NULL DEFAULT '0000-00-00',
			`numero` INT(11) NOT NULL DEFAULT '0',
			`origen` CHAR(20) NULL DEFAULT NULL,
			`cuenta` CHAR(30) NULL DEFAULT NULL,
			`referen` TEXT NULL,
			`concepto` TEXT NULL,
			`debe` DECIMAL(19,2) NOT NULL DEFAULT '0.00',
			`haber` DECIMAL(19,2) NOT NULL DEFAULT '0.00',
			`ccosto` CHAR(12) NULL DEFAULT NULL,
			`sucursal` CHAR(12) NULL DEFAULT NULL,
			`comprob` VARCHAR(30) NULL DEFAULT NULL,
			`mbanc_id` TEXT NULL,
			PRIMARY KEY (`id`),
			INDEX `comprob` (`comprob`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		AUTO_INCREMENT=1;
		";
		
		$this->db->simple_query($query);
	  
    $query="ALTER TABLE `casi` CHANGE COLUMN `comprob` `comprob` VARCHAR(45) NOT NULL ";
    $this->db->simple_query($query);
    $query="ALTER TABLE `casi`  CHANGE COLUMN `status` `status` CHAR(2) NULL DEFAULT NULL";
    $this->db->simple_query($query);
    $query="ALTER TABLE `itcasi`  CHANGE COLUMN `referen` `referen` VARCHAR(100) NULL DEFAULT NULL";
    $this->db->simple_query($query);
    $query="ALTER TABLE `casi`  CHANGE COLUMN `status` `status` CHAR(2) NULL DEFAULT 'C1'";
    $this->db->simple_query($query);
    $query="ALTER TABLE `itcasi`  CHANGE COLUMN `referen` `referen` TEXT NULL DEFAULT NULL ,  CHANGE COLUMN `concepto` `concepto` TEXT NULL DEFAULT NULL AFTER";
    $this->db->simple_query($query);
    $query="ALTER TABLE `cpla`  ADD COLUMN `fcreacion` DATE NULL";
    $this->db->simple_query($query);
    $qeury="ALTER TABLE `reglascont`  CHANGE COLUMN `concepto` `concepto` TEXT NULL DEFAULT NULL";
    $this->db->simple_query($query);
    $query="ALTER TABLE `cpla`  ADD COLUMN `felimina` DATE NULL";
    $this->db->simple_query($query);
    $query="ALTER TABLE `itcasi`  CHANGE COLUMN `referen` `referen` TEXT NULL DEFAULT NULL";
    $this->db->simple_query($query);
    $query="ALTER TABLE `itcasi`  ADD COLUMN `mbanc_id` text NULL DEFAULT NULL";
    $this->db->simple_query($query);
    $query="ALTER TABLE `itcasi` CHANGE COLUMN `cuenta` `cuenta` CHAR(50) NULL DEFAULT NULL AFTER `origen`";
    $this->db->simple_query($query);
  }
}
?>
