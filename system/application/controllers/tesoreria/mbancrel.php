<?php
require_once(BASEPATH.'application/controllers/presupuesto/common.php');
class mbancrel extends Common {

	var $genesal=true;
	var $titp='Relacionar Traslados';
	var $tits='Relacionar Traslados';
	var $url ='tesoreria/mbancrel/';
	var $on_save_redirect=TRUE;

	function mbancre(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(340,1);
	}

	function index(){
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){
		
		$this->rapyd->load("datafilter","datagrid");
		//$this->rapyd->uri->keep_persistence();

		$mBCTA=array(
                    'tabla'   =>'bcta',
                    'columnas'=>array(
                            'codigo'       =>'C&oacute;odigo',
                            'denominacion' =>'Denominacion',
                            'cuenta'       =>'Cuenta'),
                    'filtro'  =>array(
                            'codigo'       =>'C&oacute;odigo',
                            'denominacion' =>'Denominacion',
                            'cuenta'       =>'Cuenta'),
                    'retornar'=>array(
                            'codigo'       =>'bcta'),
                    'titulo'  =>'Buscar Otros Ingresos'
                    );

		$bBCTA=$this->datasis->p_modbus($mBCTA,"bcta");

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
					'saldo'=>'Saldo'),
				'retornar'=>array(
					'codbanc'=>'codbanc' ),
				'where'=>'activo = "S"',
				'titulo'  =>'Buscar Bancos'
				);

		$bBANC=$this->datasis->p_modbus($mBANC,"banc");

		$filter = new DataFilter("");

		$filter->db->select(array("bcta","IF(LENGTH(observa)>0,observa,'.') observa","deid","paid","a.multiple","tipo_doc","a.cheque cheque","a.codbanc","a.id id","a.fecha fecha","a.tipo tipo","a.status status","a.cod_prov cod_prov","a.benefi benefi","a.monto monto" ,"IF(paid >0,paid,IF(LENGTH(pcodbanc)>0,'P',IF(deid>0,deid,''))) tras"));
		$filter->db->from("mbanc a");
		$filter->db->join("banc b","b.codbanc=a.codbanc");
		//$filter->db->where("a.status != ", "E1");
		//$filter->db->where("a.status != ", "E2");
		//$filter->db->where("a.status != ", "E3");
		
		$filter->id = new inputField("Referencia", "id");
		$filter->id->db_name="a.id";
		$filter->id->size  =10;
		$filter->id->clause  ='where';
		$filter->id->operator='=';

		$filter->cheque = new inputField("Transaccion", "cheque");
		$filter->cheque->db_name="a.cheque";
		$filter->cheque->size  =10;

		$filter->fechad = new dateonlyField("Desde", "fechad",'d/m/Y');
		$filter->fechad->clause ="where";
		$filter->fechad->db_name ="fecha";
		$filter->fechad->operator=">=";
		//$filter->fechad->insertValue = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-30, date("Y")));
		$filter->fechad->group = "Fecha";
		$filter->fechad->dbformat='Y-m-d';
		
		$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechah->clause="where";
		//$filter->fechah->insertValue = date("Y-m-d");
		$filter->fechah->db_name="fecha";
		$filter->fechah->operator="<=";
		$filter->fechah->group = "Fecha";
		$filter->fechah->dbformat='Y-m-d';

		$filter->bcta = new inputField("Motivo Movimiento", 'bcta');
		$filter->bcta->size = 6;
		$filter->bcta->append($bBCTA);

		$filter->cod_prov = new inputField("Beneficiario", 'cod_prov');
		$filter->cod_prov->size = 6;
		$filter->cod_prov->append($bSPRV);
		
		$filter->tipocta = new dropdownField("Tipo de Cuenta", "tipocta");
		$filter->tipocta->style ="width:100px;";
		$filter->tipocta->option("","");
		$mf=$this->datasis->puede(333);
		$mo=$this->datasis->puede(334);
		if(!($mf && $mo))
		$filter->tipocta->options(array("K"=>"Caja","C"=>"Corriente","A" =>"Ahorros","P"=>"Plazo Fijo" ));
		elseif($mf && $mo){
		    $filter->tipocta->option("F","FideComiso");
		    $filter->tipocta->options(array("K"=>"Caja","C"=>"Corriente","A" =>"Ahorros","P"=>"Plazo Fijo" ));
		}elseif($mf){
		    $filter->db->where("tipocta","F");
		    $filter->tipocta->option("F","FideComiso");    
		}elseif($mo){
		    $filter->db->where("tipocta <>","F");
		    $filter->tipocta->options(array("K"=>"Caja","C"=>"Corriente","A" =>"Ahorros","P"=>"Plazo Fijo" ));   
		}

		$filter->codbanc = new inputField("Banco", 'codbanc');
		$filter->codbanc->size = 6;
		$filter->codbanc->append($bBANC);
		$filter->codbanc->db_name="a.codbanc";
		$filter->codbanc->clause  ='where';
		$filter->codbanc->operator='=';

		$filter->monto = new inputField("Monto", 'monto');
		$filter->monto->size = 6;
		$filter->monto->clause  ='where';
		$filter->monto->operator='=';

                $filter->benefi = new inputField("A nombre de", 'benefi');
		$filter->benefi->size = 20;

		$filter->tipo_doc = new dropdownField("Tipo Doc","tipo_doc");
		$filter->tipo_doc->option("","");
		$filter->tipo_doc->option("ND","Nota de Debito");
		$filter->tipo_doc->option("NC","Nota de Credito");
		$filter->tipo_doc->option("CH","Cheque");
		$filter->tipo_doc->option("DP","Deposito");

		$filter->status = new dropdownField("Estado","status");
		$filter->status->option("","");
		$filter->status->option("J1","Sin Ejecutar");
		$filter->status->option("J2","Ejecutado");
		$filter->status->option("A" ,"Anulado");
		$filter->status->style="width:150px";

		$filter->deid = new inputField("De Ref.", "deid");
		$filter->deid->size  =10;
		$filter->deid->clause  ='where';
		$filter->deid->operator='=';

		$filter->paid = new inputField("Para Ref.", "paid");
		$filter->paid->size  =10;
		$filter->paid->clause  ='where';
		$filter->paid->operator='=';

		$filter->buttons("reset","search");

		$filter->build();
		
		function sta($status){
			switch($status){
				case "J1":return "Sin Ejecutar";break;
				case "J2":return "Ejecutado";break;
				case "A":return "Anulado";break;
				case "AN":return "Anulado";break;
				case "NC":return "NC Anulacion de cheque";break;
				case "A2":return "Cheque Reverso";break;
			}
		}
		
		function modi($id,$tipo_doc,$deid){
			
			if(in_array($tipo_doc,array("CH","ND"))){
				return $deid;
			}else{
				
				$data = array(
			      'name'        => "M_".$id,
			      'id'          => "M_".$id,
			      'value'       => $deid,
			      'maxlength'   => '8',
			      'size'        => '5'
			    );
			return form_input($data);
			}
			
		}
		
		function modi2($id,$tipo_doc,$paid){
			if(in_array($tipo_doc,array("DP","NC"))){
				return $paid;
			}else{
			
				$data = array(
				      'name'        => "M2_".$id,
				      'id'          => "M2_".$id,
				      'value'       => $paid,
				      'maxlength'   => '8',
				      'size'        => '5'
				    );
				return form_input($data);
			}
		}

		$grid = new DataGrid("");
		$grid->order_by('id','desc');
		$grid->per_page = 100;
		$grid->use_function('substr','str_pad');
		$grid->use_function('sta','modi','modi2');

		$grid->column_orderby("Ref."             ,"id"                                            ,"id"                                    );
		$grid->column_orderby("Transaccion"      ,"<wordwrap><#cheque#>|50|\n|true</wordwrap>"    ,"cheque"                                );
		$grid->column_orderby("Tipo"             ,"tipo_doc"                                      ,"tipo_doc"                              );
		$grid->column_orderby("Motivo Mov"       ,"bcta"                                          ,"bcta"                                  );
		$grid->column_orderby("Fecha"            ,"<dbdate_to_human><#fecha#></dbdate_to_human>"  ,"fecha"          ,"align='center'"      );
		$grid->column_orderby("Banco"            ,"codbanc"                                       ,"fecha"          ,"align='center'"      );
		$grid->column_orderby("A nombre de"      ,"benefi"                                        ,"benefi"         ,"align='left'  "      );
		$grid->column_orderby("Concepto"         ,"<wordwrap><#observa#>|40|\n|true</wordwrap>"   ,"observa"                               );
		$grid->column_orderby("Monto"            ,"<nformat><#monto#>|2|,|.</nformat>","monto"    ,"align='right'"                         );
		$grid->column_orderby("Para Ref."        ,"<modi2><#id#>|<#tipo_doc#>|<#paid#></modi2>"                ,"deid"           ,"align='center'"      );
		$grid->column_orderby("De Ref."          ,"<modi><#id#>|<#tipo_doc#>|<#deid#></modi>"                  ,"paid"           ,"align='center'"      );
		
		$grid->build();
//		echo $grid->db->last_query();

		//$data['content'] = $filter->output.$grid->output;
		$data['script']  = script("jquery.js")."\n";
		$data['script'] .= '<script language="javascript" type="text/javascript">';
		$data['script'] .= '
		
		$(document).ready(function(){
			$("input[name^=\"M\"]").change(function(){
				name=$(this).attr("name");
				valor=$(this).val();
				
				$.post("'.site_url($this->url.'/modifica').'",{data: valor,nombre:name},
				function(data){
					if(data!="1"){
						$("#"+name).val("");
						alert(data);
						return false;
					}else{
						
					}
				});
			});
		});';
		$data['script'] .= '</script>';
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['title']   = "$this->titp";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function modifica(){
		$error='';
		$data   =$this->input->post('data'  );
		$nombre =$this->input->post('nombre');
		$pk     =explode('_',$nombre);
		
			if($pk[0]=='M2'){
				$ddata  =array('paid'=>$data);
				$campo  ='paid';
				$campo2 ='deid';
			}elseif($pk[0]=='M'){
				$ddata  =array('deid'=>$data);
				$campo  ='deid';
				$campo2 ='paid';
			}
			
			$where  = ' id  = '.$pk[1];
			$mSQL = $this->db->update_string('mbanc', $ddata, $where);
		
		if($data!==false && !empty($data)){	
			$cant = $this->datasis->dameval("SELECT COUNT(*) FROM mbanc WHERE status NOT IN ('J1','E1','AN') AND  id=".$data);
			if(!($cant>0))
			$error.="\n Error. El Movimiento $data no es valido";
			
			$cant = $this->datasis->dameval("SELECT COUNT(*) FROM mbanc WHERE status NOT IN ('J1','E1','AN') AND $campo =$data");
			if($cant>0)
			$error.="\n Error. El Movimiento $data ya fue utilizado";
			
			$cant = $this->datasis->dameval("SELECT SUM(IF(tipo_doc IN ('CH','ND'),-1*monto,monto)) FROM mbanc WHERE id IN ($data,".$pk[1].")");
			if($cant<>0)
			$error.="\n Error. Los movimientos no pueden ser relacionados. Puede Ser que los montos no sean iguales o que este intentando relacionar dos movimientos del mismo tipo";
			
			if(empty($error)){
				$this->db->query("UPDATE mbanc SET $campo2=".$pk[1]." WHERE id=$data");
				
				if($this->db->simple_query($mSQL))
					echo '1';
				else
					echo 'Hubo un error, comuniquese con soporte tecnico';
			}else{
				echo $error;
			}
		}else{
			$data2 = $this->datasis->dameval("SELECT $campo FROM mbanc WHERE".$where);
			$this->db->query("UPDATE mbanc SET $campo2='' WHERE id=$data2");
			if($this->db->simple_query($mSQL))
				echo '1';
		}
	}
}
