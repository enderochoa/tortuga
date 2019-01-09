<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='create' || $form->_status=='modify' )
	$container_mb=join("&nbsp;", $form->_button_status[$form->_status]["MB"]);
else
$container_mb='';

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['itanoprox'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_itanoprox_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos.=' <td class="littletablerow"><a href=# onclick="del_itanoprox(<#i#>);return false;">'.image('delete.jpg',"Haz Click aqui para eliminar este item",array("border"=>0,"class"=>"eliminait")).'</a></td></tr>';
$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if(true){//$form->_status!='show'
	$uri  =$this->datasis->get_uri();
	//$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='odirect' AND uri='$uri'");
	//$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>
	<script language="javascript" type="text/javascript">
	com=false;
	$.ajaxSetup({
   'beforeSend' : function(xhr) {
    xhr.overrideMimeType('text/html; charset=<?=$this->config->item('charset'); ?>');
    }
  });
	
	anoprox_cont=<?=$form->max_rel_count['itanoprox'] ?>;

	$(function() {
//---------------
			$(document).keydown(function(e){
				//alert(e.which);
				/*if (18 == e.which) {
					com=true;
					//c = String.fromCharCode(e.which);
					return false;
				}
				if (com && e.which == 61) {
					alert("agrega linea");
					com=false;
					return false;
				}else if (com && e.which == 109) {
					alert("quita linea");
					com=false;
					return false;
				}else if (com && e.which != 16 && e.which == 17){
					com=false;
				}*/
				return true;
			});
//---------------


		$(".inputnum").numeric(".");
		$("#ayuda").hide();
		$("#ayuda").dialog({
			height: 500,
			width: 600,
			modal: true,
			title:"Ayuda",
			autoOpen: false
		});
		$("#ayudae").click(function() {
			$("#ayuda").dialog("open");
		});

		$("input[name='btn_termina']").attr("title","Haz click aqui una vez que se halla terminado de realizar una proyecci&oacute;n. Este paso es Obligatorio para ser tomado en cuenta el Documento");
		$("input[name='btn_add_anoprox']").attr("title","Haz Click aqui para agregar un nuevo Bien, o una nueva fila ");
		$("input[name='btn_submit']").attr("title","Haz Click aqui para Guardar los datos. Este paso es necesario, de lo contrario se perderan los datos suministrados en el Formulario");
		$("input[name='btn_undo']").attr("title","Haz Click aqui para cancelar la modificacion, y perder los nuevos cambios realizados");
		$("input[name='btn_back']").attr("title","Haz Click aqui para ir al modulo de vista y busqueda de las proyecciones guardadas");
		$("input[name='btn_modify']").attr("title","Haz Click aqui para Modficar el Documento Actual");
		$("input[name='btn_delete']").attr("title","Haz Click aqui para Eliminar este Documento");
		$("input[name='btn_anula']").attr("title","Haz Click aqui para Anular un Documento, esto Indica que el Documento No sera eliminado del sistema , pero tampoco sera tomado en cuenta");

		$(".input,.select,.textarea,.button,.button_add_rel,.modbus,.eliminait,.inputnum").tooltip({ 
		    track: true, 
		    delay: 0,
		    showURL: false, 
		    opacity: 1,
		    fixPNG: true, 
		    showBody: " - ", 
		    extraClass: "pretty fancy", 
		    top: -15,
		    left: 5
		});
		auto();
	});

	function formato(row) {
		return row[0] + "-" + row[1];
	}

	function formato2(row) {
		return row[0];
	}

	function findValue(li) {
	}
	
	$(document).ready(function(){	
		auto();
		mascara();
		teclasrapidas();
	});

	function mascara(){
		$("input[name^='itcodigopres_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOPATRI"))?>');		
	}

	function teclasrapidas(){
		/*$(document).bind('keydown', 'Alt', function(e){
			a=1+anoprox_cont;
			add_itanoprox();
			$("#itcodigopres_"+a).val('hola');
		});*/

	}

	function get_uadmin(){
		$.post("<?=$link ?>",{ uejecuta:$("#uejecuta").val() },function(data){
			$("#td_uadministra").html(data);
			})
	}

	function cal_nppla(id){
		id = id.toString();
		a = $("#itcodigopres_"+id).val();		
		$.ajax({
			type: "POST",
			url: "<?=site_url("presupuesto/ppla/denomi")?>",
			data:"codigo="+a,
			success: function(data){
				$("#itdenomia_"+id).val(data);
			}
		});
	}
	
	function auto(){
		data = "<?=site_url('presupuesto/ppla/autocomplete2/')?>";
		$("input[name^='itcodigopres_']").autocomplete(""+data,
				{
					delay:10,
					minChars:1,
					matchSubset:1,
					//onItemSelect:selectItem,
					onFindValue:findValue,
					autoFill:false,
					maxItemsToShow:10,
					width:600,
					formatItem:formato
				}
			);

		data = "<?=site_url('presupuesto/anoprox/autocomplete/descrip/')?>";
		$("input[name^='itdescrip_']").autocomplete(""+data,
				{
					delay:10,
					minChars:1,
					matchSubset:1,
					//onItemSelect:selectItem,
					onFindValue:findValue,
					autoFill:false,
					maxItemsToShow:10,
					width:200,
					formatItem:formato2
				}
			);

		data = "<?=site_url('presupuesto/anoprox/autocomplete/descripd/')?>";

		$("input[name^='itcodigopres_']").blur(function(){
			cod =$(this).attr("name");
			v = cod.substr(13,1);
			cal_nppla(v);
		});
	}

	function add_itanoprox(){
		var htm = <?=$campos ?>;
		can = anoprox_cont.toString();
		con = (anoprox_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#itcant_"+can).numeric(".");
		$("#itcodigopres_"+can).val("4.");
		anoprox_cont=anoprox_cont+1;
		auto();
		mascara();
		//teclasrapidas();
	}

	function del_itanoprox(id){
		id = id.toString();
		$("#cant_"+id).val('0');
		$('#tr_itanoprox_'+id).remove();
		auto();
		//teclasrapidas();
	}

	function btn_anular(i){
		if(!confirm("Esta Seguro que desea Anular el Documento"))
			return false;
		else
			window.location='<?=site_url('presupuesto/anoprox/anular')?>/'+i
	}
		
	</script>
<?php
}
?>
	
<table align='center' width="98%" >
	<tr>
		<td width="100%">
			<table width="100%">
				<tr>
				<td>
				<div id='ayuda'>
				
	La Ventana que esta Observando es llamada Maestro/Detalle: </br>
	     Esta sirve para Crear,Modificar y alg&uacute;na otro opci&oacute;n que aparezca en la botonera</br>
	     Pasos A Realizar:</br>
	     Complete los campos que se encuentran en el encabezado(Responsable, Direccion,Unidad Administrativa de ser necesario, Concepto)</br>
	     Luego Agreque los Datos Solicitados en el Detalle (partida,unidad,bien,Descripcion Detallada,Cantidad).</br>
	     Si desea Agregar otro item, presione el bot&oacute;n AGREGAR BIEN</br>
	     </br>
	     NOTA:Al Posicionarse Sobre cada Campo aparecera un mensaje con las intrucciones para completar dicho campo</br>
	     </br>
	     Una vez Completado el registro del formulario debe presionar el boton de Guardar</br>
	     Luego cuando este seguro de la requicision presione el boton Marcar como terminado</br>
	     El Siguiente paso es Presionar el boton Imprimir que se encuentra en la parte Superior Izquierda. Este Abrira un Documento PDF el gual Puede ser guardado o impreso</br>	     
	     </br>
	Para Mayor Informaci&oacute;n visite el <a target='_blank' href='http://dremanva2.fdns.net/tortugawiki/pmwiki.php/Manual/UsoGeneral3'>http://dremanva2.fdns.net/tortugawiki/pmwiki.php/Manual/UsoGeneral3</a> o </br>
	contacte a tortuga@proteoerp.org</br></br>
	
	Vea un Video de Ejemplo <a target='_blank' href='http://tortuga.proteoerp.org/html5.html'>aqu&iacute;</a>  </br></br>
	contacte a tortuga@proteoerp.org</br></br>
</div>
				<div id="ayudae" style="font-size:14pt;color:red;cursor:help;"><strong>Haz Clik Aqui para ver un Instructivo de como usar el m&oacute;dulo</strong></div>
				</td>
				<td align=right>
					<?php echo $container_tr?>
				</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%"  style="margin:0;width:100%;">
			  <tr>
			    <td colspan=6 class="bigtableheader"><?=$this->tits?> Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->responsable->label  ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->responsable->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->fecha->label ?>&nbsp;</td> 
			    <td class="littletablerow"  ><?=$form->fecha->output?>&nbsp; </td>
			  </tr>
			  <tr>
			    <td class="littletablerowth">                    <?=$form->uejecuta->label  ?>*&nbsp;</td>
			    <td class="littletablerow"  >                    <?=$form->uejecuta->output ?>&nbsp; </td>
			  </tr>
				
	    	</table><br />
			<table width='100%' bgcolor="#FFEAEB" class="table_detalle">
			<tr>
			<tr>
     			<th class="littletableheaderb" colspan="<?=($form->_status=='show'?7:8)?>">Descripci&oacute;n de Bienes </th>
			</tr>
     		<tr>
     			<td class="littletableheaderb">Actividad*                    </td>
     			<td class="littletableheaderb">Partida*                      </td>
			    <td class="littletableheaderb">Denominaci&oacute;n*          </td>
			    <td class="littletableheaderb">Unidad*                       </td>
			    <td class="littletableheaderb">Bien*                         </td>
			    <td class="littletableheaderb">Descripci&oacute;n Detallada* </td>
					<td class="littletableheaderb" align='right'>C&aacute;ntidad*</td>			
			<?php if($form->_status!='show') {?>
			<td class="littletableheaderb">&nbsp;</td>
			<?php } ?>
			</tr>
			
			  <?php
			  for($i=0;$i<$form->max_rel_count['itanoprox'];$i++) {
			  	$obj6="itcodigoadm_$i";
		  		$obj0="itcodigopres_$i";
				$obj1="itdenomia_$i";
				$obj2="itunidad_$i";
				$obj3="itdescrip_$i";
				$obj4="itdescripd_$i";
				$obj5="itcant_$i";
			  ?>
			  <tr id='tr_itanoprox_<?=$i ?>'>
			  	<td class="littletablerow">              <?=$form->$obj6->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj0->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj1->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj2->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj3->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj4->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj5->output ?></td>
			  
			  	<?php if ($form->_status=='create' || $form->_status=='modify') {?>
			    <td class="littletablerow"><a  href=# onclick='del_itanoprox(<?=$i ?>);return false;'><?=image('delete.jpg','Haz Click aqui para eliminar este item.</br>De esta manera se eliminara toda la fila seleccionada',array("border"=>0,"class"=>"eliminait"))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			  <tr id='__UTPL__'>
			  	<td class="littletablefooterb"               align='right'>&nbsp;              </td>
			  	<td class="littletablefooterb"               align='right'><?=$container_mb ?> </td>
			    <td class="littletablefooterb" colspan="5"   align='right'>&nbsp; </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>
	    </table>
	 		<?php echo $form_end     ?>
		<td>
	<tr>
<table>
<a href="<?=site_url('forma/ver/ANOPROX/').'/'.$this->uri->segment(5)?>" target="_blank"><?=image('btn-imprimir.gif','Haz Click aqui para Abrir el Documento .PDF.</br>De esta manera podra guardarlo o imprimirlo',array("border"=>0,"class"=>"eliminait","width"=>"60px"))?></a>
<?php endif; ?>
