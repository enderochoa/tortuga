<?php
class conprecio extends Controller {
	function Consultas(){
		parent::Controller(); 		
		//$this->datasis->modulo_id('30A',1);		
	}
	
	function index(){
		redirect("inventario/conprecio/precios");
	}	
	function precios(){
		
		$data['msg1']='';
		$data['msg2']='';
		$data['msg3']='';
		$data['msg4']='';
		$ban         =false;

		if (isset($_POST['cod'])){
			$data2=array();		
			$cod=$_POST['cod'];
			if(!empty($cod)){
				$row=$this->datasis->damerow("SELECT precio1,existen, CONCAT_WS(' ',descrip ,descrip2) AS descrip, marca,modelo,iva,id FROM sinv WHERE barras='$cod' OR codigo='$cod' OR alterno='$cod'");
				if($row){
					$data['precio1']  = number_format($row['precio1'],2,',','.');				
					$data['descrip']  = $row['descrip'];
					$data['marca']    = $row['marca'];
					$data['existen']  = $row['existen'];
					$data['modelo']   = $row['modelo'];
					$data['iva']      = $row['iva'];					
					$data['total']    = number_format($row['precio1']+$row['iva'],2,',','.');
					$ban=true;
					$id=$row['id'];
					//$data2['img']      = $this->datasis->dameval("SELECT nombre FROM sinvfot WHERE sinv_id='$id'");
					$this->db->select("nombre,comentario,principal");
					$this->db->from('sinvfot');
					$this->db->where("sinv_id='$id'");
					$this->db->orderby('principal desc');
					$data['query'] = $this->db->get();					
					//foreach($query->result_array() as $row)$data2[]=$row;					
				}
				else{
					$data['precio1']  ='';
					$data['descrip']  ='';
					$data['marca']    ='';
					$data['existen']  ='';
					$data['modelo']   ='';
					$data['iva']      ='';					
					$data['total']    ='';
					$data['query']    ='';
					$cod='';
					$data['msg1']="PRODUCTO NO REGISTRADO";
					$data['msg2']="Por Favor introduzca un C&oacute;digo de identificaci&oacute;n del Producto";
					$data['msg3']="Presente el producto en el lector de c&oacute;digo de barras o escriba ";
					$data['msg4']="directamente algun codigo de identificacion y luego presione ENTER";									
				}
			}else{
				$data['precio1']  ='';
				$data['descrip']  ='';
				$data['marca']    ='';
				$data['existen']  ='';
				$data['modelo']   ='';
				$data['iva']      ='';				
				$data['total']    ='';
				$data['query']    ='';
				$cod='';   
				$data['msg1']="POR FAVOR, INTRODUZCA UN CODIGO DE IDENTIFICACION DEL PRODUCTO";
				$data['msg2']="Presente el producto en el lector de codigo de barras o escriba";
				$data['msg3']="directamente algun codigo de identificacion y luego presione ENTER";
			}
		}
		else{
			$data['precio1']  ='';
			$data['descrip']  ='';
			$data['marca']    ='';
			$data['existen']  ='';
			$data['modelo']   ='';
			$data['iva']      ='';
			$data['total']    ='';
			$data['query']    ='';			
			$cod='';
		}
		$data['ban']=$ban;
		$data['cod']=$cod;
		//$data['query']=$data2;
		$this->load->view('view_conprecios', $data);
	}
	function datab(){
			
	}
}