<?php
class Pasareca extends Controller {
	var $titp='Pasa de Recaudacion a Bancos';
	var $tits='Pasa de Recaudacion a Bancos';
	var $url ='tesoreria/pasareca/';
	function Pasareca(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(472,1);
	}
	
	function index(){
		redirect($this->url."filteredgrid");
	}
	
	function eliminar($id){
		$query="UPDATE r_mbanc SET id_mbancrel=null WHERE id_mbancrel=$id";
		$this->db->query($query);
		
		$query="DELETE FROM r_mbancrel WHERE id=$id";
		$this->db->query($query);
		
		redirect($this->url);
		
	}
	
	function filteredgrid(){
		//$this->datasis->modulo_id(71,1);
		$this->rapyd->load("datafilter","datagrid");
		$this->load->helper('form');
		//$this->rapyd->uri->keep_persistence();
		
		$mBANC=array(
				'tabla'   =>'banc',
				'columnas'=>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta'),
				'filtro'  =>array(
					'codbanc' =>'C&oacute;odigo',
					'banco'=>'Banco',
					'numcuent'=>'Cuenta'),
				'retornar'=>array(
					'codbanc'=>'codbanc' ),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$from="(
			SELECT 'r_mbancrel' tipo,id,codbanc,tipo_doc,cheque,monto,fecha,fechaing,concepto,id_mbanc 
			FROM r_mbancrel
			
			UNION ALL

			SELECT 'r_mbanc' tipo,a.id,a.codbanc,a.tipo_doc,a.cheque,a.monto,a.fecha,MAX(c.fecha) fechaing,GROUP_CONCAT(c.numero SEPARATOR ' ') concepto,id_mbanc 
			FROM r_mbanc a
			JOIN r_abonosit b ON a.abono=b.abono
			JOIN r_recibo c ON b.recibo=c.id
			WHERE a.tipo_doc='DP'
			GROUP BY a.id
			
		)t";

		$filter = new DataFilter("");
		$filter->db->select(array("t.id_mbanc","t.tipo","t.id","t.codbanc","t.tipo_doc","t.cheque","t.monto","t.fecha","t.fechaing","t.concepto","b.numcuent","b.banco"));
		$filter->db->from($from);
		$filter->db->join("banc b","b.codbanc=t.codbanc");
		//$filter->db->where("LENGTH(a.id_mbancrel )=0 OR a.id_mbancrel IS NULL");
		
		//$filter->db->orderby("a.cheque");
		//$filter->db->where("a.tipo =", "Trabajo");

		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		$filter->fecha->clause='where';
		$filter->fecha->operator='=';
		
		$filter->fechaing = new dateonlyField("Fecha Ingreso", "fechaing");
		$filter->fechaing->size=12;
		$filter->fechaing->clause='where';
		$filter->fechaing->operator='=';
		
		$filter->codbanc = new inputField("Banco", 'codbanc');
		$filter->codbanc->size = 6;
		$filter->codbanc->append($bBANC);
		$filter->codbanc->db_name="a.codbanc";
		
		$filter->tipo_doc = new dropdownField("Tipo Documento","tipo_doc");
		$filter->tipo_doc->db_name   = 'tipo_doc';
		$filter->tipo_doc->style     ="width:130px;";
		$filter->tipo_doc->option(""  ,""                     );
		$filter->tipo_doc->option("EF","Efectivo"             );
		$filter->tipo_doc->option("DP","Deposito"             );
		$filter->tipo_doc->option("DB","Tarjeta D&eacute;bito");
		$filter->tipo_doc->option("CR","Tarjeta Credito"      );
		$filter->tipo_doc->option("DF","Diferencia"           );
		
		$filter->tipo = new dropdownField("Origen","tipo");
		$filter->tipo->style     ="width:130px;";
		$filter->tipo->option(""  ,""                     );
		$filter->tipo->option("r_mbancrel","Relaciones"   );
		$filter->tipo->option("r_mbanc"   ,"Cobranzas"    );

		$filter->buttons("reset","search");
		$filter->build();
		
		$total = new inputField("Total", "total");
		$total->status="create";
		$total->size  =15;
		$total->build();
		$salida=$total->label.$total->output;
		
		
		$grid = new DataGrid("");

		function sel($numero,$tipo){
			return form_checkbox('data[]', $tipo.'_._'.$numero);
		}
		
		$codbanc = new inputField("Cod Banco", "codbanc");
		$codbanc->grid_name='codbanc_<#tipo#>_<#id#>';
		$codbanc->status   ='modify';
		$codbanc->size     =12;
		$codbanc->type     ='inputhidden';
		
		$fecha = new inputField("Fecha", "fecha");
		$fecha->grid_name='fecha_<#tipo#>_<#id#>';
		$fecha->status   ='modify';
		$fecha->size     =12;
		$fecha->type     ='inputhidden';
		
		$fechaing = new inputField("Fecha Ingreso", "fechaing");
		$fechaing->grid_name='fechaing_<#tipo#>_<#id#>';
		$fechaing->status   ='modify';
		$fechaing->size     =12;
		$fechaing->type     ='inputhidden';
		
		$tipo_doc = new inputField("Tipo Documento","tipo_doc");
		$tipo_doc->grid_name='tipo_doc_<#tipo#>_<#id#>';
		$tipo_doc->status   ='modify';
		$tipo_doc->size     =12;
		$tipo_doc->type     ='inputhidden';
		
		$cheque = new inputField("Transaccion", "cheque");
		$cheque->grid_name='cheque_<#tipo#>_<#id#>';
		$cheque->status   ='modify';
		$cheque->size     =12;
		
		$monto = new inputField("Monto", "monto");
		$monto->grid_name='monto_<#tipo#>_<#id#>';
		$monto->status   ='modify';
		$monto->size     =12;
		$monto->css_class='inputnum';
		$monto->readonly=true;
		
		$concepto = new textAreaField("Concepto", "concepto");
		$concepto->grid_name='concepto_<#tipo#>_<#id#>';
		$concepto->status   ='modify';
		$concepto->rows     =1;
		$concepto->cols     =15;

		$data = array(
			'name'        => 'todo',
			'id'          => 'todo',
			//'value'       => 'accept',
			'checked'     => FALSE,
			'style'       => 'margin:10px',
			);

		$salida1=form_checkbox($data);


		$atts3 = array(
		'width'     =>'640',
		'height'    =>'480',
		'scrollbars'=>'yes',
		'status'    =>'yes',
		'resizable' =>'yes',
		'screenx'   =>'5',
		'screeny'   =>'5'   );
		$uri = anchor_popup('tesoreria/mbanc/dataedit/show/<#id_mbanc#>','<#id_mbanc#>',$atts3);

		$grid = new DataGrid($salida);
		$grid->order_by("cheque","asc");

		$grid->per_page = 100;
		$grid->use_function('substr','str_pad','sel','nformat');

		$grid->column($salida1              ,"<sel><#id#>|<#tipo#></sel>");
		$grid->column_orderby("Cod. Banco"      ,$codbanc                                         ,"codbanc"    ,"align='left'  ");
		$grid->column_orderby("Banco"           ,"banco"                                          ,"banco"      ,"align='left'  ");
		$grid->column_orderby("Cuenta"          ,"numcuent"                                       ,"numcuent"   ,"align='left'  ");
		$grid->column_orderby("Transaccion"     ,$cheque                                          ,"cheque"     ,"align='left'  ");
		$grid->column_orderby("Fecha"           ,$fecha                                           ,"fecha"      ,"align='center'");
		$grid->column_orderby("Fecha Ingreso"   ,$fechaing                                        ,"fechaing"   ,"align='center'");
		$grid->column_orderby("Tipo Doc"        ,$tipo_doc                                        ,"tipo_doc"   ,"align='center'");
		$grid->column_orderby("Monto"           ,$monto                                           ,"monto"      ,"align='right' ");
		$grid->column_orderby("Concepto"        ,$concepto                                        ,"concepto"   ,"align='left'  ");
		$grid->column_orderby("Mov Bancario"    ,$uri                                             ,"id_mbanc"   ,"align='left'  ");

		$grid->build();
		//echo $grid->db->last_query();

		$salida =form_open($this->url.'guarda');
		$salida.=$grid->output;
		$salida.=form_submit('Pasar  Datos', 'Pasar  Datos');
		$salida.=form_close();

		$data['filtro']  = $filter->output;
		$data['content'] = $salida;
		$data['script']  = script("jquery.js")."\n";
		$data['script']  = '<script language="javascript" type="text/javascript">';
		$data['script'] .= '
		function suma(){
			t=0;
			$(":checkbox").each(function(i,val){
				name =val.name;
				if(name.substring(0,4)=="data"){
				
					if(val.checked==true){
						monto=parseFloat($("#monto_"+val.value).val());
						tipo =val.name.substr(2,2);
						t=t+monto;
					}
				}
			});
			$("#total").val(Math.round(t*100)/100);
		}

		$(document).ready(function(){
			suma();
			$("#todo").change(function(){
				
				console.log("aaa");
				var ch=$(this).is(":checked");
				$(":checkbox").each(function(i,val){
					if(ch==true){
						val.checked=true;
					}else{
						val.checked=false;
					}
					
				});
			});
			
			
			$(":checkbox").change(function(){
				suma();
			});
		});';
		$data['script'] .= '</script>';
		$data['title']   = "Seleccione las Movimientos Bancarios ";
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$this->load->view('view_ventanas', $data);
	}

	function guarda(){
		$data    =$this->input->post('data');
		$bcta_r_mbanc    = $this->datasis->traevalor('BCTA_R_MBANC');
		$bcta_r_mbancrel = $this->datasis->traevalor('BCTA_R_MBANCREL');
		
		foreach($data as $v){
			$val     =explode('_._',$v);
			$concepto=$this->input->post('concepto_'.$val[0].'_'.$val[1]);
			$cheque  =$this->input->post('cheque_'.$val[0].'_'.$val[1]);
			$tipo_doc=$this->input->post('tipo_doc_'.$val[0].'_'.$val[1]);
			$fecha   =$this->input->post('fecha_'.$val[0].'_'.$val[1]);
			$fechaing=$this->input->post('fechaing_'.$val[0].'_'.$val[1]);
			$codbanc =$this->input->post('codbanc_'.$val[0].'_'.$val[1]);
			$monto   =$this->input->post('monto_'.$val[0].'_'.$val[1]);
			
			$codbance = $this->db->escape($codbanc);
			$banco = $this->datasis->dameval("SELECT titular FROM banc WHERE codbanc=$codbance");
			
			if($val[0]=='r_mbancrel'){
				$bcta = $bcta_r_mbancrel;
			}elseif($val[0]=='r_mbanc'){
				$bcta = $bcta_r_mbanc;
			}
			$data = array(
               'id'           => ''         ,         
               'codbanc'      =>$codbanc    ,
               'tipo_doc'     =>$tipo_doc   ,
               'cheque'       =>$cheque     ,
               'fecha'        =>str_replace('-','',$fecha   )   ,
               'fecha2'       =>str_replace('-','',$fechaing)   ,
               'monto'        =>$monto      ,
               'observa'      =>$concepto   ,
               'status'       =>'J2'        ,
               'bcta'         =>$bcta       ,
               'benefi'       =>$banco      
            );

			$this->db->insert('mbanc', $data); 
			$id = $this->db->insert_id();
			if($val[0]=='r_mbancrel'){
				$query="UPDATE r_mbancrel SET id_mbanc=$id WHERE id=".$val[1];
				$this->db->query($query);
			}elseif($val[0]=='r_mbanc'){
				$query="UPDATE r_mbanc SET id_mbanc=$id WHERE id=".$val[1];
				$this->db->query($query);
			}
		}
		$query="call sp_recalculo()";
		$this->db->query($query);

		redirect($this->url.'filteredgrid');
	}

	function _valida($do){
		$error = '';

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function _pre_delete($do){
		$error = '';
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}
	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}
	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		$query="CREATE TABLE `r_mbancrel` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `codbanc` varchar(10) NOT NULL,
		  `tipo_doc` char(2) NOT NULL,
		  `cheque` text NOT NULL,
		  `monto` decimal(19,2) NOT NULL,
		  `total` decimal(19,2) NOT NULL,
		  `fecha` date NOT NULL,
		  `fechaing` date NOT NULL,
		  `concepto` text NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";
		$this->db->simple_query($query);
	}
}
?>
