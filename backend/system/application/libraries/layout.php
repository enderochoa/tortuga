<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Layout Library Class
 *
 * @package		YATS 1.2 -- The Layout Library
 * @subpackage	Libraries
 * @category	Template
 * @author		Mario Mariani
 * @copyright	Copyright (c) 2006-2007, mariomariani.net All rights reserved.
 * @license		http://svn.mariomariani.net/yats/trunk/license.txt
 */
class Layout 
{
	var $settings;
	var $elements;
	
    /**
     * Constructor
     *
     * @access	public
     */    
    function Layout()
    {
        $this->layout =& get_instance();
        $this->layout->load->model($this->layout->config->item('layout_model'),'layoutmodel');
		$this->settings();
        log_message('debug','Layout class initialized');
    }
    
    // --------------------------------------------------------------------

    /**
     * Build the whole thing
     *
     * @access	public
     * @param	string	view file
     * @param	mixed	array with the output data 
     * @return	string
     */    
    function buildPage($view, $data = null)
    {
        /* Theme settings */
        $data['settings'] = $this->settings;
        
        /* Layout commons */
        foreach ($this->settings['elements'] as $key => $item)
        {
            $data[$key] = $this->layout->layoutmodel->$key($item);
        }
        
        /* Load the view file */
        $this->layout->load->view('loader', array('view'=>$view, 'data'=>$data));
    }

    // --------------------------------------------------------------------

    /**
     * Dump the whole thing
     *
     * @access	public
     * @param	string	view file
     * @param	mixed	array with the output data 
     * @return	string
     */    
    function dumpPage($view, $data = null)
    {
        /* Theme settings */
        $data['settings'] = $this->settings;
        
        /* Return the view file */
        return $this->layout->load->view($data['settings']['theme_name'] . "/content/$view", $data, true);
    }

    // --------------------------------------------------------------------

	/**
	 * Returns an array with all template properties
	 *
	 * @access	public
	 * @param	null
	 * @return	string
	 */ 
	function settings()
	{
		$config = (array) new CI_Config();
		
		foreach ($config['config'] as $key => $item)
		{
			if (strstr($key, 'layout_') || strstr($key, 'app_')) 
			{
				$settings[str_replace('layout_', '', $key)] = $item;	
			}
		}
		$settings['assets'] = base_url() . $settings['assets'] . $settings['default'] . "/";
		$settings['styles'] = $settings['assets'] . $settings['styles'];
		$settings['images'] = $settings['assets'] . $settings['images'];
		$settings['script'] = $settings['assets'] . $settings['script'];
		
		$this->settings = $settings;
	}
}

// EOF
?>