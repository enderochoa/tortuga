<?php
class chequecaja extends Controller {

	function chequecaja(){
		parent::Controller(); 
		$this->load->library('rapyd');
	}

	function index(){
	
	
	}


	function index2(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();
		$this->datasis->modulo_id(205,1);

		$filter = new DataFilter('');
		$filter->db->select(array('a.id','MID(a.observa,1,50)observa','a.cheque','b.nombre AS benefi','a.fecha','a.monto','a.ffirma','a.status'));
		$filter->db->from('mbanc AS a');
		$filter->db->join('sprv  AS b','a.cod_prov = b.proveed');
		$filter->db->join('relch AS c','a.relch = c.id');
		$filter->db->where('a.tipo_doc','CH');
		//$filter->db->where('a.status','E2');

		$filter->numero = new inputField('N&uacute;mero de relaci&oacute;n', 'numero');
		$filter->numero->db_name = 'c.numero';

		$filter->buttons('reset','search');
		$filter->build();

		function entrega($ffirma,$id,$status){
			if(empty($ffirma) && $status ='E2'){
				return anchor('tesoreria/chequecaja/entre/'.$id,'Recibir');
			}else{
				return dbdate_to_human($ffirma);
			}
		}

		$grid = new DataGrid('');
		$grid->use_function('entrega' );
		$grid->order_by('cheque','desc');
		$grid->per_page = 20;

		$grid->column_orderby('Fecha'     ,'<dbdate_to_human><#fecha#></dbdate_to_human>'   ,'fecha'   ,'align=\'center\'');
		$grid->column_orderby('Cheque'    ,'cheque'                                         ,'cheque'  ,'align=\'left\'  ');
		$grid->column_orderby('Benefiario','benefi'                                         ,'benefi'  ,'align=\'left\'  ');
		$grid->column_orderby('Monto'     ,'monto'                                          ,'monto'   ,'align=\'right\' ');
		$grid->column('Recibido en caja'  ,'<entrega><#ffirma#>|<#id#>|<#status#></entrega>','align=\'center\'');
		$grid->column_orderby('Concepto'  ,'observa'                                        ,'observa' ,'align=\'left\'  ');

		$grid->build();
		//$grid->db->last_query();

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script']  = script('jquery.js')."\n";
		$data['title']   = 'Cheques Recibido en Caja';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function entre($id){
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence('tesoreria/chequecaja/index', $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ?$persistence['back_uri'] : 'tesoreria/chequecaja/index';

		$id=$this->db->escape($id);
		$data = array('ffirma'=>date('Ymd'),'sta'=>'E21');
		$where = "id = $id";
		$mSQL = $this->db->update_string('mbanc', $data, $where);
		$this->db->simple_query($mSQL);
		redirect($back);
	}


}
