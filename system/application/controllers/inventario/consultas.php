<?php
class Consultas extends Controller {
	function Consultas(){
		parent::Controller(); 
		$this->load->library("rapyd");
		//$this->datasis->modulo_id('30A',1);
		define ("THISFILE",   APPPATH."controllers/compras/". $this->uri->segment(2).EXT);
	}

	function index(){
		redirect("inventario/consultas/precios2");	
	}	
	
	function precios2(){
		$this->rapyd->load("dataform","datatable");
		$cod=$this->uri->segment(4);
		
		$script='
		$("#codigo").focus();
			$(document).ready(function() {
				$("a").fancybox();
				$("#codigo").attr("value", "");
				$("#codigo").focus();
			});
			
		$("#df1").submit(function() {
					valor=$("#codigo").attr("value");
					location.href="'.site_url('inventario/consultas/precios2').'/"+valor;
					return false;
				});
		';
		
		$form = new DataForm();
		$form->script($script);
		
		$form->codigo = new inputField("C�digo", "codigo");
		$form->codigo->size=20;
		$form->codigo->insertValue='';
		$form->codigo->append('Presente el articulo frente al lector de codigo de barras o escriba directamente algun codigo de identificacion y luego presione ENTER');

		$form->build_form();
		$contenido = $form->output;
		if(!empty($cod)){
			$data2=$this->rprecios($cod);			
			if($data2){
				$contenido .=$this->load->view('view_rprecios', $data2,true);
			}else{
				$t=array();
				$t[1][1]="<b>PRODUCTO NO REGISTRADO</b>";
				$t[2][1]="";
				$t[3][1]="<b>Por Favor introduzca un Codigo de identificaci&oacute;n del Producto</b>";
				$t[4][1]="Presente el producto en el lector de codigo de barras";
				$t[5][1]="o escriba directamente algun codigo de identificacion y luego presione ENTER";

				$table = new DataTable(null,$t);
				$table->cell_attributes = 'style="vertical-align:middle; text-align: center;"';

				$table->per_row  = 1;
				
				$table->cell_attributes = '';//$t[2][1]="";style="vertical-align:top;" 
				$table->cell_template = "<div style='color:red;' align='center'><#1#></div></br>";
				$table->build();
				$contenido .=$table->output;
				
				}
		}else{
			//$data['content'] = $form->output;
		}
		
		$data['content'] =$contenido;
		$data["head"]    =  script("jquery.js").script("plugins/jquery.fancybox.pack.js").script("plugins/jquery.easing.js").style('fancybox/jquery.fancybox.css').style("ventanas.css").style("estilos.css").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);		
	}
	
	function rprecios($cod_bar=NULL){
		
		$mSQL_p='SELECT precio1,base1, barras,existen, CONCAT_WS(" ",descrip ,descrip2) AS descrip, codigo,marca,alterno,id,modelo,iva,unidad FROM sinv';
		
		$mSQL  =$mSQL_p." WHERE barras='$cod_bar'";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() == 0){
			$mSQL  =$mSQL_p." WHERE codigo='$cod_bar'";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() == 0){
				$mSQL  =$mSQL_p." WHERE alterno='$cod_bar'";
				$query = $this->db->query($mSQL);
				if ($query->num_rows() == 0){
					return false;
				}
			}
		}

		$row = $query->row();
		$data['precio1'] = number_format($row->precio1,2,',','.');
		//$data['precio2'] = number_format($row->precio2,2,',','.');
		$data['descrip'] = $row->descrip;
		$data['base1']   = $row->base1;
		$data['codigo']  = $row->codigo;
		$data['alterno'] = $row->alterno;
		$data['unidad']  = $row->unidad;
		$data['marca']   = $row->marca;
		$data['existen'] = $row->existen;
		$data['barras']  = $row->barras;
		$data['modelo']  = $row->modelo;
		$data['iva']     = $row->iva;
		$data['iva2']    = number_format($row->base1*($row->iva/100),2,',','.');
		//$data['img']     = site_url('inventario/fotos/obtener/'.$row->id);
		$data['moneda']  = 'Bs.F.';		
		
		$this->rapyd->load("datatable");
		$this->load->helper('string');
		
		$table = new DataTable(null);
		$table->cell_attributes = 'style="vertical-align:middle; text-align: center;"';
		
		$table->db->select(array('nombre','id','comentario'));
		$table->db->from("sinvfot");
		$table->db->where("sinv_id='$row->id' ");
		
		$prin=$this->datasis->dameval("select nombre from sinvfot where sinv_id='$row->id' and principal='S'");
		
		$comment=$this->datasis->dameval("select comentario from sinvfot where nombre='$prin'");

		$link=site_url('uploads/inventario/Image/ver/<#nombre#>/');
		$link2=site_url('uploads/inventario/Image/ver/<#nombre#>/');
		
		$scr=reduce_double_slashes(base_url()."uploads/inventario/Image/<#nombre#>");
		
		
		$table->per_row = 6;
		$table->per_page = 60;//onclick='javascript:fancy();'
		$table->cell_attributes = 'style="vertical-align:top; width:126;"';
		$table->cell_template = "</br><div align='center' class='dos'><a title='<#comentario#>' href='$scr' ><img style='margin: 0px 4px' title='<#comentario#>' src='$scr' width='100' height='75' border=0 /></a></div></br>";//<div width='120'></div>
		$table->build();
		$data['comment']=$comment;
		$data['prin']=$prin;
		$data['content'] = $table->output;
		
		//$data["head"]    = script("jquery.js").script("plugins/jquery.fancybox.pack.js").script("plugins/jquery.easing.js").style('fancybox/jquery.fancybox.css').$this->rapyd->get_head();
		
		return $data;
	}
	function sprecios($formato='CPRECIOS'){
		$data['conf']=$this->layout->settings;
		
		$query = $this->db->query("SELECT proteo FROM formatos WHERE nombre='$formato'");
		if ($query->num_rows() > 0){
			$row = $query->row();
			extract($data);
			ob_start();
				echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $row->proteo)).'<?php ');
				$_html=ob_get_contents();
			@ob_end_clean();
			echo $_html;
		}else{
			$this->load->view('view_cprecios', $data);
		}
	}
	
	function ssprecios($formato='CIPRECIOS',$cod_bar=NULL){
		$query = $this->db->query("SELECT proteo FROM formatos WHERE nombre='$formato'");
		if ($query->num_rows() > 0){
			$row = $query->row();
			ob_start();
				echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $row->proteo)).'<?php ');
				$_html=ob_get_contents();
			@ob_end_clean();
			echo $_html;
		}else{
			echo 'Formato $formato no definido';
		}
	}
	
	function precios(){

		$barras = array(
			'name'      => 'barras',
			'id'        => 'barras',
			'value'     => '',
			'maxlength' => '15',
			'size'      => '16',
			//'style'     => 'display:none;',
		);
		
		$out  = form_open('inventario/consultas/precios');
		$out .= form_label("Introduzca un Codigo ");
		$out .= form_input($barras);//form_submit('mysubmit', 'Consultar!');
		$out .= form_close();

		$link=site_url('inventario/consultas/rprecios');

		$data['script']= <<<script
		
<script type="text/javascript">
		$(document).ready(function(){
			$("a").fancybox();
			$("#resp").hide();
			$("#barras").attr("value", "");
			$("#barras").focus();
			$("form").submit(function() {
				mostrar();
				return false;
			});
		});
		  
		  
		function mostrar(){
			$("#resp").hide();
			var url = "$link";
			$.ajax({
				type: "POST",
				url: url,
				data: $("input").serialize(),
				success: function(msg){
					$("#resp").html(msg).fadeIn("slow");
				  $("#barras").attr("value", "");
					$("#barras").focus();
				}
			});
		} 
		  
      
</script>
script;
      
      
		$data['content'] = '<div id="resp" style=" width: 100%;" ></div>';
		$data['title']   = " <center><a title='ender' href='http://192.168.0.99/proteoerp/assets/shared/images/3_b.jpg'><img src='http://192.168.0.99/proteoerp/assets/shared/images/3_s.jpg' /></a>$out</center> ";
		//$data['style']="a {outline: none;}";
		$data["head"]    = script("jquery.js").script("plugins/jquery.fancybox.pack.js").script("plugins/jquery.easing.js").style('fancybox/jquery.fancybox.css').$this->rapyd->get_head();
		//$data2['content']=$this->load->view('view_ventanas_consulta', $data,true);
		$this->load->view('view_ventanas', $data);
	}

	function _gconsul($mSQL_p,$cod_bar,$busca){
		foreach($busca AS $b){
			$mSQL  =$mSQL_p." WHERE ${b}='${cod_bar}' LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() != 0) break;
		}
		if ($query->num_rows() == 0) return false;
		return $query;
	}
}
?>