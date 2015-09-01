<?php
$status   = $form->get_from_dataobjetct('status');
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if (($form->_status=='modify') )
	$container_pa=join("&nbsp;", $form->_button_status[$form->_status]["PA"]);
elseif ($form->_status=='create')
	$container_pa=join("&nbsp;", $form->_button_status[$form->_status]["PA"]);
else
	$container_pa = '';

if ($form->_status=='create' || $form->_status=='modify' )
	$container_mb=join("&nbsp;", $form->_button_status[$form->_status]["MB"]);
	//$container_mb = '';
else
	$container_mb = '';

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['sfpa'] AS $ind=>$data)
	$campos2[]=$data['field'];
$campos2='<tr id="tr_sfpa_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos2).'</td>';
$campos2.=' <td class="littletablerow"><a href=# onclick="del_sfpa(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos2=$form->js_escape($campos2);

foreach($form->detail_fields['itabonos'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_itabonos_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos.=' <td class="littletablerow"><a href=# onclick="del_itabonos(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
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
	sfpa_cont   =<?=$form->max_rel_count['sfpa'] ?>;
	itabonos_cont=<?=$form->max_rel_count['itabonos'] ?>;

	$(function(){
		cal_totm();
		cal_totr();
		$(".inputnum").numeric(".");
		$(":checkbox").change(function(){
			b=$(this).attr("name");
			id  = this.name.substring(9);
			data=<?=$porcobrarj ?>;
			jQuery.each(data, function(i, val) {
				if(i==id){
					$('#tr_porcobrar_'+id).remove();
					limpia();
					add_itabonos();
					$("#recibo_"+(itabonos_cont-1)).val(val.id);
					$("#numerop_"+(itabonos_cont-1)).val(val.numero);
					$("#fechap_"+(itabonos_cont-1)).val(val.fecha);
					$("#montop_"+(itabonos_cont-1)).val(val.monto);
					$("#nombrep_"+(itabonos_cont-1)).val(val.nombre);
					$("#observap_"+(itabonos_cont-1)).val(val.observa);
					
					cal_totr();
					cal_totm();
					
					
				}
			});
		});
	});
	
	function limpia(){
		arr=$('input[name^="monto_"]');
		jQuery.each(arr, function() {
			nom=this.name
			pos=this.name.lastIndexOf('_');
			if(pos>0){
				id  = this.name.substring(pos+1);
				recibo=$("#recibo_"+id).val();
				if(recibo==''){
					$('#tr_itabonos_'+id).remove();
					itabonos_cont=itabonos_cont-1;
				}
			}
		});
	}

	function cal_totm(){
		tmonto =0;
		arr=$('input[name^="monto_"]');
		jQuery.each(arr, function() {
			nom=this.name
			pos=this.name.lastIndexOf('_');
			if(pos>0){
				id  = this.name.substring(pos+1);
				monto=parseFloat($("#monto_"+id).val());
				tmonto+=monto;
			}
		});
		$("#totb").val(Math.round(tmonto * 100)/100);
	}
	
	function cal_totr(){
		tmonto =0;
		arr=$('input[name^="montop_"]');
		jQuery.each(arr, function() {
			nom=this.name
			pos=this.name.lastIndexOf('_');
			if(pos>0){
				id  = this.name.substring(pos+1);
				monto=parseFloat($("#montop_"+id).val());
				tmonto+=monto;
			}
		});
		$("#monto_0").val(Math.round(tmonto * 100)/100);
		$("#totr").val(Math.round(tmonto * 100)/100);
	}

	function add_itabonos(){
		var htm = <?=$campos ?>;
		can = itabonos_cont.toString();
		con = (itabonos_cont+1).toString();
		cin = (itabonos_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#monto_"+can).numeric(".");
		itabonos_cont=itabonos_cont+1;
		cal_totr()
	}

	function del_itabonos(id){
		$('#tr_itabonos_'+id).remove();
		$('#tr_itabonos'+id).remove();
		cal_totr()
	}

	function add_sfpa(){
		var htm = <?=$campos2 ?>;
		can = sfpa_cont.toString();
		con = (sfpa_cont+1).toString();
		cin = (sfpa_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__SFPAPL__").before(htm);
		$("#monto_"+can).numeric(".");
		sfpa_cont=sfpa_cont+1;
		cal_totm()
	}

	function del_sfpa(id){
		$('#tr_sfpa_'+id).remove();
		$('#tr_sfpa'+id).remove();
		cal_totm();
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
					<td colspan=6 class="bigtableheader"><?=$this->tits?> Nro. <?php  echo $form->id->output ?></td>
				</tr>
			</table><br />
			
			
			<table align="center"  <?=($form->_status!='show'?' border="0" cellpadding="0" cellspacing="0"':'')?>  bgcolor="#FFEAEB">
			<tr>
			<th class="littletableheaderb" colspan="<?=($form->_status=='show'?6:7)?>">RECIBOS A CANCELAR</th>
			<? if($form->_status=='show'){ ?>
			<th class="littletableheaderb" colspan="4">VISTA EN PDF</th>
			<th class="littletableheaderb" colspan="4">IMPRIMIR               </th>
			<?
			}
			?>
			</tr>
			<tr>
				<td class="littletableheaderb"              >Recibo Ref.         </td>
				<td class="littletableheaderb"              >N&uacute;mero       </td>
				<td class="littletableheaderb"              >Fecha               </td>
				<td class="littletableheaderb" align='right'>Monto               </td>
				<td class="littletableheaderb"              >Nombre              </td>
				<td class="littletableheaderb"              >Observaci&oacute;n  </td>
				<? if($form->_status=='show'){ ?>
					<td class="littletableheaderb" >Recibo   </td>
					<td class="littletableheaderb" >Patente   </td>
					<td class="littletableheaderb" >Licores   </td>
					<td class="littletableheaderb" >Solvencia   </td>
					<td class="littletableheaderb" >Recibo   </td>
					<td class="littletableheaderb" >Patente   </td>
					<td class="littletableheaderb" >Licores   </td>
					<td class="littletableheaderb" >Solvencia   </td>
				<?
				}
				?>
				<?php if($form->_status!='show') {?>
				<td class="littletableheaderb">&nbsp;</td>
				<?php } ?>
			</tr>
				<?php
				for($i=0;$i<$form->max_rel_count['itabonos'];$i++) {
					$obj0="itrecibo_$i";
					$obj1="itnumerop_$i";
					$obj2="itfechap_$i";
					$obj3="itmontop_$i";
					$obj4="itnombrep_$i";
					$obj5="itobservap_$i";
					?>
					<tr id='tr_itabonos_<?=$i ?>'>
					<td class="littletablerow">              <?=$form->$obj0->output ?></td>
					<td class="littletablerow">              <?=$form->$obj1->output ?></td>
					<td class="littletablerow">              <?=$form->$obj2->output ?></td>
					<td class="littletablerow" align="right"><?=$form->$obj3->output ?></td>
					<td class="littletablerow"              ><?=$form->$obj4->output ?></td>
					<td class="littletablerow"              ><?=$form->$obj5->output ?></td>
					<? if($form->_status=='show'){
						$recibo=$form->_dataobject->get_rel('itabonos','recibo',$i);
						$numerop=$form->_dataobject->get_rel_pointer('itabonos','numerop',$i);
						$tipo   =$form->_dataobject->get_rel_pointer('itabonos','tipop'  ,$i);
						?>
						<td class="littletablerow" ><?=anchor_popup("forma1/ver/IRECIBO/$recibo"  ,$numerop) ?></td>
						<td class="littletablerow" ><?=($tipo==14?anchor_popup("forma1/ver/PATENTE/$recibo"  ,$numerop):'') ?></td>
						<td class="littletablerow" ><?=($tipo==2?anchor_popup("forma1/ver/LICORES/$recibo"  ,$numerop):'') ?></td>
						<td class="littletablerow" ><?=($tipo==22?anchor_popup("forma1/ver/SOLVENCIA/$recibo"  ,$numerop):'') ?></td>
						
						<td class="littletablerow" ><?=anchor_popup("forma1/ver/IRECIBO/$recibo/P"  ,$numerop) ?></td>
						<td class="littletablerow" ><?=($tipo==14?anchor_popup("forma1/ver/PATENTE/$recibo/P"  ,$numerop):'') ?></td>
						<td class="littletablerow" ><?=($tipo==2?anchor_popup("forma1/ver/LICORES/$recibo/P"  ,$numerop):'') ?></td>
						<td class="littletablerow" ><?=($tipo==22?anchor_popup("forma1/ver/SOLVENCIA/$recibo/P"  ,$numerop):'') ?></td>
					<?
					}
					?>
					
					<?php
					if (($form->_status=='create' || $form->_status=='modify') &&  $status!='C') {?>
						<td class="littletablerow"><a href=# onclick='del_itabonos(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
					<?php
					} ?>
					</tr>
					<?php
				} ?>
				<tr id='__UTPL__'>
					<td class="littletablefooterb" colspan="2" align='left'><?=$container_pa ?> </td>
					<td class="littletablefooterb"             align="right"><?=$form->totr->label ?></td>
					<td class="littletablefooterb"             align="right"><?=$form->totr->output ?></td>
					<td class="littletablefooterb" colspan="2"  >&nbsp;</td>
					<?php if($form->_status!='show') {?>
					<td class="littletablefooterb">&nbsp;</td>
					<?php } ?>
					<? if($form->_status=='show'){?>
					<td class="littletablefooterb" colspan="8">&nbsp;</td>
					<?}?>
					
				</tr>
			</table>
</br>
			<table align="center"  <?=($form->_status!='show'?' border="0" cellpadding="0" cellspacing="0"':'')?>  bgcolor="#E2E0F4">
				<tr>
					<th class="littletableheaderb" colspan="<?=(($form->_status=='create'?11:12))?>">MOVIMIENTOS BANCARIOS </th>
				<tr>
					<th class="littletableheaderb"              >Banco                   </th>
					<th class="littletableheaderb"              >Tipo                    </th>
					<th class="littletableheaderb"              >Transaccion             </th>
					<th class="littletableheaderb"align='center'>Fecha                   </th>
					<th class="littletableheaderb"align='right' >Monto                   </th>
					<? if($form->_status=='show'){
					?>
					
					<?php
					}else{
					?>
					<th class="littletableheaderb">&nbsp;</th>
					<?php
					}?>
				</tr>
				<?php
				for($i=0;$i<$form->max_rel_count['sfpa'];$i++) {
					$obj1 = "itcodbanc_$i";
					$obj2 = "ittipo_doc_$i";
					$obj3 = "itcheque_$i";
					$obj4 = "itfecha_$i";
					$obj5 = "itmonto_$i";
					?>
					<tr id='tr_mbanc_<?=$i ?>'>
						<td class="littletablerow"               ><?=$form->$obj1->output  ?> </td>
						<td class="littletablerow"               ><?=$form->$obj2->output  ?> </td>
						<td class="littletablerow"               ><?=$form->$obj3->output  ?> </td>
						<td class="littletablerow"               ><?=$form->$obj4->output  ?> </td>
						<td class="littletablerow" align='right' ><?=$form->$obj5->output  ?> </td>

						<?php if ($form->_status=='create' || $form->_status=='modify') {?>
							<td class="littletablerow"><a href=# onclick='del_sfpa(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
							<?php
						} ?>
					</tr>
					<?php
				} ?>

				<tr id='__SFPAPL__'>
					<td class="littletablefooterb" align='left' colspan="2"><?=$container_mb ?></td>
					<td class="littletablefooterb" colspan="2" align="right"><?=$form->totb->label ?></td>
					<td class="littletablefooterb"             align="right"><?=$form->totb->output ?></td>
					<?php if ($form->_status=='create' || $form->_status=='modify') {?>
						<td class="littletablefooterb">&nbsp;</td>
						<?php
					} ?>
				</tr>
			</table>
			</br>
			<? if($form->_status!='show'){ ?>
			<table align="center" style="font-weight:bold" border="0" bgcolor="#AAE8AB">
			<tr>
			<th class="littletableheaderb" colspan="7">RECIBOS PENDIENTES POR CANCELAR</th>
			</tr>
			<tr>
				<td class="littletableheaderb"              >Cobrar              </td>
				<td class="littletableheaderb"              >N&uacute;mero       </td>
				<td class="littletableheaderb"              >Fecha               </td>
				<td class="littletableheaderb" align='right'>Monto               </td>
				<td class="littletableheaderb"              >Nombre              </td>
				<td class="littletableheaderb"              >Observaci&oacute;n  </td>
			</tr>
				<?php
				for($i=0;$i<count($porcobrar);$i++) {
					?>
					<tr id='tr_porcobrar_<?=$i ?>'>
					<td class="littletablerow">              <?=form_checkbox('chrecibo_'.$i, '', FALSE);?>&nbsp;</td>
					<td class="littletablerow">              <?=$porcobrar[$i]['numero'  ] ?>&nbsp;</td>
					<td class="littletablerow">              <?=dbdate_to_human($porcobrar[$i]['fecha'   ]) ?>&nbsp;</td>
					<td class="littletablerow" align="right"><?=nformat($porcobrar[$i]['monto'   ]) ?>&nbsp;</td>
					<td class="littletablerow"              ><?=$porcobrar[$i]['nombre'  ] ?>&nbsp;</td>
					<td class="littletablerow"              ><?=$porcobrar[$i]['observa' ] ?>&nbsp;</td>
					</tr>
				<?php
				} ?>
				
			<tr>
				<th class="littletableheaderb" colspan="8">&nbsp;</th>
			</tr>
			</table>
			<?
			}
			?>

			<?php echo $form_end     ?>
			<?php echo $container_bl ?>
			<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>
