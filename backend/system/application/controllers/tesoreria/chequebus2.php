<?php
class chequebus2 extends Controller {

	var $titp='	Buscador Cheque, O.Pago, O.Compra   ';
	var $tits='	Buscador Cheque, O.Pago, O.Compra   ';
	var $url ='tesoreria/cheque/';

	function chequebus2(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->rapyd->config->set_item('theme','clean');
	}

	function index(){
		//$this->datasis->modulo_id(138,1);
		$this->rapyd->load('datafilter','datagrid');

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;odigo',
			'nombre'=>'Nombre',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'cod_prov','nombre'=>'nombre' ),
			'titulo'  =>'Buscar Beneficiario');
		$bSPRV=$this->datasis->p_modbus($mSPRV,'proveed');

		$filter = new DataForm('tesoreria/chequebus2/index/process');

		$filter->cod_prov = new inputField('Beneficiario', 'cod_prov');
		//$filter->cod_prov->rule = 'required';
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);

		$filter->nombre = new inputField('Nombre', 'nombre');
		//$filter->nombre->readonly=true;
		$filter->nombre->in='cod_prov';

		$filter->benefi = new inputField('A Nombre de', 'benefi');

		$filter->numero = new inputField('Orden de Pago', 'numero');

		$filter->rif = new inputField('Rif', 'rif');
		
		$filter->cheque = new inputField('Cheque', 'cheque');
		
		$filter->concepto = new inputField('Concepto', 'concepto');
		
		$filter->observacaj = new inputField('Observac&oacute;n', 'observacaj');

		$filter->submit('btnsubmit','Consultar');
		$filter->build_form();

		$tablas='';
		 if ($filter->on_success()){
			$cod_proveed=$filter->cod_prov->newValue;
			$benefi     =$filter->benefi->newValue;
			$numero     =$filter->numero->newValue;
			$rif        =$filter->rif->newValue;
			$cheque     =$filter->cheque->newValue;
			$concepto   =$filter->concepto->newValue;
			$observacaj =$filter->observacaj->newValue;
			//ocompra/nomi
			//odirec
			//mban
			$limite=10000;
			
			function observac($id,$observa){
				$data = array(
				  'name'        => 'txt_'.$id,
				  'id'          => 'txt_'.$id,
				  'value'       => $observa,
				  'cols'        => '20',
				  'rows'        => '2'
				);
				
				return form_textarea($data);
			}

			$grid = new DataGrid(heading('Cheques Emitidos'));
			$grid->db->select(array('a.observacaj','GROUP_CONCAT(c.pago) pagos','id','fecha','cheque','nombre','benefi','monto','a.observa','fentrega','fdevo','fcajrecibe','fcajdevo'));
			$grid->db->from('mbanc AS a');
			$grid->db->join('sprv AS b',' a.cod_prov = b.proveed ','LEFT' );
			$grid->db->join('pades AS c',' a.desem = c.desem ','LEFT' );
			$grid->db->where('a.tipo_doc'   ,'CH');
			$grid->db->where('MID(a.status,2,1)','2');
			$grid->db->where('a.destino','C');
			if(!empty($cod_proveed))
			$grid->db->where('a.cod_prov',$cod_proveed);
			if(!empty($benefi))
			$grid->db->where('a.benefi LIKE ',"%$benefi%");
			if(!empty($numero))
			$grid->db->where('c.pago LIKE ',"%$numero%");
			if(!empty($rif))
			$grid->db->where('b.rif LIKE ',"%$rif%");
			if(!empty($cheque))
			$grid->db->where('a.cheque LIKE ',"%$cheque%");
			if(!empty($concepto))
			$grid->db->where('a.observa LIKE ',"%$concepto%");
			if(!empty($observacaj))
			$grid->db->where('a.observacaj LIKE ',"%$observacaj%");
			
			
			$grid->db->limit($limite);
			$grid->db->groupby('a.id');
			$grid->order_by('id','desc');
			$grid->use_function('observac');

			$grid->column('Fecha'             ,'<dbdate_to_human><#fecha#></dbdate_to_human>'  ,"align='center'");
			$grid->column('Cheque'            ,'cheque');
			$grid->column('Beneficiario'      ,'nombre');
			$grid->column('A Nombre de'       ,'benefi');
			$grid->column('Monto'             ,'<number_format><#monto#>|2|,|.</number_format>' ,"align='right'");
			$grid->column('Concepto'          ,"<wordwrap><#observa#>|50|\n|true</wordwrap>"    ,"align='left' ");
			$grid->column('F. Devo Caja'      ,'<dbdate_to_human><#fcajdevo#></dbdate_to_human>'    ,"align='left' ");
			$grid->column('F. Recibe Caja'    ,'<dbdate_to_human><#fcajrecibe#></dbdate_to_human>'  ,"align='left' ");
			$grid->column('F. Entregado'      ,'<dbdate_to_human><#fentrega#></dbdate_to_human>'    ,"align='left' ");
			$grid->column('F. Devuel'         ,'<dbdate_to_human><#fdevo#></dbdate_to_human>'       ,"align='left' ");
			$grid->column('Ordenes de Pagos'  ,'pagos'                                          ,"align='left' ");
			$grid->column('Observaci&oacute;n','<observac><#id#>|<#observacaj#></observac>'     ,"align='left' ");
			$grid->build();
			
			$grid->db->last_query();
			
			
			function sta($status){
				$status2=substr($status,1,1);
				switch($status2){
					case "2":return "Por Pagar";break;
					case "3":return "Pagada";break;
					case "A":return "Anulada";break;
					default: return $status;
				}
			}
			
			function opobservac($id,$observa){
				$data = array(
				  'name'        => 'optxt_'.$id,
				  'id'          => 'optxt_'.$id,
				  'value'       => $observa,
				  'cols'        => '20',
				  'rows'        => '2'
				);
				
				return form_textarea($data);
			}

			if(!$cheque){
			$grid2 = new DataGrid(heading('Ordenes de Pago'));
			$grid2->db->select(array("numero","proveed","b.nombre","rif","fecha","total","a.reteiva","reten","imptimbre","total2","a.observa","status","
			(SELECT GROUP_CONCAT(cheque) FROM pades x JOIN desem y ON x.desem=y.numero JOIN mbanc z ON y.numero=z.desem WHERE a.numero=x.pago ".(strlen($concepto)>0?" AND z.observa LIKE '%$concepto%' ":"").(strlen($observacaj)>0?" AND z.observacaj LIKE '%$observacaj%' ":"")." ) cheque"));
			$grid2->db->from('odirect AS a');
			$grid2->db->join('sprv AS b',' a.cod_prov = b.proveed ' ,'LEFT');
			$grid2->db->where('MID(a.status,2,1) <>','A');
			$grid2->db->where('MID(a.numero,1,1) <>','_');
			if(!empty($cod_proveed))
			$grid2->db->where('a.cod_prov',$cod_proveed);
			if(!empty($benefi))
			$grid->db->where('b.nombre LIKE',"%$benefi%");
			if(!empty($numero))
			$grid->db->where('a.numero LIKE ',"%$numero%");
			if(!empty($rif))
			$grid->db->where('b.rif LIKE ',"%$rif%");
			
			//$grid->db->where("(SELECT COUNT(*) FROM pades x JOIN desem y ON x.desem=y.numero JOIN mbanc z ON y.numero=z.desem WHERE a.numero=x.pago ".(strlen($concepto)>0?" AND z.observa LIKE '%$concepto%' ":"").(strlen($observacaj)>0?" AND z.observacaj LIKE '%$observacaj%' ":"")." ) >0 ");
			
			$grid2->db->limit($limite);
			$grid2->order_by('numero','desc'  );
			
			$grid2->use_function('substr','str_pad','sta','opobservac');
			
			$grid2->column('N&uacute;mero'    ,'numero'                                                                 );
			$grid2->column('Cod. '            ,'proveed'                                                                );
			$grid2->column('Beneficiario'     ,'nombre'                                                                 );
			$grid2->column('RIF'              ,'rif'                                                                    );
			$grid2->column('Fecha'            ,'<dbdate_to_human><#fecha#></dbdate_to_human>'      ,"align='center'    ");
			$grid2->column('Transacciones'    ,'cheque'                                                                 );
			$grid2->column('Monto A Pagar'    ,'<nformat><#total#></nformat>'                      ,"align='right'     ");
			$grid2->column('R. IVA'           ,'<nformat><#reteiva#></nformat>'                    ,"align='right'     ");
			$grid2->column('R. ISLR'          ,'<nformat><#reten#></nformat>'                      ,"align='right'     ");
			$grid2->column('R. 1X1000'        ,'<nformat><#imptimbre#></nformat>'                  ,"align='right'     ");
			$grid2->column('Monto Total'      ,'<nformat><#total2#></nformat>'                     ,"align='right'     ");
			$grid2->column('Concepto'         ,"<wordwrap><#observa#>|50|\n|true</wordwrap>"       ,"align='left'      ");
			$grid2->column('Estado'           ,'<sta><#status#></sta>'                                                  );
			$grid->column('Observaci&oacute;n','<opobservac><#numero#>|<#opobservacaj#></observac>'     ,"align='left' "    );
			$grid2->build();
			}
			//echo $grid2->db->last_query();
			
			function sta3($status){
				
				switch($status){
					case "M":return "Inicio Proceso";break;
					case "P":return "Por Comprometer";break;
					case "C":return "Comprometida";break;
					case "T":return "Causada";break;
					case "O":return "Orden Asignada";break;
					case "E":return "Pagada";break;
					case "A":return "Anulada";break;
					default: return $status;
				}
			}

			$grid3 = new DataGrid(heading('Orden de Compra'));
			$grid3->use_function('substr','str_pad','sta3');
			
			$grid3->db->select(array('a.numero','a.fecha','b.nombre','a.observa','a.total2','status','GROUP_CONCAT(pacom.pago) pagos'));
			$grid3->db->from('ocompra AS a');
			$grid3->db->join('pacom','a.numero=pacom.compra','LEFT');
			$grid3->db->join('sprv AS b','a.cod_prov = b.proveed' ,'LEFT');
			$grid3->db->group_by('a.numero');
			
			if(!empty($cod_proveed))
			$grid2->db->where('a.cod_prov',$cod_proveed);
			if(!empty($benefi))
			$grid->db->where('b.nombre LIKE',"%$benefi%");
			if(!empty($rif))
			$grid->db->where('b.rif LIKE ',"%$rif%");
			if(!empty($numero))
			$grid->db->where("(SELECT COUNT(*) FROM pacom WHERE a.numero=pacom.compra AND pago LIKE ".$this->db->escape('%'.$numero.'%')." ) > 0");
	 	
			//$grid->db->where("(SELECT COUNT(*) FROM pacom p JOIN odirect q ON p.pago=q.numero JOIN  pades x ON x.pago=q.numero JOIN desem y ON x.desem=y.numero JOIN mbanc z ON y.numero=z.desem WHERE p.compra=a.numero AND a.numero=x.pago ".(strlen($concepto)>0?" AND z.observa LIKE '%$concepto%' ":"").(strlen($observacaj)>0?" AND z.observacaj LIKE '%$observacaj%' ":"")." ) >0 ");
			
			
			$grid3->db->limit($limite);
			$grid3->order_by('numero','asc');
			$grid3->column('N&uacute;mero','numero');
			$grid3->column('Fecha'        ,'<dbdate_to_human><#fecha#></dbdate_to_human>',"align='center'");
			$grid3->column('Nombre'       ,'nombre');
			$grid3->column('Monto'        ,'<nformat><#total2#></nformat>',"align='right'");
			$grid3->column('Observaci&oacute;n'  ,'observa'                     ,"align='left'");
			$grid3->column('Estado'       ,"<sta3><#status#></sta3>"                                   );
			$grid3->column('O.Pago'      ,"pagos"                                       );
			$grid3->build();
			//echo $grid3->db->last_query();


			$tablas ='<table width=\'100%\'>';
			$tablas.='<tr><td scrollbar="yes" width="100%" height="100px">';
			$tablas.=$grid->output;
			$tablas.='</td></tr>';
			$tablas.='<tr><td scrollbar="yes" width="100%" max-height="100px">';
			$tablas.=($cheque?'':$grid2->output);
			$tablas.='</td></tr>';
			$tablas.='<tr><td scrollbar="yes" width="100%" height="100px">';
			$tablas.=$grid3->output;
			$tablas.='</td></tr>';
			$tablas.='<tr><td scrollbar="yes" width="100%" max-height="100px">';
			//$tablas.=$grid3->output;
			$tablas.='</td></tr>';
			$tablas.='</table>';
		}
		
		$data['script']  = script('jquery.js');
		$data['script'] .= '<script  type="text/javascript">
		$(function(){
			var a=0;
			var nombre="";
			$("textarea[name^=\'txt_\']").keyup(function(){
				nombre=this.name;
				idv=this.name.substr(4,100);
				val=$(this).val();
				$("#"+nombre).css("color","red");
				if(a==0){
					a=1;
					setTimeout(function() {
						$.post("'.site_url('tesoreria/chequebus/observacaj').'",{ id: idv,observacaj:val},function(data){
							});
						a=0;
						$("#"+nombre).css("color","black");
					} , 3000
					);
				}
			});
			
			$("textarea[name^=\'optxt_\']").keyup(function(){
				nombre=this.name;
				idv=this.name.substr(4,100);
				val=$(this).val();
				$("#"+nombre).css("color","red");
				if(a==0){
					a=1;
					setTimeout(function() {
						$.post("'.site_url('tesoreria/chequebus/opobservacaj').'",{ id: idv,observacaj:val},function(data){
						});
						a=0;
						$("#"+nombre).css("color","black");
					}, 3000
					);
				}
			});
		});
		</script>';

		$data['content'] = $filter->output.$tablas;
		//$data['filtro']  = $filter->output;
		//$data['content'] = $grid->output;
		$data['title']   = 'Buscador Cheque, O.Pago, O.Compra';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function instalar(){
		$query="ALTER TABLE `mbanc` ADD COLUMN `observacaj` TEXT NULL DEFAULT NULL	";
		$this->db->simple_query($query);
	}
}
