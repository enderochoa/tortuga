<?php
//personal
class Pers extends Controller {
		var $url="nomina/pers/";

        function pers(){
                parent::Controller(); 
                $this->load->library("rapyd");
        }

        function index(){
                $this->datasis->modulo_id(52,1);
                redirect("nomina/pers/filteredgrid");
        }

        function filteredgrid(){
				$this->load->helper('form');
                $this->rapyd->load('dataobject',"datafilter","datagrid");
                $this->rapyd->uri->keep_persistence();
                $this->datasis->modulo_id(52,1);
                
                 $mNOCO=array(
                'tabla'   =>'noco',
                'columnas'=>array(
                      'codigo'  =>'C&oacute;digo de Sucursal',
                      'nombre'  =>'Nombre'),
                'filtro'  =>array('codigo'=>'C&oacute;digo','nombre'  =>'Nombre'),
                'retornar'=>array('codigo'=>'contrato'),
                'titulo'  =>'Buscar Contratos',
                'where'   =>'tipo <> "O"');
                
                $bNOCO=$this->datasis->modbus($mNOCO);

                $script ='
                $(function() {
                        
                });
                ';
                
				$filter = new DataFilter("", 'pers');
				$filter->db->select(array("noco.nombre noconombre","pers.codigo","pers.nacional","pers.cedula","pers.nombre","pers.nombre2","pers.apellido","pers.apellido2","pers.direc1","pers.direc2","pers.direc3","pers.telefono","pers.nacimi","pers.sso","pers.sexo","pers.civil","pers.depto","pers.cargo","pers.sueldo","pers.ingreso","pers.retiro","pers.tipo","pers.contrato","pers.dialib","pers.status","pers.banco","pers.cutipo","pers.cuenta","pers.vari1","pers.vari2","pers.vari3","pers.vari4","pers.vari5","pers.vari6","pers.vari7","pers.vari8","pers.uaumento","pers.formato","pers.dialab","pers.xdialab","pers.sucursal","pers.divi","pers.carnet","pers.enlace","pers.estampa","pers.usuario","pers.hora","pers.transac","pers.cuentab","pers.profes","pers.niveled","pers.vence","pers.horario","pers.rif","pers.codigoadm","pers.fondo","pers.codigopres","pers.email","pers.tipoe","pers.observa","pers.sueldo","divi.descrip division"));
				$filter->db->join("noco","pers.contrato=noco.codigo","LEFT");
				$filter->db->join("divi","divi.division=pers.divi","LEFT");

                $filter->script($script, "create");
                $filter->script($script, "modify");
                
                $filter->codigo = new inputField("Codigo", "codigo");
                $filter->codigo->size=30;
                $filter->codigo->db_name ='pers.codigo';
                
                $filter->cedula = new inputField("C&eacute;dula", "cedula");
                $filter->cedula->size=10;
                $filter->cedula->css_class='inputnum';
                $filter->cedula->db_name ='pers.cedula';
                
                $filter->nombre = new inputField("Nombre", "nombre");
                $filter->nombre->size=30;
                $filter->nombre->db_name ='pers.nombre';
                
                $filter->apellido = new inputField("Apellido", "apellido");
                $filter->apellido->size=30;
                $filter->apellido->db_name ='pers.apellido';
                
                $filter->fondo = new inputField("Fondo", "fondo");
                $filter->fondo->db_name ='pers.fondo';
                
                $filter->divi = new dropdownField("Divisi&oacute;n", "divi");
                $filter->divi->style ="width:250px;";
                $filter->divi->option("","");
                $filter->divi->options("SELECT division,CONCAT_WS(' ',division,descrip)des FROM divi ORDER BY division");
                //$filter->divi->group = "Relaci&oacute;n Laboral";
                
                $filter->codigoadm = new inputField("Est. Administrativa", "codigoadm");
                $filter->codigoadm->db_name ='pers.codigoadm';
                
                $filter->codigopres = new inputField("Codigo Partida", "codigopres");
                $filter->codigopres->db_name ='pers.codigopres';
                
				$filter->contrato = new inputField("Contrato", "contrato");
				$filter->contrato->size =4;
				$filter->contrato->maxlength=5;
				$filter->contrato->append($bNOCO);
				$filter->contrato->clause='where';
				$filter->contrato->operator='=';
				
				$filter->sueldo = new inputField("Sueldo", "sueldo");
                $filter->sueldo->db_name ='pers.sueldo';
                
                $filter->status = new dropdownField("Estatus", "status");
                $filter->status->option("","");
                $filter->status->options(array("A"=> "Activo","V"=>"Vacaciones","R"=>"Retirado"));
                $filter->status->group = "Relaci&oacute;n Laboral";
                $filter->status->style = "width:100px;";
				
                //$filter->script->css_class='inputnum';
                
                $filter->buttons("reset","search");
                $filter->build();

                $uri = anchor('nomina/pers/dataedit/show/<raencode><#codigo#></raencode>','<#codigo#>');

                $grid = new DataGrid("");
                $grid->order_by("pers.codigo","asc");
                $grid->per_page = 20;

                $grid->column_orderby("C&oacute;digo",$uri      ,"pers.codigo");
                $grid->column_orderby("C&eacute;dula","cedula"  ,"cedula");
                $grid->column_orderby("Nombre"       ,"nombre"  ,"nombre"  ,"align='left'NOWRAP");
                $grid->column_orderby("Apellidos"    ,"apellido","apellido","align='left'NOWRAP");
                $grid->column_orderby("Division"     ,"division","divi.descrip","");
                $grid->column_orderby("Contrato"     ,"noconombre","noconombre","");
                $grid->column_orderby("Sueldo"       ,"sueldo"    ,"sueldo","");
                $grid->add("nomina/pers/dataedit/create");
                $grid->build();
                
                $sql=$grid->db->last_query();
                $cantidad = $grid->recordCount;
                $hidden = array('sql' => $sql,'cantidad'=>$cantidad);
				$form   = form_open($this->url."masivo",'',$hidden);  
				$form  .=form_submit('modificar','Modificar Masivo');
                
                //$data['content'] = $filter->output.$grid->output;
                $data['filtro']  = $filter->output;
                $data['content'] = $grid->output.$form;
                $data['script'] = script("jquery.js")."\n";
                $data['title']   = "Personal";
                $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
                $this->load->view('view_ventanas', $data);      
        }
        
        function masivo(){
			$this->rapyd->uri->keep_persistence();
				$sql      = $this->input->post('sql');
				$cantidad = $this->input->post('cantidad');
				
				$msj="</br>Usted esta modificando $cantidad Registros";
				
				$this->rapyd->load("dataform");
                                
                $edit = new DataForm($this->url."masivo/process");
                
                //$edit->post_process('insert','_post_insert');
                //$edit->post_process('update','_post_update');
                //$edit->post_process('delete','_post_delete');
                                          
                $mNOCO=array(
                'tabla'   =>'noco',
                'columnas'=>array(
                      'codigo'  =>'C&oacute;digo de Sucursal',
                      'nombre'  =>'Nombre'),
                'filtro'  =>array('codigo'=>'C&oacute;digo','nombre'  =>'Nombre'),
                'retornar'=>array('codigo'=>'contrato'),
                'titulo'  =>'Buscar Contratos',
                'where'   =>'tipo <> "O"');
                
                $bNOCO=$this->datasis->modbus($mNOCO);
                
                $edit->sql = new hiddenField("", "sql");
                $edit->sql->value =$sql;
                
                $edit->cantidad = new hiddenField("", "cantidad");
                $edit->cantidad->value =$cantidad;

                $edit->contrato = new inputField("Contrato", "contrato");
                $edit->contrato->size =4;
                $edit->contrato->maxlength=5;
                $edit->contrato->readonly =true;
                $edit->contrato->group = "Relaci&oacute;n Laboral";
                $edit->contrato->append($bNOCO);
                
                $edit->m_contrato= new checkboxField('','m_contrato','Y','N');
				$edit->m_contrato->insertValue='N';
				$edit->m_contrato->in ='contrato';
				$edit->m_contrato->append("Modificar");
                
                $edit->codigoadm = new dropdownField("Estructura Administrativa","codigoadm");
                $edit->codigoadm->option("","");
                $edit->codigoadm->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
                $edit->codigoadm->group="Relaci&oacute;n Laboral";
                
                $edit->m_codigoadm= new checkboxField('','m_codigoadm','Y','N');
				$edit->m_codigoadm->insertValue='N';
				$edit->m_codigoadm->in ='codigoadm';
				$edit->m_codigoadm->append("Modificar");
				
				$edit->codigopres = new inputField("Partida", "codigopres");
				$edit->codigopres->size=20;
				$edit->codigopres->group = "Relaci&oacute;n Laboral";
				
				$edit->m_codigopres= new checkboxField('','m_codigopres','Y','N');
				$edit->m_codigopres->insertValue='N';
				$edit->m_codigopres->in ='codigopres';
				$edit->m_codigopres->append("Modificar");
				$edit->m_codigopres->group = "Relaci&oacute;n Laboral";
                
                $edit->fondo = new dropdownField("Fondo", "fondo");
                $edit->fondo->style="width:300px;";
                $edit->fondo->group="Relaci&oacute;n Laboral";
                $edit->fondo->option("","");
                $edit->fondo->options("SELECT tipo,tipo n FROM presupuesto GROUP BY tipo");
                
                $edit->m_fondo= new checkboxField('','m_fondo','Y','N');
				$edit->m_fondo->insertValue='N';
				$edit->m_fondo->in ='fondo';
				$edit->m_fondo->append("Modificar");
                
                $edit->divi = new dropdownField("Divisi&oacute;n", "divi");
                $edit->divi->style ="width:250px;";
                $edit->divi->option("","");
                $edit->divi->options("SELECT division,CONCAT_WS(' ',division,descrip)des FROM divi ORDER BY division");
                $edit->divi->group = "Relaci&oacute;n Laboral";
                
                $edit->m_divi= new checkboxField('','m_divi','Y','N');
				$edit->m_divi->insertValue='N';
				$edit->m_divi->in ='divi';
				$edit->m_divi->append("Modificar");
                
                $edit->status = new dropdownField("Estatus", "status");
                $edit->status->option("","");
                $edit->status->options(array("A"=> "Activo","V"=>"Vacaciones","R"=>"Retirado"));
                $edit->status->group = "Relaci&oacute;n Laboral";
                $edit->status->style = "width:100px;";
                
                $edit->m_status= new checkboxField('','m_status','Y','N');
				$edit->m_status->insertValue='N';
				$edit->m_status->in ='status';
				$edit->m_status->append("Modificar");
				
				$edit->tipemp = new dropdownField("Condicion", "tipemp");
                $edit->tipemp->style = "width:100px;";
                $edit->tipemp->option(""     ,""                      );
                $edit->tipemp->option("F"    ,"Fijo"                  );
                $edit->tipemp->option("C"    ,"Contratado"            );
                $edit->tipemp->option("J"    ,"Jubilado"              );
                $edit->tipemp->option("P"    ,"Pensionado"            );
                $edit->tipemp->option("A"    ,"Alto Nivel y Direccion");
                $edit->tipemp->option("E"    ,"Eleccion Popular"      );
                $edit->tipemp->group = "Relaci&oacute;n Laboral";
                
                $edit->m_tipemp= new checkboxField('','m_tipemp','Y','N');
				$edit->m_tipemp->insertValue='N';
				$edit->m_tipemp->in ='tipemp';
				$edit->m_tipemp->append("Modificar");
				$edit->m_tipemp->group = "Relaci&oacute;n Laboral";
                
                $edit->vari1 = new inputField($this->datasis->traevalor("VARI1")."XVARI1", "vari1");
                $edit->vari1->group = "Variables";
                $edit->vari1->size =16;
                $edit->vari1->maxlength=14;
                $edit->vari1->rule="trim";
                $edit->vari1->css_class='inputnum';
                
                $edit->m_vari1= new checkboxField('','m_vari1','Y','N');
				$edit->m_vari1->insertValue='N';
				$edit->m_vari1->in ='vari1';
				$edit->m_vari1->append("Modificar");
                
                $edit->vari2 = new inputField($this->datasis->traevalor("VARI2")."XVARI2", "vari2");
                $edit->vari2->group = "Variables";
                $edit->vari2->size =16;
                $edit->vari2->maxlength=14;
                $edit->vari2->rule="trim";
                $edit->vari2->css_class='inputnum';
                
                $edit->m_vari2= new checkboxField('','m_vari2','Y','N');
				$edit->m_vari2->insertValue='N';
				$edit->m_vari2->in ='vari2';
				$edit->m_vari2->append("Modificar");
                
                $edit->vari3 = new inputField($this->datasis->traevalor("VARI3")."XVARI3", "vari3");
                $edit->vari3->group = "Variables";
                $edit->vari3->size =16;
                $edit->vari3->maxlength=14;
                $edit->vari3->rule="trim";
                $edit->vari3->css_class='inputnum';
                
                $edit->m_vari3= new checkboxField('','m_vari3','Y','N');
				$edit->m_vari3->insertValue='N';
				$edit->m_vari3->in ='vari3';
				$edit->m_vari3->append("Modificar");
                        
                $edit->vari4 = new inputField($this->datasis->traevalor("VARI4")."XVARI4", "vari4");
                $edit->vari4->group = "Variables";
                $edit->vari4->size =12;
                $edit->vari4->maxlength=11;
                $edit->vari4->rule="trim";
                $edit->vari4->css_class='inputnum';
                
                $edit->m_vari4= new checkboxField('','m_vari4','Y','N');
				$edit->m_vari4->insertValue='N';
				$edit->m_vari4->in ='vari4';
				$edit->m_vari4->append("Modificar");
                      
                $edit->vari5 = new inputField($this->datasis->traevalor("VARI5")."XVARI5", "vari5");
                $edit->vari5->group = "Variables";
                $edit->vari5->size =12;
                $edit->vari5->maxlength=12;
                $edit->vari5->rule="trim";
                
                $edit->m_vari5= new checkboxField('','m_vari5','Y','N');
				$edit->m_vari5->insertValue='N';
				$edit->m_vari5->in ='vari5';
				$edit->m_vari5->append("Modificar");
                
                $edit->vari6 = new inputField($this->datasis->traevalor("VARI6")."XVARI6", "vari6");
                $edit->vari6->group = "Variables";
                $edit->vari6->size =16;
                $edit->vari6->maxlength=14;
                $edit->vari6->rule="trim";
                $edit->vari6->css_class='inputnum';
                
                $edit->m_vari6= new checkboxField('','m_vari6','Y','N');
				$edit->m_vari6->insertValue='N';
				$edit->m_vari6->in ='vari6';
				$edit->m_vari6->append("Modificar");
                
                $edit->vari7 = new inputField($this->datasis->traevalor("VARI7")."XVARI7", "vari7");
                $edit->vari7->group = "Variables";
                $edit->vari7->size =16;
                $edit->vari7->maxlength=14;
                $edit->vari7->rule="trim";
                
                $edit->m_vari7= new checkboxField('','m_vari7','Y','N');
				$edit->m_vari7->insertValue='N';
				$edit->m_vari7->in ='vari7';
				$edit->m_vari7->append("Modificar");
                
                $edit->vari8 = new inputField($this->datasis->traevalor("VARI8")."XVARI8", "vari8");
                $edit->vari8->group = "Variables";
                $edit->vari8->size =16;
                $edit->vari8->maxlength=14;
                $edit->vari8->rule="trim";
                
                $edit->m_vari8= new checkboxField('','m_vari8','Y','N');
				$edit->m_vari8->insertValue='N';
				$edit->m_vari8->in ='vari8';
				$edit->m_vari8->append("Modificar");
                
                $edit->sueldo = new inputField("Sueldo ", "sueldo");
                $edit->sueldo->group = "Relaci&oacute;n Laboral";
                $edit->sueldo->size  =15;
                $edit->sueldo->maxlength=10;
                $edit->sueldo->css_class='.inputnum';
                
                $edit->m_sueldo= new checkboxField('','m_sueldo','Y','N');
				$edit->m_sueldo->insertValue='N';
				$edit->m_sueldo->in ='sueldo';
				$edit->m_sueldo->append("Modificar");
                
                $edit->submit("btnsubmit","Guardar");  
				$edit->build_form();
				
				if  ($edit->on_show()) {  
				//do something  
				}  

				if ($edit->on_success()){
					$data = array();
					$sql = $this->input->post('sql');
					$campos = array('m_contrato','m_codigoadm','m_fondo','m_divi','m_status','m_sueldo','m_vari1','m_vari2','m_vari3','m_vari4','m_vari5','m_vari6','m_vari7','m_vari8','m_codigopres','m_tipemp');
					foreach($campos as $campo){
						if($this->input->post($campo)=='Y'){
							$data[substr($campo,2)]=$this->input->post(substr($campo,2));
						}
					}
					
					$sql = substr($sql,0, strpos($sql,'LIMIT'));
					$query = $this->db->query($sql);
					$query = $query->result_array();
					$codigos=array();
					foreach($query as $row){
						//echo $row['codigo'].br();
						$codigos[]=$this->db->escape($row['codigo']);
					}
					$codigos = implode($codigos,',');					
					
					if(count($data)>0){
						$where="codigo IN  ($codigos)";
						$query = $this->db->update_string('pers', $data,$where);
						if($this->db->query($query))
						$msj.="</br>Usted ha modificado satisfacrotiamente $cantidad de registros";
					}
				}

				if ($edit->on_error()){  
				//do something else (display suggestions.. etc)  
				//note: validation messages are integrated, so you don't have to do anything (just make fields rules).  
				}  

				$atras = anchor($this->url.'filteredgrid','Ir al Filtro');
				
                $data['content'] = $atras.$msj.$edit->output; 
				$data['title']   = "Modificar Personal Masivo";
				$data["head"]    = $this->rapyd->get_head();
				$this->load->view('view_ventanas', $data);  
		}
        
        function dataedit(){
                $this->rapyd->load("dataedit","dataobject");
                
                $mPPLA=array(
					'tabla'   =>'ppla',
					'columnas'=>array(
						'codigo'      =>'C&oacute;digo',
						'denominacion'=>'Denominaci&oacute;n'),
					'filtro'  =>array('codigo'=>'C&oacute;digo','denominacion'=>'Denominaci&oacute;n'),
					'retornar'=>array('codigo'=>'codigopres'),
					'titulo'  =>'Buscar Cuenta',
					'where'=>'movimiento = "S"',
				);
                $bPPLA    =$this->datasis->p_modbus($mPPLA ,'ppla');
                
                $script ='
                $(function() {
                        $(".inputnum").numeric(".");
                });
                
                
                function damerne(){
					rifci = $("#cedula").val();
					$.post("'.site_url($this->url.'damerne').'",{ cedula:rifci },function(data){
						rne=jQuery.parseJSON(data);
						$("#nombre"   ).val(rne[0].primer_nombre   );
						$("#nombre2"  ).val(rne[0].segundo_nombre  );
						$("#apellido" ).val(rne[0].primer_apellido );
						$("#apellido2").val(rne[0].segundo_apellido);
					});
				}
                '; 

				$do = new DataObject("pers");
				$do->pointer('carg' ,'carg.cargo=pers.cargo',"carg.descrip cargop","LEFT");

                $edit = new DataEdit("Personal", $do);
                $edit->back_url = site_url("nomina/pers/filteredgrid");
                $edit->script($script, "create");
                $edit->script($script, "modify");
                
                $edit->post_process('insert','_post_insert');
                $edit->post_process('update','_post_update');
                $edit->post_process('delete','_post_delete');
                                          
                $mNOCO=array(
                'tabla'   =>'noco',
                'columnas'=>array(
                      'codigo'  =>'C&oacute;digo de Sucursal',
                      'nombre'  =>'Nombre'),
                'filtro'  =>array('codigo'=>'C&oacute;digo','nombre'  =>'Nombre'),
                'retornar'=>array('codigo'=>'contrato'),
                'titulo'  =>'Buscar Contratos',
                'where'   =>'tipo <> "O"');
                
                $bNOCO=$this->datasis->modbus($mNOCO);
                
                $sucu=array(
                'tabla'   =>'sucu',
                'columnas'=>array(
                      'codigo'  =>'C&oacute;digo de Sucursal',
                      'sucursal'=>'Sucursal'),
                'filtro'  =>array('codigo'=>'C&oacute;digo de Sucursal','sucursal'=>'Sucursal'),
                'retornar'=>array('codigo'=>'sucursal'),
                'titulo'  =>'Buscar Sucursal');
                
                $boton=$this->datasis->modbus($sucu);
                
                $cargo=array(
			  'tabla'   =>'carg',
			  'columnas'=>array(
					'cargo'  =>'C&oacute;digo de Cargo',
					'descrip'=>'Descripcion'),
			  'filtro'  =>array('cargo'=>'C&oacute;digo de Cargo','descrip'=>'Descripcion'),
			  'retornar'=>array('cargo'=>'cargo'),
			  'titulo'  =>'Buscar Cargo');
                
                $boton1=$this->datasis->modbus($cargo);

                $edit->codigo =  new inputField("C&oacute;digo", "codigo");
                $edit->codigo->rule="required|callback_chexiste";
                $edit->codigo->mode="autohide";
                $edit->codigo->maxlength=15;
                $edit->codigo->size=16;
                
                $edit->nacional = new dropdownField("C&eacute;dula", "nacional");
                $edit->nacional->style = "width:110px;";
                $edit->nacional->option("V","Venezolano");
                $edit->nacional->option("E","Extranjero");
                $edit->nacional->group = "Datos del Trabajador";
                 
                $damenombre   ='<a href="javascript:damerne();">Dame Nombre</a>';
                $edit->cedula =  new inputField("C&eacute;dula", "cedula");
                $edit->cedula->size = 14;
                $edit->cedula->maxlength= 12;
                $edit->cedula->in = "nacional";
                $edit->cedula->rule="trim|numeric|required";
                $edit->cedula->css_class='inputnum';
                $edit->cedula->append($damenombre);
                
                $edit->rif =  new inputField("Rif", "rif");
                $edit->rif->size = 14;
                $edit->rif->maxlength= 12;
                $edit->rif->rule="trim";
                
                //$edit->cedula->group = "Datos del Trabajador";

                $edit->nombre =  new inputField("Nombre", "nombre");
                $edit->nombre->group = "Datos del Trabajador";
                $edit->nombre->size = 40;
                $edit->nombre->maxlength=30;
                $edit->nombre->rule="required|strtoupper";
                
                $edit->nombre2 =  new inputField("Nombre", "nombre2");
                $edit->nombre2->group = "Datos del Trabajador";
                $edit->nombre2->size = 40;
                $edit->nombre2->maxlength=30;
                $edit->nombre2->rule="strtoupper";
                
                $edit->apellido = new inputField("Apellidos", "apellido");
                $edit->apellido->group = "Datos del Trabajador";
                $edit->apellido->size = 40;
                $edit->apellido->maxlength=30;
                //$edit->apellido->in = "nombre";
                $edit->apellido->rule="required|strtoupper";
                
                $edit->apellido2 = new inputField("Apellidos", "apellido2");
                $edit->apellido2->group = "Datos del Trabajador";
                $edit->apellido2->size = 40;
                $edit->apellido2->maxlength=30;
                $edit->apellido2->rule="strtoupper";
                
                $edit->sexo = new dropdownField("Sexo", "sexo");
                $edit->sexo->style = "width:100px;";
                $edit->sexo->option("F","Femenino");
                $edit->sexo->option("M","Masculino");
                $edit->sexo->group = "Datos del Trabajador";
                
                //$edit->label1 = new freeField("EC","EC","<id class='littletableheader'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estado Civil&nbsp;&nbsp; </id>");
                //$edit->label1->in = "sexo";

                $edit->civil = new dropdownField("Estado Civil", "civil");
                $edit->civil->style = "width:100px;";
                $edit->civil->option("S","Soltero");
                $edit->civil->option("C","Casado");
                $edit->civil->option("D","Divorciado");
                $edit->civil->option("V","Viudo");
                $edit->civil->group = "Datos del Trabajador";
                //$edit->civil->in = "sexo";
                
                $edit->direc1 = new inputField("Direcci&oacute;n", "direc1");
                $edit->direc1->group = "Datos del Trabajador";
				$edit->direc1->size =40;
				$edit->direc1->maxlength=30;
                
                $edit->direc2 = new inputField("&nbsp;", "direc2");
                $edit->direc2->size =40;
                $edit->direc2->group = "Datos del Trabajador";
                $edit->direc2->maxlength=30;
                
                $edit->direc3 = new inputField("&nbsp;", "direc3");
                $edit->direc3->size =40;
                $edit->direc3->group = "Datos del Trabajador";
                $edit->direc3->maxlength=30;
                
                $edit->telefono = new inputField("Tel&eacute;fono", "telefono");
                $edit->telefono->size =40;
                $edit->telefono->group = "Datos del Trabajador";
                $edit->telefono->maxlength=30;
                
                $edit->nacimi = new DateonlyField("Fecha de Nacimiento", "nacimi","d/m/Y");
                $edit->nacimi->size = 12;
                $edit->nacimi->group = "Datos del Trabajador";
                
			  //$edit->sucursal = new inputField("Sucursal", "sucursal");
			  //$edit->sucursal->size =4;
			  //$edit->sucursal->maxlength=2;
                //$edit->sucursal->group = "Relaci&oacute;n Laboral";
                //$edit->sucursal->append($boton);
                
                $edit->contrato = new inputField("Contrato", "contrato");
                $edit->contrato->size =4;
                $edit->contrato->maxlength=5;
                $edit->contrato->readonly =true;
                $edit->contrato->group = "Relaci&oacute;n Laboral";
                $edit->contrato->append($bNOCO);
                $edit->contrato->rule  = 'required';
                
                $edit->codigoadm = new dropdownField("Estructura Administrativa","codigoadm");
                $edit->codigoadm->option("","Seleccione");
                $edit->codigoadm->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presupuesto AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
                $edit->codigoadm->group="Relaci&oacute;n Laboral";
                
                $edit->fondo = new dropdownField("Fondo", "fondo");
                $edit->fondo->style="width:300px;";
                $edit->fondo->group="Relaci&oacute;n Laboral";
                $edit->fondo->option("","");
//              $estadmin=$edit->get('codigoadm');
//              if($estadmin!==false){
                        $edit->fondo->options("SELECT tipo,tipo a  FROM presupuesto GROUP BY tipo");
//              }else{
//                      $edit->fondo->option("","Seleccione una estructura administrativa primero");
//              }
                
                $edit->codigopres = new inputField("Partida", "codigopres");
				//$edit->codigopres->rule='required';//callback_repetido|
				$edit->codigopres->size=20;
				$edit->codigopres->append($bPPLA);
				$edit->codigopres->group = "Relaci&oacute;n Laboral";
                
                $edit->divi = new dropdownField("Divisi&oacute;n", "divi");
                $edit->divi->style ="width:250px;";
                $edit->divi->rule  = 'required';
                $edit->divi->options("SELECT division,CONCAT_WS(' ',division,descrip)des FROM divi ORDER BY division");
                //$edit->divi->onchange = "ajaxsubcategories();";
                $edit->divi->group = "Relaci&oacute;n Laboral";
                
                //$edit->depa = new dropdownField("Departamento", "depto");
                //$edit->depa->style ="width:250px;";
                //$edit->depa->option("","");
                //$edit->depa->options("SELECT division,descrip FROM divi ORDER BY division");
                //$edit->depa->group = "Relaci&oacute;n Laboral";
    
                $edit->cargo = new inputField("Cargo", "cargo");
                $edit->cargo->group = "Relaci&oacute;n Laboral";
                $edit->cargo->size =11;
                $edit->cargo->maxlength=8;
                $edit->cargo->append($boton1);
                
                $edit->cargop = new inputField("", "cargop");
				$edit->cargop->db_name ='cargop';
				$edit->cargop->size    =20;
				$edit->cargop->readonly=true;
				$edit->cargop->pointer =true;
				$edit->cargop->in      ="cargo";
				
				$edit->tipemp = new dropdownField("Condicion", "tipemp");
                $edit->tipemp->style = "width:100px;";
                $edit->tipemp->option(""     ,""                      );
                $edit->tipemp->option("F"    ,"Fijo"                  );
                $edit->tipemp->option("C"    ,"Contratado"            );
                $edit->tipemp->option("J"    ,"Jubilado"              );
                $edit->tipemp->option("P"    ,"Pensionado"            );
                $edit->tipemp->option("A"    ,"Alto Nivel y Direccion");
                $edit->tipemp->option("E"    ,"Eleccion Popular"      );
                
                $edit->tipemp->group = "Relaci&oacute;n Laboral";
                
                $edit->sso = new inputField("Nro. Seguro Social", "sso");
                $edit->sso->size =13;
                $edit->sso->maxlength=11;
                $edit->sso->group = "Relaci&oacute;n Laboral";

                $edit->ingreso = new DateonlyField("Fecha de Ingreso", "ingreso","d/m/Y");
                $edit->ingreso->size = 12;
                $edit->ingreso->group = "Relaci&oacute;n Laboral";

                $edit->label2 = new freeField("Edo. C","edoci","<id class='littletableheader'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha de Retiro&nbsp;&nbsp; </id>");
                $edit->label2->in = "ingreso";
                
                $edit->retiro =  new DateField("Fecha de Retiro", "retiro","d/m/Y");    
                $edit->retiro->size = 12;
                $edit->retiro->in = "ingreso" ;

                //$edit->tipo = new dropdownField("Tipo de N&oacute;mina", "tipo");
                //$edit->tipo->options(array("Q"=> "Quincenal","M"=>"Mensual","S"=>"Semanal"));
                //$edit->tipo->group = "Relaci&oacute;n Laboral";
                //$edit->tipo->style = "width:100px;";
        
                $edit->dialib = new inputField("Dias libres", "dialib");
                $edit->dialib->group = "Relaci&oacute;n Laboral";
                $edit->dialib->size =4;
                $edit->dialib->maxlength=2;

                $edit->label3 = new freeField("DL","DL","<id class='littletableheader'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dias Laborables&nbsp;&nbsp; </id>");
                $edit->label3->in = "dialib";
                
                $edit->dialab =  new inputField("Dias laborables", "dialab");
                $edit->dialab->group = "Relaci&oacute;n Laboral";
                $edit->dialab->size =4;
                $edit->dialab->maxlength=2;
                $edit->dialab->in = "dialib";
                
                $edit->status = new dropdownField("Estatus", "status");
                $edit->status->options(array("A"=> "Activo","V"=>"Vacaciones","R"=>"Retirado"));
                $edit->status->group = "Relaci&oacute;n Laboral";
                $edit->status->style = "width:100px;";
                
                $edit->carnet =  new inputField("Nro. Carnet", "carnet");
                $edit->carnet->size = 13;
                $edit->carnet->maxlength=10;
                $edit->carnet->group = "Relaci&oacute;n Laboral";
                
                $edit->formacob =  new dropDownField("Forma de Cobro", 'formacob');
                $edit->formacob->option("","");
				$edit->formacob->option("D","Deposito");
				$edit->formacob->option("C","Cheque");
				$edit->formacob->option("E","Efectivo");
				$edit->formacob->group = "Relaci&oacute;n Laboral";
                
                $edit->banco =  new dropDownField("Banco", 'banco');
                $edit->banco->option("","");
				$edit->banco->options("SELECT cod_banc,nomb_banc FROM tban ORDER BY nomb_banc");
				$edit->banco->group = "Relaci&oacute;n Laboral";
				
				$edit->cutipo =  new dropDownField("Tipo Cuenta", 'cutipo');
                $edit->cutipo->option("","");
				$edit->cutipo->option("A","Ahorro");
				$edit->cutipo->option("C","Corriente");
				$edit->cutipo->group = "Relaci&oacute;n Laboral";
                
                $edit->cuenta =  new inputField("Cuenta", "cuenta");
                $edit->cuenta->size = 25;
                $edit->cuenta->maxlength=25;
                $edit->cuenta->group = "Relaci&oacute;n Laboral";
                
                $edit->vari1 = new inputField($this->datasis->traevalor("VARI1")."XVARI1", "vari1");
                $edit->vari1->group = "Variables";
                $edit->vari1->size =16;
                $edit->vari1->maxlength=14;
                $edit->vari1->rule="trim|numeric";
                $edit->vari1->css_class='inputnum';
                
                $edit->vari2 = new inputField($this->datasis->traevalor("VARI2")."XVARI2", "vari2");
                $edit->vari2->group = "Variables";
                $edit->vari2->size =16;
                $edit->vari2->maxlength=14;
                $edit->vari2->rule="trim|numeric";
                $edit->vari2->css_class='inputnum';
                
                $edit->vari3 = new inputField($this->datasis->traevalor("VARI3")."XVARI3", "vari3");
                $edit->vari3->group = "Variables";
                $edit->vari3->size =16;
                $edit->vari3->maxlength=14;
                $edit->vari3->rule="trim|numeric";
                $edit->vari3->css_class='inputnum';
                        
                $edit->vari4 = new inputField($this->datasis->traevalor("VARI4")."XVARI4", "vari4");
                $edit->vari4->group = "Variables";
                $edit->vari4->size =12;
                $edit->vari4->maxlength=11;
                $edit->vari4->rule="trim|numeric";
                $edit->vari4->css_class='inputnum';
                      
                $edit->vari5 = new inputField($this->datasis->traevalor("VARI5")."XVARI5", "vari5");
                $edit->vari5->group = "Variables";
                $edit->vari5->size =12;
                $edit->vari5->maxlength=12;
                $edit->vari5->rule="trim";
                
                $edit->vari6 = new inputField($this->datasis->traevalor("VARI6")."XVARI6", "vari6");
                $edit->vari6->group = "Variables";
                $edit->vari6->size =16;
                $edit->vari6->maxlength=14;
                $edit->vari6->rule="trim|numeric";
                $edit->vari6->css_class='inputnum';
                
                $edit->vari7 = new inputField($this->datasis->traevalor("VARI7")."XVARI7", "vari7");
                $edit->vari7->group = "Variables";
                $edit->vari7->size =16;
                $edit->vari7->maxlength=14;
                $edit->vari7->rule="trim";
                //$edit->vari7->css_class='inputnum';
                
                $edit->vari8 = new inputField($this->datasis->traevalor("VARI8")."XVARI8", "vari8");
                $edit->vari8->group = "Variables";
                $edit->vari8->size =16;
                $edit->vari8->maxlength=14;
                $edit->vari8->rule="trim";
//                $edit->vari8->css_class='inputnum';
                
                $edit->sueldo = new inputField("Sueldo ", "sueldo");
                $edit->sueldo->group = "Relaci&oacute;n Laboral";
                $edit->sueldo->size  =15;
                $edit->sueldo->maxlength=10;
                $edit->sueldo->css_class='inputnum';
                
                $edit->buttons("modify", "save", "undo", "delete", "back","add");
                $edit->build();

				$smenu['link']   = barra_menu('407');
				$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
                $data['content'] = $edit->output; 
				$data['title']   = "Personal";        
				$data["head"]    = $this->rapyd->get_head().script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css');
				$this->load->view('view_ventanas', $data);  
        }
        function ajaxsubcategories(){  
                $this->rapyd->load("fields");  
                $where = "";  
                if (isset($_POST["divi"])){  
                  $where = "WHERE division = ".$this->db->escape($_POST["divi"]);  
                }   
                $sql = "SELECT departa, depadesc FROM depa $where";
                $subcategory = new dropdownField("Subcategoria", "depa");  
                $subcategory->option("","");  
                $subcategory->options($sql);  
                $subcategory->status = "modify";
                $subcategory->build();  
                echo $subcategory->output;
        }
			
		function damerne(){
			$cedula = $this->input->post("cedula");
			$cedula = str_replace('.','',$cedula);
			$cedula = str_replace('-','',$cedula);
			$cedula = str_replace('V','',$cedula);
				
			$arreglo=array();
			if(is_numeric($cedula)){
				
				$query  ="select 
				rne.primer_nombre,
				rne.segundo_nombre,
				rne.primer_apellido,
				rne.segundo_apellido
				from rne.rne
				 where cedula=$cedula";
				
				$mSQL   = $this->db->query($query);
				$arreglo= $mSQL->result_array($query);
				foreach($arreglo as $key=>$value)
					foreach($value as $key2=>$value2) 
					$arreglo[$key][$key2] = ($value2);
			}
			echo json_encode($arreglo);
		}
		
		function colocarif(){
			$query=" SELECT codigo,CONCAT(nacional,cedula) ced FROM pers";
			$personas = $this->datasis->consularray($query);
			
			foreach($personas as $k=>$v){
				$rif   =citorif($v);
				echo "actualizando $k con $rif</br>";
				$query = "UPDATE pers SET rif='".$rif."' WHERE codigo='$k'";
				$this->db->query($query);
			}
		}
        
        function _pre_del($do) {
                $codigo=$do->get('codigo');
                $chek =  $this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE codigo='$codigo'");
                $chek += $this->datasis->dameval("SELECT COUNT(*) FROM asig   WHERE codigo='$codigo'");
        
                if ($chek > 0){
                        $do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Cliente con Movimiento no puede ser Borrado';
                        return False;
                }
                return True;
        }
        function _post_insert($do){
                $codigo=$do->get('codigo');
                $nombre=$do->get('nombre');
                logusu('pers',"PERSONAL $codigo NOMBRE  $nombre CREADO");
        }
        function _post_update($do){
                $codigo=$do->get('codigo');
                $nombre=$do->get('nombre');
                logusu('pers',"PERSONAL $codigo NOMBRE  $nombre  MODIFICADO");
        }
        function _post_delete($do){
                $codigo=$do->get('codigo');
                $nombre=$do->get('nombre');
                logusu('pers',"PERSONAL $codigo NOMBRE  $nombre  ELIMINADO ");
        }
        function chexiste($codigo){
                $codigo=$this->input->post('codigo');
                $chek=$this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE codigo='$codigo'");
                if ($chek > 0){
                        $nombre=$this->datasis->dameval("SELECT nombre FROM pers WHERE codigo='$codigo'");
                        $this->validation->set_message('chexiste',"Personal con el codigo $codigo nombre $nombre ya existe");
                        return FALSE;
                }else {
                return TRUE;
                }       
        }
  
	function instalar(){
	  $query="ALTER TABLE pers ADD COLUMN `rif` VARCHAR(50) NULL DEFAULT NULL";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers`  CHANGE COLUMN `cuenta` `cuenta` VARCHAR(20) NULL DEFAULT NULL";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers` ADD COLUMN `nombre2` VARCHAR(30) NULL DEFAULT NULL AFTER `nombre`";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers` ADD COLUMN `apellido2` VARCHAR(30) NULL DEFAULT NULL AFTER `apellido`";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers` ADD COLUMN `codigoadm` VARCHAR(25) NULL DEFAULT NULL";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers` ADD COLUMN `fondo` VARCHAR(25) NULL DEFAULT NULL";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers`  ADD COLUMN `codigopres` VARCHAR(25) NULL DEFAULT NULL";
	  $this->db->simple_query($query);
	  $query="ALTER TABLE `pers`  ADD COLUMN `vari7` VARCHAR(50) NULL DEFAULT '0.00' AFTER `vari6`";
	   $this->db->simple_query($query);
	  $query="ALTER TABLE `pers`   ADD COLUMN `vari8` VARCHAR(50) NULL DEFAULT '0.00' AFTER `vari7`;";
	   $this->db->simple_query($query);
	   $query="ALTER TABLE `pers` 	CHANGE COLUMN `direc1` `direc1` TEXT NULL DEFAULT NULL AFTER `apellido2`";
	   $this->db->simple_query($query);
	   $query="ALTER TABLE `pers` 	ADD COLUMN `horario` CHAR(4) NULL DEFAULT NULL AFTER `vence`";
	   $this->db->simple_query($query);
	   $query="ALTER TABLE `pers` ADD COLUMN `formacob` CHAR(1) NULL DEFAULT NULL AFTER `status`";
	   $this->db->simple_query($query);
	   
		$query="ALTER TABLE `pers` CHANGE COLUMN `vari1` `vari1` VARCHAR(50) NULL DEFAULT '0.00' AFTER `cuenta` ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `pers` CHANGE COLUMN `vari2` `vari2` VARCHAR(50) NULL DEFAULT '0.00' AFTER `vari1`  ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `pers` CHANGE COLUMN `vari3` `vari3` VARCHAR(50) NULL DEFAULT '0.00' AFTER `vari2`  ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `pers` CHANGE COLUMN `vari4` `vari4` VARCHAR(50) NULL DEFAULT NULL AFTER `vari3`    ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `pers` CHANGE COLUMN `vari5` `vari5` VARCHAR(50) NULL DEFAULT NULL AFTER `vari4`    ";
		$this->db->simple_query($query);
		$query="ALTER TABLE `pers` CHANGE COLUMN `vari6` `vari6` VARCHAR(50) NULL DEFAULT '0.00' AFTER `vari5`  ";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `pers` 	CHANGE COLUMN `nombre` `nombre` VARCHAR(255) NULL DEFAULT NULL AFTER `cedula`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `pers` 	ADD COLUMN `tipemp` VARCHAR(20) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE `pers` 	CHANGE COLUMN `rif` `rif` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		
	   
	}
	
	function prueba(){
		echo  (strtotime(20150203) - strtotime(20140203))/60/60/24;
		//echo 60/60/24/365;
	}
}
?>
