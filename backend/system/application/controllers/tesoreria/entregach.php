<?php
class entregach extends Controller {

	function Entregach(){
		parent::Controller();
		$this->load->library('rapyd');
	}

	function index(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();
		$this->datasis->modulo_id(206,1);

		$filter = new DataFilter('');

		$filter->db->select(array('c.numero relacion','a.id','MID(a.observa,1,50) observa','a.cheque','a.benefi','a.fecha','a.monto','a.fentrega','fdevo','sta'));
		$filter->db->from('mbanc AS a');
		$filter->db->join('sprv AS b',' a.cod_prov = b.proveed ','left' );
		$filter->db->join('relch AS c','a.relch = c.id');
		$filter->db->where('a.tipo_doc','CH');
		$filter->db->where('a.fcajrecibe IS NOT NULL AND a.fcajdevo IS NULL');

		$filter->benefi = new inputField('Beneficiario', 'benefi');

		$filter->cheque = new inputField('Cheque', 'cheque');
		$filter->cheque->clause='where';
		$filter->cheque->operator='=';

		$filter->fecha = new dateonlyField('Fecha', 'fecha');
		$filter->fecha->dbformat = 'Y-m-d';
		$filter->fecha->size=9;
		
		$filter->numero = new inputField('N&uacute;mero de relaci&oacute;n', 'numero');
		$filter->numero->db_name = 'c.numero';
		
		$filter->concepto = new inputField('Concepto Cheque', 'concepto');
		$filter->concepto->db_name = 'a.observa';

		$filter->buttons('reset','search');
		$filter->build();

		$fecha=new dateField('', 'fe','d/m/Y');
		$fecha->status='create';
		$fecha->size=10;
		$fecha->insertValue = date('Ymd');
		$fecha->build();

		function entrega($fentrega,$id,$sta){
			//if(empty($ffirma) && $status ='E2'){
				if(strlen($fentrega)>0)
				return form_checkbox('cch', $id, TRUE);
				else
				return form_checkbox('cch', $id, FALSE);
				//return anchor('tesoreria/cajateso/entre/'.$id,'Recibir');
			//}else{
			//	return dbdate_to_human($ffirma);
		}

		function devo($fdevo,$id,$fentrega,$sta){
				if(strlen($fentrega)>0){
					if(strlen($fdevo)>0)
					return form_checkbox('cch2', $id, TRUE);
					else
					return form_checkbox('cch2', $id, FALSE);
				}else{
				return '';
				}
		}
		
		function fentrega($id,$fentrega){
			$salida ='<span id="f_'.$id.'">';
			if(strlen($fentrega)>0)
			$salida.=dbdate_to_human($fentrega);
			else
			$salida.='';
			
			$salida.='</span>';
			return $salida;
		}

		$grid = new DataGrid('Fecha de Entrega'.$fecha->output);
		$grid->use_function('entrega','devo','fentrega');
		$grid->order_by("cheque","desc");
		$grid->per_page = 20;
		$grid->column_orderby("Fecha"        ,"<dbdate_to_human><#fecha#></dbdate_to_human>"        ,"fecha"   ,"align='center'" );
		$grid->column_orderby("Relaci&oacute;n" ,"relacion"                                         ,"relacion"                  );
		$grid->column_orderby("Numero"       ,"cheque"                                              ,"cheque"                   );
		$grid->column_orderby("Benefiario"   ,"benefi"                                              ,"benefi"  ,"align='left'  ");
		$grid->column_orderby("Monto"        ,"<nformat><#monto#></nformat>"                                               ,"monto"   ,"align='right' ");
		$grid->column_orderby("F. Entregado" ,"<fentrega><#id#>|<#fentrega#></fentrega>"                   ,"fentrega","align='center'");
		$grid->column_orderby("Entregado"    ,"<entrega><#fentrega#>|<#id#>|<#sta#></entrega>"      ,"fentrega","align='center'");
		//$grid->column_orderby("Devuelto"     ,"<devo><#fdevo#>|<#id#>|<#fentrega#>|<#sta#></devo>"  ,"fdevo"   ,"align='center'");
		$grid->column_orderby("Concepto"      ,"observa"                                             ,"observa" ,"align='left'  ");
		$grid->build();
		//echo $grid->db->last_query();

		$data['script']  = script('jquery.js');
		$data['script'] .= '<script  type="text/javascript">
		$(function(){
			$(":checkbox").change(function(){
				name =$(this).attr("name");
					fechav=$("#fe").val();
					idv   =$(this).val();
					chv   =$(this).is(":checked");
					
					if(name=="cch"){
						if(chv){
							msj="Esta Seguro que desea entregar el Cheque con fecha "+fechav;
						}else{
							msj="Esta seguro de reversar la entrega del Cheque";
						}
					
						if(confirm(msj)){
							$.post("'.site_url('tesoreria/entregach/entregar').'",{ id: idv,fecha:fechav,val:chv},function(data){
								
									$("#f_"+idv).html(data);
							})
						}else{
							if(chv){
								$(this).attr("checked", false);	
							}else{
								$(this).attr("checked", true);
							}
						}
					}else{
						$.post("'.site_url('tesoreria/entregach/devolver').'",{ id: idv,fecha:fechav,val:chv},function(data){

						})
					}
			});
		});
		</script>';

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['title']   = 'Entrega de cheques';

		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function entregar(){
				$id    =$this->input->post("id");
				$fecha =$this->input->post("fecha");
				$val   =$this->input->post("val");

				$fecha =explode('/',$fecha);

				$fecha =$fecha[2].'-'.$fecha[1].'-'.$fecha[0];

				if($val=='true')
						$data = array('fentrega'=>$fecha,'sta'=>'E23','estampaentrega'=>date('YmdHms'));
				else{
						$data = array('fentrega'=>null,'sta'=>'E22','estampaentrega'=>date('YmdHms'));
				}
				$where = "id = $id";
				$mSQL = $this->db->update_string('mbanc', $data, $where);

				if($this->db->query($mSQL))
				echo dbdate_to_human($data['fentrega']);
	}

	function devolver(){
				$id    =$this->input->post("id");
				$fecha =$this->input->post("fecha");
				$val   =$this->input->post("val");
				
				$fecha =explode('/',$fecha);
				
				$fecha =$fecha[2].'-'.$fecha[1].'-'.$fecha[0];

				if($val=='true')
						$data = array('fdevo'=>$fecha,'sta'=>'E22');
				else{
						$data = array('fdevo'=>null,'sta'=>'E23');
				}
				$where = "id = $id";
				$mSQL = $this->db->update_string('mbanc', $data, $where);

				$this->db->simple_query($mSQL);
	}

	function entre($id,$externo=''){
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence('tesoreria/entregach/index', $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ?$persistence['back_uri'] : 'tesoreria/entregach/index';

		$id=$this->db->escape($id);
		$data = array('fentrega'=>date('Ymd'),'sta'=>'E23');
		$where = "id = $id";
		$mSQL = $this->db->update_string('mbanc', $data, $where);
		$this->db->simple_query($mSQL);
		
		redirect($back);
	}

	function devo($id){
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence('tesoreria/entregach/index', $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ?$persistence['back_uri'] : 'tesoreria/entregach/index';

		$id=$this->db->escape($id);
		$data = array('fdevo'=>date('Ymd'),'sta'=>'E22');
		$where = "id = $id";
		$mSQL = $this->db->update_string('mbanc', $data, $where);
		$this->db->simple_query($mSQL);
		redirect($back);
	}
	
	function instalar(){
			$query="ALTER TABLE `mbanc` ADD COLUMN `estampaentrega` DATETIME NULL DEFAULT NULL AFTER `observacaj`";
			$this->db->simple_query($query);
	}
}
