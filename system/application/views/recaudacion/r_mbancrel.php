<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if($form->_status!='show'){
	
	?>
<style >
</style>

	<script language="javascript" type="text/javascript">	
	
	$(document).ready(function(){
		$(".inputnum").numeric(".");
		$(".inputonlynum").numeric("0");
	});
	
	function del_r_mbanc(id){
		id = id.toString();
		$('#tr_r_mbanc_'+id).remove();
	}
	
	</script>
	<?php  
	} 
	?>
	<script language="javascript" type="text/javascript">
	
	</script>
<table align='center'width="98%" >
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
			    <td colspan=6 class="bigtableheader">Relacion Movimiento Bancarios de Ingresos <? echo $form->id->output ?></td>
			  </tr>
			</table>
			<table width="100%"  style="margin:0;width:100%; background:rgb(230,230,250)" >
				
				<tr id="tr_tipo" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->codbanc->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->codbanc->output ?> </td>
				</tr>
				<tr id="tr_rifci" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->tipo_doc->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->tipo_doc->output ?> </td>
				</tr>
				<tr id="tr_nombre" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->cheque->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->cheque->output ?> </td>
				</tr>
				<tr id="tr_telefono" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->monto->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->monto->output ?> </td>
				</tr>
				<tr id="tr_email" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->total->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->total->output ?> </td>
				</tr>
				<tr id="tr_fecha" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->fecha->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->fecha->output ?> </td>
				</tr>
				<tr id="tr_fechaing" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->fechaing->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->fechaing->output ?> </td>
				</tr>
				<tr id="tr_concepto" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->concepto->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->concepto->output ?> </td>
				</tr>
			</table>
			
			<table class="table_detalle" >
			<tr>
				<th  bgcolor="black" colspan="<?=($form->_status=='show'?5:6)?>"><span style="color:white"><STRONG>MOVIMIENTOS BANCARIOS</STRONG></span></th>
			</tr>
     		<tr>
				<th class="littletableheaderb" >Banco          </th>
				<th class="littletableheaderb" >Tipo doc       </th>
				<th class="littletableheaderb" >Fecha          </th>
				<th class="littletableheaderb" >Transaccion    </th>
				<th class="littletableheaderb" >Monto          </th>
			  </tr>
			  <?php 
			  
			  for($i=0;$i<$form->max_rel_count['r_mbanc'];$i++) {
					$obj0="itid_$i";
					$obj1="itabono_$i";
					$obj2="itcodmbanc_$i";
					$obj3="itcodbanc_$i";
					$obj4="ittipo_doc_$i";
					$obj5="itfecha_$i";
					$obj6="itcheque_$i";
					$obj7="itmonto_$i";					
			  ?>
			  <tr id='tr_r_contribuit_<?=$i ?>'>
			    <td class="littletablerow"><?=$form->$obj0->output.$form->$obj1->output.$form->$obj2->output.$form->$obj3->output ?></td>
			    <td class="littletablerow"><?=$form->$obj4->output ?></td>
			    <td class="littletablerow"><?=$form->$obj5->output ?></td>
			    <td class="littletablerow"><?=$form->$obj6->output ?></td>
			    <td class="littletablerow"><?=$form->$obj7->output ?></td>
			  </tr>
			  <?php } ?>
			  
			   <tr id='__UTPL__'>
			    <td class="littletablefooterb" colspan="5">&nbsp;</td>
			  </tr>
	    </table>
		<?php echo $form_end     ?>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>
