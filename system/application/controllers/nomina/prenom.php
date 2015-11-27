<?php
class Prenom extends Controller {
	function Prenom(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->titulo='Generar Prenomina';
		$this->load->library('pnomina');
	}
	function index($var1='',$var2='',$var3='',$var4=''){		$this->rapyd->load("dataform","datatable");
		$this->db->simple_query("ALTER TABLE `retenomi`  ADD COLUMN `nombre` VARCHAR(100) NULL AFTER `status`");		$this->db->simple_query("ALTER TABLE `asignomi` ADD COLUMN `denominacion` TINYTEXT NULL  AFTER `status`");		$form1 = new DataForm('nomina/prenom/index/process/aa');
		$form1->contrato = new dropdownField("Contrato", "contrato");
		$mSQL=$this->db->query("SHOW TABLES LIKE 'PRENOM%'");
		foreach($mSQL->result_array() AS $row){
			foreach($row AS $key=>$value){
				$c=$this->db->escape(substr($value,6));
				$nombre = $this->datasis->dameval("SELECT nombre FROM noco WHERE codigo=$c");
				$form1->contrato->option($value,$nombre);
			}
				
		}
		$form1->submit("btnsubmit","Recuperar");		$form1->build_form();
		if ($form1->on_success() && $var2=='aa'){
			$this->load->dbforge();
			$tabla   ='prenom';
			$tablap  ='pretab';
			$this->db->query("TRUNCATE $tabla");
			$this->db->query("TRUNCATE $tablap");
			$contrato=$form1->contrato->newValue;
			$mSQL  = "INSERT IGNORE INTO $tabla SELECT * FROM $contrato ";
			$this->db->query($mSQL);
			$mSQL  = "DROP TABLE `$contrato`";
			$this->db->query($mSQL);
			redirect('nomina/prenom/montos');
		}
		$script='
		$(function() {
			$("#pagar").click(function(){
				if(confirm("Esta Seguro de Crear Orden de Pago de Prenomina actual"))
				return true;
				else
				return false;
			});
			$("#guarda").click(function(){
				if(confirm("Esta Seguro que desea Guardar la Prenomina actual. Esto eliminara la prenomina guardada anteriormente")){
					$.post("'.site_url('nomina/prenom/respalda').'",{ partida:"a" },function(data){
						if(data="_Si"){
							alert("Se respaldo correctamente la Prenomina.")
							nueva=window.open("'.site_url('nomina/prenom').'","_self");
							
						}else{
							alert("No se Pudo respaldar la Prenomina")
						}
					})					return false;				}else{					return false;				}			});
			$("#recibo").click(function(){
			    cant = $("#cant").val();
			    var caracteristicas = "height=240,width=420,scrollTo,resizable=1,scrollbars=1,location=0";
			    nueva=window.open("'.site_url('forma/ver/RECIBO/').'/"+cant, "Popup", caracteristicas);
			    return false;
			});

			$("#contrato").change(function (){
				c=$("#contrato").val();
				$.post("'.site_url('nomina/noco/tipo').'",{ con: c},
				function(data){
					$("#contrato2").html(data);
				});
			});
		});
		';
		$form = new DataForm('nomina/prenom/index/process');		$form->script($script);
		$form->contrato = new dropdownField("Contrato", "contrato");		$form->contrato->option("","Seleccionar");		$form->contrato->options("SELECT codigo,CONCAT_ws(' ',codigo,nombre) nom FROM noco ORDER BY nombre");		$form->contrato->rule='required';

		$form->contrato2 = new dropdownField("Aplicar a", "contrato2");
		//$form->contrato2->options("SELECT codigo,CONCAT_ws(' ',codigo,nombre) nom FROM noco WHERE tipo<>'O' ORDER BY nombre");
		$form->fechac = new dateonlyField("Fecha de corte", "fechac");		$form->fechac->rule='required|chfecha';		$form->fechac->insertValue = date("Y-m-d");		$form->fechac->size=12;
		$form->fechap = new dateonlyField("Fecha de pago", "fechap");
		$form->fechap->rule='required|chfecha';
		$form->fechap->insertValue = date("Y-m-d");
		$form->fechap->size=12;
		$form->submit("btnsubmit","Generar");
		$form->build_form();
		if($form->on_success()){
			$this->load->dbforge();
			$contrato = $form->contrato->newValue;
			$contratoe = $this->db->escape($form->contrato->newValue);
			$trabajae  = NULL;

			$contrato2= $form->contrato2->newValue;
			$contrato2e = $this->db->escape($form->contrato2->newValue);

			$fechac    = $form->fechac->newValue;
			$fechap    = $form->fechap->newValue;
			$tabla   ='prenom';
			$tablap  ='pretab';

			$this->db->query("TRUNCATE $tabla" );
			$this->db->query("TRUNCATE $tablap");

			if(!empty($contrato2)){
				$scontratoe=$contratoe;
				$trabajae  =$contrato2e;
				$wcontratoe=$contrato2e;
				
				$mSQL  = "INSERT IGNORE INTO $tabla (contrato, codigo,nombre, concepto, grupo, tipo, descrip, formula, monto, fecha, fechap,cuota,cuotat,pprome,trabaja,modo,orden,vari1,vari2,vari3,vari4,vari5,vari6,vari7,vari8) ";
				$mSQL .= "SELECT $scontratoe, b.codigo, CONCAT(RTRIM(b.apellido),' ',b.nombre) nombre, ";
				$mSQL .= "a.concepto, a.grupo, a.tipo, a.descrip, a.formula, 0, $fechac, $fechap , 0, 0, 0, $trabajae,modo,e.orden,b.vari1,b.vari2,b.vari3,b.vari4,b.vari5,b.vari6,b.vari7,b.vari8 ";
				$mSQL .= "FROM conc a ";
				$mSQL .= "JOIN itnoco c ON a.concepto=c.concepto ";
				$mSQL .= "JOIN noco d ON c.codigo=d.codigo ";
				$mSQL .= "JOIN itnoco e ON d.codigo=e.codigo ";
				$mSQL .= "JOIN pers b ON $scontratoe=c.codigo "; 
				$mSQL .= "WHERE b.contrato=$wcontratoe AND b.status='A' ";
			}else{

				$scontratoe=$contratoe;
				$trabajae  =$this->db->escape('');
				$wcontratoe=$contratoe;
				
				$mSQL  = "INSERT IGNORE INTO $tabla (contrato, codigo,nombre, concepto, grupo, tipo, descrip, formula, monto, fecha, fechap,cuota,cuotat,pprome,trabaja,modo,orden,vari1,vari2,vari3,vari4,vari5,vari6,vari7,vari8) ";
				$mSQL .= "SELECT $scontratoe, b.codigo, CONCAT(RTRIM(b.apellido),'/',b.nombre) nombre, ";
				$mSQL .= "a.concepto, a.grupo, a.tipo, a.descrip, a.formula, 0, $fechac, $fechap , 0, 0, 0, $trabajae,modo,c.orden,b.vari1,b.vari2,b.vari3,b.vari4,b.vari5,b.vari6,b.vari7,b.vari8 ";
				$mSQL .= "FROM conc a JOIN itnoco c ON a.concepto=c.concepto ";
				$mSQL .= "JOIN noco d ON c.codigo=d.codigo ";
				$mSQL .= "JOIN pers b ON b.contrato=d.codigo 
				WHERE d.codigo=$wcontratoe AND b.status='A' ";
			}

			
			$this->db->query($mSQL);
			$fields = $this->db->list_fields($tablap);
			$ii=count($fields);
			for($i=5;$i<$ii;$i++)
				$this->dbforge->drop_column($tablap,$fields[$i]);
			unset($fields);
			$query = $this->db->query("SELECT concepto FROM itnoco WHERE codigo=$scontratoe ORDER BY concepto");
			foreach ($query->result() as $row){
				$ind    = 'c'.trim($row->concepto);
				$fields[$ind]=array('type' => 'decimal(17,2)','default' => 0);
			}

			$this->dbforge->add_column($tablap, $fields);			unset($fields);
			$frec=$this->datasis->dameval("SELECT tipo FROM noco WHERE codigo=$scontratoe");
			$this->calculaprenom();
			redirect('nomina/prenom/montos');
		}
		$atts = array(
		'width'     =>'420',
		'height'    =>'300',
		'scrollbars'=>'yes',
		'status'    =>'yes',
		'resizable' =>'yes',
		'screenx'   =>'5',
		'screeny'   =>'5' );
		$atts2 = array(
		'width'     =>'1024',
		'height'    =>'768',
		'scrollbars'=>'yes',
		'status'    =>'yes',
		'resizable' =>'yes',
		'screenx'   =>'0',
		'screeny'   =>'0' );
		$atts3 = array(
		'width'     =>'420',
		'height'    =>'240',
		'scrollbars'=>'yes',
		'status'    =>'yes',
		'resizable' =>'yes',
		'screenx'   =>'5',
		'screeny'   =>'5',		'id'        =>'recibo' );
		$contratoactual=$this->datasis->dameval("SELECT b.nombre FROM prenom a JOIN noco b ON a.contrato=b.codigo LIMIT 1");
		$des01='';
		if($contratoactual)
			$des01="<div style='background-color:black;padding:5px;color:red;' ><strong>$contratoactual</strong></div>";
		$des0 = new containerField("",$des01);
		$des0->build();
				
		$des3 = "<strong>Prenomina:</strong> es el termino utilizado para referirse a la nomina en la cual se esta trabajando que pertenece a un contrato y a una fecha dada. En la prenomina es el momento donde se introducen valores extra para los conceptos de nomina. por ejemplo la cantidad de horas extra trabajadas. y de donde se imprimen los recibos de pagos para los empleados";
		$des2 = new containerField("","<div style='background-color:#EEDDEE;padding:5px;'> ".$des3."</div>");		$des2->build();
		$des4 = new containerField("","<div style='background-color:#FFDDFF;padding:5px;'> ".anchor('','<span id="guarda">Guardar Prenomina Actual</span>')." Este se utiliza para almacenar la prenomina en la que se esta trabajando y porder continuar con otra sin perder los cambios realizados a esta</div>");		$des4->build();
		$des5 = new containerField("","<div style='background-color:#DDDDFF;padding:5px;'> ".anchor('nomina/prenom/montos','Modificar Prenomina Actual')." Este se utiliza para continuar introduciendo valores a conceptos de la prenomina actual</div>");		$des5->build();
		$des10 = new containerField("","<div style='background-color:#DDFFFF;padding:5px;'> ".anchor('nomina/prenom/calculaprenom','Recalcular Prenomina')." Recalcular montos de la prenomina de haber cambiado sueldos formulas</div>");		$des10->build();
		$des6 = new containerField("","<div style='background-color:#EEFFEE;padding:5px;'> ".anchor_popup('reportes/ver/PRENOM/-1','Ver Listado de Prenomina',$atts)." Este Muestra el listado de prenomina actual en formatos pdf. o .xls</div>");		$des6->build();
		//$des8 = new containerField("","<div style='background-color:#CCEEEE;padding:5px;'> ".anchor_popup('nomina/recibo/','Imprimir recibos de pago',$atts2)." Abre el modulo de impresion de recibos de pago, donde hay distintas opciones de impresion</div>");		//$des8->build();
		$des7 = new containerField("","<div style='background-color:#FFCCFF;padding:5px;'> ".anchor_popup('nomina/prenom/creanomi','<span id="pagar">Crear Nomina en Base a Prenomina</span>',$atts2)."Esta es la ultima operacion a realizar para una prenomina, la cual convierte la prenomina actual en una nomina.</div>");		$des7->build();
		$t=array();		$t[1][1]=$form->output;		$t[2][1]="<strong>Generar una Prenomina:</strong> </br>Se utiliza para calcular los saldos de un contrato y sus trabajadores para una fecha.</br> Este borra la prenomina anterior y genera la nueva prenomina con los nuevos datos, para luego introducir los valores necesarios por cada concepto de ser necesario.";
		$table = new DataTable(null,$t);		$table->cell_attributes = 'style="vertical-align:middle; text-align: center;"';		$table->per_row  = 2;		$table->cell_attributes = 'style="vertical-align:top;background-color:#DDFFFF;text-align: left;"';		$table->cell_template = "<#1#>";		$table->build();
		$t=array();
		$t[1][1]=$form1->output;		$t[2][1]="<strong>Recuperar Prenomina:</strong> </br>Esta opcion es para utilizar un prenomina previamente almacenada y continuar modificandola o terminarla.</br>Esta opcion borra los datos de la prenomina actual";
		$table2 = new DataTable(null,$t);		$table2->cell_attributes = 'style="vertical-align:middle; text-align: center;"';		$table2->per_row  = 2;		$table2->cell_attributes = 'style="vertical-align:top;background-color:#FFFFDD;text-align: left;"';		$table2->cell_template = "<#1#>";		$table2->build();
		$cant = new dropdownField("cant","cant");		$cant->status = 'create';
		for($i=1;$i<5;$i++)		$cant->option($i,$i);		$cant->style = "width:40px;";		$cant->build();
		$des9 = new containerField("","<div style='background-color:#EEEECC;padding:5px;'> ".$cant->output.' Copias de '.anchor('#','Recibos de Pago',$atts3)." Este genera los recibos de pagos para toda la prenomina.</div>");
		$des9->build();
		$data['content'] = $des0->output.$des2->output.$table->output.$des4->output.$des5->output.$des10->output.$table2->output.$des6->output.$des9->output.$des7->output;
		$data['title']   = ' '.$this->titulo.' ';
		$data["head"]    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}
	function respalda(){		$tabla   ='prenom';
		$contra = $this->datasis->dameval("SELECT * FROM $tabla GROUP BY contrato");		$this->db->query("DROP TABLE IF EXISTS PRENOM$contra");
		$bool = $this->db->query("CREATE TABLE PRENOM$contra SELECT * FROM $tabla");
		if($bool)echo "_Si";
	}
	function montos(){
		$this->rapyd->load('datagrid','fields','datafilter');
		$error='';
		if($this->input->post('pros')!==FALSE){
			$concepto =$this->input->post('concepto');
			$conceptoe=$this->db->escape($concepto);
			$pmontos  =$this->input->post('monto');

			//$this->load->library('pnomina');
			$formula=$this->datasis->dameval("SELECT formula FROM conc WHERE concepto=$conceptoe");
			foreach($pmontos AS $cod=>$cant){
				if(!is_numeric($cant)){
					$error.="$cant no es un valor num&erico;rico<br>";
				}else{
					$this->actualizaprenom($cod,$cant,$concepto,$formula);
					$this->calculaprenom(false,$cod);
					
				}
			}
		}
		$filter = new DataFilter("&nbsp;", 'prenom');
		$filter->error_string=$error;
		$filter->concepto = new dropdownField("Concepto", "concepto");		$filter->concepto->option("","Seleccionar");		$filter->concepto->options("SELECT concepto,CONCAT_WS(' ',concepto,descrip) descrip FROM prenom WHERE formula like '%XMONTO%' GROUP BY concepto ORDER BY descrip");		$filter->concepto->clause  ="where";		$filter->concepto->operator="=";		$filter->concepto->rule    = "required";
		$filter->buttons("reset","search");		$filter->build();
		$ggrid='';
		if ($filter->is_valid()){
			$ggrid =form_open('/nomina/prenom/montos/search/osp');
			$ggrid.=form_hidden('concepto', $filter->concepto->newValue);
			$monto = new inputField("Monto", "monto");			$monto->grid_name='monto[<#codigo#>]';			$monto->status   ='modify';			$monto->size     =12;			$monto->css_class='inputnum';
			$grid = new DataGrid("Concepto (".$filter->concepto->newValue.") ".$filter->concepto->options[$filter->concepto->newValue]);
			$grid->column("C&oacute;digo", "codigo");
			$grid->column("Nombre", "nombre");
			$grid->column("Monto" , $monto  ,'align=\'right\'');
			$grid->column("Valor" , 'valor' ,'align=\'right\'');
			$grid->submit('pros', 'Guardar',"BR");
			$grid->build();

			$ggrid.=$grid->output;
			$ggrid.=form_close();
		}
		$script ='
		<script type="text/javascript">
		$(function() {
			$(".inputnum").numeric(".");
		});
		</script>';
		$data['content'] = $filter->output.anchor('nomina/prenom','Inicio').$ggrid;
		$data['title']   = ' Asignaci&oacute;n de montos ';
		$data['script']  = $script;
		$data["head"]    = $this->rapyd->get_head().script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js");
		$this->load->view('view_ventanas', $data);
	}
	function formulas(){		$this->load->library('pnomina');
		$query = $this->db->query('SELECT * FROM conc');
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){				#echo $row->formula." = ";				$this->pnomina->evalform($row->formula);				#echo "\n";			}		}	}
	function tabla(){		$this->rapyd->load('datagrid','fields');		$contrato="'DIR01'";
		$ggrid =form_open('/nomina/prenom/montos/search/osp');		$ggrid.=form_hidden('concepto', 'alguno');
		$grid = new DataGrid("Asignaciones",'pretab');
		$grid->column("C&oacute;digo", "codigo");		$grid->column("Nombre", "nombre");
		$query = $this->db->query("SELECT descrip,concepto FROM itnoco WHERE codigo=$contrato ORDER BY concepto");
		foreach ($query->result() as $row){
			$ind = 'c'.trim($row->concepto);
			$campo = new inputField("Campo", $ind);
			$campo->grid_name=$ind.'[<#codigo#>]';
			$campo->status   ='modify';
			$campo->size     =12;
			$campo->css_class='inputnum';
			$grid->column($row->descrip , $campo,'align=\'center\'');		}		$grid->submit('pros', 'Guardar',"TR");		$grid->build();
		$ggrid.=$grid->output;		$ggrid.=form_close();
		$data['content'] = $ggrid;
		$data['title']   = ' Tabla de montos ';
		//$data['script']  = $script;
		$data["head"]    = $this->rapyd->get_head().script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js");
		$this->load->view('view_ventanas', $data);
	}
	function creanomi(){		$c = $this->datasis->dameval("SELECT COUNT(*) FROM prenom");
		$error='';		if($c > 0){
			$noco = $this->datasis->damerow("SELECT a.nombre,b.fecha,a.tipo FROM noco a JOIN prenom b ON a.codigo=b.contrato LIMIT 1");
			if(count($noco)>0){
				$descrip=$noco['nombre'];
				$mFECHAS=$this->datasis->periodo($noco['tipo'],$noco['fecha']);
				$descrip.=' Desde el '.dbdate_to_human($mFECHAS[0]).' Al '.dbdate_to_human($mFECHAS[1]);
				if($noco['tipo']=='S')
				$descrip.=" Semana del año: ".date('W',$noco['fecha']);
			}else{
				$descrip='';
			}
			$descripe=$this->db->escape($descrip);
			
			$mSQL  = "INSERT INTO nomi (fecha,status,descrip)values(now('%y%m%d'),'P',$descripe)";
			$this->db->query($mSQL);
			$nomi  = $this->db->insert_id();
			$query  = "
			INSERT INTO nomina (numero,frecuencia,contrato,codigo,nombre,concepto,tipo,descrip,formula     ,monto  ,fecha  ,  valor, estampa          ,fechap  ,trabaja,modo,orden,vari1,vari2,vari3,vari4,vari5,vari6,vari7,vari8)
			SELECT $nomi,b.tipo frecuencia ,a.contrato,a.codigo,a.nombre,a.concepto,a.tipo,a.descrip,a.formula,a.monto,a.fecha,a.valor,now('Ymd') estampa,a.fechap,a.trabaja,a.modo,a.orden,vari1,vari2,vari3,vari4,vari5,vari6,vari7,vari8
			FROM prenom a JOIN noco b ON a.contrato = b.codigo
			";
			if(!$this->db->query($query))				$error.="<div class='alert'>No se Pudo Guardar La nomina</div>";
			$modo=$this->datasis->dameval("SELECT modo FROM prenom LIMIT 1");
			
			if($modo==2){
				$query ="INSERT INTO asignomi (numero,codigoadm,fondo,codigopres,monto,denominacion)
				SELECT nomi,f.codigoadm,f.fondo,f.codigopres,SUM(valor),e.denominacion FROM (
				SELECT $nomi nomi,e.codigoadm,e.fondo,e.codigopres,(valor)
				FROM prenom a
				JOIN conc d ON a.concepto = d.concepto
				JOIN pers b ON a.codigo = b.codigo
				JOIN carg e ON b.cargo=e.cargo
				JOIN divi c ON b.divi = c.division
				WHERE a.tipo='A'
				) f
				LEFT JOIN v_presaldo e ON f.codigoadm=e.codigoadm AND f.fondo = e.fondo AND f.codigopres=e.codigo
				GROUP BY f.codigoadm,f.fondo,f.codigopres";	
			}else{
				//,IF(d.tipoa='A',IF(LENGTH(TRIM(b.fondo))>0,b.fondo,IF(LENGTH(TRIM(d.fondo))>0,d.fondo,c.fondo))),IF(d.tipoa='C',IF(LENGTH(TRIM(d.fondo))>0,d.fondo,c.fondo),IF(d.tipoa='P'	,b.fondo,c.fondo))) fondo
				$query ="INSERT INTO asignomi (numero,codigoadm,fondo,codigopres,monto,denominacion)
				SELECT nomi,f.codigoadm,f.fondo,f.codigopres,SUM(valor),e.denominacion FROM (
				SELECT $nomi nomi
				
				,IF(d.tipoa='A',IF(LENGTH(TRIM(b.codigoadm))>0,b.codigoadm,IF(LENGTH(TRIM(d.codigoadm))>0,d.codigoadm,c.codigoadm)),IF(d.tipoa='C',IF(LENGTH(TRIM(d.codigoadm))>0,d.codigoadm,c.codigoadm),IF(d.tipoa='P'	,b.codigoadm,'c.codigoadm'))) codigoadm
				
				,IF(LENGTH(TRIM(b.fondo))>0,b.fondo,IF(LENGTH(TRIM(d.fondo))>0,d.fondo,c.fondo)) fondo
				,IF(d.tipoa='A',IF(LENGTH(TRIM(b.codigopres))>0,b.codigopres,d.codigopres),IF(d.tipoa='C',d.codigopres,IF(d.tipoa='P'	,b.codigopres,''))) codigopres
				,(valor)
				FROM prenom a
				JOIN conc d ON a.concepto = d.concepto
				LEFT JOIN pers b ON a.codigo = b.codigo
				LEFT JOIN divi c ON b.divi = c.division
				WHERE a.tipo='A'
				AND a.valor<>0
				) f
				LEFT JOIN v_presaldo e ON f.codigoadm=e.codigoadm AND f.fondo = e.fondo AND f.codigopres=e.codigo
				GROUP BY f.codigoadm,f.fondo,f.codigopres
				ORDER BY f.codigoadm,f.codigopres";
				
			}
			if(!$this->db->query($query))
				$error.="<div calss='alert'>No se Pudieron Guardar Las asignaciones de nomina</div>";
			$query ="INSERT INTO retenomi (numero,cod_prov,monto,nombre)
			SELECT $nomi,c.cod_prov,SUM(-1*valor) a,d.nombre
			FROM prenom a
			JOIN conc c ON a.concepto = c.concepto
			JOIN sprv d ON c.cod_prov=d.proveed
			WHERE a.tipo='D' AND a.valor<>0
			GROUP BY c.cod_prov";
			if(!$this->db->query($query))
				$error.="<div calss='alert'>No se Pudieron Guardar Las Deducciones de nomina</div>";

			$query2 ="INSERT INTO otrosnomi (numero,cod_prov,monto,nombre,codigoadm,fondo,codigopres)
			SELECT $nomi,c.cod_prov,SUM(valor) a,d.nombre,c.codigoadm,c.fondo,c.codigopres
			FROM prenom a
			JOIN conc c ON a.concepto = c.concepto
			JOIN sprv d ON c.cod_prov=d.proveed
			WHERE a.tipo='O' AND a.valor<>0
			GROUP BY c.cod_prov";

			if(!$this->db->query($query2))
				$error.="<div calss='alert'>No se Pudieron Guardar Otros Conceptos de nomina</div>";
			$query ="UPDATE nomi SET
			asig=(SELECT SUM(valor)	FROM prenom a JOIN noco b ON a.contrato = b.codigo	JOIN conc c ON a.concepto = c.concepto	WHERE a.tipo='A' ),
			rete=(SELECT SUM(-1*valor)	FROM prenom a JOIN conc c ON a.concepto = c.concepto WHERE a.tipo='D')
			WHERE numero=$nomi";
			if(!$this->db->query($query))
				$error.="<div calss='alert'>No se Pudieron Actualizar las asignaciones y deducciones de nomina</div>";
		}else{
			$error.="<div class='alert'>No hay prenomina generada</div>";
		}
		if(!empty($error))$salida=$error;
		else redirect("nomina/nomi/dataedit/show/$nomi");
		$data['content'] = $salida;
		$data['title']   = ' Crear Nomina ';
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	function prueba(){
		$tipo   = $this->datasis->dameval("SELECT tipo FROM noco WHERE codigo=(SELECT contrato FROM prenom LIMIT 1) LIMIT 1");
		$fcorte = $this->datasis->dameval("SELECT fecha FROM prenom LIMIT 1");
		$f      = explode('-',$fcorte);
		switch($tipo){
			case 'S':return 1;break;
			case 'M':{
				if ( date('d',strtotime($fcorte)) > 15 )
					$fechaInicio = date("15-m-Y", mktime(0, 0, 0, $f[1]-1, 15, $f[0]));
				else
					$fechaInicio = date('01-m-Y',mktime(0, 0, 0, $f[1]-1, 01, $f[0]));
			}break;
			case 'Q':{
				if ( date('d',strtotime($fcorte)) > 15 )
					$fechaInicio = date('15-m-Y',strtotime($fcorte));
				else
					$fechaInicio = date("01-m-Y", strtotime($fcorte));
			}break;
		}
		$fechaInicio  =strtotime( $fechaInicio                    );
		$fcorte       =strtotime( date('d-m-Y',strtotime($fcorte)));
		$c=0;
		for($i=$fechaInicio; $i<=$fcorte; $i+=86400)
			if(date("l", 1*$i)=="Monday")$c++;
		return $c;
	}
	function actualizaprenom($codigo,$cant,$concepto,$formula,$modo=1){
		$this->pnomina->CODIGO=$codigo;
		$this->pnomina->MONTO =$cant;
		$this->pnomina->modo =$modo;
		$valor=$this->pnomina->evalform($formula);
		$data     = array('monto' => $cant,'valor'=>$valor,'formula'=>$formula);
		$concepto =$this->db->escape("$concepto");
		$codigo   = $this->db->escape("$codigo");
		$where    = "codigo = $codigo AND concepto =$concepto ";
		$mSQL     = $this->db->update_string('prenom', $data, $where);
		$this->db->query($mSQL);
	}
	function calculaprenom($redirect=true,$persona=false){
		$where ="";
		if($persona)
			$where="WHERE  codigo=".$this->db->escape($persona);
		$query = $this->db->query("SELECT codigo,concepto,formula,monto,modo FROM prenom $where ORDER BY 1*orden, (formula LIKE '%ASIGNA()%')");

		foreach ($query->result() as $row){
			$codigo   = $row->codigo;
			$monto    = $row->monto;
			$concepto = $row->concepto;
			$modo     = $row->modo;
			$formula  = $this->datasis->dameval("SELECT formula FROM conc WHERE concepto='".$row->concepto."'");
			$this->actualizaprenom($codigo,$monto,$concepto,$formula,$modo);
		}
		if($redirect)
			redirect('nomina/prenom');
	}
  
	function instalar(){
		$query="ALTER TABLE `prenom` ADD COLUMN `modo` CHAR(1) NULL DEFAULT NULL AFTER `pprome`";
		$this->db->simple_query($query);
		$query="ALTER TABLE `nomina` ADD COLUMN `modo` CHAR(1) NOT NULL AFTER `total`";
		$this->db->simple_query($query);
		$query="ALTER TABLE prenom ADD orden INTEGER";
		$this->db->simple_query($query);
		$query="ALTER TABLE nomina ADD orden INTEGER";
		$this->db->simple_query($query);
		$mSQL="ALTER TABLE `prenom` CHANGE COLUMN `formula` `formula` TEXT NULL DEFAULT NULL ";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `prenom` CHANGE COLUMN `nombre` `nombre` VARCHAR(255) NULL DEFAULT NULL AFTER `codigo`";
		$this->db->simple_query($mSQL);
		$query="ALTER TABLE prenom ADD COLUMN`vari1` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE prenom ADD COLUMN`vari2` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE prenom ADD COLUMN`vari3` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE prenom ADD COLUMN`vari4` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE prenom ADD COLUMN`vari5` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE prenom ADD COLUMN`vari6` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE prenom ADD COLUMN`vari7` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE prenom ADD COLUMN`vari8` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE nomina ADD COLUMN`vari1` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE nomina ADD COLUMN`vari2` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE nomina ADD COLUMN`vari3` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE nomina ADD COLUMN`vari4` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE nomina ADD COLUMN`vari5` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE nomina ADD COLUMN`vari6` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE nomina ADD COLUMN`vari7` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		$query="ALTER TABLE nomina ADD COLUMN`vari8` VARCHAR(50) NULL DEFAULT NULL";
		$this->db->simple_query($query);
	}
}
?>
