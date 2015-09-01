<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Rapyd Components
 *
 * An open source library for CodeIgniter application development framework for PHP 4.3.2 or newer
 *
 * @package		rapyd.components
 * @author		Felice Ostuni
 * @license		http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @version		0.9.6
 * @filesource
 */

/**
 * DataSet - return a paged/subset of data from a given source (multidim array or database query).
 * It transform the result in a clean and standard array.
 * It support pagination.
 *
 * @package    rapyd.components
 * @author     Felice Ostuni
 * @access     public
 */
class DataSet {
  
  var $data        = array();
  var $recordCount = 0;
  
  var $_default_orderby = false;
  var $_default_orderby_field = null;
  var $_default_orderby_direction = "asc";
  
  //pagination default settings
  var $paged        = null;  
  var $_button_container = array( "TL"=>array(),"TR"=>array(), "BL"=>array(), "BR"=>array() );
  var $_title = null;
  
  var $per_page     = null;
  var $base_url     = "";
  var $pagination_function = "";
  var $uri_segment  = null;
  var $num_links    = 5;
    
  var $first_link   		= '&lsaquo; Primero';
  var $next_link			= '&gt;';
  var $prev_link			= '&lt;';
  var $last_link    		= '&Uacute;ltimo &rsaquo;';
  var $extra_anchor   = "";


  var $full_tag_open		= '';
  var $full_tag_close		= '';
  var $first_tag_open		= '';
  var $first_tag_close	= '&nbsp;';
  var $last_tag_open		= '&nbsp;';
  var $last_tag_close		= '';
  var $cur_tag_open	  	= '&nbsp;<b>';
  var $cur_tag_close		= '</b>';
  var $next_tag_open		= '&nbsp;';
  var $next_tag_close		= '&nbsp;';
  var $prev_tag_open		= '&nbsp;';
  var $prev_tag_close		= '';
  var $num_tag_open		= '&nbsp;';
  var $num_tag_close		= '';
    var $html			=null;


    
 /**
  * PHP4 constructor.
  *
  * @access   public
  * @param    array   $data   a multidimensional associative array of data
  * @return   void
  */
  function DataSet($data=null){
    
    $this->ci =& get_instance();
    $this->rapyd =& $this->ci->rapyd;
    $this->uri =& $this->ci->uri;

    $this->ci->load->helper('url');


    //AR preset or SQL query passed, so database lib needed
    if (!isset($data) || is_string($data))
    {
//rapyd->db (active record??) If we use DS with DF we look if DF is loaded first and then have instatiate the Rapyd shared database AR
			if(isset($this->rapyd->db))
			{
				$this->db =& $this->rapyd->db;
			}
			else
//If dataset is instantiate first we set the rapyd shared	database AR	in case that DF will be loaded in second....
			{
        $data_conn =(isset($this->rapyd->data_conn))?$this->rapyd->data_conn:'';
      	$this->db = $this->ci->load->database($data_conn,TRUE);
      	$this->rapyd->db =& $this->db;
      }
      
    }
    $this->type = "query";
		
    //tablename
    if (is_string($data))
    {
      $this->db->select("*");
      $this->db->from($data);

    //array
    } elseif (is_array($data)){
      $this->type        = "array";
      $this->arraySet    = $data;

    }
    $this->html = new Html();
  }

  function _sniff_orderby(){

    if ($this->rapyd->uri->is_set("orderby"))
    {
      $this->db->order_by($this->rapyd->uri->get("orderby",1), $this->rapyd->uri->get("orderby",2));
      
    } elseif($this->_default_orderby) {
    
      $this->db->order_by($this->_default_orderby_field, $this->_default_orderby_direction);
    }
    

    //if no datafilter present or doing searches, and keep_persistence is on
    //then save persistence of page.
    if (!$this->rapyd->uri->is_set("search") && isset($this->rapyd->uri->gfid))
    {
      $page = array (
        "back_post" => serialize($_POST),
        "back_uri" => $this->rapyd->uri->implode_uri()
      );

      //var_dump($this->rapyd->uri->uri_array);
      
      $this->rapyd->session->save_persistence($this->rapyd->uri->implode_uri("base_uri"), $page, $this->rapyd->uri->gfid);
    }
    
  }

  function order_by($field, $direction="asc"){
    $this->_default_orderby = true;
    $this->_default_orderby_field = $field;
    $this->_default_orderby_direction = $direction;
  }

  function title($title){
    $title = (lang($title)!="") ? lang($title) : $title;
    $this->_title = $title;
  }

 /**
  * useFunction add your custom(or php precompiled) functon/s to the list of "replace_functions" 
  * and enable the component to use a rapyd raplacing/formatting sintax  like these:
  * "..<customfunction><#body#>|0|100</customfunction>.."  (where <#body#> is a fieldname, and |0|100 are function parameters).
  *
  * @access   private
  * @return   void
  */
  function use_function(){
    $functions = func_get_args();
    foreach($functions as $function){
      if (!in_array(strtolower($function), $this->rapyd->config->item("replace_functions"))){
        array_push($this->rapyd->config->config["replace_functions"], strtolower($function));
      }
    }
  }

  function add($uri, $caption=RAPYD_BUTTON_ADD, $position="TR"){
    //append gfid clause 
    if (isset($this->rapyd->uri->gfid))
    {
      $uri_array = $this->rapyd->uri->explode_uri($uri);
      $uri_array["gfid"] = array("gfid", $this->rapyd->uri->gfid);
      $uri = $this->rapyd->uri->reverse_implode_uri($uri_array,"osp");
    }
    $action = "javascript:window.location='" . site_url($uri) . "'";
    $this->button("btn_add", $caption, $action, $position); 
  }

  function button($name, $caption, $action, $position="BL"){
     $this->_button_container[$position][] = $this->html->button($name, $caption, $action, "button", "button");
  }

  function submit($name, $caption, $position="BL"){
     $this->_button_container[$position][] = $this->html->button($name, $caption, "", "submit", "button");
  }

 /**
  * exec the query and fills the $data property, with a result array
  *
  * @access   public
  * @return   void
  */
  function build(){
		
    if ($this->type!="array"){
      $this->_sniff_orderby();
    }else{ 
    	//MODIF 4  dataset array persistance
		    if (!$this->rapyd->uri->is_set("search") && isset($this->rapyd->uri->gfid))
		   	{
		       $page = array (
		           "back_post" => serialize($_POST),
		           "back_uri" => $this->rapyd->uri->implode_uri()
		       );
		       $this->rapyd->session->save_persistence($this->rapyd->uri->implode_uri("base_uri"), $page, $this->rapyd->uri->gfid);
		    }
    }

    //base uri
    if ($this->base_url == ""){
      $this->base_url = site_url($this->rapyd->uri->implode_uri("base_uri","gfid","orderby","search")."/".RAPYD_URI_OSP);//site_url($this->rapyd->uri->reverse_implode_uri("osp")."/".RAPYD_URI_OSP);
    } 

    //segment_uri
    if (!isset($this->uri_segment)){
      $this->uri_segment = $this->rapyd->uri->offset_pos();
    }

    //orderby reset in the uri the "offset" and current "orderby"
    $order_by_base           = $this->rapyd->uri->implode_uri("base_uri","gfid","search");
    $this->_orderby_asc_url  = site_url($this->rapyd->uri->add_clause($order_by_base, "orderby/<#field#>/asc"));
    $this->_orderby_desc_url = site_url($this->rapyd->uri->add_clause($order_by_base, "orderby/<#field#>/desc"));


    $this->pageLength = $this->per_page;

    if (isset($this->pageLength) && !isset($this->paged) ){
      $this->paged = true;
    } else {
      $this->paged = false;
    }
    
    if (!isset($this->pageIndex) || !is_numeric($this->pageIndex)){
        $this->pageIndex = 0;
    }
        
    switch($this->type){
    
      case "array":
        $this->recordCount = count($this->arraySet);
        if ($this->paged){
          $this->data = array_slice ($this->arraySet, $this->ci->uri->segment($this->uri_segment), $this->pageLength);          
        } else {
          $this->data = $this->arraySet;
        }
        if (!$this->paged){
          $this->data = array_slice ($this->data, 0, $this->pageLength);
        }
        
        break;
      
             
case "query":
                              
        if ($this->paged){

          //pagination limit and offset  
          $this->db->limit($this->per_page, $this->ci->uri->segment($this->uri_segment));  
           
          //compile the select  
          $sql = $this->db->_compile_select();  
           
          //rebuild AR query to get total rows (needed for navigator)     
          $this->db->ar_limit = FALSE;
           
          //if there aren't aggregation functions, we add a count to current AR
          if(!preg_match("/(count|distinct|group by|sum)/i", $sql))
          {
            $this->db->ar_select = array('COUNT(*) AS totalrows');  

            //postres compat. suggested by thierry  
            $this->db->ar_orderby = array();  
            $this->db->ar_order = FALSE;   
            
            //get total rows  
            $query = $this->db->get();  
            if ($query===false) show_error("DB Error");  
            $row = $query->row();       
            $this->recordCount = $row->totalrows;  
                  
          } else {

            //get total rows  
            $query = $this->db->get();  
            if ($query===false) show_error("DB Error");  
            $this->recordCount = $query->num_rows();

          }

          //exec original query  
          $query = $this->db->query($sql);       
          $this->recordSet = $query->result_array();  


        } else {  
          $query = $this->db->get();

          if (isset($query->num_rows)){
            $this->recordCount = $query->num_rows;
          } else {
            $this->recordCount = 0;
          }
          $this->recordSet = $query->result_array();
        }
                    
        if(!$this->recordSet){
          $this->data = array();
        } else {
          $this->data = $this->recordSet;
        }

        break;
        
        
    }
    
    //navigator 
    if ($this->paged) {

      //load needed libraries 
      if (!isset($this->ci->pagination)) {
        $this->ci->load->library('pagination');
      }
      
      
      $config = array();
      $config['total_rows']  = $this->recordCount; //computed
      $config['per_page']    = $this->per_page;  
      $config['base_url']    = $this->base_url; 
      $config['pagination_function']    = $this->pagination_function;     
      $config['uri_segment'] = $this->uri_segment;
      $config['num_links']   = $this->num_links;
      
      $config['first_link']  = $this->first_link;
      $config['next_link']   = $this->next_link;  
      $config['prev_link']   = $this->prev_link;
      $config['last_link']   = $this->last_link;
      
      $config['full_tag_open']  = $this->full_tag_open;      
      $config['full_tag_close'] = $this->full_tag_close;
      $config['first_tag_open'] = $this->first_tag_open;  
      $config['first_tag_close']= $this->first_tag_close;        
      $config['last_tag_open']  = $this->last_tag_open;
      $config['last_tag_close'] = $this->last_tag_close;
      $config['cur_tag_open']   = $this->cur_tag_open;      
      $config['cur_tag_close']  = $this->cur_tag_close;
      $config['next_tag_open']  = $this->next_tag_open;  
      $config['next_tag_close'] = $this->next_tag_close;        
      $config['prev_tag_open']  = $this->prev_tag_open;
      $config['prev_tag_close'] = $this->prev_tag_close;
      $config['num_tag_open']   = $this->num_tag_open;
      $config['num_tag_close']  = $this->num_tag_close;      
      
      $this->ci->pagination->initialize($config);

      $this->navigator = $this->ci->pagination->create_links();


    }

  }


}

?>
