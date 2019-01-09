<?php
    class Catalogov extends Controller {
    		var	$depto;
			var	$linea;
			var	$grupo;
			var	$descrip;
			var	$comentario;
			var	$action;
		
    	function catalogov(){
			parent::Controller(); 
			
			$this->load->helper('string');
			$protocolo=explode('/',$_SERVER['SERVER_PROTOCOL']);
			$this->_direccion=$protocolo[0].'://'.$_SERVER['SERVER_NAME'].'/'.trim_slashes($this->config->item('base_url'));
			
		}
		
		function index(){
			$data['site_url']=site_url('');
			echo $this->load->view('view_catalogo_ver', $data,true);
		}
		
		function filter(){
			$this->load->library("rapyd");
			
			$this->depto     =$this->input->post('depto');
			$this->linea     =$this->input->post('linea');
			$this->grupo     =$this->input->post('grupo');
			$this->descrip   =$this->input->post('descrip');
			$this->comentario=$this->input->post('comentario');
			$this->action    =$this->input->post('action');
			
			switch($this->action){
				case 'CPpdf':$this->_cppdf();break;
				case 'CPhtml':$this->_cphtml();break;
				case 'CTpdf':$this->_ctpdf();break;
			}
		}
		
		function dtable($articulos){
			$per_row=2;
			$per_page=4;
			
			$table = new DataTable(null,$articulos);
			$table->per_row = $per_row;
			$table->per_page = $per_page;
			//$table->cell_attributes = 'style="vertical-align:middle;align:center; text-align: center;"';
			//$table->cell_template = "<div align='center' style='width:180px; padding:10px; height:140px; background-color:#559955'><#1#><div/>";
			//$table->cell_attributes ='align="center" height="220px" width="380px"';
			$table->cell_template ='<#1#>';
			$table->build();
			return $table->output;
			
		}
		
		function _cp(){
			$this->load->library("rapyd");
			
			$this->rapyd->load("datatable");

			$mSQL="SELECT sinv_id,comentario,nombre FROM sinvfot AS a JOIN sinv AS b ON b.id=a.sinv_id";
			
			if(!empty($this->grupo))
			{
				$mSQL.=" WHERE grupo='$this->grupo'";
			}elseif(!empty($this->linea))
			{
				$mSQL2="SELECT grupo FROM grup WHERE linea='$this->linea'";
				$mSQL2=$this->db->query($mSQL2);
				if($mSQL2->num_rows()>0)
				{
					foreach($mSQL2->result() AS $row)
					{
						$grupos[]=$row->grupo;
					}
					$mSQL.=" WHERE grupo IN ('".implode("','",$grupos)."')";
				}
			}elseif(!empty($this->depto))
			{
				$mSQL2="SELECT a.grupo FROM grup AS a JOIN line AS b ON a.linea=b.linea JOIN dpto AS c ON b.depto=c.depto WHERE c.depto='$deptoid'";
				$mSQL2=$this->db->query($mSQL2);
				if($mSQL2->num_rows()>0)
				{
					foreach($mSQL2->result() AS $row)
					{
						$grupos[]=$row->grupo;
					}
					$mSQL.=" WHERE grupo IN ('".implode("','",$grupos)."')";
				}
			}
			
			if(!empty($descrip)){
				if(strpos($mSQL,"WHERE")===false){
					$mSQL.=" WHERE (descrip like '%$this->descrip%' OR descrip2 like '%$this->descrip%') ";
				}else{
					$mSQL.=" AND (descrip like '%$this->descrip%' OR descrip2 like '%$this->descrip%') ";
				}
			}
			
			if(!empty($titulo)){
				if(strpos($mSQL,"WHERE")===false){
					$mSQL.=" WHERE comentario like '%$this->comentario%' ";
				}else{
					$mSQL.=" comentario like '%$this->comentario%' ";
				}
			}
		
			
			$mSQL.=" GROUP BY nombre ORDER BY portada is null,comentario";
			
			$art=$this->db->query($mSQL);
			if($art->num_rows()>0){
				
				$inventario=$this->_direccion."/uploads/inventario/Image";
				$link=site_url('inventario/catalogover');
				
				foreach($art->result() as $row){
					$temp="
					<div class='articulo' onclick='javascript:html(\"$row->sinv_id\",\"$row->nombre\",\"$row->comentario\");'>
					<table align='center' width='100%'>
						<tr>
							<td colspan='2' align='center'><div class='descrip'>$row->comentario</div></td>
						</tr>
						<tr>
							<td width='40%' ><img  width='50px' src='$inventario/$row->nombre' class='img' /></td>
							<td width='60%' valign='top'>
								<div >
								<table>
								<tr><td><div class='columt'>
								CODIGO
								</div></td><td><div class='columt'>
								PRECIO
								</div></td><td><div class='columt'>
								DESCRIPCION
								</div></td></tr>
							";
							$arts=$this->db->query("SELECT a.codigo,a.descrip,a.precio1 FROM sinv AS a JOIN sinvfot AS b ON a.id=b.sinv_id WHERE b.nombre='$row->nombre'");
							
							foreach($arts->result() AS $fila){
								$temp.="<tr><td><div class='colum'>".
								$fila->codigo.
								"</div></td><td><div class='colum'>".
								$fila->precio1.
								"</div></td><td><div class='colum'>".
								$fila->descrip.
								"</div></td></tr>";	;
							}
					$temp.="
								</table>
								</div>
							</td>
						</tr>
					</table>
					</div>
					";
					$articulos[][1]=$temp;
				}
				return $articulos;
			}else{
				 return false;
			}
		}
		
		function _cphtml(){
			
			$table=$this->_cp();
			$table=$this->dtable($table);
			echo $table;
			
		}
		
		function _cppdf(){
			
			$this->load->plugin('html2pdf');
			$table=$this->_cp();
			$table=$this->dtable($table);
			pdf_create($table, 'CATALOGO');
		}
		
		function ctpdf(){
			
			$this->load->plugin('html2pdf');
			
			$catalogos=$this->_cp();
			$formato='CATAPDF';
			$formato=$this->datasis->dameval("SELECT proteo FROM formatos WHERE nombre='$formato'");
			
			ob_start();
				echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $formato)).'<?php ');
				$table=ob_get_contents();
			@ob_end_clean();
			echo $table;
			//pdf_create($table, 'CATALOGO');
		}
	}	
?>
