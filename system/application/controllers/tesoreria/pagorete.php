<?php
class Pagorete extends Controller {

	var $url = 'tesoreria/pagorete/';
	
	function Pagorete(){
		parent::Controller(); 
		$this->load->library("rapyd");		
	}
	
	function index() {
		//$this->datasis->modulo_id(210,1);
		redirect($this->url."busca");
	}
	
	function busca(){
		$this->rapyd->load("dataform");		
			
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo'),
				'retornar'=>array(
					'codbanc'=>'codbanc'),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos');

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");
			
		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;odigo',
			'nombre'=>'Nombre',
			'rif'=>'Rif',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
			'retornar'=>array('proveed'=>'cod_prov'),
			
			//'retornar'=>'ca_total',
			'titulo'  =>'Buscar Beneficiario');
		
		$bSPRV =$this->datasis->p_modbus($mSPRV ,"sprv");
		
		$filter = new DataForm($this->url."busca/process");
    
		$filter->cod_prov = new inputField("Proveedor", "cod_prov");
		$filter->cod_prov->rule='required';
		$filter->cod_prov->size=5;
		$filter->cod_prov->append($bSPRV);
		
		$filter->codbanc =  new inputField("Banco", 'codbanc');
		$filter->codbanc-> size     = 5;
		$filter->codbanc-> rule     = "required";
		$filter->codbanc-> append($bBANC);
		
		$filter->submit("btnsubmit","Buscar");		
		$filter->build_form();
		//exit('=============');
		if ($filter->on_success()){
			
			$cod_prov = $filter->cod_prov->newValue;
			$codbanc  = $filter->codbanc->newValue;
			
			redirect($this->url."selecciona/".raencode($cod_prov)."/".raencode($codbanc));
		} 

		$data['content'] = $filter->output;
		$data['title']   = "Pago de Deducciones de n&oacute;mina";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function selecciona($cod_prov,$codbanc){
		$this->rapyd->load("datagrid","dataobject","fields");
		$cod_prov = radecode($cod_prov);
		$codbanc  = radecode($codbanc);
		$codbance = $this->db->escape($codbanc);

		function asigna($id,$value="D"){
			$campo = new dropdownField("Title", "gt[$id]");
			$campo->status  = "modify";
			$campo->option("D","Pendiente"    );
			$campo->option("E","Cancelar"     );
			$campo->value   = $value;
			$campo->style="width:100px";
			$campo->build();
			return $campo->output;
		}

		$tabla=form_open($this->url."carga/");
		
		$ddata = array(
			'cod_prov'  => $cod_prov,
			'codbanc'   =>$codbanc
		);	
		
		$grid = new DataGrid("Lista de Deducciones de N&oacute;mina por Pagar");
		
		$grid->db->select(array("a.id","a.numero nomina","b.numero opago","d.numero desembolso","a.monto","a.status","b.observa"));
		$grid->db->from('retenomi a');
		$grid->db->join('odirect b','a.numero=b.nomina');
		$grid->db->join('pades c','b.numero=c.pago');
		$grid->db->join('desem d','c.desem=d.numero');
		$grid->db->where("a.cod_prov ",$cod_prov);
		$grid->db->where("a.status "  ,"D"  );
		$grid->db->where("d.status "  ,"D2" );
		$grid->db->where("(SELECT COUNT(*) FROM mbanc WHERE d.numero=mbanc.desem AND  codbanc=$codbance)>"  ,"0" );
		$grid->use_function('asigna');
		
		$grid->column("Nomina"           ,"nomina"                                           ,'align=left'    );
		$grid->column("O.Pago"           ,"opago"                                            ,'align=left'    );
		$grid->column("Desembolso"       ,"desembolso"                                       ,'align=left'    );
		$grid->column("Monto"            ,"<number_format><#monto#>|2|,|.</number_format>"   ,"align='right'" );
		$grid->column("Accio&oacute;n"   ,"<asigna><#id#>|<#status#></asigna>"               ,"align='right'" );
		$grid->build();
		//$grid->db->last_query();
		$tabla.=$salida = anchor($this->url.'busca','Regresar');
		$tabla.=$grid->output.form_submit('mysubmit', 'Guardar').form_hidden($ddata);
		$tabla.=form_close();
		
		if($grid->recordCount==0){
			$tabla.=$salida = anchor($this->url.'busca','Regresar');
			$tabla="<p>No hay deducciones pendientes por pagar al proveedor ($cod_prov)</p>";
			$tabla.=$salida = anchor($this->url.'busca','Regresar');
		}

		$data['content'] = $tabla;
		$data['title']   = " Pago de  Deducciones de N&oacute;mina ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function carga(){
		$this->rapyd->load("dataobject");
		$cod_prov = $this->input->post('cod_prov');
		$codbanc  = $this->input->post('codbanc');

		$do = new DataObject("retenomi");

		$data=$this->input->post('gt');

		//print_r($data);
		
		$tot=0;$nominas=array();
		foreach($data AS $id2=>$value){
			$do->load($id2);
			
			$status   = $do->get('status'   );
			$monto    = $do->get('monto'    );
			$nomina   = $do->get('numero'   );
			$cod_prov = $do->get('cod_prov' );
			if($value == 'E' && $status=='D')$tot  += $monto;
			if(!in_array($nomina,$nominas))$nominas[]=$nomina;
		}

		if($tot >0){
		
			$mbanc = new DataObject("mbanc");
			 
			$mbanc->set("monto"     ,$tot     );
			$mbanc->set("status"    ,"J"      );
			$mbanc->set("tipo"      ,"N"      );
			$mbanc->set("tipo_doc"  ,"CH"     );
			$mbanc->set("codbanc"   ,$codbanc );
			$mbanc->set("cod_prov"  ,$cod_prov);
			$mbanc->set("observa"   ,"Monto cancelado por motivo de deducciones de nomina");
			$mbanc->set('rel'       ,implode(',',$nominas));
			$mbanc->save();
			
			$id = $mbanc->get('id');
			
			if($id>0){
				
				$salida = '<p>Se creo el movimiento bancarios numero '.str_pad($id,8,'0',STR_PAD_LEFT).'</p>';
				$tot=0;
				foreach($data AS $id2=>$value){
					$tot+=$value;
					$do->load($id2);
					$do->set("status"   ,$value );
					$do->set("mbanc"    ,$id    );
					$do->save();
				}
				redirect("tesoreria/mbanc/dataedit/modify/$id");
			}else{
				$salida = "<p>Error al crear movimiento bancario</p>";
			}
		}else{
			$salida = "<p>La suma de la seleccion es cero(0)</p>";
		}
		
		logusu('pagorete',$salida);
		
		$data['content'] = $salida.'<p>'.anchor($this->url,'Regresar').'</p>';
		$data['title']   = " Pago de Deducciones de N&oacute;mina ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}