<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
class Presasig extends validaciones {
	
	var $url='presupuesto/presasig/';
	
	function Presasig(){
		parent::Controller();
		$this->load->library("rapyd");
	}
	
	function  index(){
		redirect($this->url."filteredgrid");
	}
	
	function filteredgrid(){
		//$this->datasis->modulo_id(101,1);
		
		$this->rapyd->load("datafilter","datagrid");
		$this->load->helper('form');
		
		$filter = new DataFilter("","presupuesto");
		$filter->db->select(array("REPLACE(CONCAT(codigoadm,codigopres),'.','') codigo2","CONCAT(codigoadm,'.',codigopres) codigo","codigoadm","codigopres","tipo","denominacion","asignacion"));
		
		$filter->tipo = new inputField("Fuente de Financiamiento", "tipo");
		$filter->tipo->size=20;
		
		$filter->codigoadm = new inputField("Estructura Administrativa", "codigoadm");
		$filter->codigoadm->size=20;
		
		$filter->codigopres = new inputField("C&oacute;digo Presupuesto", "codigopres");
		$filter->codigopres->size=20;
		
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=20;
		$filter->codigo->db_name="CONCAT(codigoadm,'.',codigopres)";
		
		$filter->denominacion = new inputField("Denominaci&oacute;n", "denominacion");
		$filter->denominacion->size=40;
		
		$filter->asignacion = new inputField("Asignaci&oacute;n", "asignacion");
		$filter->asignacion->size=20;
		$filter->asignacion->oper='=';
		$filter->asignacion->clause='where';
		
		$filter->buttons("reset","search");
		
		$filter->build();
		
		function fondo($valor,$codigo){
			$input = new inputField2('Title', '_-_F_-_'.$valor.'_-_'.$codigo);
			$input->status     = 'create';
			$input->size       =10;
			$input->insertValue=$valor;
			$input->style      ="height:18px;width:100px; padding:0px;margin:0px";
			$input->build();
			return $input->output;
		}
		
		function codigo($valor,$codigo){
			$input = new inputField2('Title', '_-_C_-_'.$valor.'_-_'.$codigo);
			$input->status     = 'create';
			$input->size       =20;
			$input->insertValue=$codigo;
			$input->style      ="height:18px;width:200px; padding:0px;margin:0px";
			$input->build();
			return $input->output;
		}
		
		function denominacion($tipo,$codigo,$valor){
			$input = new inputField2('Title', '_-_D_-_'.$tipo.'_-_'.$codigo);
			$input->status     = 'create';
			$input->size       =40;
			$input->insertValue=$valor;
			$input->style      ="height:18px;width:100%; padding:0px;margin:0px";
			$input->build();
			return $input->output;
		}
		
		function asignacion($tipo,$codigo,$valor){
			$input = new inputField2('Title', '_-_A_-_'.$tipo.'_-_'.$codigo);
			$input->status     = 'create';
			$input->size       =10;
			$input->insertValue=$valor;
			$input->css_class  ='inputnum';
			$input->style      ="height:18px;width:100%; padding:0px;margin:0px";
			$input->build();
			return $input->output;
		}
		
		$options = array(
                  'small'  => 'Small Shirt',
                  'med'    => 'Medium Shirt',
                  'large'   => 'Large Shirt',
                  'xlarge' => 'Extra Large Shirt',
                );

		
		$selected=$this->datasis->dameval("SELECT fondo FROM fondo WHERE fondo LIKE '%SITUADO%'");
		$options=$this->datasis->consularray("SELECT fondo,descrip FROM fondo WHERE LENGTH(fondo)>0 ORDER BY descrip");
		$form1=form_dropdown('fondo',$options,$selected);
		//$form1=form_input(array('name'=> $n='fondo'       ,'id'=> $n,'size' => '10','style'=> '','class'=>''));
		$form2=form_input(array('name'=> $n='codigo'      ,'id'=> $n,'size' => '25','style'=> '','class'=>''));
		$form3=form_input(array('name'=> $n='denominacion','id'=> $n,'size' => '40','style'=> '','class'=>''));
		$form4=form_input(array('name'=> $n='asignacion'  ,'id'=> $n,'size' => '20','style'=> '','class'=>'inputnum'));
		
		$data = array(
		'name' => 'crear',
		'id'   => 'crear',
		'value' => 'Crear Partida',
		'content' => 'Crear Partida'
		);
	
		$table="
		<table class='tableheader'>
		<tr><td>F.Financiamiento</td><td>C&oacute;digo Presupuestario</td><td>Denominaci&oacute;n</td><td>Asignaci&oacute;n</td></tr>
		<tr><td>$form1</td><td>$form2</td><td>$form3</td><td>$form4</td><td>".form_button($data)."</td></tr>
		</table>";
		
		
		$grid = new DataGrid($table);
		$grid->use_function('fondo','codigo','denominacion','asignacion');
		$grid->order_by("codigoadm","asc");
		$grid->per_page=$per_page = 100;

		$grid->column("F. Financiamiento"            ,"<fondo><#tipo#>|<#codigo#>|</fondo>"                                ,"align='left'  style='border-bottom:0px;margin:0px;padding:0px'");
		$grid->column("C&oacute;digo Presupuestario" ,"<codigo><#tipo#>|<#codigo#>|</codigo>"                              ,"align='left'  style='border-bottom:0px;margin:0px;padding:0px'");
		$grid->column("Denominaci&oacute;n"          ,"<denominacion><#tipo#>|<#codigo#>|<#denominacion#></denominacion>"  ,"align='left'  style='border-bottom:0px;margin:0px;padding:0px'");
		$grid->column("Asignaci&oacute;n"            ,"<asignacion><#tipo#>|<#codigo#>|<#asignacion#></asignacion>"        ,"align='right' style='border-bottom:0px;margin:0px;padding:0px'");
		
		$grid->build();

		$mask=str_replace('X','9',$this->datasis->traevalor('FORMATOESTRU').'.'.$this->datasis->traevalor('FORMATOPRES'));
		$script  ='<script language="javascript" type="text/javascript">

		$(function(){			
			$("#crear").click(function(){
				fondo=$("fondo").val();
				codigo=$("codigo").val();
				denominacion=$("denominacion").val();
				asignacion=$("asignacion").val();
				$.post("'.site_url($this->url.'crea').'",{f:fondo,c:codigo,d:denominacion,a:asignacion },function(data){
					$("#row_1").before(data);
				});
			});
		
			$("input[name^=\'_-_C\']").setMask("'.$mask.'");
			$("input[name^=\'codigo\']").setMask("'.$mask.'");
			$("input[name^=\'_-_\']").change(function(){
				var caja =$(this).attr("name");
				id=caja.substring(4,100);
				
				f=$("#_-_F"+id).val();
				c=$("#_-_C"+id).val();
				d=$("#_-_D"+id).val();
				a=$("#_-_A"+id).val();
				
				if(c.length <'.strlen($this->datasis->traevalor('FORMATOESTRU')).'){
					alert("El Codigo Presupuestario esta incompleto");
					return false;
				}
				
				$.post("'.site_url($this->url.'cambio').'",{ cod:caja,fondo:f,codigo:c,denomi:d,asigna:a },function(data){
					if(data=="SI"){
						$("#_-_C"+id).attr("name",c);
						$("#_-_C"+id).attr("id",c);
						$("#_-_F"+id).attr("name",f);
						$("#_-_F"+id).attr("id",f);
						$("#_-_D"+id).attr("name",c);
						$("#_-_D"+id).attr("id",c);
						$("#_-_A"+id).attr("name",f);
						$("#_-_A"+id).attr("id",f);
					}
				});
			});
		});
		</script>';

		
		//echo $grid->db->last_query();
		
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['script']  = script("jquery.js").script('jquery-ui.js').script('plugins/jquery.meiomask.js').$script;
		$data['title']   = 'Formulaci&oacute;n de Presupuesto';
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function cambio(){
		$fondo   =$this->input->post('fondo' );
		$codigo  =$this->input->post('codigo');
		$denomi  =$this->input->post('denomi');
		$asigna  =$this->input->post('asigna');
		$cod     =$this->input->post('cod');
		$anterior=explode('_-_',$cod);
		
		array_splice($anterior,0,1);
		
		$formatoestru=$this->datasis->traevalor('FORMATOESTRU');
		$codigoadm   =substr($codigo,0,strlen($formatoestru));
		$codigopres  =substr($codigo,strlen($formatoestru)+1,100);
		$data =array('codigoadm'=>$codigoadm,'tipo'=>$fondo,'codigopres'=>$codigopres,'denominacion'=>$denomi,'asignacion'=>$asigna);
		
		$where=array('codigoadm'=>str_replace('_','.',substr($anterior[2],0,strlen($formatoestru))),'tipo'=>str_replace('_','.',$anterior[1]),'codigopres'=>str_replace('_','.',substr($anterior[2],strlen($formatoestru)+1,100)));
		$cant=$this->datasis->dameval("SELECT COUNT(*) FROM presupuesto where CONCAT(codifoadm,'.',codigopres)=$codigoe AND tipo=$$fondoe ");
		
		$this->db->query($query);
		
		$this->db->insert('presupuesto',$data );
		echo "SI";
	}
	
	function crea(){
		$fondo   =$this->input->post('f');
		$codigo  =$this->input->post('c');
		$denomi  =$this->input->post('d');
		$asigna  =$this->input->post('a');
		
		$formatoestru=$this->datasis->traevalor('FORMATOESTRU');
		$codigoadm   =substr($codigo,0,strlen($formatoestru));
		$codigopres  =substr($codigo,strlen($formatoestru)+1,100);
		
		$where=array('codigoadm'=>$codigoadm,'tipo'=>$fondo,'codigopres'=>$codigopres);
		
		$campos='<tr class="odd">
			<td class="littletablerow" align="left" style="border-bottom:0px;margin:0px;padding:0px">
				<input id="_-_F_-_00-SITUADO_-_01_01_00_51_4_03_18_01_0" class="input" type="text" style="height:18px;width:100px; padding:0px;margin:0px" onchange="" onclick="" size="10" maxlength="" value="00-SITUADO" name="_-_F_-_00-SITUADO_-_01_01_00_51_4_03_18_01_0">
			</td>
			<td class="littletablerow" align="left" style="border-bottom:0px;margin:0px;padding:0px">
				<input id="_-_C_-_00-SITUADO_-_01_01_00_51_4_03_18_01_0" class="input" type="text" style="height:18px;width:200px; padding:0px;margin:0px" onchange="" onclick="" size="20" value="01.01.00.51.4.03.18.01.0" name="_-_C_-_00-SITUADO_-_01_01_00_51_4_03_18_01_0">
			</td>
			<td class="littletablerow" align="left" style="border-bottom:0px;margin:0px;padding:0px">
				<input id="_-_D_-_00-SITUADO_-_01_01_00_51_4_03_18_01_0" class="input" type="text" style="height:18px;width:100%; padding:0px;margin:0px" onchange="" onclick="" size="50" maxlength="" value="ender" name="_-_D_-_00-SITUADO_-_01_01_00_51_4_03_18_01_0">
			</td>
			<td class="littletablerow" align="right" style="border-bottom:0px;margin:0px;padding:0px">
				<input id="_-_A_-_00-SITUADO_-_01_01_00_51_4_03_18_01_0" class="inputnum" type="text" style="height:18px;width:100%; padding:0px;margin:0px" onchange="" onclick="" size="10" maxlength="" value="0.00" name="_-_A_-_00-SITUADO_-_01_01_00_51_4_03_18_01_0">
			</td>
		</tr>';
		
		echo $campos;
		//echo $campos=js_escape($campos);
	}
}
?>
