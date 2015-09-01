<?php
class tablas extends Controller {
	
	var $url="doc/tablas/";
	
	function tablas(){
		parent::Controller(); 
		$this->load->library("rapyd");
		
	}
	
	#### index #####
	function index()
	{
		redirect("doc/tablas/filteredgrid");
	}

	 ##### DataFilter + DataGrid #####
	 
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
 
		$filter = new DataFilter("Filtro de Tablas");
		$filter->db->from('doc_tablas');
		$filter->db->join('doc_campos','doc_tablas.nombre=doc_campos.tabla');
		$filter->db->groupby('doc_tablas.nombre');
				
		$filter->nombre = new inputField("Nombre", "nombre");
		$filter->nombre->size   = 20;
		
		$filter->campo = new inputField("Campo", "campo");
		$filter->campo->size=20;
		
		$filter->buttons("reset","search");
		$filter->build();
    
    	$uri = anchor('doc/tablas/dataedit/show/<#nombre#>','<#nombre#>');

		$grid = new DataGrid("Lista de Tablas");
		$grid->order_by("nombre","asc");                          
		$grid->per_page = 15;

		$grid->column("Nombre",$uri );
		$grid->column("Campo"             ,"campo");
		$grid->column("Descripci&oacute;n","referen");
		$grid->column("Comentario campo","dcomment");
        		
		$grid->add($this->url."dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "Documentaci&oacute;n de Tablas";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	##### dataedit ##### 
	function dataedit()
 	{
 		$this->rapyd->load('dataobject','datadetails');
				
		$do = new DataObject("doc_tablas");
		$do->rel_one_to_many('doc_campos', 'doc_campos', array('nombre'=>'tabla'));	

		$edit = new DataDetails("Documentaci&oacute;n de Tablas", $do);
		$edit->back_url = site_url($this->url."filteredgrid");
		$edit->set_rel_title('doc_campos','Rubro <#o#>');
		
		$edit->pre_process('insert'  ,'_valida');
		$edit->pre_process('update'  ,'_valida');
				
		$edit->nombre= new inputField("Nombre", "nombre");
		$edit->nombre->mode = "autohide";
		$edit->nombre->size = 15;
		
		$edit->referen   = new textareaField("Descripcion", "referen");
		$edit->referen->size = 50;
		$edit->referen->rows = 2;
		$edit->referen->cols = 90;
		
		$edit->itcampo = new inputField("(<#o#>) Campo","itcampo_<#i#>");
		$edit->itcampo->db_name='campo';
		$edit->itcampo->rel_id ='doc_campos';				
		$edit->itcampo->rule   ='required|callback_chexiste';
		$edit->itcampo->size   =15;
		
		$edit->ittype = new inputField("(<#o#>) Type","ittype_<#i#>");
		$edit->ittype->db_name='dtype';
		$edit->ittype->rel_id ='doc_campos';
		$edit->ittype->size   =15;
		
		$edit->itnull = new inputField("(<#o#>) Null","itnull_<#i#>");
		$edit->itnull->db_name='dnull';
		$edit->itnull->rel_id ='doc_campos';
		$edit->itnull->size   =3;
		
		$edit->itkey = new inputField("(<#o#>) key","itkey_<#i#>");
		$edit->itkey->db_name='dkey';
		$edit->itkey->rel_id ='doc_campos';
		$edit->itkey->size   =3;
		
		$edit->itdefault = new inputField("(<#o#>) Default","itdefault_<#i#>");
		$edit->itdefault->db_name='ddefault';
		$edit->itdefault->rel_id ='doc_campos';
		$edit->itdefault->size   =5;
		
		$edit->itextra = new inputField("(<#o#>) Extra","itextra_<#i#>");
		$edit->itextra->db_name='dextra';
		$edit->itextra->rel_id ='doc_campos';
		$edit->itextra->size   =10;
		
		$edit->itdcomment = new textareaField("(<#o#>) Comentario","itdcomment_<#i#>");
		$edit->itdcomment->db_name='dcomment';
		$edit->itdcomment->rel_id ='doc_campos';
		$edit->itdcomment->cols   =40;
		$edit->itdcomment->rows   =2; 
			
		$edit->buttons("modify", "save", "undo", "back","add_rel");
		$edit->build();
		
		$conten["form"]  =&$edit;
		$data['content'] = $this->load->view('view_tablas', $conten,true);
		$data['title']   = "Documentaci&oacute;n de Tablas";
		$data["head"]    = $this->rapyd->get_head().script('jquery.js');
		$this->load->view('view_ventanas', $data);
	}
	
	function chexiste(){
		$nombre = $this->input->post('referen');
			
		//$this->validation->set_message('chexiste',"pp esto erud $nombre");	
		//return false;
	}
	
	function _valida($do){
		//$do->get_all();
		//$user = $this->datasis->damerow("SELECT * FROM usuario WHERE  us_codigo='123'");
		//$user = $this->datasis->dameval("SELECT nombre FROM usuario WHERE  us_codigo='123'");
		//$do->set('referen',$user);
		//
		//$error='';
        //
		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
	} 
	
	
	function traer(){
		$mSQL=$this->db->query("SHOW TABLE STATUS");
		foreach($mSQL->result() AS $row){
			$nombree = $this->db->escape($row->Name);
			$commente= $this->db->escape($row->Comment);
			$this->db->query("INSERT IGNORE INTO doc_tablas (`nombre`,`referen`) value ($nombree,$commente)");
			$mSQL2=$this->db->query("SHOW FULL COLUMNS FROM $row->Name");
			foreach($mSQL2->result() AS $row2){
				$c=0;
				$row->Namee     =$this->db->escape( $row->Name    );
				$row2->Fielde   =$this->db->escape( $row2->Field  );
				$c = $this->datasis->dameval("SELECT COUNT(*) FROM doc_campos WHERE tabla=$row->Namee AND campo=$row2->Fielde");
				if($c>0){
					$typee     =$this->db->escape( $row2->Type   );
					$nulle     =$this->db->escape( $row2->Null   );
					$keye      =$this->db->escape( $row2->Key    );
					$defaulte  =$this->db->escape( $row2->Default);
					$extrae    =$this->db->escape( $row2->Extra  );
					$namee     =$this->db->escape( $row2->Name   );
					$fielde    =$this->db->escape( $row2->Field  );

					$this->db->query("UPDATE doc_campos SET dtype=$typee,dnull=$nulle,dkey=$keye,ddefault=$defaulte,dextra=$extrae 
					WHERE tabla=$namee AND campo=$fielde");
				}else{
					$row->Name     =$this->db->escape(             $row->Name             );
					$row2->Field   =$this->db->escape(               $row2->Field         );
					$row2->Type    =$this->db->escape(              $row2->Type           );
					$row2->Null    =$this->db->escape(              $row2->Null           );
					$row2->Key     =$this->db->escape(             $row2->Key             );
					$row2->Default =$this->db->escape(                 $row2->Default     );
					$row2->Extra   =$this->db->escape(               $row2->Extra         );
					$row2->Comment =$this->db->escape(                 $row2->Comment     );
					$this->db->query("INSERT INTO doc_campos (`tabla`,`campo`,`dtype`,`dnull`,`dkey`,`ddefault`,`dextra`,`dcomment`) 
					value ($row->Name,$row2->Field,$row2->Type,$row2->Null,$row2->Key,$row2->Default,$row2->Extra,$row2->Comment)");
				}
		  	}
  		}
	}
	
	function cambiaautf8(){
		$mSQL=$this->db->query("select * from doc_tablas WHERE MID(nombre,1,2)<>'v_' AND MID(nombre,1,4)<>'view' AND nombre REGEXP BINARY '^[a-z]+$'");
		foreach($mSQL->result() AS $row){
			$query="alter table $row->nombre  charset = utf8";
			$this->db->query($query);
		}
			//$mSQL2=$this->db->query("SHOW FULL COLUMNS FROM $row->Name");
			$mSQL2=$this->db->query("SELECT * FROM doc_campos WHERE dtype LIKE 'varchar%' OR dtype LIKE '%text%' OR dtype LIKE 'char%'");
			foreach($mSQL2->result() AS $row2){
				$query="update $row2->tabla set $row2->campo = convert( convert($row2->campo using binary) using utf8)";
				$this->db->query($query);
				$query= "ALTER TABLE $row2->tabla CHANGE `$row2->campo` `$row2->campo` $row2->dtype CHARACTER SET utf8 COLLATE
utf8_general_ci COMMENT '$row2->dcomment'";
				
				$this->db->query($query);
				
			}	
  	//}
	}

	function instalar(){
		$this->db->simple_query("
		CREATE TABLE `doc_campos` (
		  `tabla` varchar(64) NOT NULL,
		  `campo` varchar(64) NOT NULL,
		  `type` varchar(45) DEFAULT NULL,
		  `null` varchar(45) DEFAULT NULL,
		  `key` varchar(45) DEFAULT NULL,
		  `default` varchar(45) DEFAULT NULL,
		  `extra` varchar(45) DEFAULT NULL,
		  PRIMARY KEY (`tabla`,`campo`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
		
		$this->db->simple_query("
		CREATE TABLE `doc_tablas` (
		  `nombre` varchar(64) NOT NULL,
		  `referen` text,
		  PRIMARY KEY (`nombre`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8
		");
	
	}
  
}
?>


