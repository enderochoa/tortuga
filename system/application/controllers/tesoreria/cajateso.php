<?php
class cajateso extends Controller {

	function cajateso(){
		parent::Controller();
		$this->load->library('rapyd');
	}

	function index(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();
		$this->datasis->modulo_id(206,1);

		$filter = new DataFilter("");

		$filter->db->select(array('a.observa','a.observacaj','c.numero relacion','a.id','MID(a.observa,1,50)observa','a.cheque','a.benefi','a.fecha','a.monto','a.fentrega','fdevo','fcajrecibe','fcajdevo','sta'));
		$filter->db->from('mbanc AS a');
		$filter->db->join('sprv AS b',' a.cod_prov = b.proveed ','left');
		$filter->db->join('relch AS c','a.relch = c.id');
		$filter->db->where('a.tipo_doc','CH');
		$filter->db->where('a.status in ("AN","A2","E2","J2")');

		$filter->benefi = new inputField('Beneficiario', 'benefi');

		$filter->cheque = new inputField('Cheque', 'cheque');
		$filter->cheque->clause='where';
		$filter->cheque->operator='=';

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->dbformat = "Y-m-d";
		$filter->fecha->size=9;

		$filter->numero = new inputField('N&uacute;mero de relaci&oacute;n', 'numero');
		$filter->numero->db_name = 'c.numero';
		
		$filter->observa = new inputField('Concepto', 'observa');
		$filter->observa->db_name = 'a.observa';
		
		$filter->observacaj = new inputField('Observaci&oacute;n', 'observacaj');
		$filter->observacaj->db_name = 'a.observacaj';

		$filter->buttons('reset','search');
		$filter->build();
		
		$fecha=new dateField('', 'fe','d/m/Y');
		$fecha->status='create';
		$fecha->size=10;
		$fecha->insertValue = date('Ymd');
		$fecha->build();
		
		$caduco2 =new dropdownField('', 'caduco2');
		$caduco2->status='create';
		$caduco2->option("","");
		$caduco2->option("S","Si");
		$caduco2->build();

		function recibe($fcajrecibe,$id,$fcajdevo,$fentrega){
			if(!(strlen($fcajdevo.$fentrega)>0)){
				if(strlen($fcajrecibe)>0)
				return form_checkbox('cch', $id, TRUE);
				else
				return form_checkbox('cch', $id, FALSE);
			}
		}

		function devo($fcajdevo,$id,$fcajrecibe,$fentrega){
			if(strlen($fcajrecibe)>0 && !(strlen($fentrega)>0)){
				if(strlen($fcajdevo)>0)
				return form_checkbox('cch2', $id, TRUE);
				else
				return form_checkbox('cch2', $id, FALSE);
			}
		}
		
		$grid = new DataGrid("Fecha".$fecha->output." Caduco ".$caduco2->output);
		$grid->use_function('recibe','devo');
		$grid->order_by("cheque","desc");
		$grid->per_page =100;
		$grid->column_orderby("Fecha"           ,"<dbdate_to_human><#fecha#></dbdate_to_human>"                     ,"fecha"      ,"align='center'");
		$grid->column_orderby("Relaci&oacute;n" ,"relacion"                                                         ,"relacion"                  );
		$grid->column_orderby("Transaccion"     ,"cheque"                                                           ,"cheque"                    );
		$grid->column_orderby("Benefiario"      ,"benefi"                                                           ,"benefi"     ,"align='left'  ");
		$grid->column_orderby("Monto"           ,"<nformat><#monto#></nformat>"                                                   ,"monto"    ,"align='right' ");
		$grid->column_orderby("F.Recibido"      ,"<dbdate_to_human><#fcajrecibe#></dbdate_to_human>"                ,"fcajrecibe" ,"align='center'");
		$grid->column_orderby("Recibido"        ,"<recibe><#fcajrecibe#>|<#id#>|<#fcajdevo#>|<#fentrega#></recibe>" ,"fentrega"   ,"align='center'");
		$grid->column_orderby("F.Dev.Tesoreria" ,"<dbdate_to_human><#fcajdevo#></dbdate_to_human>"                  ,"fcajdevo"   ,"align='center'");
		$grid->column_orderby("Dev.Tesoreria"   ,"<devo><#fcajdevo#>|<#id#>|<#fcajrecibe#>|<#fentrega#></devo>"     ,"fcajdevo"   ,"align='center'");
		
		//$grid->column_orderby("Recibido"        ,"<recibe><#fcajrecibe#>|<#id#>|<#sta#></recibe>"                   ,"fentrega"   ,"align='center'");
		//$grid->column_orderby("Devuelto"        ,"<devo><#fcajdevo#>|<#id#>|<#fcajrecibe#>|<#sta#></devo>"          ,"fdevo"      ,"align='center'");
		$grid->column_orderby("Concepto"           ,"observa"                                                        ,"observa"    ,"align='left'  ");
		$grid->column_orderby("Observaci&oacute;n" ,"observacaj"                                                     ,"observacaj" ,"align='left'  ");
		
		$grid->build();
		//echo $grid->db->last_query();

		//$data['content'] = $filter->output.$grid->output;
		$data['filtro']  = $filter->output;
		$data['content'] = anchor('/tesoreria/cajateso/recepcion','Recepci&oacute;n por relaciones');
		$data['content'].= $grid->output;
		$data['title']   = 'Control de Cheques en Caja';
		$data['script']  = script('jquery.js');
		$data['script'] .= '<script  type="text/javascript">
		$(function(){
			$(":checkbox").change(function(){
				name =$(this).attr("name");
					fechav=$("#fe").val();
					idv   =$(this).val();
					chv   =$(this).is(":checked");
					caduco=$("#caduco2").val();
					if(name=="cch"){
						if(chv){
							msj="Esta Seguro que desea Recibir el Cheque";
						}else{
							msj="Esta seguro de reversar la recepcion del Cheque";
						}
					
						if(confirm(msj)){
							$.post("'.site_url('tesoreria/cajateso/recibir').'",{ id: idv,fecha:fechav,val:chv},function(data){

							})
						}else{
							if(chv){
								$(this).attr("checked", false);	
							}else{
								$(this).attr("checked", true);
							}
							
						}
						
					}else{
						if(chv){
							msj="Esta Seguro que desea devolver el Cheque";
						}else{
							msj="Esta seguro de reversar la devolucion del Cheque";
							
						}
						
					
						if(confirm(msj)){
							$.post("'.site_url('tesoreria/cajateso/devolver').'",{ id: idv,fecha:fechav,val:chv,cad:caduco},function(data){
							})
						}else{
							if(chv){
								$(this).attr("checked", false);	
							}else{
								$(this).attr("checked", true);	
							}
						}
					}

			});
		});
		</script>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function recepcion(){
		$this->rapyd->load('datafilter','datagrid','fields');
		$this->rapyd->uri->keep_persistence();
		$this->datasis->modulo_id(205,1);

		$filter = new DataFilter('');
		$filter->db->select(array('c.numero','a.id','MID(a.observa,1,50)observa','a.cheque','a.benefi','a.fecha','a.monto','a.ffirma','a.status','a.fcajrecibe','a.fcajdevo','sta'));
		$filter->db->from('mbanc AS a');
		$filter->db->join('sprv  AS b','a.cod_prov = b.proveed','left');
		$filter->db->join('relch AS c','a.relch = c.id');
		$filter->db->where('a.tipo_doc','CH');
		$filter->db->order_by("a.desem,a.cheque");
		//$filter->db->where('a.status','E2');

		$filter->numero = new inputField('N&uacute;mero de relaci&oacute;n', 'numero');
		$filter->numero->db_name = 'c.numero';
		$filter->numero->rule='required';

		$filter->buttons('reset','search');
		$filter->build();

		$fecha=new dateField('', 'recibido','d/m/Y');
		$fecha->status='create';
		$fecha->size=10;
		$fecha->insertValue = date('Y-m-d H:i:s');
		$fecha->build();

		function entrega($ffirma,$id,$status){
			if(empty($ffirma) && $status ='E2'){
				return form_checkbox('cch[]', $id, TRUE);
				//return anchor('tesoreria/cajateso/entre/'.$id,'Recibir');
			}else{
				return dbdate_to_human($ffirma);
			}
		}

		function devo($fdevo,$id,$frecibe,$sta){
			if(empty($frecibe)){
				return '';
			}elseif(empty($fdevo) && $sta=='E22'){
				return '';anchor('tesoreria/cajateso/recedevo/'.$id,'Devolver a Tesoreria');
			}else{
				return dbdate_to_human($fdevo);
			}
		}

		$table='';
		if($filter->is_valid()){
			$grid = new DataGrid('Fecha de recepci&oacute;n '.$fecha->output);
			$grid->use_function('entrega','devo');
			$grid->order_by('desem','asc');
			$grid->per_page = 1000;

			$grid->column_orderby('Relacion'  ,'numero'                                         ,'numero'   ,'align=\'center\'');
			$grid->column_orderby('Fecha'     ,'<dbdate_to_human><#fecha#></dbdate_to_human>'   ,'fecha'   ,'align=\'center\'');
			$grid->column_orderby('Cheque'    ,'cheque'                                         ,'cheque'  ,'align=\'left\'  ');
			$grid->column_orderby('Benefiario','benefi'                                         ,'benefi'  ,'align=\'left\'  ');
			$grid->column_orderby('Monto'     ,'<nformat><#monto#></nformat>'                                          ,'monto'   ,'align=\'right\' ');
			$grid->column('Recibido en caja'  ,'<entrega><#fcajrecibe#>|<#id#>|<#status#></entrega>','align=\'center\'');
			$grid->column('Devuelto'  ,'<devo><#fcajdevo#>|<#id#>|<#fcajrecibe#>|<#sta#></devo>','align=\'center\'');
			$grid->column_orderby('Concepto'  ,'observa'                                        ,'observa' ,'align=\'left\'  ');
			$grid->build();

			if($grid->recordCount>0){
				$attr = array('id' => 'procesach');
				$table = form_open('tesoreria/cajateso/pross',$attr);
				$table.= $grid->output;
				$table.= form_submit('mysub', 'Guardar');
				$table.= form_close();
			}else{
				$table = '<center>N&uacute;mero de Orden inv&aacute;lida</center>';
			}
		}

		$data['content'] = $filter->output;
		$data['content'].= $table;
		$data['script']  = '<script  type="text/javascript">
		$(function(){
			$("#procesach").submit(function() {
				var fecha=$("#recibido").val();
				return confirm("Recibir los cheques con fecha "+fecha+"?");
			});
		});
		</script>';

		$data['title']   = 'Recepci&oacute;n de cheques en Caja por relaci&oacute;n';
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$this->load->view('view_ventanas', $data);
	}

	function pross(){
		$this->load->library('validation');
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence('tesoreria/cajateso/recepcion', $this->rapyd->uri->gfid);
		$back  = (isset($persistence['back_uri'])) ?$persistence['back_uri'] : 'tesoreria/cajateso/recepcion';
		$fecha = $this->input->post('recibido');
		$rt=$this->validation->chfecha($fecha);

		if($rt){
			$fecha = human_to_dbdate($fecha);

			$cch=$this->input->post('cch');
			foreach($cch AS $id){
				$id=$this->db->escape($id);
				$data = array('ffirma'=>$fecha,'fcajrecibe'=>$fecha,'sta'=>'E22');
				$where = "id = $id";
				$mSQL = $this->db->update_string('mbanc', $data, $where);
				$this->db->simple_query($mSQL);
			}
		}
		redirect($back);
	}

	function recibe($id){
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence('tesoreria/cajateso/index', $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ?$persistence['back_uri'] : 'tesoreria/cajateso/index';

		$id=$this->db->escape($id);
		$fecha=date('Y-m-d');
		$data = array('ffirma'=>$fecha,'fcajrecibe'=>$fecha,'sta'=>'E22');
		$where = "id = $id";
		$mSQL = $this->db->update_string('mbanc', $data, $where);

		$this->db->simple_query($mSQL);
		redirect($back);
	}

	function devo($id){
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence('tesoreria/cajateso/index', $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ?$persistence['back_uri'] : 'tesoreria/cajateso/index';
		$this->_devo($id);

		redirect($back);
	}

	function recedevo($id){
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence('tesoreria/cajateso/recepcion', $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ?$persistence['back_uri'] : 'tesoreria/cajateso/recepcion';
		$this->_devo($id);

		redirect($back);
	}

	function _devo($id){
		$id=$this->db->escape($id);
		$fecha=date('Ymd');
		$data = array('fcajdevo'=>$fecha,'sta'=>'E21');
		$where = "id = $id";
		$mSQL = $this->db->update_string('mbanc', $data, $where);

		$this->db->simple_query($mSQL);
	}
	
	function recibir(){
				$id    =$this->input->post("id");
				$fecha =$this->input->post("fecha");
				$val   =$this->input->post("val");

				$fecha =explode('/',$fecha);

				$fecha =$fecha[2].'-'.$fecha[1].'-'.$fecha[0];

				if($val=='true')
						$data = array('fcajrecibe'=>$fecha);
				else{
						$data = array('fcajrecibe'=>null,'sta'=>'E22');
				}
				$where = "id = $id";
				$mSQL = $this->db->update_string('mbanc', $data, $where);

				$this->db->simple_query($mSQL);
	}

	function devolver(){
				$id    =$this->input->post("id");
				$fecha =$this->input->post("fecha");
				$val   =$this->input->post("val");
				$caduco=$this->input->post("cad");
				
				if(!$caduco)
				$caduco='N';
				
				$fecha =explode('/',$fecha);
				
				$fecha =$fecha[2].'-'.$fecha[1].'-'.$fecha[0];

				if($val=='true')
						$data = array('fcajdevo'=>$fecha,'caduco'=>$caduco);
				else{
						$data = array('fcajdevo'=>null,'caduco'=>$caduco);
				}
				$where = "id = $id";
				$mSQL = $this->db->update_string('mbanc', $data, $where);

				$this->db->simple_query($mSQL);
	}
}
