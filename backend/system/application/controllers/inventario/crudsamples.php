<?php


class crudsamples extends Controller {

  var $data_type = null;   
  var $data = null;

	function crudsamples()
	{
		parent::Controller(); 

    //required helpers for samples
    $this->load->helper('url');
    $this->load->helper('text');

		//rapyd library
		$this->load->library("rapyd");
    
    //I use THISFILE, instead __FILE__ to prevent some documented php-bugs with higlight_syntax()&__FILE__
    define ("THISFILE",   APPPATH."controllers/rapyd/". $this->uri->segment(2).EXT);
	}



  ##### index #####
  function index()
  {
    redirect("inventario/crudsamples/filteredgrid");
  }

  ##### utility, show you $_SESSION status #####
  function _session_dump()
  {
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
    //endcallbacktest//
  }



  ##### DataFilter + DataGrid #####
  function filteredgrid(){
    //filteredgrid//
    $this->rapyd->load("datafilter","datagrid");
    $this->rapyd->uri->keep_persistence();
    
    //filter
    $filter = new DataFilter("Article Filter");
    /*
    $filter->db->select("articles.*, authors.*");
    $filter->db->from("articles");
    $filter->db->join("authors","authors.author_id=articles.author_id","LEFT");*/

    $filter->title = new inputField("Title", "title");
    $filter->ispublic = new dropdownField("Public", "public");
    $filter->ispublic->option("","");
    $filter->ispublic->options(array("y"=>"Yes","n"=>"No"));
    
    $filter->buttons("reset","search");    
    $filter->build();
    
    $uri = "inventario/crudsamples/dataedit/show/<#numero#>";
    
    //grid
    $grid = new DataGrid("Article List","stra");
    //$grid->use_function("callback_test");
    $grid->order_by("numero","desc");
    $grid->per_page = 20;
   // $grid->use_function("substr");
    $grid->column_detail("observ1","observ1", $uri);
    //$grid->column_orderby("title","title","title");
    //$grid->column("body","<substr><#body#>|0|4</substr>....");
    //$grid->column("Author","<#firstname#> <#lastname#>");
    $grid->column("callback test","<#numero#>");
    
    $grid->add("inventario/crudsamples/dataedit/create");
    $grid->build();
    
    $data["crud"] = $filter->output . $grid->output;
    
    //endfilteredgrid//
    
    //$this->_session_dump();
    
    $content["content"] = $this->load->view('rapyd/crud', $data, true);    
    $content["rapyd_head"] = $this->rapyd->get_head();
    $content["code"] = highlight_code_file(THISFILE, "//filteredgrid//", "//endfilteredgrid//");
    $content["code"] .= '<br/><br/>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #FF8000">//callback test function<br /></span><br/>';
    $content["code"] .= highlight_code_file(THISFILE, "//callbacktest//", "//endcallbacktest//");
    
    $this->load->view('rapyd/template', $content);
  }
  
  
  
  
  ##### iframes & actions #####
  function loadiframe($data=null, $head="", $resize="")
  {
    $template['head'] = $head;
    $template['content'] = $data;
    $template['onload'] = "";
    if ($resize!=""){
      $template['onload'] = "autofit_iframe('$resize');";
    }
    $this->load->view('rapyd/iframe', $template);
  }

  // comments datagrid 
  function comments_grid()
  {
    //commentsgrid//
    $this->rapyd->load("datagrid");
    
    $art_id = intval($this->uri->segment(4));
    
    $grid = new DataGrid("Comments","comments");
    $grid->db->where("article_id", $art_id);

    $modify = site_url("inventario/crudsamples/comments_edit/$art_id/modify/<#comment_id#>");
    $delete = anchor("inventario/crudsamples/comments_edit/$art_id/do_delete/<#comment_id#>","delete");
    
    $grid->order_by("comment_id","desc");
    $grid->per_page = 6;
    $grid->column_detail("ID","comment_id", $modify);
    $grid->column("comment","<substr><#comment#>|0|100</substr>....");
    $grid->column("delete", $delete);
    $grid->add("inventario/crudsamples/comments_edit/$art_id/create");
    $grid->build();
    
    $head = $this->rapyd->get_head();    
    $this->loadiframe($grid->output, $head, "related");
    //endcommentsgrid//
  }


  // comments dataedit 
  function comments_edit()
  {  
    //commentsedit//
    $this->rapyd->load("dataedit");

    $art_id = intval($this->uri->segment(4));
    
    $edit = new DataEdit("Comment Detail", "comments");
    $edit->back_uri = "inventario/crudsamples/comments_grid/$art_id/list";

    $edit->aticle_id = new autoUpdateField("article_id",   $art_id);
    $edit->body = new textareaField("Comment", "comment");
    $edit->body->rule = "required";
    $edit->body->rows = 5;
    
    $edit->back_save = true;
    $edit->back_cancel_save = true;
    $edit->back_cancel_delete = true;
    
    $edit->buttons("modify", "save", "undo", "delete", "back");
    $edit->build();
    
    $head = $this->rapyd->get_head();
    $this->loadiframe($edit->output, $head, "related");
    //endcommentsedit//
  }



  ##### dataedit #####
  function dataedit()
  {  
    if (($this->uri->segment(5)==="1") && ($this->uri->segment(4)==="do_delete")){
      show_error("Please do not delete the first record, it's required by DataObject sample");
    }
  
    //dataedit//
    $this->rapyd->load("dataedit");


    $edit = new DataEdit("Article Detail", "articles");
    $edit->back_url = site_url("inventario/crudsamples/filteredgrid");

    $edit->title = new inputField("Title", "title");
    $edit->title->rule = "trim|required|max_length[20]";
    
    $edit->body = new editorField("Body", "body");
    $edit->body->rule = "required";
    $edit->body->rows = 10;    

    $edit->author = new dropdownField("Author", "itstra");
    $edit->author->option("","");
    $edit->author->options("SELECT numero, codigo FROM itstra");

    $r_uri = "inventario/crudsamples/comments_grid/<#article_id#>/list";
    $edit->related = new iframeField("related", $r_uri, "210");
    $edit->related->when = array("show","modify");

    $edit->checkbox = new checkboxField("Public", "public", "y","n");
    
    $edit->datefield = new dateField("Date", "datefield","eu"); 
    
    if ($this->uri->segment(4)==="1"){
      $edit->buttons("modify", "save", "undo", "back");
    } else {
      $edit->buttons("modify", "save", "undo", "delete", "back");
    }

    $edit->use_function("callback_test");
    $edit->test = new freeField("Test", "test", "<callback_test><#numero#>|3</callback_test>");
    
    
    $edit->build();
    $data["edit"] = $edit->output;
     
    //enddataedit//

    //$this->_session_dump();

    $content["content"] = $this->load->view('rapyd/dataedit', $data, true);    
    $content["rapyd_head"] = $this->rapyd->get_head();
    $content["code"]  = highlight_code_file(THISFILE, "//dataedit//", "//enddataedit//");
    $content["code"] .= '<br/><br/>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #FF8000">//comments grid <br /></span><br/>';
    $content["code"] .= highlight_code_file(THISFILE, "//commentsgrid//", "//endcommentsgrid//");
    $content["code"] .= '<br/><br/>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #FF8000">//comments edit <br /></span><br/>';
    $content["code"] .= highlight_code_file(THISFILE, "//commentsedit//", "//endcommentsedit//");
    $this->load->view('rapyd/template', $content);
  }
  

}
?>