<?php
$status   = $form->get_from_dataobjetct('status');

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if (($form->_status=='modify') || $form->_status=='create')
	$container_as=join("&nbsp;", $form->_button_status[$form->_status]["AS"]);
else
	$container_as = '';
	
if ($form->_status=='create' || $form->_status=='modify' )
	$container_re=join("&nbsp;", $form->_button_status[$form->_status]["RE"]);
	//$container_mb = '';
else
	$container_re = '';

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['asignomi'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_asignomi_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos.=' <td class="littletablerow"><a href=# onclick="del_asignomi(<#i#>);return false;">Eliminar</a></td></tr>';
$campos=$form->js_escape($campos);

foreach($form->detail_fields['retenomi'] AS $ind=>$data)
	$campos2[]=$data['field'];
$campos2='<tr id="tr_retenomi_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos2).'</td>';
$campos2.=' <td class="littletablerow"><a href=# onclick="del_retenomi(<#i#>);return false;">Eliminar</a></td></tr>';
$campos2=$form->js_escape($campos2);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if(true){//$form->_status!='show'
	$uri  =$this->datasis->get_uri();
	//$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='odirect' AND uri='$uri'");
	//$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>
	<script language="javascript" type="text/javascript">
	asignomi_cont=<?=$form->max_rel_count['asignomi'] ?>;
	retenomi_cont=<?=$form->max_rel_count['retenomi'] ?>;

	$(function() {
		$(".inputnum").numeric(".");
		$("#temp").hide();
	});

	function cal_asig(){
		tot =0;
		
		for(i=0;i<asignomi_cont;i++){
			id=i.toString();
			monto  =parseFloat($("#montoa_"+id).val());
			if(isNaN(monto))monto=0;
			tot+=monto;
		}		
		if(!isNaN(tot)){
			$("#asig").val(tot);
		}
	}

	function cal_rete(){
		tot =0;
		
		for(i=0;i<retenomi_cont;i++){
			id=i.toString();
			monto  =parseFloat($("#montor_"+id).val());
			if(isNaN(monto))monto=0;
			tot+=monto;
		}		
		if(!isNaN(tot)){
			$("#rete").val(tot);
		}
	}

	function add_asignomi(){
		var htm = <?=$campos ?>;
		can = retenomi_cont.toString();
		con = (retenomi_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		retenomi_cont=retenomi_cont+1;
		cal_asig();
	}

	function del_asignomi(id){
		id = id.toString();
		$("#montoa_"+id).val('0');
		$('#tr_asignomi_'+id).remove();		
		cal_rete();
	}
	
	function add_retenomi(){

		var htm = <?=$campos2 ?>;
		can = retenomi_cont.toString();
		con = (retenomi_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__RETE__").before(htm);
		$("#montor_"+can).numeric(".");
		retenomi_cont=retenomi_cont+1;
		cal_rete();
	}

	function del_retenomi(id){
		id = id.toString();
		$("#montor_"+id).val('0');
		
		$('#tr_retenomi_'+id).remove();
		$('#tr_retenomi'+id).remove();		
		cal_rete();
	}

	
	</script>
	<?php  
	} 
	?>
	
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
			    <td colspan=6 class="bigtableheader"><?=$this->tits?> Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->fecha->label  ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?//=$form->fdesem->label ?>&nbsp;</td> 
			    <td class="littletablerow"  ><?//=$form->fdesem->output?>&nbsp; </td>
			  </tr>
				<tr>
			    <td class="littletablerowth">            <?=$form->descrip->label   ?>*&nbsp;</td>
			    <td class="littletablerow" colspan="3"  ><?=$form->descrip->output  ?>&nbsp; </td>
			  </tr>
	    	</table><br />
			<table width='100%' bgcolor="#FFEAEB">
			<tr>
			<tr>
     			<th class="littletableheaderb" colspan="<?=($form->_status=='show'?6:7)?>">ASIGNACIONES DE N&oacute;MINA </th>
			</tr>
     		<tr>
     			<td class="littletableheaderb">Est. Administrativa </td>
     			<td class="littletableheaderb">F. Financiamiento   </td>
			    <td class="littletableheaderb">Partida             </td>
			    <td class="littletableheaderb">Ordinal             </td>
			    <td class="littletableheaderb">Denominaci&oacute;n </td>
					<td class="littletableheaderb" align='right'>Monto </td>			
			
			</tr>
			
			  <?php
			  for($i=0;$i<$form->max_rel_count['asignomi'];$i++) {
		  		$obj0="itcodigoadm_$i";
				  $obj1="itfondo_$i"; 
					$obj2="itcodigopres_$i";
					$obj3="itordinal_$i";
					$obj4="itdenomi_$i";
					$obj5="itmontoa_$i";
			  ?>
			  <tr id='tr_pades_<?=$i ?>'>
			    <td class="littletablerow">              <?=$form->$obj0->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj1->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj2->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj3->output ?></td>
			    <td class="littletablerow">              <?//=$form->$obj4->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj5->output ?></td>
			  
			  </tr>
			  <?php } ?>

			  <tr id='__UTPL__'>
			    <td class="littletablefooterb" colspan="5"   align='right'><?=$form->asig->label  ?></td>
			    <td class="littletablefooterb"   align='right'>            <?=$form->asig->output  ?></td>
			  
			  </tr>
	    </table>
	    
		<br />

			<table width='100%' bgcolor="#E2E0F4">
				<tr>
     			<th class="littletableheaderb" colspan="<?=(($form->_status=='create'?9:10))?>">DEDUCCIONES DE N&Oacute;MINA </th>
     		<tr>
     			<td class="littletableheaderb">Est. Administrativa </td>
     			<td class="littletableheaderb">F. Financiamiento   </td>
			    <td class="littletableheaderb">Partida             </td>
			    <td class="littletableheaderb">Ordinal             </td>
			    <td class="littletableheaderb">Denominaci&oacute;n </td>
					<td class="littletableheaderb" align='right'>Monto </td>			
			<?php if($form->_status!='show') {?>
			<td class="littletableheaderb">&nbsp;</td>
			<?php } ?>
			</tr>
			
			  <?php
			  for($i=0;$i<$form->max_rel_count['retenomi'];$i++) {
		  		$obj0="it2codigoadm_$i";
				  $obj1="it2fondo_$i"; 
					$obj2="it2codigopres_$i";
					$obj3="it2ordinal_$i";
					$obj4="it2denomip_$i";
					$obj5="it2montor_$i";
			  ?>
			  <tr id='tr_pades_<?=$i ?>'>
			    <td class="littletablerow">              <?=$form->$obj0->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj1->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj2->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj3->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj4->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj5->output ?></td>
			  
			  	<?php if ($form->_status=='create' || $form->_status=='modify') {?>
			    <td class="littletablerow"><a href=# onclick='del_asignomi(<?=$i ?>);return false;'>Eliminar</a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>

			  <tr id='__RETE__'>
			    <td class="littletablefooterb" colspan="5"   align='right'><?=$form->rete->label  ?></td>
			    <td class="littletablefooterb"   align='right'>            <?=$form->rete->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			   <tr>
			  	<td colspan="3">
			  		<?php echo $container_re ?>
			  	</td>
			  </tr>
			  			 
	    </table>  
		<?php echo $form_end ?>
		
		<td>
	<tr>
<table>
<?php endif; ?>