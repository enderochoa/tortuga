<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['itrequi'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_itstra_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itrequi(<#i#>);return false;">Eliminar</a></td></tr>';
$campos=$this->db->escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin; 
if($form->_status!='show'){
	$uri  =$this->datasis->get_uri();
	$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_presaldo' AND uri='$uri'");
	$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
?>
<script language="javascript" type="text/javascript">
itrequi_cont=<?=$form->max_rel_count['itrequi'] ?>;

$(function() {
	$(".inputnum").numeric(".");
	$("#estadmin").change(function(){
		$.post("<?=site_url('presupuesto/presupuesto/get_tipo')?>",{ codigoadm:$("#estadmin").val() },function(data){$("#fondo").html(data);})
	});
});
		
function get_uadmin(){
	$.post("<?=$link ?>",{ uejecuta:$("#uejecuta").val() },function(data){$("#td_uadministra").html(data);})
}

function modbusdepen(i){
		var id = i.toString();
		var fondo   =$("#fondo").val();
		var estadmin=$("#estadmin").val();
	
		if(fondo.length*estadmin.length == 0){
			alert('Debe Seleccionar primero un fondo y una unidad administrativa');
			return false;
		}
		var link='<?=$modblink2 ?>'+'/'+fondo+'/'+estadmin;
		link =link.replace(/<#i#>/g,id);
		vent=window.open(link,'ventbuscarppla','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'); 
		vent.focus(); 
	
		document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarppla\');vent.close();');
		
	}

function cal_total(){
	tot=can=0;
	for(i=0;i<itrequi_cont;i++){
		id=i.toString();
		valor=parseFloat($("#importe_"+id).val());
		cana =parseFloat($("#cantidad_"+id).val());
		if(!isNaN(valor))
			tot=tot+valor;
		if(!isNaN(cana))
			can=can+cana;
		$("#tcantidad").val(can);
		$("#timporte").val(tot);
	}
}


function cal_importe(i){
	id=i.toString();
	cana  =parseFloat($("#cantidad_"+id).val());
	precio=parseFloat($("#precio_"+id).val());
	op=cana*precio;
	if(!isNaN(op))
		$("#importe_"+id).val(cana*precio);
	cal_total();
}
					
function add_itrequi(){
var htm = <?=$campos ?>;
	can = itrequi_cont.toString();
	con = (itrequi_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	itrequi_cont=itrequi_cont+1;
}
					
function del_itrequi(id){
	id = id.toString();
	$('#tr_itrequi_'+id).remove();
}
</script>
<?php  } ?>


<table align='center'width="80%" >
	<tr>
		<td align=right>
			<?php echo $container_tr?>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%"  style="margin:0;width:100%;">
			  <tr>
			    <td colspan=6 class="bigtableheader">Clasificaci&oacute;n Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
			  </tr>
			  <tr>
			    <td class="littletablerowth">              <?=$form->estadmin->label   ?>    *&nbsp;</td>
			    <td class="littletablerow" id='td_estadmin'><?=$form->estadmin->output ?>    &nbsp; </td>
					<td class="littletablerowth">               <?=$form->fondo->label     ?>    *&nbsp;</td>
					<td class="littletablerow" >                <?=$form->fondo->output    ?>    &nbsp; </td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->uejecuta->label     ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->uejecuta->output    ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->responsable->label  ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->responsable->output ?>&nbsp; </td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->uadministra->label ?>&nbsp;</td>
			    <td class="littletablerow" id='td_uadministra'><?=$form->uadministra->output ?>&nbsp;</td>
			    <td class="littletablerowth">&nbsp;</td>
			    <td class="littletablerow"  >&nbsp;</td>
			  </tr>
			    <td class="littletablerowth"><?=$form->objetivo->label     ?>&nbsp;</td>
			    <td class="littletablerow" colspan=3><?=$form->objetivo->output    ?>&nbsp;</td>
			  </tr>
			  
	    </table><br />

			<table width='100%'>
     		<tr>
			    <td class="littletableheaderb">Unidad*     </td>
			    <td class="littletableheaderb">Descripci&oacute;n</td>
			    <td class="littletableheaderb">Cantidad</td>
			    <td class="littletableheaderb">Precio  </td>
			    <td class="littletableheaderb">Importe </td>
			    <td class="littletableheaderb">Partida </td>
			  </tr>
			  <?php for($i=0;$i<$form->max_rel_count['itrequi'];$i++) { 
					$obj1="itunidad_$i";
					$obj2="itdescrip_$i";
					$obj3="itcantidad_$i";
					$obj4="itprecio_$i";
					$obj5="itimporte_$i";
					$obj6="itpartida_$i";
			  ?>
			  <tr id='tr_itrequi_<?=$i ?>'>
			    <td class="littletablerow"><?=$form->$obj1->output ?></td>
			    <td class="littletablerow"><?=$form->$obj2->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj3->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj4->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj5->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj6->output ?></td>
			  </tr>
			  <?php } ?>
			  
			  <tr id='__UTPL__'>
			    <td class="littletablefooterb">&nbsp;</td>
			    
			    <td class="littletablefooterb" align='right'><?=$form->tcantidad->label  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->tcantidad->output ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->timporte->label   ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->timporte->output  ?></td>
			    <td class="littletablefooterb">&nbsp;</td>
			  </tr>
	    </table>
		<?php echo $form_end     ?>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>