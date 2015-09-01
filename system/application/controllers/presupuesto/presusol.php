<?
class presusol extends Controller {
	
	var $url="presupuesto/presusol/";
	
	function presusol(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->formatopres=$this->datasis->traevalor('FORMATOPRES');
		$this->flongpres  =strlen(trim($this->formatopres)); 
	}
	
	function  index(){
		redirect($this->url."/filteredgrid");
	}
	
	function filteredgrid(){
		//$this->datasis->modulo_id(101,1);
		
		$this->rapyd->load("datafilter","datagrid");
		
		$filter = new DataFilter("","presusol");
		
		$filter->fondo = new inputField("Tipo", "tipo");
		$filter->fondo->size=20;
		
		$filter->codigoadm = new inputField("Estructura Administrativa", "codigoadm");
		$filter->codigoadm->size=20;
		
		$filter->codigopres = new inputField("C&oacute;digo Presupuesto", "codigopres");
		$filter->codigopres->size=20;
		
		$filter->buttons("reset","search");
		
		$filter->build(); 
		
		$uri = anchor($this->url.'/dataedit/show/<#tipo#>/<#codigoadm#>/<#codigopres#>','<#tipo#><#codigoadm#><#codigopres#>');
		$uri_2 = anchor($this->url.'/dataedit/create/<raencode><#tipo#></raencode>/<#codigoadm#>/<#codigopres#>','Duplicar');
		
		$grid = new DataGrid("Lista de Sectores");
		
		$grid->order_by("codigoadm","asc");
		$grid->per_page = 20;
		
		$grid->column("Tipo"                  ,$uri           ,"align='left'"  );
		$grid->column("Estructura Adm."       ,"codigoadm"    ,"align='left'"  );
		$grid->column("C&oacute;digo Presup." ,"codigopres"   ,"align='left'"  );
		$grid->column("Denominaci&oacute;"    ,"denominacion" ,"align='left'"  );
		$grid->column("Solicitado"            ,"solicitado"   ,"align='left'"  );
		$grid->column("Asignado"              ,"asignacion"   ,"align='left'"  );
		$grid->column("Duplicar"              ,$uri_2         ,"align='center'");
		
		$grid->add($this->url."/dataedit/create");
		
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "Formulacion de Presupuesto";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit($status='',$fondo='',$codigoadm='',$codigopres=''){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("dataobject","dataedit");
		
		$link =site_url('presupuesto/ppla/autocompleteppla');
		$script="
		$(document).ready(function() {
			$('#codigopres#').setMask('9.99.99.99.99.99');
			$.post('$link',{ partida:'' },function(data){
				datos=jQuery.parseJSON(data);
				
				$('#codigopres').autocomplete({
					delay: 0,
					minLength: 4,
					source: datos,
					focus: function( event, ui ) {
						$( '#codigopres').val( ui.item.codigo );
						$( '#denominacion').val( ui.item.denominacion );
						return false;
					},
					select: function( event, ui ) {
						$( '#codigopres').val( ui.item.codigo );
						return false;
					}
				})
				.data( 'autocomplete' )._renderItem = function( ul, item ) {
					return $( '<li></li>' )
					.data( 'item.autocomplete', item )
					.append( '<a>' +item.codigo+'-'+ item.denominacion + '</a>' )
					.appendTo( ul );
				};
			});
		});
		";
		
		$do = new DataObject("presusol");
		if($status=="create" && !empty($fondo) && !empty($codigoadm) && !empty($codigopres)){
			$keys=array(
			"tipo"       =>$fondo,
			"codigoadm"  =>$codigoadm,
			"codigopres" =>$codigopres
			);
			$do->load($keys);
		}
		
		$edit = new DataEdit("Presupuesto",$do);
		$edit->back_url = "presupuesto/presusol";
		$edit->post_process('update'  ,'_post');
		$edit->post_process('insert'  ,'_post');
		$edit->script($script,'create');
		$edit->script($script,'modify');
		
		$edit->codigoadm = new dropdownField("Estructura Administrativa","codigoadm");
		$edit->codigoadm->options("SELECT codigo,CONCAT_WS(' ',codigo,denominacion) FROM estruadm WHERE LENGTH(codigo)=(SELECT LENGTH(valor) from valores WHERE nombre='FORMATOESTRU')");
		$edit->codigoadm->mode = "autohide";
		$edit->codigoadm->rule ="required";
		
		$edit->tipo = new dropdownField("Fuente de Financiamiento","tipo");
		$edit->tipo->options("SELECT fondo, fondo AS val FROM fondo");
		$edit->tipo-> style='width:150px;';
		$edit->tipo->mode = "autohide";
		$edit->tipo->rule ="required";
		
		$edit->codigopres = new inputField("C&oacute;digo Presupuesto", "codigopres");
		$edit->codigopres->db_name = "codigopres";
		$edit->codigopres->size=20;
		$edit->codigopres->mode = "autohide";
		$edit->codigopres->rule ="required";
		
		$edit->denominacion = new textareaField("Denominacion", "denominacion");
		$edit->denominacion->rows=2;
		$edit->denominacion->cols=70;
		$edit->denominacion->rule ="required";
		 
		$edit->asignacion = new inputField("Asignado", "asignacion");
		$edit->asignacion->size=20;
		//$edit->asignacion->mode = "autohide";
		//$edit->asignacion->when = array("show");
    
		$edit->solicitado = new inputField("Solicitado", "solicitado");
		$edit->solicitado->size=20;
		//$edit->solicitado->mode = "autohide";
		//$edit->solicitado->when = array("show");
    		
		$edit->buttons("modify","save", "undo","delete", "back");
		$edit->build();
		
		$style="
		.ui-autocomplete {
		  max-height: 250px;
		  overflow-y: auto;
		  max-width: 600px;
		}
		 html.ui-autocomplete {
		  height: 250px;
		  width: 600px;
		}
		";
		
		$data['content'] = $edit->output;
		$data["style"]  = $style;
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$data['title']   = ' Presupuesto ';
		$this->load->view('view_ventanas', $data);
	}
	
	function _post($do){
		$codigoadm      = $do->get('codigoadm' );
		$tipo           = $do->get('tipo'      );
		$codigopres     = $do->get('codigopres');
		$denominacion   = $do->get('denominacion');
		$codigoadm      = $this->db->escape($codigoadm);
		$tipo           = $this->db->escape($tipo     );
		
		$codigos = explode('.',$codigopres);
		$max     = count($codigos);
		
		$i=0;
		for($i=0 ; $i<$max-1;$i++){
			$temp = array();
			$j=0;
			for($j=0 ; $j<=$i;$j++){
				$temp[$j]=$codigos[$j];
			}
			$c = $this->db->escape(implode('.',$temp));
			$query = "INSERT IGNORE INTO presusol (`codigoadm`,`tipo`,`codigopres`,`denominacion`) values ($codigoadm,$tipo,$c,$denominacion)";
			$this->db->simple_query($query);
		}
		$c = $this->db->escape($this->datasis->traevalor('PARTIDAIVA'));
		$denominacion = $this->datasis->dameval("SELECT denominacion FROM ppla WHERE codigo=$c");
		$query = "INSERT IGNORE INTO presusol (`codigoadm`,`tipo`,`codigopres`,`denominacion`) values ($codigoadm,$tipo,$c,$denominacion)";
		$this->db->simple_query($query);
	}
	
	function get_tipo(){
		$codigoadm=$this->input->post('codigoadm');
		$codigoadm=$this->db->escape($codigoadm);
		if($codigoadm!==false){
			$query=$this->db->query("SELECT tipo FROM presusol WHERE codigoadm=$codigoadm GROUP BY  tipo");
			if($query){
				if($query->num_rows()>0){
					echo "<option value=''>Seleccionar</option>";
					foreach($query->result() AS $fila ){
						echo "<option value='".$fila->tipo."'>".$fila->tipo."</option>";
					}
				}else{
					echo "<option value=''>No hay registros disponibles</option>";
				}
			}
		}
	}
	
	
	function get_estruadm(){
		$tipo=$this->input->post('tipo');
		if($tipo!==false){
			$query=$this->db->query("SELECT b.codigo,b.denominacion  FROM estruadm b JOIN presusol a ON b.codigo=a.codigoadm WHERE a.tipo='$tipo' GROUP BY  b.codigo");
			if($query){
				if($query->num_rows()>0){
					echo "<option value=''>Seleccionar</option>";
					foreach($query->result() AS $fila ){
						echo "<option value='".$fila->codigo."'>".$fila->codigo.' '.$fila->denominacion."</option>";
					}
				}else{
					echo "<option value=''>No hay registros disponibles</option>";
				}
			}
		}
	}
  
	function get_estrupres(){
		$tipo=$this->db->escape($this->input->post('tipo'));
		$estruadm=$this->db->escape($this->input->post('codigoadm'));
		$ff=explode('.',$this->formatopres);
		if(count($ff)>2)
			$cana=strlen($ff[0].$ff[1])+1;
		else
			$cana=strlen($ff[0])+1;
		if($tipo!==false AND $estruadm!==false){
			$query=$this->db->query("SELECT b.codigo,b.denominacion  FROM presusol AS a JOIN ppla AS b ON b.codigo=a.codigopres WHERE a.tipo=$tipo AND a.codigoadm=$estruadm AND LENGTH(a.codigopres)<=$cana GROUP BY  b.codigo");
			if($query){
				if($query->num_rows()>0){
					echo "<option value=''>Seleccionar</option>";
					foreach($query->result() AS $fila ){
						echo "<option value='".$fila->codigo."'>".$fila->codigo.' '.$fila->denominacion."</option>";
					}
				}else{
					echo "<option value=''>No hay registros disponibles</option>";
				}
			}
		}
	}
	
	
}
?>
