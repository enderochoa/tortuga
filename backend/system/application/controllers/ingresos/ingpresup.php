<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
class ingpresup extends validaciones {

	var $url="ingresos/ingpresup";

	function Ingpresup(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function  index(){
		redirect($this->url."/filteredgrid");
	}

	function filteredgrid(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load("datafilter","datagrid");

		$filter = new DataFilter("");
		$filter->db->select(array('codigoadm','fondo','codigopres','estimado','recaudado','denominacion','cuenta','codigopres_r','refe',"IF(tipo='o','Ordinario',IF(tipo='e','ExtraOrdinario',IF(tipo='r','Reversion',''))) tipo"));
		$filter->db->from("ingpresup");

		$filter->codigopres = new inputField("C&oacute;digo Presupuesto", "codigopres");
		$filter->codigopres->size=20;
		
		$filter->tipo  = new dropdownField("Tipo", "tipo" );
		$filter->tipo->option('' ,''                      );
		$filter->tipo->option('o','Ingreso Ordinario'     );
		$filter->tipo->option('e','Ingreso Extraordinario');
		$filter->tipo->option('r','Reversion de Pago'     );
		$filter->tipo->style="100px";
		

		$filter->buttons("reset","search");

		$filter->build();

		$uri = anchor($this->url.'/dataedit/show//<#codigopres#>','<#codigopres#>');

		$grid = new DataGrid("Presupuesto de Ingresos");

		$grid->order_by("codigopres","asc");
		$grid->per_page = 20;

		$grid->column_orderby("C&oacute;digo Presup." ,$uri           ,"codigopres"               ,"align='left'");
		$grid->column_orderby("Codigo"                ,"cuenta" ,"cuenta","align='left'");
		$grid->column_orderby("Denominaci&oacute;n"   ,"denominacion" ,"","align='left'");
		$grid->column_orderby("Tipo"                  ,"tipo"         ,"","align='left'");
		$grid->column_orderby("Estimado"              ,"estimado"     ,"","align='left'");
		$grid->column_orderby("Recaudado"             ,"recaudado"    ,"","align='left'");

		$grid->add($this->url."/dataedit/create");

		$grid->build();

		//echo $grid->db->last_query();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script']  = script("jquery.js")."\n";
		$data['title']   = " Presupuesto de Ingresos";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		//$this->datasis->modulo_id(101,1);
		$this->rapyd->load("dataedit");

		$link =site_url('presupuesto/ppla/autocompleteppla');
		$script="
		$(document).ready(function() {
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

		$edit = new DataEdit("Presupuesto de Ingresos","ingpresup");
		$edit->back_url = $this->url;
		$edit->post_process('update'  ,'_post');
		$edit->post_process('insert'  ,'_post');
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->codigopres = new inputField("C&oacute;digo Presupuesto", "codigopres");
		$edit->codigopres->db_name = "codigopres";
		$edit->codigopres->size =20;
		$edit->codigopres->mode = "autohide";
		$edit->codigopres->rule ="required";

		$edit->denominacion = new textareaField("Denominacion", "denominacion");
		$edit->denominacion->rows=2;
		$edit->denominacion->cols=70;
		$edit->denominacion->rule ="required";
		
		$edit->tipo  = new dropdownField("Tipo", "tipo");
		$edit->tipo->option('o','Ingreso Ordinario');
		$edit->tipo->option('e','Ingreso Extraordinario');
		$edit->tipo->option('r','Reversion de Pago');
		$edit->tipo->style="50px";

		$edit->estimado = new inputField("Estimado", "estimado");
		$edit->estimado->size =20;
		//$edit->estimado->mode = "autohide";
		//$edit->estimado->when = array("show","create");

		$edit->cuenta = new inputField("C&oacute;digo ", "cuenta");
		$edit->cuenta->db_name = "cuenta";
		$edit->cuenta->size    =20;
		//$edit->cuenta->mode = "autohide";
		//$edit->cuenta->rule ="required";
		
		$edit->abreviatura = new inputField("Abreviatura", "abreviatura");
		$edit->abreviatura->size      = 15;
		$edit->abreviatura->maxlenght = 20;

		$edit->buttons("add","modify","save", "undo","delete", "back");
		$edit->build();

		$style='

		.ui-autocomplete {
		  max-height: 250px;
		  overflow-y: auto;
		  max-width: 600px;
		}
		 html.ui-autocomplete {
		  height: 250px;
		  width: 600px;
		}

		';

		$data['content'] = $edit->output;
		$data["style"]  = $style;
		$data["head"]    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$data['title']   = ' Presupuesto ';
		$this->load->view('view_ventanas', $data);
	}

	function _post($do){

	}

	function arregla(){
		$result = $this->db->query("SELECT codigoadm,tipo,codigopres FROM presupuesto");
		foreach($result->result() as $row){

			$codigoadm  = $this->db->escape($row->codigoadm);
			$tipo       = $this->db->escape($row->tipo     );
			$codigopres = $row->codigopres;

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
				$query = "INSERT IGNORE INTO presupuesto (`codigoadm`,`tipo`,`codigopres`) values ($codigoadm,$tipo,$c)";
				$this->db->simple_query($query);
			}
			$c = $this->db->escape($this->datasis->traevalor('PARTIDAIVA'));
			$query = "INSERT IGNORE INTO presupuesto (`codigoadm`,`tipo`,`codigopres`) values ($codigoadm,$tipo,$c)";
			$this->db->simple_query($query);
		}
		$this->db->query("CALL sp_presupuestonivelar()");
		$this->db->query("CALL sp_presucalct()");
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
			$query=$this->db->query("SELECT b.codigo,b.denominacion  FROM presupuesto AS a JOIN ppla AS b ON b.codigo=a.codigopres WHERE a.tipo=$tipo AND a.codigoadm=$estruadm AND LENGTH(a.codigopres)<=$cana GROUP BY  b.codigo");
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

	function get_estruprest(){
		$tipo=$this->db->escape($this->input->post('tipo'));
		$estruadm=$this->db->escape($this->input->post('codigoadm'));
		//$ff=explode('.',$this->formatopres);
		//if(count($ff)>2)
		//	$cana=strlen($ff[0].$ff[1])+1;
		//else
		//	$cana=strlen($ff[0])+1;

		if($tipo!==false AND $estruadm!==false){
			$query=$this->db->query("SELECT b.codigo,b.denominacion  FROM presupuesto AS a JOIN ppla AS b ON b.codigo=a.codigopres WHERE a.tipo=$tipo AND a.codigoadm=$estruadm AND b.movimiento='S' GROUP BY  b.codigo");
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

	function auto_presupuesto($campo,$cod=FALSE,$fondo=FALSE,$codigoadm = FALSE){

		if($cod!==false){
			$mSQL="SELECT $campo FROM presupuesto WHERE $campo LIKE '$cod%' ";
			if($fondo!==FALSE)
				$mSQL.=" AND tipo = ".$this->db->escape($fondo);
			if($codigoadm!==FALSE)
				$mSQL.=" AND codigoadm = ".$this->db->escape($codigoadm);

			$query=$this->db->query($mSQL);
			if($query->num_rows() > 0){
				foreach($query->result() AS $row){
					echo $row->$campo."\n";
				}
			}
		}
	}


	function auto_presaldo3($cod=FALSE,$codigoadm=''){
		//if($cod!==false){
		  $codigoadm=$this->input->post('codigoadm');
      $fondo=$this->input->post('fondo');
		  $this->db->query('UPDATE valores SET valor="'.$codigoadm.'" where  nombre="ARMAS"');

      if($cod!==false){
  			$mSQL="SELECT codigo AS c1,denominacion AS c2 FROM v_presaldo WHERE movimiento = 'S' AND codigopres LIKE '$cod%'";//
  			if(!empty($codigoadm))
  				$mSQL.=" AND codigoadm = ".$this->db->escape($codigoadm);

  			if(!empty($fondo))
  				$mSQL.=" AND fondo = ".$this->db->escape($fondo);

  			$query=$this->db->query($mSQL);


  			$salida = '';
  			if($query->num_rows() > 0){
  				foreach($query->result() AS $row){
  					$salida.=$row->c1.'|'.$row->c2.",";
  					$salida.=$row->$campo.",";
  				}
  			}
  			echo $salida;
		  }
	}

	function auto_presaldo($codigoadm='',$fondo=''){//,
		//if($cod!==false){
			$mSQL="SELECT codigo AS c1,denominacion AS c2 FROM v_presaldo WHERE saldo > 0 AND movimiento = 'S'";
			if(!empty($codigoadm) && $codigoadm!='null')
				$mSQL.=" AND codigoadm = ".$this->db->escape($codigoadm);

			if(!empty($fondo) && $fondo!='null')
				$mSQL.=" AND fondo = ".$this->db->escape($fondo);

			$query=$this->db->query($mSQL);
			$salida = '';
			if($query->num_rows() > 0){
				foreach($query->result() AS $row){
					echo $salida.=$row->c1.'|'.$row->c2."\n";
				}
			}
			//echo $salida;

		//}
	}

	function auto_presaldo2(){
		//if($cod!==false){
			$mSQL="SELECT codigo AS c1,denominacion AS c2 FROM v_presaldo WHERE saldo > 0 AND movimiento = 'S'";

			$query=$this->db->query($mSQL);
			$salida = '';
			if($query->num_rows() > 0){
				foreach($query->result() AS $row){
					$salida.=$row->c1.'|'.$row->c2."\n";
				}
			}
			echo $salida;
		//}
	}

	function instalar(){
		$query="
		CREATE TABLE `ingpresup` (
			`codigoadm` VARCHAR(12) NOT NULL DEFAULT '',
			`fondo` VARCHAR(20) NOT NULL DEFAULT '',
			`codigopres` VARCHAR(25) NOT NULL DEFAULT '',
			`estimado` DECIMAL(19,2) NULL DEFAULT '0.00',
			`recaudado` DECIMAL(19,2) NULL DEFAULT '0.00',
			`denominacion` TEXT NULL,
			PRIMARY KEY (`codigopres`)
		)
		COLLATE='utf8_general_ci'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT
		";
		$this->db->simple_query($query);
		$query="ALTER TABLE `ingpresup`  ADD COLUMN `cuenta` VARCHAR(25) NULL";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `ingpresup` ADD COLUMN `tipo` CHAR(1) NULL";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `ingpresup` ADD COLUMN  `codigopres_r` VARCHAR(25) NULL DEFAULT NULL";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `ingpresup` ADD COLUMN  `refe` MEDIUMTEXT NULL";
		$this->db->simple_query($query);
		
		$query="ALTER TABLE `ingpresup`	ADD COLUMN `abreviatura` VARCHAR(25) NULL ";
		$this->db->simple_query($query);
	}

}

?>
