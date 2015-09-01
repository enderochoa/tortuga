<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class Conci extends Common {

	function Conci(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		//SELECT * FROM bmov
		//WHERE codbanc='01' AND EXTRACT(YEAR_MONTH FROM fecha)<=201004 AND anulado!='S' AND liable!='N' AND
		//(concilia=0 OR EXTRACT(YEAR_MONTH FROM concilia)>=20100430 OR concilia<fecha)
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter('');

		$filter->db->from('mbanc');
		$filter->db->where('(MID(status,2,1) =2 or status="NC")' );
		//$filter->db->where('liable  !=','N' );

		$where='activo = "S"';
		$mf=$this->datasis->puede(333);
		$mo=$this->datasis->puede(334);
		if($mf && $mo){
		    
		}elseif($mf){
		    $where.=' AND tipocta="F"';
		}elseif($mo){
		    $where.=' AND tipocta<>"F"';
		}
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta',
					'saldo'=>'Saldo'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'saldo'=>'Saldo',
					'numcuent'=>'Cuenta'),
				'retornar'=>array(
					'codbanc'=>'codbanc' ),
				'where'=>$where,
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
		    'rif'=>'Rif',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre','rif'=>'Rif'),
				'retornar'=>array('proveed'=>'cod_prov' ),
				'titulo'  =>'Buscar Beneficiario');

		$bSPRV=$this->datasis->p_modbus($mSPRV,"proveed");

		$filter->tipo_doc = new dropdownField('Tipo de Operacion', 'tipo_doc');
		$filter->tipo_doc->option('','Todos');
		$filter->tipo_doc->option('NC','Nota de Cr&eacute;dito');
		$filter->tipo_doc->option('ND','Nota de Debito');
		$filter->tipo_doc->option('DE','Deposito');
		$filter->tipo_doc->option('CH','Cheque');
		//$filter->tipo_op->db_name='a.tipo_doc';

		$filter->fechad = new dateonlyField("Desde", "fechad",'d/m/Y');
		$filter->fechad->clause  ="where";
		$filter->fechad->db_name ="fecha";
		$filter->fechad->operator=">=";
		$filter->fechad->insertValue = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-30, date("Y")));
		$filter->fechad->group = "Fecha de Cheque";
		//$filter->fechad->dbformat='Y-m-d';
		//$filter->fechad->db_name='a.fecha';
		$filter->fechad->rule    ='required';

		$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechah->clause="where";
		$filter->fechah->insertValue = date("Ymd");
		$filter->fechah->db_name="fecha";
		$filter->fechah->operator="<=";
		$filter->fechah->group = "Fecha de Cheque";
		//$filter->fechaH->dbformat='Y-m-d';
		//$filter->fechah->db_name='a.fecha';
		
		$filter->fecha2d = new dateonlyField("Desde", "fecha2d",'d/m/Y');
		$filter->fecha2d->clause  ="where";
		$filter->fecha2d->db_name ="fecha2";
		$filter->fecha2d->operator=">=";
//		$filter->fecha2d->insertValue = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-30, date("Y")));
		$filter->fecha2d->group = "Fecha de Planilla";
		//$filter->fechad->dbformat='Y-m-d';
		//$filter->fechad->db_name='a.fecha';
//		$filter->fecha2d->rule    ='required';

		$filter->fecha2h = new dateonlyField("Hasta", "fecha2h",'d/m/Y');
		$filter->fecha2h->clause="where";
//		$filter->fecha2h->insertValue = date("Ymd");
		$filter->fecha2h->db_name="fecha2";
		$filter->fecha2h->operator="<=";
		$filter->fecha2h->group = "Fecha de Planilla";
		//$filter->fechaH->dbformat='Y-m-d';
		//$filter->fechah->db_name='a.fecha';
		
		

		$filter->cheque = new inputField("Transaccion", "cheque");
		$filter->cheque->db_name="cheque";
		$filter->cheque->size  =10;
		//$filter->cheque->db_name='a.cheque';

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		//$filter->cod_prov->db_name='a.cod_prov';
		
		$filter->benefi = new inputField("A Nombre de", "benefi");
		$filter->benefi->size  =40;
		//$filter->benefi->db_name='a.benefi';

		$filter->concilia = new dropdownField('Conciliado', 'concilia');
		$filter->concilia->option('' ,'Todos');
		$filter->concilia->option('S','Si');
		$filter->concilia->option('N','No');
		//$filter->concilia->db_name='a.concilia';
		
		$filter->vnoc = new dropdownField("Ver No Conciliados con Fecha anterior", "vnoc" );
		$filter->vnoc->clause  =" ";
		$filter->vnoc->db_name = " "; 
		$filter->vnoc->option('' ,'NO');
		$filter->vnoc->option('S','SI');

		$filter->des8 = new containerField("","<div style='background-color:#CCEEEE;padding:5px;'>INTRODUCE LOS DATOS DEL ESTADO DE CUENTA</div>");
		$filter->des8->group = "Datos de Estado de Cuenta";
		$filter->des8->clause=' ';

		$filter->codbanc = new inputField("Banco", 'codbanc');
		$filter->codbanc->db_name = 'codbanc';
		$filter->codbanc->rule    = 'required';
		$filter->codbanc-> size   = 3;
		$filter->codbanc->append($bBANC);
		$filter->codbanc->group = "Datos de Estado de Cuenta";
		$filter->codbanc->clause  ="where";
		$filter->codbanc->operator="=";
		//$filter->codbanc->db_name='a.codbanc';

		$y=date('Y');
		$filter->anio = new dropdownField('A&ntilde;o', 'anio');
		$filter->anio->option($y,$y);
		for($i=$y-2;$i<=$y+2;$i++)
		$filter->anio->option($i,$i);
		$filter->anio->rule   ='required';
		$filter->anio->clause ='';
		$filter->anio->in     ='mes';
		$filter->anio->size   =5;
		$filter->anio->style  ="width:100px;";
		$filter->anio->group  = "Datos de Estado de Cuenta";

		$filter->mes = new dropdownField('Fecha mes/a&ntilde;o', 'mes');
		$filter->mes->clause='';
		$filter->mes->rule  ='required';
		$filter->mes->style = 'width:50px';
		for($i=1;$i<=12;$i++){
			$mmes=str_pad($i,2,'0',STR_PAD_LEFT);
			$filter->mes->option($mmes,$mmes);
		}
		$filter->mes->group = "Datos de Estado de Cuenta";

		$filter->buttons('reset','search');
		$filter->build();

		$tipos = array(
		"tch"=>"T. Cheques",
		"tnd"=>"T. ND",
		"tdp"=>"T. Depositos",
		"tnc"=>"T. NC"
		);
		$salida='';
		foreach($tipos AS $key=>$value){
			$$key = new inputField($value, $key);
			$$key->status="create";
			$$key->size  =15;
			$$key->build();
			$salida.=$$key->label.$$key->output;
		}


		if($this->rapyd->uri->is_set('search') AND $filter->is_valid()){
			$q1       = $filter->db->_compile_select();
			$fechad   = $filter->fechad->newValue;
			$codbanc  = $filter->codbanc->newValue;
			$tipo_doc = $filter->tipo_doc->newValue;
			$codbanc  = $filter->codbanc->newValue;
			$cheque   = $filter->cheque->newValue;
			$cod_prov = $filter->cod_prov->newValue;
			$benefi   = $filter->benefi->newValue;
			$codbance = $this->db->escape($codbanc);
			$fechaddb = $fechad;
			$tipo_doce= $this->db->escape($tipo_doc);
			$chequee  = $this->db->escape($cheque  );
			$benefie  = $this->db->escape($benefi  );
			$vnoc     = $this->input->post('vnoc');
			$anio     = $filter->anio->newValue;
			$mes      = $filter->mes->newValue;
			
			$q  ="SELECT todo.tipo_doc='DP' tdp,MID(cheque,LENGTH(cheque)-3,4) ordena,todo.* FROM ( ";
			$q .=$q1;
			if($vnoc){
				$q2 = " SELECT * FROM mbanc WHERE codbanc=$codbance AND fecha<$fechaddb AND concilia='N' AND status NOT IN ('AN') ";
				if($tipo_doc)
				$q2.=" AND tipo_doc=$tipo_doce ";
				if($cheque  )
				$q2.=" AND tipo_doc=$chequee   ";
				if($benefi  )
				$q2.=" AND tipo_doc=$benefie   ";
				$q .=" UNION ALL ";
				$q .=$q2;
			}
			
			$q .=")todo ORDER BY ".$this->datasis->traevalor("CONCIORDENA","fecha,cheque");
			
			$qa=$this->db->query($q);
			$qa=$qa->result_array($qa);
			//'MID(status,2,1) ','2'
			
			function conci($conci,$codbanc,$tipo_op,$numero,$fecha,$id){
			//$numero='hoa';
			$numero=raencode($numero);
				$arr=array($codbanc,$tipo_op,$numero,$fecha,$id);
				//print_r($arr);
				//echo $id;
				if($conci!='S'){
					return form_checkbox($codbanc.$tipo_op.$numero.$id, serialize($arr));
				}else{
					return form_checkbox($codbanc.$tipo_op.$numero.$id, serialize($arr),TRUE);
				}
			}

			function caja($codbanc,$tipo_doc,$cheque,$monto){
				//$fech=explode('-',$conci);
				$arr=array($codbanc,$tipo_doc,$numero,$fecha);
				return form_input($codbanc.$tipo_op.$numero, serialize($arr),TRUE);
			}
			

			$monto = new inputField("Monto", "monto");
			$monto->grid_name='monto<#codbanc#><#tipo_doc#><#cheque#>';
			$monto->status   ='modify';
			$monto->size     =12;
			$monto->css_class='inputnum';
			
			$fconci=new dateField('', 'fconci','d/m/Y');
			$fconci->status='create';
			$fconci->size=10;
			$fconci->insertValue = date('Ymt',mktime( 0, 0, 0, $mes, 1, $anio ));
			$fconci->build();
			
			$fecha=$filter->anio->newValue.$filter->mes->newValue.days_in_month($filter->mes->newValue);
			$grid = new DataGrid('Efectos Conciliables'."</br>Fecha Conciliaci&oacute;n ".$fconci->output,$qa);
			$grid->use_function('conci');
			
			//$grid->order_by($ord);
			$grid->per_page = 10000;

			$grid->column_orderby('Ref.'                 ,'id'                                                                           ,'id'       );
			$grid->column_orderby('Tipo doc'             ,'tipo_doc'                                                                     ,'tipo_doc' );
			$grid->column_orderby('Fecha'                ,'<dbdate_to_human><#fecha#></dbdate_to_human>'                                 ,'fecha'    );
			$grid->column_orderby('Fecha Doc'            ,'<dbdate_to_human><#fecha2#></dbdate_to_human>'                                ,'fecha2'   );
			$grid->column_orderby('Transaccion'          ,"<wordwrap><#cheque#>|50|\n|true</wordwrap>"                                                                       ,'cheque'   ,'width="200px"');
			$grid->column_orderby('Monto'                ,$monto                                                                         ,'monto'    );
			$grid->column_orderby('Conciliado'           ,"<conci><#concilia#>|<#codbanc#>|<#tipo_doc#>|<#cheque#>|$fecha|<#id#></conci>",'concilia' );
			$grid->column_orderby('F conciliacion'       ,'<dbdate_to_human><#fconcilia#></dbdate_to_human>'                              ,'fcocilia' );
			$grid->column_orderby('Beneficiario'         ,'benefi'                                                                       ,'benefi'   );
			$grid->column_orderby('Concepto'             ,'observa'                                                                      ,'observa'  );

			$grid->build();
				
			$ggrid=$grid->output;
		}else{
			$ggrid='';
		}
		



		$data['filtro']  = $filter->output;
		$data['content'] = $salida.$ggrid;
		$data['title']   = 'Conciliaciones de Bancos';
		$data['script']  = '<script language="javascript" type="text/javascript">';
		$data['script'] .= '
		function suma(){
			tch =0;tdp=0;tnc=0;tnd=0;
			$(":checkbox").each(function(i,val){
				if(val.checked==true){
					monto=parseFloat($("#monto"+val.name).val());
					tipo =val.name.substr(2,2);
					if(tipo=="CH")tch=tch+monto;
					if(tipo=="DP")tdp=tdp+monto;
					if(tipo=="NC")tnc=tnc+monto;
					if(tipo=="ND")tnd=tnd+monto;
				}
			});
			$("#tch").val(Math.round(tch*100)/100);
			$("#tdp").val(Math.round(tdp*100)/100);
			$("#tnc").val(Math.round(tnc*100)/100);
			$("#tnd").val(Math.round(tnd*100)/100);
		}

		$(document).ready(function(){
			suma();
			$(":checkbox").change(function(){
				name=$(this).attr("name");
				ch=$(this).is(":checked");
				fc=$("#fconci").val();
				$.post("'.site_url('tesoreria/conci/cconci').'",{ data: $(this).val(),accion:ch,fconci:fc},
					function(data){
						if(data=="1"){
							suma();
							return true;
						}else{
							$("input[name=\'"+name+"\']").removeAttr("checked");
							alert(data);
							return false;
						}
					});
			});
		});';
		$data['script'] .= '</script>';
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$this->load->view('view_ventanas', $data);
	}

	function cconci(){
		$error     ='';
		$data      =$this->input->post('data');
		$accion    =$this->input->post('accion');
		$fconci    =human_to_dbdate($this->input->post('fconci'));
		
		if($data!==false ){
			$pk=unserialize($data);
			$pk[2]=radecode($pk[2]);
			if($accion=='true')
				$ddata = array('fconcilia' => $fconci,'concilia'=>'S');
			else
				$ddata = array('fconcilia' => null,'concilia'=>'N');

			$where  = ' codbanc             = '.$this->db->escape($pk[0]);
			$where .= ' AND tipo_doc        = '.$this->db->escape($pk[1]);
			$where .= ' AND cheque          = '.$this->db->escape($pk[2]);
			$where .= ' AND id              = '.$this->db->escape($pk[4]);
			//$where .= ' AND MID(status,2,1) = "2"';

			$fechas=$this->datasis->damerow("SELECT replace(fecha,'-','') fecha,fecha f FROM mbanc WHERE $where");
			$error  .=$this->chbanse($pk[0],$fconci);
//			$error  .=$this->chbanse($pk[0],$fechas['f']);
			if($fechas['fecha']>$pk[3] && $accion=='true')
			$error.='La fecha de Conciliacion no puede ser menor a la fecha del documento';
			
			$mSQL = $this->db->update_string('mbanc', $ddata, $where);
			
			if(empty($error)){
				logusu("conci",$mSQL);
				if($this->db->query($mSQL)){
					echo '1';
				}else{
					echo 'Hubo un error, comuniquese con soporte tecnico';
				}	
			}else{
				echo $error;
			}
		}
	}
}
