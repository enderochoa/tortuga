<?php

class Piva extends Controller {
	
	var $titp  = 'Pago de Retenciones de IVA';      
	var $tits  = 'Pago de Retencion de IVA';
	var $url   = 'tesoreria/piva/';    
	
	function Piva(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id('11B',1);
	}
	
	function index() {
		$this->rapyd->load("datafilter","datagrid");

		$filter = new DataFilter("Filtro de $this->titp","odirect");

		$filter->db->where("status !=", "B3");
		$filter->db->where("status !=", "B2");
		$filter->db->where("status !=", "B1");
		$filter->db->where("status !=", "G3");
		$filter->db->where("status !=", "G2");
		$filter->db->where("status !=", "G1");
		$filter->db->where("status !=", "R3");
		$filter->db->where("status !=", "R2");
		$filter->db->where("status !=", "R1");
		$filter->db->where("status !=", "F3");
		$filter->db->where("status !=", "F2");
		$filter->db->where("status !=", "F1");
	
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->db_name="a.id";
		$filter->numero->size  =10;
	
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
	
		$filter->beneficiario = new inputField("Beneficiario", "beneficiario");
		$filter->beneficiario->db_name="a.id";
		$filter->beneficiario->size = 20;
		
		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("I1","Sin Ejecutar");
		$filter->status->option("I2","Ejecutado");
		$filter->status->option("I3","Pagado");
		$filter->status->style="width:150px";
	
		$filter->buttons("reset","search");

		$filter->build();
		$uri = anchor($this->url.'dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|STR_PAD_LEFT</str_pad>');

		function sta($status){
			switch($status){
				case "I1":return "Sin Ejecutar";break;
				case "I2":return "Ejecutado";break;
				case "I3":return "Pagado";break;
			}
		}

		$grid = new DataGrid("Lista de ".$this->titp);
		$grid->order_by("numero","desc");
		$grid->per_page = 10;
		$grid->use_function('substr','str_pad');
		$grid->use_function('sta');

		$grid->column("N&uacute;mero"    ,$uri);
		$grid->column("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"  ,"align='center'" );
		$grid->column("Beneficiario"     ,"beneficiario"                                                    );
		$grid->column("Pago"             ,"<number_format><#total#>|2|,|.</number_format>","align='right'"  );
		$grid->column("Estado"           ,"<sta><#status#></sta>"                         ,"align='center'" );

		//echo $grid->db->last_query();
		$grid->add($this->url."dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " $this->titp ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
 		$this->rapyd->load("dataedit");
		
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo'),//39, 40
				'retornar'=>array(
					'codbanc'=>'codbanc','banco'=>'nombanc'
					 ),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos');

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$edit = new DataEdit($this->tits,"mbanc");
		
		$edit->back_url = $this->url."index/filteredgrid";
		
		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
		
		$edit->db->select(array("id","codbanc","cod_prov","cheque","abonado","tipo","numero","fecha","monto","observa","status","benefi","usuario","estampa","uejecutora","devo"));
		$edit->db->from('mbanc');
		//$edit->db->where("status ","B");
		 		 
		$edit->periodo = new dateonlyField("Perido", "periodo",'m/Y');
		//$edit->periodo->db_name     = " ";
		$edit->periodo->dbformat    = 'Ym';
		$edit->periodo->insertValue = date("Y-m");
		 
		$edit->codbanc =  new inputField("Banco", 'codbanc');
	  $edit->codbanc-> size     = 3;
	  $edit->codbanc-> rule     = "required";
	  $edit->codbanc-> append($bBANC);
    $edit->codbanc-> readonly=true;
     
    $edit->fecha = new  dateonlyField("Fecha Pago",  "fecha");
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size        =12;
		$edit->fecha->rule        = 'required';
     
    $edit->nombanc = new inputField("Nombre","nombanc");
    $edit->nombanc->size  =30;
    $edit->nombanc->readonly=true;
    $edit->nombanc->db_name =" ";
     
    $edit->cheque =  new inputField("Cheque", 'cheque');
	  $edit->cheque-> size  = 20;
	  $edit->cheque-> rule  = "required";
	   
	  $edit->beneficiario =  new inputField("Beneficiario", 'beneficiario');
	  $edit->beneficiario-> size  = 20;
	  $edit->beneficiario-> rule  = "required";
	   
	  $edit->observa =  new inputField("Observaciones", 'observa');
	  $edit->observa-> size  = 20;
	  $edit->observa-> rule  = "required";
		 
		$edit->buttons( "save","undo", "back");
		 
		$edit->build();
	
		$data['content'] = $edit->output;
		$data['title']   = " $this->tits ";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function _valida($do){
		$this->rapyd->load('dataobject');

		$error='';
		
		$codbanc=$do->get('codbanc');

		$banc   = new DataObject("banc");
		$banc   ->load($codbanc);
		$saldo  = $banc->get('saldo');
		$activo = $banc->get('activo');
		
		if($activo != 'S' )$error.="<div class='alert'><p>El banco ($banco) esta inactivo</p></div>";
		
		$reteiva= $this->datasis->dameval("SELECT SUM(reteiva) FROM riva WHERE periodo = $periodo AND status = 'B' ");

		if($reteiva > $saldo )$error.="<div class='alert'><p>El Monto ($reteiva) del cheque es mayor al disponible ($saldo) en el banco ($banco)</p></div>";
	
	
		$periodo = $do->get('periodo');
		
		print_r($do->get_all());
		return false;
		
		
	}
	
}
?>

$nombre        = $row->nombre        ;
$rif           = $row->rif           ;
$contrato      = $row->contrato      ;
$observa       = $row->observa       ;
$telefono      = $row->telefono      ;
$fecha         = $row->fecha         ;
$factura       = $row->factura       ;
$controlfac    = $row->controlfac    ;
$fechafac      = $row->fechafac      ;
$subtotal      = $row->subtotal      ;
$mivaa         = $row->mivaa         ;
$observa       = $row->observa       ;
$status        = $row->status        ;
$impmunicipal  = $row->impmunicipal  ;
$crs           = $row->crs           ;
$imptimbre     = $row->imptimbre     ;
$pcrs          = $row->pcrs          ;
$pimptimbre    = $row->pimptimbre    ;
$pimpmunicipal = $row->pimpmunicipal ;

$direccion     = $row->direccion     ;
$numcuent      = $row->numcuent      ;
$banco         = $row->banco         ;
$codbanc       = $row->codbanc       ;



<tr>   
	<td>Factura: <?=$factura ?>            </td>
	<td>Total Factura:<?=$total2 ?></td>
</tr>
<tr>   
	<td>Control Fiscal: <?=$controlfac ?> </td>
	<td>Objeto de Retenci�n:<?=$subtotal ?></td>
</tr>
<tr>   
	<td>Fecha Factura: <?=$fechafac ?>    </td>
	<td>% Tarifa 1X1000:<?=$subtotal ?></td>
</tr>

 