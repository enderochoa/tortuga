<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Eye View Design CMS module Ajax Model
 *
 * PHP version 5
 *
 * @category  CodeIgniter
 * @package   EVD CMS
 * @author    Frederico Carvalho
 * @copyright 2008 Mentes 100Limites
 * @version   0.1
*/

class Ajax_model extends Model 
{
	/**
	* Instanciar o CI
	*/
	public function Ajax_model()
    {
        parent::Model();
		$this->CI =& get_instance();
    }
	
	public function get_ppla() 
	{
		//Select table name
		$table_name = "ppla";
		
		//Build contents query
		$this->db->select('codigo,ordinal,denominacion,nivel')->from($table_name);
		$this->CI->flexigridlib->build_query();
		
		//Get contents
		$return['records'] = $this->db->get();
		
		//Build count query
		$this->db->select('count(codigo) as record_count')->from($table_name);
		$this->CI->flexigridlib->build_query(FALSE);
		$record_count = $this->db->get();
		$row = $record_count->row();
		
		//Get Record Count
		$return['record_count'] = $row->record_count;
	
		//Return all
		return $return;
	}
	
	/**
	* Remove country
	* @param int country id
	* @return boolean
	*/
	public function delete_country($codigo) 
	{
		$delete = $this->db->query('DELETE FROM ppla WHERE codigo="'.$codigo.'"');
		
		return TRUE;
	}
}
?>