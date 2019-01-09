<?php
class Flexigrid extends Controller {

	function Flexigrid  ()
	{
		parent::Controller();	
		$this->load->helper('flexigrid');
		$this->load->model('ajax_model');
		$this->load->library('flexigridlib');
	}
	
	function index()
	{
		//ver lib
		
		/*
		 * 0 - display name
		 * 1 - width
		 * 2 - sortable
		 * 3 - align
		 * 4 - searchable (2 -> yes and default, 1 -> yes, 0 -> no.)
		 */
		$colModel['codigo']        = array('Codigo',       80,TRUE,'left',2);
		$colModel['ordinal']       = array('Ordinal',      40,TRUE,'left',2);
	  $colModel['denominacion']  = array('Denominacion',400,TRUE,'left',0);
		$colModel['nivel']         = array('Nivel',        30,TRUE,'center',1);
		
		
		/*
		 * Aditional Parameters
		 */
		$gridParams = array(
		'width' => 'auto',
		'height' => 400,
		'rp' => 15,
		'rpOptions' => '[10,15,20,25,40]',
		'pagestat' => 'Mostrando: {from} hasta {to} de {total} registros.',
		'blockOpacity' => 0.5,
		'title' => 'Clasificador Presupuestario',
		'showTableToggleBtn' => true
		);
		
		/*
		 * 0 - display name
		 * 1 - bclass
		 * 2 - onpress
		 */
		$buttons[] = array('Eliminar','delete','test');
		$buttons[] = array('separator');
		$buttons[] = array('Marcar Todo','add','test');
		$buttons[] = array('Desmarca Todos','delete','test');
		$buttons[] = array('separator');

		
		//Build js
		//View helpers/flexigrid_helper.php for more information about the params on this function
		$grid_js = build_grid_js('flex1',site_url("/flexigrid/tabla"),$colModel,'id','asc',$gridParams,$buttons);

		$data['js_grid'] = $grid_js;
		$data['version'] = "0.36";
		$data['download_file'] = "Flexigrid_CI_v0.36.rar";
		
		$this->load->view('flexigrid',$data);
	}
	
	function example () 
	{
		$data['version'] = "0.36";
		$data['download_file'] = "Flexigrid_CI_v0.36.rar";
		
		$this->load->view('example',$data);	
	}


	function tabla()
	{
		//List of all fields that can be sortable. This is Optional.
		//This prevents that a user sorts by a column that we dont want him to access, or that doesnt exist, preventing errors.
		$valid_fields = array('codigo','ordinal','denominacion','nivel');
		
		$this->flexigridlib->validate_post('codigo','asc',$valid_fields);

		$records = $this->ajax_model->get_ppla();
		
		$this->output->set_header($this->config->item('json_header'));
		
		/*
		 * Json build WITH json_encode. If you do not have this function please read
		 * http://flexigrid.eyeviewdesign.com/index.php/flexigrid/example#s3 to know how to use the alternative
		 */
		foreach ($records['records']->result() as $row)
		{
			$record_items[] = array($row->codigo,
			$row->codigo,
			$row->ordinal,
			$row->denominacion,
			//'<span style=\'color:#ff4400\'>'.addslashes($row->printable_name).'</span>',
			$row->nivel,
			//$row->numcode,
			'<a href=\'#\'><img border=\'0\' src=\''.image("close.png").'\'></a> '
			);
		}
		//Print please
		$this->output->set_output($this->flexigridlib->json_build($records['record_count'],$record_items));
	}
	
	
	//Delete Country
	function deletec()
	{
		$countries_ids_post_array = split(",",$this->input->post('items'));
		
		foreach($countries_ids_post_array as $index => $country_id)
			if (is_numeric($country_id) && $country_id > 1) 
				$this->ajax_model->delete_country($country_id);
			
			
		$error = "Selected countries (id's: ".$this->input->post('items').") deleted with success";

		$this->output->set_header($this->config->item('ajax_header'));
		$this->output->set_output($error);
	}
}

?>