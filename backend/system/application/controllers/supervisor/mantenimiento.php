<?php
class Mantenimiento extends Controller{
	
	var $url='supervisor/mantenimiento/';
	function Mantenimiento(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){		
		$list = array();
		$list[]=anchor('supervisor/mantenimiento/bmodbus'         ,'Vaciar la tabla ModBus');
		$list[]=anchor('supervisor/mantenimiento/centinelas'      ,'Centinelas');
		$list[]=anchor('supervisor/mantenimiento/actualizasvn'    ,'Actualiza Tortuga');
		$list[]=anchor('supervisor/mantenimiento/enviadata'       ,'Enviar Data');
		$list[]=anchor('supervisor/mantenimiento/recupera'        ,'Ejecutar script de reparacion');
		$list[]=anchor('supervisor/mantenimiento/reparatabla'     ,'Reparar Tablas');
		$list[]=anchor('supervisor/mantenimiento/crea_item_orden' ,'Descargar item_orden.dbf');
		$list[]=anchor('supervisor/mantenimiento/crea_orden_pago' ,'Descargar orden_pago.dbf');
		$list[]=anchor('supervisor/mantenimiento/respaldo'        ,'Descargar base de datos .zip');
		$list[]=anchor('supervisor/mantenimiento/view_pres'       ,'Crear view_pres');
		$list[]=anchor('supervisor/mantenimiento/recalculo'       ,'CAll sp_recalculo()');
		$list[]=anchor('supervisor/mantenimiento/pasa'            ,'Pasa de Tortuga a Prueba');
		$list[]=anchor('supervisor/mantenimiento/carga'            ,'Sube DBF');
		
		$attributes = array(
			'class' => 'boldlist',
			'id'    => 'mylist'
			);

		$out=ul($list, $attributes);
		$data['content'] = $out;
		$data["head"]    = script("jquery.pack.js").script("jquery.treeview.pack.js").$this->rapyd->get_head().style('jquery.treeview.css');
		$data['title']   = ' Mantenimiento ';
		$this->load->view('view_ventanas', $data);
	}
	
	function bmodbus(){
		$mSQL="DELETE FROM sitems WHERE MID(numa,1,1)='_' AND fecha<CURDATE()";
		$this->db->simple_query($mSQL);
		$mSQL="TRUNCATE modbus";
		$this->db->simple_query($mSQL);
		redirect('mantenimiento');
	}
	
	function centinelas(){
		$this->load->helper('directory');
		$this->load->library('table');
		$tmpl = array('row_start' => '<tr valign="top">');
		$this->table->set_template($tmpl); 

		$map = directory_map('./system/logs/');
		$lista=array();
		foreach($map AS $file) {
			if($file!='index.html')
				$lista[]=anchor("supervisor/mantenimiento/borracentinela/$file",'X')." <a href='javascript:void(0)' onclick=\"carga('$file')\" >$file</a>";
		}
		$copy="<br><a href='javascript:void(0)' class='mininegro'  onclick=\"copiar()\" >Copiar texto</a>";
		$tadata = array(
		          'name'    => 'sql',
		          'id'      => 'log',
		          'rows'    => '20',
		          'cols'    => '60'
		        );
		
		$form= form_open('ejecutasql/filteredgrid/process').form_textarea($tadata).'<br>'.form_submit('mysubmit', 'Ejecutar como SQL').form_close();
		$this->table->add_row(ul($lista), '<b id="fnom">Seleccione un archivo de centinela</b><br>'.$form);
		$link=site_url('supervisor/mantenimiento/vercentinela');
		$data['script']  ="<script>
		  function carga(arch){
		    link='$link'+'/'+arch;
		    //alert(link);
		    $('#fnom').text(arch);
		    $('#log').load(link);
		  };
		  function copiar(){
		    $('#log').copy();
		  };
		</script>";
		
		$data['content'] = $this->table->generate();
		$data['title']   = " Centinelas ";
		//script('plugins/jquery.clipboard.pack.js')
		$data["head"]    =  script("jquery.pack.js").script('plugins/jquery.copy.min.js').$this->rapyd->get_head().style('marcos.css').style('estilos.css');
		$this->load->view('view_ventanas', $data);
	}
	
	function vercentinela($file=NULL){
		if(empty($file)) return FALSE;
		$this->load->helper('file');
		$string = read_file("./system/logs/$file");
		$string = $string;
		echo $string;
	}
	
	function borracentinela($file=NULL){
		if(!empty($file)){
			$this->load->helper('file');
			unlink("./system/logs/$file");
		}
		redirect('supervisor/mantenimiento/centinelas');
	}

	function actualizasvn(){
		if (!extension_loaded('svn')) {
				$data['content'] = 'La extension svn no esta cargada, debe cargarla para poder usar estas opciones';
			}else{
				$dir=getcwd();
				$svn=$dir.'/.svn';
				
				if(!is_writable($svn)){
					$data['content']= 'No se tiene permiso al directorio .svn, comuniquese con soporte t&eacute;cnico';
				}else{
					$aver=0; //<-- falta consultar la version actual
					$ver =svn_update($dir);
					
					if($ver>0){
						if($ver>$aver){
							$data['content'] = 'Actualizado a la versi&oacute;n: '.$ver;
						}else{
							$data['content'] = 'Ya estaba la ultima versi&oacute;n instalada '.$arr['revision'];
						}
					}else{
						$data['content'] = 'Hubo problemas con la actualizaci&oacute;n, comuniquese con soporte t&eacute;cnico';
					}
			}
		}
	
		$data['title']   = 'Actualizacion de ProteoERP desde el svn';
		$data['head']    = '';
		$this->load->view('view_ventanas', $data);
	}

	function enviadata(){
		$user           = $this->db->username;
		$passwd         = $this->db->password;
		$database       = $this->db->database;
		$enviadatadb    = $this->datasis->traevalor('enviadatadb'    );
		$enviadatahost  = $this->datasis->traevalor('enviadatahost'  );
		$enviadatauser  = $this->datasis->traevalor('enviadatauser'  );
		$enviadatapasswd= $this->datasis->traevalor('enviadatapasswd');
		
		echo " enviando..";
		set_time_limit(0);
		system("mysqldump -u $user --routines --ignore-table view_pres --opt $database ".(!empty($passwd) ?" -p $passwd ":"")." | mysql -u $enviadatauser ".(!empty($enviadatapasswd) ?" -p $passwd ":"")." -h $enviadatahost -D $enviadatadb",$return);
		redirect('supervisor/mantenimiento');
		
	}

	function reparatabla(){
		$this->load->dbutil();
		$tables = $this->db->list_tables();
		foreach ($tables as $table){
			$this->dbutil->repair_table($table);
		}
		redirect('supervisor/mantenimiento');
	}

	function recupera(){
		$cliente = $this->datasis->traevalor('cliente');
		switch($cliente){
			case 'independencia':{
				
			}break;
		}
	}
	
	function subedbf_sipres(){
		$this->load->dbforge();
			$tabla="DPRESU04";
		if (extension_loaded('dbase')) {
			$db = dbase_open("/home/ender/www/htdocs/COMPRO2.DBF", 0);
			$this->dbforge->drop_table($tabla);
			
			if ($db) {
				echo "habrio";
				$cols=dbase_numfields($db);
				$rows=dbase_numrecords($db);
				//$row=dbase_get_record($db,10);
				$row=dbase_get_record_with_names($db,10);
				foreach($row AS $key=>$value){
					$fields[$key] = array('type' => 'TEXT');
				}
				
				$this->dbforge->add_field($fields);
				$this->dbforge->create_table($tabla);
				
				$insert =array();
				for($i=0;$i<=$rows;$i++){
					$r=dbase_get_record_with_names($db,$i);
					foreach($row AS $key=>$value){
						echo "inserto";
						$insert[$key]=$r[$key];
					}
					$this->db->insert($tabla, $insert);
				}
				
				dbase_close($db);
			}else{
				echo "No pudo abrir el archivo";
			}
			
			}else{
			echo 'Debe cargar las librerias dbase para poder usar este modulo';
		}
	}
	
	function crea_item_orden(){
		$this->load->dbforge();
		if (extension_loaded('dbase')) {
			$item_orden=array(
			array('nro_op'    ,'N'  ,8  ,0),
			array('cod_part'  ,'C'  ,20   ),
			array('desc_pres' ,'C'  ,200  ),
			array('monto_pres','N'  ,16 ,2)
			);
			
			if(!dbase_create('/tmp/item_orden.dbf', $item_orden)){
				echo "Error, can't create the database\n";
			}else{
				$item_orden = dbase_open('/tmp/item_orden.dbf', 2);
				$query="SELECT a.numero,CONCAT(REPLACE(a.codigoadm,'.',''),REPLACE(a.codigopres,'.','')) codigo,b.denominacion,a.importe FROM v_pagos a LEFT JOIN v_presaldo b ON a.codigoadm=b.codigoadm AND a.fondo=b.fondo AND a.codigopres=b.codigo WHERE MID(status,2,1)=2";
				$mSQL = $this->db->query($query);
				$datos=array();
				foreach($mSQL->result() AS $row){
					$datos[0]=$row->numero;
					$datos[1]=$row->codigo;
					$datos[2]=$row->denominacion;
					$datos[3]=$row->importe;
					dbase_add_record($item_orden,$datos);
				}
				dbase_close($item_orden);
			}
		}
		
		$this->load->helper('download');
		$data = file_get_contents("/tmp/item_orden.dbf"); // Read the file's contents
		$name = 'item_orden.dbf';

		force_download($name, $data); 
		unlink("/tmp/item_orden.dbf");
	}

	function crea_orden_pago(){
		$this->load->dbforge();
		if (extension_loaded('dbase')) {
			$item_orden=array(
			array('nro_op'    ,'N'  ,8  ,0),
			array('orden_nro' ,'C'  ,8    ),
			array('tipo_pres' ,'C'  ,20   ),
			array('fecha_op'  ,'C'  ,10   ),
			array('benef_op'  ,'C'  ,50   ),
			array('cedb_op'   ,'C'  ,15   ),
			array('autor1_op' ,'C'  ,50   ),
			array('compr_op'  ,'C'  ,20   ),
			array('monto_op'  ,'N'  ,10,2 ),
			array('descrip_op','C'  ,1000 ),
			);
			
			if(!dbase_create('/tmp/orden_pago.dbf', $item_orden)){
				echo "Error, can't create the database\n";
			}else{
				$item_orden = dbase_open('/tmp/orden_pago.dbf', 2);
				$query="SELECT a.numero,a.numero numero2,a.fondo,a.fecha,c.nombre,c.rif,c.contacto,a.compromiso,a.total2,a.observa
				FROM v_pagos a 
				JOIN sprv c ON a.cod_prov=c.proveed
				LEFT JOIN v_presaldo b ON a.codigoadm=b.codigoadm AND a.fondo=b.fondo AND a.codigopres=b.codigo
				WHERE MID(status,2,1) IN (2,3) 
				GROUP BY a.numero";
				$mSQL = $this->db->query($query);
				$datos=array();
				foreach($mSQL->result() AS $row){
					$datos[0]=$row->numero;
					$datos[1]=$row->numero2;
					$datos[2]=$row->fondo;
					$datos[3]=$row->fecha;
					$datos[4]=$row->nombre;
					$datos[5]=$row->rif;
					$datos[6]=$row->contacto;
					$datos[7]=$row->compromiso;
					$datos[8]=$row->total2;
					$datos[9]=$row->observa;
					dbase_add_record($item_orden,$datos);
				}
				dbase_close($item_orden);
			}
		}
		
		$this->load->helper('download');
		$data = file_get_contents("/tmp/orden_pago.dbf"); // Read the file's contents
		$name = 'orden_pago.dbf';

		force_download($name, $data); 
		unlink("/tmp/orden_pago.dbf");
	}

	function respaldo(){
		$this->load->library('zip');
		$host= $this->db->hostname;
		$db  = $this->db->database;
		$pwd = $this->db->password;
		$usr = $this->db->username;

		$file= $fname = tempnam('/tmp',$db.'.sql');
		$cmd="mysqldump -u $usr -p$pwd -h $host --opt --routines $db > $file";
		$sal=exec($cmd);
		$this->zip->read_file($file);
		$this->zip->download($db.date('Y-m-d_H:i:s').'.zip');
		
		unlink($file);
	}
	
	function view_pres(){
		$query="call sp_view_pres()";
		$this->db->query($query);
		redirect('supervisor/mantenimiento');
	}
	
	function recalculo(){
		$query="call sp_recalculo()";
		$this->db->query($query);
		redirect('supervisor/mantenimiento');
	}
	
	function pasa(){

		$host= $this->db->hostname;
		$db  = $this->db->database;
		$pwd = $this->db->password;
		$usr = $this->db->username;

		$cmd="mysqldump -u $usr -p $pwd -h $host --opt --routines tortuga | mysql -D prueba -u $usr -p $pwd -h $host ";


	}
	
	function carga(){
		$salida='';
		//ini_set('upload_max_filesize','2000M');
		$salida.=form_open_multipart($this->url.'guarda');
		$salida.=form_upload("archivo","archivo");
		$salida.=form_submit('subir'  , 'Subir Archivos');
		$salida.=form_close();
		
		$data['content']=$salida;
		$data['title']  ='Importar Archivos .dbf Archivos dbf.';
		$this->load->view('view_ventanas', $data);
	}
	
	function guarda(){
		//ini_set('upload_max_filesize','2000M');
		
		$name=$_FILES['archivo']['name'];
		move_uploaded_file($_FILES['archivo']['tmp_name'], 'uploads/'.$name);
		$this->subedbf($name);
	}
	
	function subedbf($file='DATONO02.DBF',$insertar=true){
		set_time_limit(3600);
		$this->load->dbforge();
		$this->load->dbutil();
		
		$filea      =explode('.',$file);
		$name       =$filea[0];
		$ext        =$filea[1];
		$uploadsdir =getcwd().'/uploads/';
		$filedir    =$uploadsdir.$file;
		
		$tabla=strtoupper($ext.$name);
		
		if (extension_loaded('dbase')) {
			$db = @dbase_open($filedir, 0);
			
			$this->dbforge->drop_table($tabla);
			if ($db) {
				$cols=@dbase_numfields($db);
				$rows=@dbase_numrecords($db);
				$row=@dbase_get_record_with_names($db,10);
				print_r($row);
				count($row);
				if(is_array($row)>0){
					foreach($row AS $key=>$value){
						$fields[trim($key)] = array('type' => 'TEXT');
					}
					//print_r($fields);
					//exit();
					
					@$this->dbforge->add_field($fields);
					@$this->dbforge->create_table($tabla);
					
					if($insertar){
						$insert =array();
						for($i=0;$i<=$rows;$i++){
							$r=@dbase_get_record_with_names($db,$i);
							foreach($row AS $key=>$value){
								$a=@utf8_encode(trim($r[trim($key)]));
								
								$insert[trim($key)]=$a;
							}
							
							@$this->db->insert($tabla, $insert);
							echo 1;
						}
					}
					
					@dbase_close($db);
				}
			}else{
				echo "No pudo abrir el archivo";
			}
			
			}else{
			echo 'Debe cargar las librerias dbase para poder usar este modulo';
		}
	
	}
	
	function subedbf_todouploads($insertar='S'){
		$directorio = opendir("/var/www/tortuga/uploads/"); //ruta actual
		while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
		{
			if (is_dir($archivo))//verificamos si es o no un directorio
			{
			}
			else
			{
				echo $archivo . "\n";
				if($insertar!='S')
				$this->subedbf($archivo,false);
				else
				$this->subedbf($archivo);
				
				echo $archivo . "\n";
			}
		}

	}
}
