<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['itmuebles'] AS $ind=>$data)
$campos[]=$data['field'];
$campos='<tr id="tr_itmuebles_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itmuebles(<#i#>);return false;">Eliminar</a></td></tr>';
$campos=$form->js_escape($campos);
if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;

if($form->_status!='show'){
	?>
<script language="javascript" type="text/javascript">
itmuebles_cont=<?=$form->max_rel_count['itmuebles'] ?>;


function add_itmuebles(){
	var htm = <?=$campos ?>;
	can = itmuebles_cont.toString();
	con = (itmuebles_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	
	itmuebles_cont=itmuebles_cont+1;

}
					
function del_itmuebles(id){
	id = id.toString();
	$('#tr_itmuebles_'+id).remove();
}
</script>
<?php } ?>
<table align='center' width="80%">
	<tr>
		<td align=right><?php echo $container_tr?></td>
	</tr>
	<tr>
		<td>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td colspan=6 class="bigtableheader">Expediente Nro. <?php  echo $form->expediente->output ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?=$form->fecha->label  ?>*&nbsp;</td>
				<td class="littletablerow"><?=$form->fecha->output ?>&nbsp;</td>
				<td class="littletablerowth"><?=$form->depende->label  ?>*&nbsp;</td>
				<td class="littletablerow"><?=$form->depende->output ?>&nbsp;</td>
				
			</tr>
			<tr>
				<td class="littletablerowth"><?=$form->direcc->label  ?>&nbsp;</td>
				<td class="littletablerow"'><?=$form->direcc->output ?>&nbsp;</td>
				<td class="littletablerowth"><?=$form->ubica->label     ?>&nbsp;</td>
				<td class="littletablerow"'><?=$form->ubica->output    ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletablerowth"><?=$form->denomi->label     ?>&nbsp;</td>
				<td class="littletablerow" colspan=3><?=$form->denomi->output    ?>&nbsp;</td>
			</tr>

		</table>
		<br>
		</td>
	</tr>
</table>

<table width='100%'>
	<tr>
		
		<td class="littletableheaderb">Grupo</td>
		<td class="littletableheaderb">Sub-Grupo</td>
		<td class="littletableheaderb">C&oacute;digo</td>
		<td class="littletableheaderb">Descripci&oacute;n</td>
		<td class="littletableheaderb">Color</td>
		<td class="littletableheaderb">Modelo</td>
		<td class="littletableheaderb">Cantidad</td>
		<td class="littletableheaderb">Valor</td>
		<td class="littletableheaderb">Secci&oacute;n</td>
		<td class="littletableheaderb">Num_rel</td>
		<?php if($form->_status!='show') {?>
		<td class="littletableheaderb">&nbsp;</td>
		<?php } ?>
	</tr>
	<?php for($i=0;$i<$form->max_rel_count['itmuebles'];$i++) {
		
		$obj1="itgrupo_$i";
		$obj2="itsubgrupo_$i";
		$obj0="itcodigo_$i";
		$obj3="itdescrip_$i";
		$obj5="itcolor_$i";
		$obj6="itmodelo_$i";
		$obj4="itcantidad_$i";
		$obj7="itvalor_$i";
		$obj8="itnumiden_$i";
		$obj9="itseccion_$i";
		?>
	<tr id='tr_itmuebles_<?=$i ?>'>
		
		<td class="littletablerow"><?=$form->$obj1->output ?></td>
		<td class="littletablerow"><?=$form->$obj2->output ?></td>
		<td class="littletablerow"><?=$form->$obj0->output ?></td>
		<td class="littletablerow"><?=$form->$obj3->output ?></td>
		<td class="littletablerow"><?=$form->$obj5->output ?></td>
		<td class="littletablerow"><?=$form->$obj6->output ?></td>
		<td class="littletablerow"><?=$form->$obj4->output ?></td>
		<td class="littletablerow"><?=$form->$obj7->output ?></td>
		<td class="littletablerow"><?=$form->$obj9->output ?></td>
		<td class="littletablerow"><?=$form->$obj8->output ?></td>
		<?php if($form->_status!='show') {?>
		<td class="littletablerow"><a href=#
			onclick='del_itmuebles(<?=$i ?>);return false;'>Eliminar</a></td>
			<?php } ?>
	</tr>
	<?php } ?>
	<tr id='__UTPL__'>
		<td class="littletablefooterb" align="right">&nbsp;</td>
		<td class="littletablefooterb" align="right">&nbsp;</td>
		<td class="littletablefooterb" align="right">&nbsp;</td>
		<td class="littletablefooterb" align="right">&nbsp;</td>
		<td class="littletablefooterb" align="right">&nbsp;</td>
		<td class="littletablefooterb" align="right">&nbsp;</td>
		<td class="littletablefooterb" align="right">&nbsp;</td>
		<td class="littletablefooterb" align="right">&nbsp;</td>
		<td class="littletablefooterb" align="right">&nbsp;</td>
		<td class="littletablefooterb" align="right">&nbsp;</td>

		<?php if($form->_status!='show') {?>
		<td class="littletablefooterb" align="right">&nbsp;</td>
		<?php } ?>
	</tr>
	

</table>

		<?php echo $form_end     ?>
		<?php echo $container_bl ?>

		<?php echo $container_br ?>
<?php endif?>