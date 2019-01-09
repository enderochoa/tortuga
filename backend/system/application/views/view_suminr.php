<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['itsuminr'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_itsuminr_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itsuminr(<#i#>);return false;">Eliminar</a></td></tr>';
$campos=$this->db->escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){
		$uri  =$this->uri->uri_string();
		$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='sumi' AND uri='$uri'");
		$modblink=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>

<script language="javascript" type="text/javascript">
itsuminr_cont=<?=$form->max_rel_count['itsuminr'] ?>;

	function get_uadmin(){
		$.post("<?=site_url('presupuesto/requisicion/getadmin') ?>",{ uejecuta:$("#uejecutora").val() },function(data){$("#td_uadministra").html(data);})
	}
	
	$(function(){
		$(".inputnum").numeric(".");
	})

function modbusdepen(i){
	var id = i.toString();
	var link='<?=$modblink ?>';
	link =link.replace(/<#i#>/g,id);
	vent=window.open(link,'ventbuscarsumi','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'); 
	vent.focus(); 
	document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarppla\');vent.close();');
}

function cal_total(){
	tot=0;
	for(i=0;i<itsuminr_cont;i++){
		id=i.toString();
		valor3=parseFloat($("#cantidad_"+id).val());
		valor2=parseFloat($("#precio_"+id).val());
		$("#total_"+id).val(valor3*valor2);
		valor=parseFloat($("#total_"+id).val());		
		
		if(!isNaN(valor))
			tot=tot+valor;
		$("#ttotal").val(tot);
	}
}

function cal_cant(){
	tot=0;
	for(i=0;i<itsuminr_cont;i++){
		id=i.toString();
		valor=parseFloat($("#cantidad_"+id).val());
		valor2=parseFloat($("#precio_"+id).val());
		total=parseFloat($("#total_"+id).val());
		
		if(!isNaN(valor))
			tot=tot+valor;

		if(!(valor2>=0))
		$("#precio_"+id).val(0);
		
		if(!(total>=0))
		$("#total_"+id).val(0);
		
	}
	valor3=valor*valor2;
	if(!isNaN(valor3))
		$("#total_"+id).val(valor3);	
	cal_total();
}



					
function add_itsuminr(){
	var htm = <?=$campos ?>;
	can = itsuminr_cont.toString();
	con = (itsuminr_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	$("#cantidad_"+can).numeric(".");
	itsuminr_cont=itsuminr_cont+1;
}
					
function del_itsuminr(id){
	id = id.toString();
	$('#tr_itsuminr_'+id).remove();
}
</script>

<?php
	}else{
	?>
	<script language="javascript" type="text/javascript">
		function btn_anula(i){
			if(!confirm("Esta Seguro que desea Reversar la Nota de Recepcion"))
				return false;
			else
				window.location='<?=site_url('suministros/suminr/reversar')?>/'+i
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
					<td colspan=6 class="bigtableheader">Nota de Recepci&oacute;n Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
				</tr>
				<tr>
					<td class="littletablerowth"><?=$form->caub->label ?>&nbsp;</td>
					<td class="littletablerow"  ><?=$form->caub->output ?>&nbsp;</td>
					<td class="littletablerowth"><?=$form->status->label  ?>*&nbsp;</td>
					<td class="littletablerow"  ><?=$form->status->output ?>&nbsp; </td>
				</tr> 
				<tr>
					<td class="littletablerowth"><?=$form->proveed->label ?>&nbsp;</td>
					<td class="littletablerow" ><?=$form->proveed->output ?>&nbsp;</td>
					<td class="littletablerowth"><?=$form->fecha->label  ?>*&nbsp;</td>
					<td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth"        ><?=$form->alma->label     ?>&nbsp;</td>
					<td class="littletablerow" colspan=3><?=$form->alma->output    ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletablerowth"        ><?=$form->conc->label     ?>&nbsp;</td>
					<td class="littletablerow" colspan=3><?=$form->conc->output    ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletablerowth"><?=$form->observacion->label     ?>&nbsp;</td>
					<td class="littletablerow" colspan=3><?=$form->observacion->output    ?>&nbsp;</td>
				</tr>
			  
	    </table><br />
<?=anchor_popup('suministros/sumi/dataedit/create','Agregar Suministro') ?>
			<table width='100%'>
     		<tr>
			    <td class="littletableheaderb">Codigo*     </td>
			    <td class="littletableheaderb">Descripci&oacute;n</td>
			    <td class="littletableheaderb" align="right" >Cantidad</td>
			    <td class="littletableheaderb" align="right" >Precio</td>
			    <td class="littletableheaderb" align="right" >Total</td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletableheaderb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <?php for($i=0;$i<$form->max_rel_count['itsuminr'];$i++) { 
					$obj1="codigo_$i";
					$obj2="descripcion_$i";
					$obj3="cantidad_$i";
					$obj4="precio_$i";
					$obj5="total_$i";
			  ?>
			  <tr id='tr_itsuminr_<?=$i ?>'>
			    <td class="littletablerow"><?=$form->$obj1->output ?></td>
			    <td class="littletablerow" ><?=$form->$obj2->output ?></td>
			    <td class="littletablerow" align="right"><?=$form->$obj3->output ?></td>
			    <td class="littletablerow" align="right"><?=$form->$obj4->output ?></td>
			    <td class="littletablerow" align="right"><?=$form->$obj5->output ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_itsuminr(<?=$i ?>);return false;'>Eliminar</a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			   
			  <tr id='__UTPL__'>
			    <td class="littletablefooterb" align="right">&nbsp;</td>
			    <td class="littletablefooterb" align="right">&nbsp;<?=$form->tcantidad->label  ?></td>
			    <td class="littletablefooterb" align="right"><?=$form->tcantidad->output ?>&nbsp;</td>
			    <td class="littletablefooterb" align="right">Total</td>
			    <td class="littletablefooterb" align="right"><?=$form->ttotal->output ?>&nbsp;</td>
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
