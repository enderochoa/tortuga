<?php
class fallas extends Controller {
	var $falla;
	
	function fallas(){
		parent::Controller(); 
		$this->datasis->modulo_id(313,1);
		$this->load->library("rapyd");
		
		$this->falla[]=array('sql' =>'precio1<=0 OR precio2<=0 OR precio3<=0 OR precio4<=0 OR precio1=NULL OR precio2=NULL OR precio3=NULL OR precio4=NULL', 'nombre' => 'Productos sin precios');
		$this->falla[]=array('sql' =>'ultimo<=0 OR ultimo=NULL', 'nombre' => 'Productos sin costos');
		$this->falla[]=array('sql' =>'base1<=0 OR base2<=0  OR base3<=0  OR base4<=0 ', 'nombre' => 'Productos sin bases');
		$this->falla[]=array('sql' =>'precio1<ultimo OR precio2<ultimo OR precio3<ultimo OR precio4<ultimo', 'nombre' => 'Productos con precio por debajo de costo');
		$this->falla[]=array('sql' =>'LENGTH(descrip)<5', 'nombre' => 'Productos con descripciones menores a 5 cararteres');
		$this->falla[]=array('sql' =>'margen1>100 OR margen2>100 OR margen3>100 OR margen4>100', 'nombre' => 'Productos con margenes altos');
		$this->falla[]=array('sql' =>'margen1<10 OR margen2<10 OR margen3<10 OR margen4<10', 'nombre' => 'Productos con margenes bajos');
		$this->falla[]=array('sql' =>'existen<=0', 'nombre' => 'Productos sin existencia');
	}
	
	function index(){
		$this->rapyd->load("datagrid",'dataform','datafilter');
		$this->rapyd->uri->keep_persistence();  

		$form = new DataFilter("Seleccione las fallas");  
		foreach($this->falla AS $ind=>$checkbox){
			$id='f_'.$ind;
			$form->$id = new checkboxField($checkbox['nombre'], $id, '1');
			$form->$id->clause='';
		}
		$form->submit("reset","Resetear"); 
		$form->submit("btnsubmit","Buscar"); 
		$form->build_form();  

		$algo['falla']=$this->falla;
		$algo['form'] =& $form;
		$salida=$this->load->view('view_fallas', $algo,true);

		if($this->input->post('btnsubmit')){
			$grid = new DataGrid("Lista de Productos");
			$grid->db->select('codigo,LEFT(descrip,20)AS descrip,margen1,margen2,margen3,margen4,base1,base2,base3,base4,precio1,precio2,precio3,precio4,id,existen,ultimo,pond');
			$grid->db->from('sinv');
			$grid->per_page = 20;
			foreach($this->falla AS $ind=>$data){
				$id='f_'.$ind;
				if($this->input->post($id)){
					$grid->db->or_where($data['sql']);
				}
			}
			$link=anchor('/inventario/sinv/dataedit/show/<#id#>','<#codigo#>');

			//$grid->column("Código",$link);
			$grid->column("Código",'codigo');
			$grid->column("Descripción","descrip");
			$grid->column("Margenes"   ,"<ol><li><#margen1#></li><li><#margen2#></li><li><#margen3#></li><li><#margen4#></li></ol>"  );
			$grid->column("Bases"      ,"<ol><li><#base1#></li><li><#base2#></li><li><#base3#></li><li><#base4#></li></ol>" );
			$grid->column("Precios"    ,"<ol><li><#precio1#></li><li><#precio2#></li><li><#precio3#></li><li><#precio4#></li></ol>");
			$grid->column("Costos"     ,"<ul><li><b>Ultimo:</b><#ultimo#></li><li><b>Promedio:</b><#pond#></li></ul>");
			$grid->column("Existencia" ,"existen"  ,"align='right'");										
			$grid->build();
			//echo $grid->db->last_query();
			$salida.=$grid->output;
		}
		$data['content'] = $salida;
		$data['title']   = " Productos con fallas ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
 }
?>