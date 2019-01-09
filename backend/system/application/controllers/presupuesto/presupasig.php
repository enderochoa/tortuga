<?php
class presupasig extends Controller {
	var $formatopres;
	var $flongpres;
	var $formatoadm;
	var $flongadm;
	
	function presupasig(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres));
		$this->formatoadm=$this->datasis->traevalor('FORMATOESTRU');
		$this->flongadm  =strlen(trim($this->formatoadm));
		
	}
	
	function index() {
		$this->datasis->modulo_id(6,1);
		redirect("presupuesto/presupasig/sel");
	}
	
	function sel(){
		$this->rapyd->load("dataform");
		//echo $this->flongpres;
		$script='
		$(function() {

				$("#codigoadm").change(function(){
					$.post("'.site_url('presupuesto/presusol/get_tipo').'",{ codigoadm:$("#codigoadm").val() },function(data){$("#tipo").html(data);})
				});

				$("#tipo").change(function(){
					$.post("'.site_url('presupuesto/presusol/get_estrupres').'",{ codigoadm:$("#codigoadm").val(),tipo:$("#tipo").val() },function(data){$("#codigopres").html(data);})
				});
				
		});
		';
		
		$flong=$this->flongpres;
		$rlong=$this->flongadm;
		
		$filter = new DataForm("presupuesto/presupasig/sel/process");
		
		$filter->script($script);

		$filter->codigoadm = new dropdownField("Estructura Administrativa","codigoadm");
		$filter->codigoadm->option("","Seleccione");
		$filter->codigoadm->rule='required';
		$filter->codigoadm->options("SELECT b.codigo, CONCAT_WS(' ',b.codigo,b.denominacion) AS val FROM presusol AS a JOIN estruadm AS b ON a.codigoadm=b.codigo  GROUP BY b.codigo");
		
		$filter->tipo =new dropdownField('Origen de fondos','tipo');
		$filter->tipo->option("","Seleccione una Estructura Administrativa");
		$filter->tipo->rule='required';
		
		$filter->codigopres = new dropdownField("Presupuesto","codigopres");
		$filter->codigopres->option("","Seleccione un presupuesto");
		
		$filter->submit("btnsubmit","Buscar");		
		$filter->build_form();
		
		if ($filter->on_success()){
		
		
			$ttipo  = $filter->tipo->newValue;
			$codamd = $filter->codigoadm->newValue;
			$codpre = $filter->codigopres->newValue;

			redirect("presupuesto/presupasig/asignar/$ttipo/$codamd/$codpre");
		} 
		
		

		$data['content'] = $filter->output;
		$data['title']   = "Presupuestos Aprobados";
		$data["head"]    = script("jquery.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function asignar($tipo,$codigoadm,$codigopres=null)
	{
		$this->rapyd->load("datagrid","dataobject","fields");
		$flong=$this->flongpres;
		
		function asigna($valor,$value=0,$soli=0){//$tipo,$codigoadm,$codigopres,
			$campo = new inputField("Title", "gt[$valor]");
			$campo->status = "create";
			$campo->css_class='inputnum';
			$campo->size=10;
			if($value==0)$campo->insertValue=$soli;
				else $campo->insertValue=$value;
			//$campo->rule="readonly";			
			
			$campo->build();
			return $campo->output;
		}
		
		function solicitud($valor,$value=null){//$tipo,$codigoadm,$codigopres,
			//$campo = new inputField("Title2", "gs[$valor]");
			//$campo->status = "create";
			//$campo->css_class='inputnum';
			//$campo->size=10;
			//$campo->insertValue=$value;
			$campo = new containerField("Title2", $value);
			$campo->container->when = array("show");  
			//$campo->status = "create";
			//$campo->css_class='inputnum';
			//$campo->size=10;
			//$campo->insertValue=$value;
			
			$campo->build();
			return $campo->output;
		}
		
		
		$tabla=form_open("presupuesto/presupasig/carga/");
		//$codamd = $this->db->escape($codigoadm);
		//$ttipo  = $this->db->escape($tipo);
		//$codpre = $this->db->escape($codigopres.'%');
		$codamd = ($codigoadm);
		$ttipo  = ($tipo);
		$codpre = ($codigopres.'%');
		
		$ddata = array(
              'codigoadm'  => $codigoadm,
              'tipo'       => $tipo,
              'codigopres' => $codigopres
            );
		
		$grid = new DataGrid("Asignaci&oacute;n Inicial de Presupuestos");
		$grid->db->from('presusol AS a');
		$grid->db->join('ppla AS b','a.codigopres=b.codigo');
		$grid->db->where("b.movimiento","S");
		$grid->db->where("tipo "     ,$ttipo );
		$grid->db->where("codigoadm ",$codamd);
		if(!empty($codpre))
			$grid->db->where("codigopres LIKE ",$codpre);
		$grid->db->groupby("");
			
		$grid->use_function('asigna');
		$grid->use_function('solicitud');
		
		$grid->column("Partida"             ,"codigopres"  ,'align=center');
		$grid->column("Denominaci&oacute;n" ,"denominacion"  );
		$grid->column("Estimado"            ,"<solicitud><#codigopres#>|<#solicitado#></solicitud>");
		$grid->column("Asignado"            ,"<asigna><#codigopres#>|<#asignacion#>|<#solicitado#></asigna>");
		
		$grid->build();
		//echo $grid->db->last_query();
		$tabla.=$grid->output.form_submit('mysubmit', 'Guardar').form_hidden($ddata);
		$tabla.=form_close();
		if($grid->recordCount==0){
			$tabla='No hay registros para esta selecci&oacute;n';
		}
		
		$data['script']  ='<script language="javascript" type="text/javascript">
		$(function() {
			$(".inputnum").numeric(".");
		});
		</script>';
		$codigoadmdes=$this->datasis->dameval("SELECT denominacion FROM estruadm WHERE codigo='$codigoadm'");
		$data['content'] = $tabla;
		$data['title']   = "($codigoadm) $codigoadmdes $tipo";
		$data["head"]    = script("jquery.js").script("plugins/jquery.numeric.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	
	
	function carga(){
		$this->rapyd->load("dataobject");
		$tipo   = $this->input->post('tipo');
		$codamd = $this->input->post('codigoadm');

		$do = new DataObject("presusol");
		
		$data=$this->input->post('gt');
		$data2=$this->input->post('gs');
		
		
		//print_r($data);
		
		$tot=0;
		foreach($data AS $codpre=>$value){
			$ccodpre=str_replace('_','.',$codpre);
			$pk=array(
			'tipo'      =>$tipo,
			'codigoadm' =>$codamd,
			'codigopres'=> $ccodpre);
			$tot+=$value;
			$do->load($pk);
			$do->set("asignacion",$value);
			//$do->set("solicitado",$data2[$codpre]);
			$do->save();
		}
		$niveles=explode('.',$ccodpre);
		
		$ttipo  = $this->db->escape($tipo);
		$codamd = $this->db->escape($codamd);
		
		$pivote='';
		foreach($niveles AS $var){
			$pivote.=$var.'.';
			$mSQL="SELECT SUM(asignacion),SUM(solicitud) FROM presusol WHERE tipo=$ttipo AND codigoadm=$codamd AND codigopres LIKE '$pivote%'";
		}
		
		$data['content'] = 'Guardado</br>'.anchor('presupuesto/presupasig','regresar');
		$data['title']   = "Asignacion Presupuestaria";
		$data["head"]    = script("jquery.js").script("plugins/jquery.numeric.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);


	}
	/*function get_tipo(){
		$codigoadm=$this->input->post('codigoadm');
		$codigoadm=$this->db->escape($codigoadm);
		if($codigoadm!==false){
			$query=$this->db->query("SELECT tipo  FROM presusol WHERE codigoadm=$codigoadm GROUP BY  tipo");
			if($query){
				if($query->num_rows()>0){
					echo "<option value=''>Seleccionar</option>";
					foreach($query->result() AS $fila ){
						echo "<option value='".$fila->tipo."'>".$fila->tipo."</option>";
					}
				}else{
					echo "<option value=''>No hay registros disponibles</option>";
				}
			}
		}
	}
	
	
	function get_estruadm(){
		$tipo=$this->input->post('tipo');
		if($tipo!==false){
			$query=$this->db->query("SELECT b.codigo,b.denominacion  FROM estruadm b JOIN presusol a ON b.codigo=a.codigoadm WHERE a.tipo='$tipo' GROUP BY  b.codigo");
			if($query){
				if($query->num_rows()>0){
					echo "<option value=''>Seleccionar</option>";
					foreach($query->result() AS $fila ){
						echo "<option value='".$fila->codigo."'>".$fila->codigo.' '.$fila->denominacion."</option>";
					}
				}else{
					echo "<option value=''>No hay registros disponibles</option>";
				}
			}
		}
	}

	function get_estrupres(){
		$tipo=$this->db->escape($this->input->post('tipo'));
		$estruadm=$this->db->escape($this->input->post('codigoadm'));
		$ff=explode('.',$this->formatopres);
		if(count($ff)>2)
			$cana=strlen($ff[0].$ff[1])+1;
		else
			$cana=strlen($ff[0])+1;
		if($tipo!==false AND $estruadm!==false){
			$query=$this->db->query("SELECT b.codigo,b.denominacion  FROM presusol AS a JOIN ppla AS b ON b.codigo=a.codigopres WHERE a.tipo=$tipo AND a.codigoadm=$estruadm AND LENGTH(a.codigopres)<=$cana GROUP BY  b.codigo");
			if($query){
				if($query->num_rows()>0){
					echo "<option value=''>Seleccionar</option>";
					foreach($query->result() AS $fila ){
						echo "<option value='".$fila->codigo."'>".$fila->codigo.' '.$fila->denominacion."</option>";
					}
				}else{
					echo "<option value=''>No hay registros disponibles</option>";
				}
			}
		}
	}

	/*
	function get_estrupres(){		
		$codigoadm =$this->db->escape($this->input->post('codigoadm'));
		$tipo      =$this->db->escape($this->input->post('tipo'));

		if($codigoadm!==false){
			$query=$this->db->query("SELECT codigopres AS codigo FROM presupuesto WHERE tipo=$tipo AND codigoadm=$codigoadm AND  LENGTH(codigopres)<".$this->flongpres." GROUP BY  codigopres");
			if($query){
				if($query->num_rows()>0){
					echo "<option value=''>Seleccionar</option>";
					foreach($query->result() AS $fila ){
						echo "<option value='".$fila->codigo."'>".$fila->codigo."</option>";
					}
				}else{
					echo "<option value=''>No hay registros disponibles</option>";
				}
			}
		}
	}*/
	
}