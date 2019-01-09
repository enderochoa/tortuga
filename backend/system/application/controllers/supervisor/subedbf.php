<?php
class subedbf extends Controller{

	var $url='supervisor/subedbf/';

	function subedbf(){
		parent::Controller();
		$this->load->helper(array('form'));
	}

	function index(){
		redirect($this->url.'carga');
	}

	function carga(){
		$salida='';
		//ini_set('upload_max_filesize','2000M');
		$salida.=form_open_multipart($this->url.'guarda');
		$salida.=form_upload("archivo","archivo");
		$salida.=form_submit('subir'  , 'Subir Archivos');
		$salida.=form_close();

		$data['content']=$salida;
		$data['title']  ='subedbfr Archivos .dbf Archivos dbf.';
		$this->load->view('view_ventanas_sola', $data);
	}

	function guarda(){
		$name=$_FILES['archivo']['name'];
		move_uploaded_file($_FILES['archivo']['tmp_name'], 'uploads/'.$name);
		$this->subedbff($name);
	}

	function hola(){
		$this->load->library('upload');
		$config['upload_path']   = './uploads/';
		$this->upload->initialize($config);
		$data = $this->upload->data();
		print_r($data);

	}

	function subedbff($file='TRABAJAD.DBF'){
		//nota:hay que modificar el upload_max_file_size=100M del php.ini
		//nota:cambiar el tamanano de pres.nacinal a 15 caracteres
		//nota:cambiar tamano de carg.descrip a tamano de 100
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
			$db = dbase_open($filedir, 0);

			$this->dbforge->drop_table($tabla);
			if ($db) {
				$cols=dbase_numfields($db);
				$rows=dbase_numrecords($db);
				$row=dbase_get_record_with_names($db,10);
				foreach($row AS $key=>$value){
					$fields[trim($key)] = array('type' => 'TEXT');
				}
				//print_r($fields);
				//exit();

				$this->dbforge->add_field($fields);
				$this->dbforge->create_table($tabla);

				$insert =array();
				for($i=0;$i<=$rows;$i++){
					$r=dbase_get_record_with_names($db,$i);
					foreach($row AS $key=>$value){
						$a=utf8_encode(trim($r[trim($key)]));

						$insert[trim($key)]=$a;
					}

					$this->db->insert($tabla, $insert);

				}
				echo $i;

				dbase_close($db);
			}else{
				echo "No pudo abrir el archivo";
			}

			}else{
			echo 'Debe cargar las librerias dbase para poder usar este modulo';
		}

	}

}
?>
