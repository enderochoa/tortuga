<?php
//transferencias
class Stra extends Controller {

  var $data_type = null;
  var $data = null;

	function stra()
	{
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id(302,1);
    	define ("THISFILE",   APPPATH."controllers/inventario/". $this->uri->segment(2).EXT);
	}
  function index(){
    redirect("inventario/stra/filteredgrid");
  }

  ##### utility, show you $_SESSION status #####
  function _session_dump(){
    echo '<div style="height:200px; background-color:#fdfdfd; overflow:auto;">';
    echo '<pre style="font: 11px Courier New,Verdana">';
    var_export($_SESSION);
    echo '</pre>';
    echo '</div>';
  }

  ##### callback test (for DataFilter + DataGrid) #####
  function test($id,$const){
    //callbacktest//
    return $id*$const;
  }

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		
		$filter = new DataFilter("Filtro de Transferencias","stra");

		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=15;
		
		$filter->fecha = new dateonlyField("Fecha", "fecha");
		$filter->fecha->size=12;
		
		$filter->envia = new inputField("Envia", "envia");
		$filter->envia->size=12;
		
		$filter->recibe = new inputField("Recibe", "recibe");		
		$filter->recibe->size=12;
		
		$filter->buttons("reset","search");    
		$filter->build();
		
		$uri = anchor('inventario/stra/dataedit/show/<#numero#>','<#numero#>');
		
		$grid = new DataGrid("Lista de transferencias");
		$grid->order_by("numero","desc");
		$grid->per_page = 20;
		$grid->use_function("substr");
		
		$grid->column("N�mero",$uri);
		$grid->column("Fecha","<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Env�a","envia","envia");
		$grid->column("Recibe","recibe");
		$grid->column("Observaci�n","observ1");
		//echo $grid->db->last_query();
		$grid->add("inventario/stra/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = " Transferencias ";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
		  
  function loadiframe($data=null, $head="", $resize=""){
    $template['head'] = $head;
    $template['content'] = $data;
    $template['onload'] = "";
    if ($resize!=""){
      $template['onload'] = "autofit_iframe('$resize');";
    }
    $this->load->view('rapyd/iframe', $template);
  }

  function items_grid(){
   
    $this->rapyd->load("datagrid");
    
    $art_id = intval($this->uri->segment(4));
    
    $grid = new DataGrid("Art�culos","itstra");
    $grid->db->where("numero", $art_id);

    $modify = site_url("inventario/stra/items_edit/$art_id/modify/<#numero#>/<#codigo#>");
    $delete = anchor("inventario/stra/items_edit/$art_id/do_delete/<#numero#>/<#codigo#>","Eliminar");
    
    $grid->order_by("codigo","desc");
    $grid->per_page = 20;
    
    $grid->column_detail("N�mero","numero",$modify);
    $grid->column("C�digo","codigo");
    $grid->column("Descripci�n","descrip");
    $grid->column("Cantidad","cantidad");
    $grid->column("Eliminar", $delete);
    $grid->add("inventario/stra/items_edit/$art_id/create");
    $grid->build();
    
    $head = $this->rapyd->get_head();    
    $this->loadiframe($grid->output, $head, "related");
  }

  function items_edit(){  
    
    $this->rapyd->load("dataedit2");

    //$art_id = intval($this->uri->segment(4));
    
    $edit = new DataEdit2("", "itsprm");
	echo "asasa";
    //$edit->back_uri = "inventario/stra/items_grid/$art_id/";
		/*
	$edit->numero = new inputField("Numero", "numero");
    
	
    $edit->codigo   = new inputField("Codigo",   "codigo");
	
	$edit->numero->rule = "trim|required|max_length[20]";
	
    $edit->cantidad = new inputField("Cantidad", "cantidad");
	
    $edit->precio1  = new inputField("Precio 1", "precio1");
	
    $edit->precio2  = new inputField("Precio 2", "precio2");
	
    $edit->precio3  = new inputField("Precio 3", "precio3");
	
    $edit->precio4  = new inputField("Precio 4", "precio4");
	

    $edit->aticle_id = new autoUpdateField("article_id",   $art_id);
    $edit->body = new textareaField("Comment", "comment");
    $edit->body->rule = "required";
    $edit->body->rows = 5;
    
    $edit->back_save = true;
    $edit->back_cancel_save = true;
    $edit->back_cancel_delete = true;
    */
    $edit->buttons("modify", "save", "undo", "delete", "back");
    
    $edit->build();
    
	
    $data['content'] = $edit->output;           
    $data['title']   = " Transferencias ";        
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data); 
  }

  function dataedit(){
    if (($this->uri->segment(5)==="1") && ($this->uri->segment(4)==="do_delete"))
      show_error("Please do not delete the first record, it's required by DataObject sample");
		
    $this->rapyd->load("dataedit");

    $edit = new DataEdit("Transferencia", "stra");
    $edit->back_url = site_url("inventario/stra/filteredgrid");
    
    $edit->numero   = new inputField("N�mero", "numero");
    $edit->numero->mode="autohide";
    $edit->numero->size =10;
    $edit->numero->rule = "required";
    
    $edit->fecha    = new  dateonlyField("Fecha",  "fecha");
    $edit->fecha->size =12;
    
    $edit->envia    = new inputField("Env�a", "envia");
    $edit->envia->size =4;
    
    $edit->recibe   = new inputField("Recibe", "recibe");
    $edit->recibe->size = 4;
    
    $edit->observ1  = new inputField("Observaci�n 1", "observ1");
    $edit->observ1->size = 35;
    
    $edit->observ2  = new inputField("..", "observ2");
    $edit->observ2->size = 35;
    
    $edit->totalg   = new inputField("Total gr.", "totalg");
    $edit->totalg->size = 17;
    
    $r_uri = "inventario/stra/items_grid/<#numero#>";
    
    $edit->related = new iframeField("related", $r_uri, "210");
    $edit->related->when = array("show","modify");
    
    $edit->buttons("modify", "save", "undo", "delete", "back");
	/*
    $edit->use_function("callback_test");
    $edit->test = new freeField("Test", "test", "<callback_test><#article_id#>|3</callback_test>");
  */  
    $edit->build();
    
    $data['content'] = $edit->output;           
    $data['title']   = " Transferencias ";        
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data); 
  }
}
?>