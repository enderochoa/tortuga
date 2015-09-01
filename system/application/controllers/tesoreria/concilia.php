<?php
class Concilia extends Controller {

	var $url = 'tesoreria/concilia/';
	
	function concilia(){
		parent::Controller(); 
		$this->load->library("rapyd");		
	}
	
	function index() {
		$this->datasis->modulo_id(237,1);
		redirect($this->url."sel");
	}
	
	function sel(){
		$this->rapyd->load("dataform");

		 	 $mBANC=array(
		'tabla' =>'banc',
		'columnas'=>array(
		'codbanc' =>'C&oacute;odigo',
		'banco'=>'Banco',
		'saldo'=>'Saldo'),
		'filtro' =>array(
		'codbanc' =>'C&oacute;odigo',
		'banco'=>'Banco',
		'saldo'=>'Saldo'),
		'retornar'=>array(
		'codbanc'=>'codbanc'
		),
		'titulo' =>'Buscar Bancos');
		
		$bBANC=$this->datasis->p_modbus($mBANC,"mbanc");
		
			
		$filter = new DataForm($this->url."sel/process");
    
		$filter->fechad = new dateonlyField("Desde", "fechad",'d/m/Y');
		$filter->fechad->clause  ="where";
		$filter->fechad->db_name ="fecha";
		$filter->fechad->operator=">=";
		$filter->fechad->insertValue = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-30, date("Y")));
		$filter->fechad->group = "Fecha";
		//$filter->fechad->dbformat='Y-m-d';
		
		$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechah->clause="where";
		$filter->fechah->insertValue = date("Ymd");
		$filter->fechah->db_name="fecha";
		$filter->fechah->operator="<=";
		$filter->fechah->group = "Fecha";
		//$filter->fechaH->dbformat='Y-m-d';
		
		$filter->codbanc = new inputField("Banco", 'codbanc');
		$filter->codbanc->db_name = 'codbanc';
		$filter->codbanc->rule    = 'required';
		$filter->codbanc-> size   = 5;
		$filter->codbanc->clause  ="where";
		$filter->codbanc->operator="=";
		$filter->codbanc->append($bBANC);
		$filter->codbanc->clause  ="where";
		$filter->codbanc->operator="=";
		
		$filter->concilia = new dropDownField("", 'concilia');
		$filter->concilia->option("","");
		$filter->concilia->option("S","Conciliado");
		$filter->concilia->option("N","Sin Conciliar");
		
		$filter->concilia->db_name = 'concilia';
		$filter->concilia-> size   = 3;
		
		$filter->submit("btnsubmit","Buscar");		
		$filter->build_form();
		
		if ($filter->on_success()){
			$fechad    = $filter->fechad->newValue;          
			$fechah    = $filter->fechah->newValue;
			$codbanc   = $filter->codbanc->newValue;
			$concilia  = $filter->concilia->newValue;

			redirect($this->url."asignar/$fechad/$fechah/$codbanc/$concilia");
		} 

		$data['content'] = $filter->output;
		$data['title']   = "Conciliaci&oacute;n";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function asignar($fechad,$fechah,$codbanc,$concilia=''){
		$this->rapyd->load("datagrid","dataobject","fields");
				
		function asigna($id,$value="N",$anulado="N"){
			if($anulado!="S"){
			
				$campo = new dropdownField("Title", "gt[$id]");
				$campo->status  = "modify";
				$campo->option("S","conciliado");
				$campo->option("N","Sin conciliar");
				$campo->value   = $value;
				$campo->style="width:100px";
 				$campo->build();
				
				return $campo->output;
			}else{
				return 'Anulado';
			}
		}
		
		$tabla=form_open($this->url."carga/");
			
		
		$ddata = array(
		'fechad'  => $fechad,
		'fechah'  => $fechah,
		'codbanc' => $codbanc
            );
            
    if(!empty($concilia))
		$ddata['concilia'] = $concilia;
		
		$grid = new DataGrid("Conciliaci&oacute;n desde ".dbdate_to_human($fechad)." hasta ".dbdate_to_human($fechah));
		$grid->db->select(array("id","cheque","MID(observa,1,80) observa","fecha","fechapago","monto","concilia","fconcilia","anulado","IF(tipo_doc='CH','CHEQUE',IF(tipo_doc='NC','N. CREDITO', IF(tipo_doc='ND','N. DEBITO', IF(tipo_doc='DP','DEPOSITO','')))) tipo"));
		$grid->db->from('mbanc');
		if(!empty($codbanc))
		$grid->db->where("codbanc = ",$codbanc);
		if(!empty($concilia))
		$grid->db->where("concilia = ",$concilia);
		$grid->db->where("fecha >= ",$fechad);
		$grid->db->where("fecha <= ",$fechah );
		$grid->use_function('asigna');     
		
		$grid->column("Tipo Transaccion" ,"tipo"                                                 ,'align=left');
		$grid->column("Nro. Transaccion" ,"cheque"                                               ,'align=left');
		$grid->column("Fecha Transaccion","<dbdate_to_human><#fecha#></dbdate_to_human>"         ,"align='center'");
		//$grid->column("Fecha Concilia","<dbdate_to_human><#fconcilia#></dbdate_to_human>" ,"align='center'");
		$grid->column("Concepto"         ,"observa"                                              ,'align=left'    );
		$grid->column("Monto"            ,"<number_format><#monto#>|2|,|.</number_format>"       ,"align='right'" );
		$grid->column("Verificado"       ,"<asigna><#id#>|<#concilia#>|<#anulado#></asigna>"     ,"align='right'" );
		$grid->build();
		//echo $grid->db->last_query();
		$tabla.=$salida = anchor($this->url.'sel','Regresar');
		$tabla.=$grid->output.form_submit('mysubmit', 'Guardar').form_hidden($ddata);
		$tabla.=form_close();
		
		if($grid->recordCount==0){
			$tabla='No hay registros para esta selecci&oacute;n';
		}
				
		$data['content'] = $tabla;
		$data['title']   = "Conciliaci&oacute;n";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function carga(){
		$this->rapyd->load("dataobject");
		$fechad = $this->input->post('fechad');
		$fechah = $this->input->post('fechah');
		$codbanc = $this->input->post('codbanc');

		$do = new DataObject("mbanc");
				
		$data=$this->input->post('gt');
						
		$tot=0;
		foreach($data AS $id=>$value){
			$tot+=$value;
			$do->load($id);
			$do->set("concilia" ,$value);
			$do->set("fconcilia",date('Ymd'));
			$do->save();
		}
		logusu('mbanc',"Concilio Bancos");
		$data['content'] = 'Guardado'.anchor($this->url,'regresar');
		$data['title']   = "Conciliaci&oacute;n";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}
