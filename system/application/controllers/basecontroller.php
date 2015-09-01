<?php
class BaseController extends Controller {

  var $data_type = null;
  var $data = null;
  var $get_theme = true;


	function BaseController()
	{
		parent::Controller(); 

    //required helpers for samples
    $this->load->helper('url');
    $this->load->helper('text');

		//rapyd library
		$this->load->library("rapyd");

		//rapyd theme persistence
    if ($this->get_theme AND $this->rapyd->session->get("current_theme")) $this->rapyd->config->set_item("theme", $this->rapyd->session->get("current_theme"));


    //required data for (some) samples
    $this->data = array(
      array('article_id' => '1', 'title' => 'Title 1', 'body' => 'Body 1'),
      array('article_id' => '2', 'title' => 'Title 2', 'body' => 'Body 2'),
      array('article_id' => '3', 'title' => 'Title 3', 'body' => 'Body 3'),
      array('article_id' => '4', 'title' => 'Title 4', 'body' => 'Body 4'),
      array('article_id' => '5', 'title' => 'Title 5', 'body' => 'Body 5'),
      array('article_id' => '6', 'title' => 'Title 6', 'body' => 'Body 6'),
      array('article_id' => '7', 'title' => 'Title 7', 'body' => 'Body 7'),
      array('article_id' => '8', 'title' => 'Title 8', 'body' => 'Body 8'),
      array('article_id' => '9', 'title' => 'Title 9', 'body' => 'Body 9'),
      array('article_id' => '10', 'title' => 'Title 10', 'body' => 'Body 10')
    );


    //I use THISFILE, instead __FILE__ to prevent some documented php-bugs with higlight_syntax()&__FILE__
    define ("THISFILE",   APPPATH."controllers/rapyd/". $this->uri->rsegment(2).EXT);
    define ("VIEWPATH",   APPPATH."views/rapyd/");
	}
  
  
  
  ##### iframes & actions #####
  function loadiframe($data=null, $head="", $resize="")
  {

    $template['head'] = $head;
    $template['content'] = $data;
    $template["theme"] = $this->rapyd->config->item("theme");
    $template["style"] = $this->load->view("rapyd/style_".$template["theme"], null, true);
    
    $template['onload'] = "";
    if ($resize!=""){
      $template['onload'] = "autofit_iframe('$resize');";
    }
    $this->load->view('rapyd/iframe', $template);
  }
  
  
	function _render($view, $data=null, $highlight=array())
	{
  
    $content["content"] = $this->load->view($view, $data, true);
    $content["rapyd_head"] = $this->rapyd->get_head();
    
    $content["theme"] = $this->rapyd->config->item("theme");
    $content["style"] = $this->load->view("rapyd/style_".$content["theme"], null, true);

    $language_ON = $this->rapyd->config->item("rapyd_lang_ON");
    $content["language_links"] = (isset($language_ON) && $language_ON === True) ? "&nbsp;".language_links() : "|&nbsp; rapyd_lang is off";

    
    $content["code"] = "";
    foreach($highlight as $block)
    {
      if(isset($block["title"]) AND $block["title"]!="")
      {
        $content["code"] .= '<br/><br/>&nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #FF8000">### '.$block["title"].' ###</span><br/>';
      }
      if(isset($block["id"]) AND $block["id"]!="")
      {
        $content["code"] .= highlight_code_file($block["file"], "//".$block["id"]."//", "//end".$block["id"]."//");
      } else {
        $content["code"] .= highlight_code_file($block["file"]);
      }
    }    
    $this->load->view('rapyd/template', $content);
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
  
  
}
?>