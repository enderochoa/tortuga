<?php
//pasando como parametros
//index/parametro1/parametro2/parametro3
//parametro1: S/N para indicar si se ve el modulo publicitario-predeterminado S
//parametro2: nombre del formato donde se muestran los detalles del articulo.predeterminado CATALOGO
//PARAMETRO3: nombre de la imagen a usar de fondo que esta almacenada en /proteoerp/assets/default/css/le-frog/images/ ejemplo:fondo1.png
class Catalogover extends Controller {
	var $deptoid;
	var $lineaid;
	var $grupoid;
	var $descrip;
	var $_direccion;
	function catalogover(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->load->helper('string');
		$protocolo=explode('/',$_SERVER['SERVER_PROTOCOL']);
		$this->_direccion=$protocolo[0].'://'.$_SERVER['SERVER_NAME'].'/'.trim_slashes($this->config->item('base_url'));
	}
	
	function index($format='CATALOGO',$img_fondo='ui-bg_diagonals-thick_15_444444_40x40.png'){
		$data['conf']=$this->layout->settings;
		$data['site_url']=site_url('');
		
		$data['format']=$format;
		$data['img_fondo']=$img_fondo;
		echo $this->load->view('view_catalogo', $data,true);
	}
		
	function filter($pdf=false){
		$this->rapyd->load("datatable");

		$mSQL="SELECT sinv_id,comentario,nombre FROM sinvfot AS a JOIN sinv AS b ON b.id=a.sinv_id";
		$mSQL2="SELECT b.sinv_id,a.codigo AS codigo,descrip,a.id AS id FROM sinv AS a LEFT JOIN sinvfot AS b ON a.id=b.sinv_id";
		//$mSQL="SELECT codigo,descrip,id FROM sinv";
		$deptoid=$this->input->post('depto');
		$lineaid=$this->input->post('linea');
		$grupoid=$this->input->post('grupo');
		$descrip=$this->input->post('descrip');
		$titulo=$this->input->post('titulo');
		$pdf=$this->input->post('pdf');
		
		if(!empty($grupoid))
		{
			$grupodes=$this->datasis->dameval("SELECT nom_grup FROM grup WHERE grupo='$grupoid' LIMIT 1");
			$mSQL.=" WHERE grupo='$grupoid'";
		}elseif(!empty($lineaid))
		{
			$lineades=$this->datasis->dameval("SELECT descrip FROM line WHERE linea='$lineaid' LIMIT 1");
			$mSQL2="SELECT grupo FROM grup WHERE linea='$lineaid'";
			$mSQL2=$this->db->query($mSQL2);
			if($mSQL2->num_rows()>0)
			{
				foreach($mSQL2->result() AS $row)
				{
					$grupos[]=$row->grupo;
				}
				$mSQL.=" WHERE grupo IN ('".implode("','",$grupos)."')";
			}
		}elseif(!empty($deptoid))
		{
			$deptodes=$this->datasis->dameval("SELECT descrip FROM dpto WHERE depto='$deptoid' LIMIT 1");
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
				$mSQL.=" WHERE (descrip like '%$descrip%' OR descrip2 like '%$descrip%') ";
			}else{
				$mSQL.=" AND (descrip like '%$descrip%' OR descrip2 like '%$descrip%') ";
			}
		}
		
		if(!empty($titulo)){
			if(strpos($mSQL,"WHERE")===false){
				$mSQL.=" WHERE comentario like '%$titulo%' ";
			}else{
				$mSQL.=" comentario like '%$titulo%' ";
			}
		}
	
		$mSQL2=$mSQL." GROUP BY a.id ORDER BY b.sinv_id is null, a.descrip";
		$mSQL.=" GROUP BY nombre ORDER BY portada is null,comentario";
		
		
		$art=$this->db->query($mSQL);
		if($art->num_rows()>0){
			
			$inventario=$this->_direccion."/uploads/inventario/Image";
			$link=site_url('inventario/catalogover');
			
			
			
			foreach($art->result() as $row){
				$temp="
				<div class='articulo'>
				<table align='center' width='100%'>
					<tr>
						<td colspan='2' align='center'><div class='descrip' onclick='javascript:html(\"$row->sinv_id\",\"$row->nombre\",\"$row->comentario\");'>$row->comentario</div></td>
					</tr>
					<tr>
						<td width='40%' ><img src='$inventario/$row->nombre' class='img' /></td>
						<td width='60%' valign='top'>
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
							$temp.="<div class='colum'><tr><td><a class='enlace_html2' href=\"javascript:html2('".($fila->codigo)."')\" >".
							$fila->codigo.
							"</a></td><td><div class='colum'>".
							$fila->precio1.
							"</div></td><td><div class='colum'>".
							$fila->descrip.
							"</div></td></tr></div>";	;
						}
				$temp.="
							</table>
						</td>
					</tr>
				</table>
				</div>
				";
				$articulos[][1]=$temp;
			}
			
			
			//<div class='codigo'>$row->codigo</div>
			$table = new DataTable(null,$articulos);
			$table->per_row = 2;
			$table->per_page = 4;
			//$table->cell_attributes = 'style="vertical-align:middle;align:center; text-align: center;"';
			//$table->cell_template = "<div align='center' style='width:180px; padding:10px; height:140px; background-color:#559955'><#1#><div/>";
			//$table->cell_attributes ='align="center" height="220px" width="380px"';
			$table->cell_template ='<#1#>';
			$table->build();
			$f=site_url('inventario/catalogover/filter');
			/*echo '
			<style>
			a {text-decoration:none;color:#F66309}
			.pagenav {color:#F66309}
			</style>
			<script type="text/javascript">
			link="'.$f.'"
				$(document).ready(function(){
					$(".pagenav a").click(function(){
						link=$(this).attr("href");
						filtro();
						return false;
					});
				});
				
				function filtro(){
				$("#html").hide();
				$("#logo").hide();
				$("#articulos").show();
				
				$.ajax({
					type: "POST",
					url: link,
					data:"depto="+$("#depto").val()+"&&linea="+$("#linea").val()+"&&grupo="+$("#grupo").val()+"&&descrip="+$("#descrip").val()+"&&titulo="+$("#titulo").val(),
					success: function(msg){
						if(msg){
						
							$("#articulos").html(msg);							
						}
						else{						
							$("#articulos").html("");
						}
					}
				});
			}
		
		</script>
		'.*/
		echo $table->output;
			
			
		}else{
			 echo "<div class='descrip_format' >Su busqueda no ha arrojado alg&uacute;n resultado</div>";
			 echo "<div class='descrip_format' >Por Favor intentelo nuevamente</div>"; 
			
		}
		
	}
	function html($format='CATALOGO'){
		$this->rapyd->load("datatable");
		
		$comentario=$this->input->post("comentario");
		$id=$this->input->post("sinv_id");
		$nombre=$this->input->post("nombre");
		//if($codigo!==false){
			//$html=$this->datasis->dameval("SELECT contenido FROM catalogo WHERE codigo='$codigo' LIMIT 1");
			//if(!empty($html)){
			//	echo $html;
			//}else{
				
								
				if($id!=false){
					
					$formato=$this->datasis->dameval("SELECT proteo FROM formatos WHERE nombre='$format'");
					
					/*$scr=base_url()."uploads/inventario/Image/<#nombre#>";
					$table = new DataTable(null);
					$table->cell_attributes = 'style="vertical-align:middle; text-align: center;"';
					
					$table->db->select(array('nombre','id'));
					$table->db->from("sinvfot");
					$table->db->where("sinv_id='$id'");
		io is currently available to invited users 			$table->db->orderby("principal='S'");
					
					$table->per_row = 3;
					$table->per_page = 18;
					$table->cell_attributes = 'width="150px"';
					$table->cell_template = "<div align='center'><a class='imagen' title='<#comentario#>' href='$scr' ><img style='margin:10px' title='<#comentario#>' src='$scr' width='180' border=0 /></a></div>";//<div width='120'></div>
					$table->build();
					*/
					//$data['conf']=$this->layout->settings;
					//extract($data);
					ob_start();
						echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $formato)).'<?php ');
						$_html=ob_get_contents();
					@ob_end_clean();		
					echo '
					<script language="JavaScript" type="text/javascript">
						$(document).ready(function()
						{
							$("a imagen").fancybox();
						});
					</script>
					'.$_html;//.$table->output;
				}
				
			//}
		//}
	}
	
	function html2($format='CATALOGOTE'){
		$this->rapyd->load("datatable");
		$codigo=$this->input->post("codigo");
		
		
		
		
		//if($codigo!==false){
			//$html=$this->datasis->dameval("SELECT contenido FROM catalogo WHERE codigo='$codigo' LIMIT 1");
			//if(!empty($id)){
			//	echo $html;
			//}else{
				$id=$this->datasis->dameval("SELECT id FROM sinv WHERE codigo='$codigo' LIMIT 1");
				
								
				if($id!=false){
					$query="SELECT proteo FROM formatos WHERE nombre='$format'";
					$formato=$this->datasis->dameval($query);
					
					$scr=base_url()."uploads/inventario/Image/<#nombre#>";
					$table = new DataTable(null);
					$table->cell_attributes = 'style="vertical-align:middle; text-align: center;"';
					
					$table->db->select(array('nombre','id'));
					$table->db->from("sinvfot");
					$table->db->where("sinv_id='$id'");
					$table->db->orderby("principal='S'");
					
					$table->per_row = 3;
					$table->per_page = 18;
					$table->cell_attributes = 'width="150px"';
					$table->cell_template = "<div align='center'><a class='imagen' title='<#comentario#>' href='$scr' ><img style='margin:10px' title='<#comentario#>' src='$scr' width='180' border=0 /></a></div>";//<div width='120'></div>
					$table->build();
					
					//$data['conf']=$this->layout->settings;
					//extract($data);
					ob_start();
						echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $formato)).'<?php ');
						$_html=ob_get_contents();
					@ob_end_clean();		
					echo '
					<script language="JavaScript" type="text/javascript">
						$(document).ready(function()
						{
							$("a imagen").fancybox();
						});
					</script>
					'.$_html.$table->output;
				}
				
			//}
		//}
	}
	
	function busca_portada($id=8){
		$portada=$this->datasis->dameval("SELECT contenido FROM catalogo WHERE id='$id' LIMIT 1");
		if(!empty($portada))echo $portada;else echo "no";
		
	}
	
	function descargar(){
		$this->load->plugin('html2pdf');
		
		$image=$this->_direccion."/images";
		$cabecera='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		<html>
			<head>
				<style>
					.micro{ font: 8px "Trebuchet ms",Verdana,Tahoma,Arial,sans-serif; color: #bbb; font-weight: normal;}
					.codigo{
						font: 8px "Trebuchet ms",Verdana,Tahoma,Arial,sans-serif; 
						color: #ffffff; 
						font-weight: bold;
						background-color:#494949;
						padding:1px;
						height:15px;
						width:180px;
						-moz-border-radius-topleft:5%;
						-moz-border-radius-topright:5%;
					}
					.descrip{			
						font: 9px "Trebuchet ms",Verdana,Tahoma,Arial,sans-serif; 
						color: #ffffff; 
						font-weight: normal;
						background-color:#024B2D;
						padding:1px;
						height:20px;
						width:200px;
						-moz-border-radius:5%;
					}
					
					 #footer{
						background:#444444;
						position:fixed;	
						left:0%;
						right:0%;
						bottom:0px;
						color:#B9B9B9;
						font-size:10px;
						text-align:center;
					}
					
					#articulos{
						top:-30px;
						position:relative;
						margin:0 auto 0 auto;
						width:500px;
					}
					
										
					.imagen{
						margin:2px;
						height:160px;
					}
					
					#at{
						color:#ffffff;
						font-weight:bold;
						text-decoration:none;
						position:relative;
						top:-26px;
						height:15px;
					}
					
					.colum{
						font-size:5px;
						color:#000000;
					}
					
					.columt{
						font-size:6px;
						color:#000000;
					}
					
					.f{
						font-size:6px;
						color:#B4B4B4;
					}
					.img{
						width:80px;
					}
					.pagenav a,b{
						color:#FFFFFF;
					}
					
				</style>
			</head>
			<body>			
			
			<table align="center" >
				<tr>
					<td>
						<img src="'.$image.'/logo2.jpg">
					</td>
				</tr>
			</table>
			<div height="10px"></div>
			
			';
		$pie='
			</body>
		</html>
		';
		
		$contenido=$this->input->post('contenido');
		
		$foot=$this->input->post('foot');
		 
		$html=$cabecera.$contenido."<div height='10px'></div>".$foot.$pie;
		
		$data['conf']=$this->layout->settings;
		extract($data);
		ob_start();
			echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $html)).'<?php ');
			$_html=ob_get_contents();
		@ob_end_clean();
		//echo $_html;
		pdf_create($_html, 'CATALOGO');
	}
	
	function prueba(){
		echo $this->config->item('base_url');
		$parametros= func_get_args();
		//echo $_direccion;
		$_fnombre=array_shift($parametros);
		echo $_arch_nombre=implode('-',$parametros);
		echo "assets/default/css/formatos.css"."</br>";
		echo $this->config->item('base_url');
		echo "uploads/inventario/image/sgiext10f_.jpg";
	}
	
	
}


?>