<?php
class Subetxt extends Controller{
	
	var $url='supervisor/subetxt/';
	
	function Subetxt(){
		parent::Controller();
		$this->load->helper(array('form'));
		$this->load->library("rapyd");
	}

	function index(){
		redirect($this->url.'carga');
	}
	
	function carga(){
		
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
			'titulo'  =>'Buscar Bancos'
			 );

		$bBANC=$this->datasis->modbus($mBANC);
		
		$btn='<img src="/tortuga/assets/default/images/system-search.png"  alt="Busqueda de Bancos" title="Busqueda de Bancos" border="0" />';
		
		$salida='';
		//ini_set('upload_max_filesize','2000M');
		$salida.=form_open_multipart($this->url.'guarda');
		$salida.="<strong>Banco  </strong>";
		$salida.=form_dropdown('codbanc',$this->datasis->consularray("SELECT codbanc,CONCAT(codbanc,' ',banco,' ||| ',b.nomb_banc,' ',MID(a.numcuent,-4)) a,numcuent FROM banc a JOIN tban b ON a.tbanco=b.cod_banc"));
		$salida.=$btn.br();
		$salida.="<strong>Archivo</strong>";
		$salida.=form_upload("archivo","archivo");
		$salida.=form_submit('subir'  , 'Subir Archivos');
		
		$salida.=form_close();
		
		$data['content'] =$salida;
		$data["head"]    = script('jquery.js');
		$data['title']   ='Importar Archivos de Texto, Archivos txt, csv';
		$this->load->view('view_ventanas', $data);
	}
	
	function guarda(){
		$name   =$_FILES['archivo']['name'];
		move_uploaded_file($_FILES['archivo']['tmp_name'], 'uploads/'.$name);
		$codbanc=$_POST['codbanc'];
		$codbance=$this->db->escape($codbanc);
		
		$cod_banc = $this->datasis->dameval("SELECT tbanco FROM banc WHERE codbanc=$codbance");
		
		switch($cod_banc){
			case 'VEN' :$this->subeven($name,$codbanc);
		}
	}
	
	function subeven($file='',$codbanc){
		$filedir    =getcwd().'/uploads/'.$file;
		$e      =array();
		$handle = fopen ($filedir,"r");
		while(($row = fgetcsv($handle,9999999,","))!==FALSE){
			$e[]=$row;
		}
		fclose($handle);
		
		$tipo_doc='';
		foreach($e as $d){
			switch($d[6]){
				case 'NOTA DE DEBITO':$tipo_doc='ND';
				case 'NOTA DE CREDITO':$tipo_doc='NC';
				case 'CHEQUE':$tipo_doc='CH';
				case 'DEPOSITO':$tipo_doc='DP';
			}
			
			if(strlen($tipo_doc)>0 && strlen($d[0])>0 && strlen($d[1])>0)
			$this->inserta($codbanc,$tipo_doc,$d[1],$d[0],$d[2],$d[3],$d[4],$d[5]);
		}
	}
	
	function inserta($codbanc,$tipo_doc,$cheque,$fecha,$observa='',$debe=0,$haber=0,$saldo=0){
		$fecha =human_to_dbdate($fecha);
		$data=array(
			'codbanc' =>$codbanc ,
			'fecha'   =>$fecha   ,
			'cheque'  =>$cheque  ,
			'observa' =>$observa ,
			'debe'    =>$debe    ,
			'haber'   =>$haber   ,
			'saldo'   =>$saldo   ,
			'tipo_doc'=>$tipo_doc
			);
		$this->db->delete('concilia',array('codbanc'=>$codbanc,'cheque'=>$cheque));
		$this->db->insert('concilia',$data);
	}

	function instalar(){
		$query="
		CREATE TABLE `concilia` (
			`codbanc` VARCHAR(10) NULL DEFAULT NULL,
			`tipo_doc` CHAR(2) NULL DEFAULT NULL,
			`cheque` VARCHAR(50) NULL DEFAULT NULL,
			`fecha` DATE NULL DEFAULT NULL,
			`observa` TEXT NULL,
			`debe` DECIMAL(19,2) NULL DEFAULT '0.00',
			`haber` DECIMAL(19,2) NULL DEFAULT '0.00',
			`saldo` DECIMAL(19,2) NULL DEFAULT '0.00',
		PRIMARY KEY (`codbanc`, `cheque`, `tipo_doc`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT";
		$this->db->simple_query($query);
	}
}
?>
