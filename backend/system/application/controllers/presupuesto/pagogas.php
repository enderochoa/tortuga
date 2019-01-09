<?php
class Pagogas extends Controller {

	var $url = 'presupuesto/pagogas/';
	
	function Pagogas(){
		parent::Controller(); 
		$this->load->library("rapyd");		
	}
	
	function index() {
		//$this->datasis->modulo_id(210,1);
		redirect($this->url."busca");
	}
	
	function busca(){
		$this->rapyd->load("dataform");		
			
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
		
		$filter->submit("btnsubmit","Buscar");		
		$filter->build_form();
		//exit('=============');
		if ($filter->on_success()){
			
			$cod_prov = $filter->cod_prov->newValue;
			
			redirect($this->url."selecciona/$cod_prov");
		} 

		$data['content'] = $filter->output;
		$data['title']   = "Pago de Ordenes de Gasolina";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function selecciona($cod_prov){
		$this->rapyd->load("datagrid","dataobject","fields");

		function asigna($numero,$status){
			
			$data = array(
			  'name'    => "sepago[]",
			  'id'      => $numero,
			  'value'   => $numero,
			  'checked' => $status=='C' ? TRUE:FALSE,
			);
			return form_checkbox($data);
		}

		$tabla=form_open($this->url."carga/");
			
		$ddata = array(
              'cod_prov'  => $cod_prov
            );	
		
		$grid = new DataGrid("Lista de Ordenes de Compra de Gasolina");
		$grid->db->select(array("numero","fecha","placa","solicitante","concepto","monto","status"));
		$grid->db->from('ocomrapid');
		$grid->db->where("cod_prov ",$cod_prov);
		$grid->db->where("status "  ,"P" );
		$grid->use_function('asigna');
		
		$grid->column("Numero"           ,"numero"                                                ,'align=left');
		$grid->column("Fecha"            ,"fecha"                                                 ,'align=left');
		$grid->column("Placa"            ,"placa"                                                 ,'align=left');
		$grid->column("Solicitante"      ,"solicitante"                                           ,'align=left');
		$grid->column("Concepto"         ,"concepto"                                              ,'align=left');
		$grid->column("Monto"            ,"<number_format><#monto#>|2|,|.</number_format>"        ,"align='right'" );
		$grid->column("Accio&oacute;n"   ,"<asigna><#numero#>|<#status#></asigna>"                ,"align='right'" );
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

		$do = new DataObject("ocomrapid");
				
		$data=$this->input->post('sepago');
		
		$tot=0;
		foreach($data AS $id){
			$do->load($id);
			
			$status= $do->get('status');
			$monto = $do->get('monto' );
			$tot  += $monto;
		}
			
		if($tot >0){
			$query="INSERT INTO `odirect`
			( `numero`,`tipo`,`fecha`   ,`status`,`cod_prov` ,`subtotal`,`exento`,`total`, `total2`,`observa`)
			VALUES
			(''      ,'B'   ,NOW('Ymd'), 'B'    ,'$cod_prov', $tot     , $tot   , $tot  , $tot    ,' Pago correspondiente a Ordenes de gasolina')
			";
			
			if($this->db->query($query)){
				$numero = $this->db->insert_id();
				
				$query="INSERT INTO `itodirect`
				(`id`,`numero`,`cantidad`,`precio`,`importe`, `unidad`,`descripcion`)
				VALUES
				('','$numero', 1        , $tot   , $tot    , 'monto' ,' Pago correspondiente a Ordenes de gasolina')
				";
			
				$this->db->query($query);
				
				$salida = '<p>Se creo la orden de Pago numero '.str_pad($numero,8,'0',STR_PAD_LEFT).'</p>';
				$tot=0;
				foreach($data AS $id){
					$monto = $do->get('monto' );
					$do->load($id);
					$do->set("status"   ,'C'    );
					$do->set("opago"    ,$numero);
					$do->save();
				}
			}else{
				$salida = "<p>Error al crear orden de pago</p>";
			}
		}else{
			$salida = "<p>La suma de la seleccion es cero(0)</p>";
		}
		
		logusu('pagogas',$salida);
		
		$data['content'] = $salida.'<p>'.anchor($this->url,'Regresar').'</p>';
		$data['title']   = " Pago de Deducciones de N&oacute;mina ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}