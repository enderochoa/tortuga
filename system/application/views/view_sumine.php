<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
//$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['itsumine'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_itsumine_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itsumine(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos=$this->db->escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin; 
if($form->_status!='show'){
		$uri  =$this->datasis->get_uri();
		$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='view_sumi_saldo' AND uri='$uri'");
		$modblink=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>
<style >
.ui-autocomplete {
    max-height: 150px;
    overflow-y: auto;
    max-width: 600px;
  }
   html.ui-autocomplete {
    height: 150px;
    width: 600px;
  }
</style>

<script language="javascript" type="text/javascript">
	//$.ajaxSetup({
	//	'beforeSend' : function(xhr) {
	//		xhr.overrideMimeType('text/html; charset=<?=$this->config->item('charset'); ?>');
	//	}
	//});
	
itsumine_cont=<?=$form->max_rel_count['itsumine'] ?>;
function get_uadmin(){
	$.post("<?=site_url('presupuesto/requisicion/getadmin') ?>",{ uejecuta:$("#uejecutora").val() },function(data){$("#td_uadministra").html(data);})
}
	
datos    ='';
com=false;
$(function() {
	$(".inputnum").numeric(".");
	
	$(document).keydown(function(e){
		//alert(e.which);
		if (18 == e.which) {
			com=true;
			//c = String.fromCharCode(e.which);
			return false;
		}
		if (com && (e.which == 61 || e.which == 107)) {
		  add_itsumine();
		  a=itsumine_cont-1;
		  $("#codigo_"+a).focus();
			//alert("agrega linea");
				com=false;
				return false;
			}else if (com && e.which != 16 && e.which == 17){
				com=false;
			}
			return true;
		});

		$.post("<?=site_url('suministros/sumi/autocomplete')?>",{ partida:"" },function(data){
			datos=jQuery.parseJSON(data);
		});
});

function auto(){
	$("input[name^='codigo_']").focus(function(){
		id=this.name.substr(7,100);
		$( "#codigo_"+id).autocomplete({
			minLength: 0,
			source: datos,
			focus: function( event, ui ) {
			$( "#codigo_"+id).val( ui.item.codigo );
				return false;
			},
			select: function( event, ui ) {
				$( "#codigo_"+id).val( ui.item.codigo );
				$( "#descripcion_"+id).val( ui.item.descrip );
				$( "#unidad_"+id).val( ui.item.unidad );
				$( "#solicitado_"+id).focus();
				return false;
			}
		})
		.data( "autocomplete" )._renderItem = function( ul, item ) {			
			return $( "<li></li>" )
			.data( "item.autocomplete", item )
			.append( "<a>" + item.codigo + " - " + item.descrip+ "</a>" )
			.appendTo( ul );
		};
	});
	
	$("input[name^='codigo_']").focusout(function(){
			id=this.name.substr(7,100);
			$( "#solicitado_"+id).focus();
		});
}


function modbusdepen(i){
	var id = i.toString();
	var caub;
	caub=$("#caub").val();
	var link='<?=$modblink ?>/'+caub;
	link =link.replace(/<#i#>/g,id);
	vent=window.open(link,'ventbuscarsumi','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'); 
	vent.focus(); 
	document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarppla\');vent.close();');
}

function cal_total(){
	tot=0;
	for(i=0;i<itsumine_cont;i++){
		id=i.toString();
		valor=parseFloat($("#cantidad_"+id).val());
		if(!isNaN(valor))
			tot=tot+valor;
		$("#tcantidad").val(tot);
	}
}

$(document).ready(function(){ 
    auto();
  });

					
function add_itsumine(){
	var htm = <?=$campos ?>;
	can = itsumine_cont.toString();
	con = (itsumine_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	$("#cantidad_"+can).numeric(".");
	itsumine_cont=itsumine_cont+1;
	auto();
}
					
function del_itsumine(id){
	id = id.toString();
	$('#tr_itsumine_'+id).remove();
}
</script>
<?php
	}else{
	?>
	<script language="javascript" type="text/javascript">
		function btn_anula(i){
			if(!confirm("Esta Seguro que desea Reversar la Nota de Entrega"))
				return false;
			else
				window.location='<?=site_url('suministros/sumine/reversar')?>/'+i
		}
	</script>
<?
}
?>

<table align='center'width="80%" >
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td align=left>
						<?php echo $container_tl?>
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
					<td colspan=6 class="bigtableheader">Nota de Entrega Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
				</tr>
				<tr>
					<td class="littletablerowth"><?=$form->caub->label  ?>*&nbsp;</td>
					<td class="littletablerow"  ><?=$form->caub->output ?>&nbsp; </td>
					<td class="littletablerowth"><?=$form->status->label  ?>*&nbsp;</td>
					<td class="littletablerow"  ><?=$form->status->output ?>&nbsp; </td>
				</tr> 
				<tr>
					<td class="littletablerowth"><?=$form->alma->label ?>&nbsp;</td>
					<td class="littletablerow"  ><?=$form->alma->output ?>&nbsp;</td>
					<td class="littletablerowth"><?=$form->fecha->label  ?>*&nbsp;</td>
					<td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth"        ><?=$form->conc->label     ?>&nbsp;</td>
					<td class="littletablerow" colspan=3><?=$form->conc->output    ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletablerowth"        ><?=$form->observacion->label     ?>&nbsp;</td>
					<td class="littletablerow" colspan=3><?=$form->observacion->output    ?>&nbsp;</td>
				</tr>
		</table><br />

			<table width='100%'>
     		<tr>
			    <td class="littletableheaderb"               >Codigo*           </td>
			    <td class="littletableheaderb"               >Descripci&oacute;n</td>
			    <td class="littletableheaderb"               >Unidad            </td>
			    <td class="littletableheaderb" align="right" >Solicitado        </td>
			    <td class="littletableheaderb" align="right" >Cantidad          </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletableheaderb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <?php for($i=0;$i<$form->max_rel_count['itsumine'];$i++) { 
					$obj1="codigo_$i";
					$obj2="descripcion_$i";
					$obj3="cantidad_$i";
					$obj4="unidad_$i";
					$obj5="solicitado_$i";
			  ?>
			  <tr id='tr_itsumine_<?=$i ?>'>
			    <td class="littletablerow"              ><?=$form->$obj1->output ?></td>
			    <td class="littletablerow"              ><?=$form->$obj2->output ?></td>
			    <td class="littletablerow"              ><?=$form->$obj4->output ?></td>
			    <td class="littletablerow" align="right"><?=$form->$obj5->output ?></td>
			    <td class="littletablerow" align="right"><?=$form->$obj3->output ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_itsumine(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			   
			  <tr id='__UTPL__'>
			    <td class="littletablefooterb" colspan="5">&nbsp;</td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb" align="right">&nbsp;</td>
			    <?php } ?>
			  </tr>
	    </table>
		<?php echo $form_end     ?>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		<td>
	<tr>
<table>
	
<?php endif; ?>
