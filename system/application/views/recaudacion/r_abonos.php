
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

foreach($form->detail_fields['r_mbanc'] AS $ind=>$data)
	$campos2[]=$data['field'];
$campos2='<tr id="tr_r_mbanc_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos2).'</td>';
$campos2.=' <td class="littletablerow"><a href=# onclick="del_r_mbanc(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos2=$form->js_escape($campos2);

foreach($form->detail_fields['r_abonosit'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_r_abonosit_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos.=' <td class="littletablerow"><a href=# onclick="del_r_abonosit(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin;
if($form->_status!='show'){
	$uri  =$this->datasis->get_uri();
	?>
	<script language="javascript" type="text/javascript">
	r_mbanc_cont   =<?=$form->max_rel_count['r_mbanc'] ?>;
	r_abonosit_cont=<?=$form->max_rel_count['r_abonosit'] ?>;
	punto_codbanc='<?=$punto_codbanc ?>';
	ABONOCODBANCDEFECTO='<?=$ABONOCODBANCDEFECTO ?>';

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
					add_r_abonosit();
					$("#recibo_"+(r_abonosit_cont-1)).val(val.id);
					$("#numerop_"+(r_abonosit_cont-1)).val(val.numero);
					$("#fechap_"+(r_abonosit_cont-1)).val(val.fecha);
					$("#montop_"+(r_abonosit_cont-1)).val(val.monto);
					$("#nombrep_"+(r_abonosit_cont-1)).val(val.nombre);
					
					cal_totr();
					cal_totm();
				}
			});
		});
		<?php 
		if($form->_status=='create'){?>
			add_recibodeurl();
		<?php
		}?>
		
		$('select[name^="tipo_doc_"]').change(function(){
			nom = this.name;
			pos =this.name.lastIndexOf('_');
			if(pos>0){
				id  = this.name.substring(pos+1);
				val = $("#tipo_doc_"+id).val();
				if(val=='DB' || val=='CR' ){
					$("#codbanc_"+id).val(punto_codbanc);
				}
				if(val=='EF'){
					$("#codbanc_"+id).val(ABONOCODBANCDEFECTO);
				}
			}
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
					$('#tr_r_abonosit_'+id).remove();
					r_abonosit_cont=r_abonosit_cont-1;
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
		$("#totrecibos").val(Math.round(tmonto * 100)/100);
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
		$("#totmbanc").val(Math.round(tmonto * 100)/100);
	}
	
	function add_recibodeurl(){
		var response;
		<?php 
		if($recibo>0){?>
			recibo=<?php echo $recibo ?>;
		<?php 
		}else{?>
			recibo=0;
		<?php
		}?>
		if(recibo>0){
			$.ajax({
				type: "POST",
				dataType: 'json',
				url:"<?php echo site_url('recaudacion/r_abonos/damerecibojson/') ?>",
				data: { numero:recibo },
				success: function(data){
					limpia();
					add_r_abonosit();
					$("#recibo_"+(r_abonosit_cont-1)).val( data[0].id);
					$("#numerop_"+(r_abonosit_cont-1)).val(data[0].numero);
					$("#fechap_"+(r_abonosit_cont-1)).val( data[0].fecha);
					$("#montop_"+(r_abonosit_cont-1)).val( data[0].monto);
					$("#nombrep_"+(r_abonosit_cont-1)).val(data[0].nombre);
					
					data2=<?=$porcobrarj ?>;
					
					<?php 
					if($recibo>0){?>
						recibo2=<?php echo $recibo ?>;
					<?php 
					}else{?>
						recibo2=0;
					<?php
					}?>
					
					jQuery.each(data2, function(i, val) {
						if(val.id==recibo2){
							idi =i.toString();
							$('#tr_porcobrar_'+idi).remove();
						}
					});
					
					cal_totr();
					cal_totm();
				}
			});
		}
	}

	function add_r_abonosit(){
		var htm = <?=$campos ?>;
		can = r_abonosit_cont.toString();
		con = (r_abonosit_cont+1).toString();
		cin = (r_abonosit_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#monto_"+can).numeric(".");
		r_abonosit_cont=r_abonosit_cont+1;
		cal_totr()
	}

	function del_r_abonosit(id){
		$('#tr_r_abonosit_'+id).remove();
		cal_totr()
	}

	function add_r_mbanc(){
		var htm = <?=$campos2 ?>;
		can = r_mbanc_cont.toString();
		con = (r_mbanc_cont+1).toString();
		cin = (r_mbanc_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__R_MBANCPL__").before(htm);
		$("#monto_"+can).numeric(".");
		r_mbanc_cont=r_mbanc_cont+1;
		cal_totm()
	}

	function del_r_mbanc(id){
		$('#tr_r_mbanc_'+id).remove();
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
			</tr>
			<tr>
				<td class="littletableheaderb"              >Recibo Ref.         </td>
				<td class="littletableheaderb"              >N&uacute;mero       </td>
				<td class="littletableheaderb"              >Fecha               </td>
				<td class="littletableheaderb" align='right'>Monto               </td>
				<td class="littletableheaderb"              >Nombre              </td>
				<td class="littletableheaderb">&nbsp;</td>
			</tr>
				<?php
				for($i=0;$i<$form->max_rel_count['r_abonosit'];$i++) {
					$obj0="itrecibo_$i";
					$obj1="itnumerop_$i";
					$obj2="itfechap_$i";
					$obj3="itmontop_$i";
					$obj4="itnombrep_$i";
					?>
					<tr id='tr_r_abonosit_<?=$i ?>'>
					<?php if($form->_status=='show') {?>
					<td class="littletablerow">              <?=anchor('recaudacion/r_recibo/dataedit/show/'.$form->$obj0->output,$form->$obj0->output) ?></td>
					<?php }else{ ?>
					<td class="littletablerow">              <?=$form->$obj0->output ?></td>
					<?php } ?>
					<td class="littletablerow">              <?=$form->$obj1->output ?></td>
					<td class="littletablerow">              <?=$form->$obj2->output ?></td>
					<td class="littletablerow" align="right"><?=$form->$obj3->output ?></td>
					<td class="littletablerow"              ><?=$form->$obj4->output ?></td>
					<? if($form->_status=='show'){
						?>
						<td class="littletablerow" ><?=anchor("formatos/descargar/R_RECIBO/".(1*$form->$obj0->output)  ,'Imprimir Recibo') ?></td>
					<?
					}
					?>
					
					<?php
					if (($form->_status=='create' || $form->_status=='modify') &&  $status!='C') {?>
						<td class="littletablerow"><a href=# onclick='del_r_abonosit(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
					<?php
					} ?>
					</tr>
					<?php
				} ?>
				<tr id='__UTPL__'>
					<td class="littletablefooterb" colspan="2" align='left'><?=$container_pa ?> </td>
					<td class="littletablefooterb"             align="right"><?=$form->totrecibos->label ?></td>
					<td class="littletablefooterb"             align="right"><?=$form->totrecibos->output ?></td>
					<td class="littletablefooterb">&nbsp;</td>
					<?php if($form->_status!='show') {?>
					<td class="littletablefooterb">&nbsp;</td>
					<?php } ?>
					<? if($form->_status=='show'){?>
					<td class="littletablefooterb" colspan="">&nbsp;</td>
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
				for($i=0;$i<$form->max_rel_count['r_mbanc'];$i++) {
					$obj1 = "itcodbanc_$i";
					$obj2 = "ittipo_doc_$i";
					$obj3 = "itcheque_$i";
					$obj4 = "itfecha_$i";
					$obj5 = "itmonto_$i";
					?>
					<tr id='tr_r_mbanc_<?=$i ?>'>
						<td class="littletablerow"               ><?=$form->$obj1->output  ?> </td>
						<td class="littletablerow"               ><?=$form->$obj2->output  ?> </td>
						<td class="littletablerow"               ><?=$form->$obj3->output  ?> </td>
						<td class="littletablerow"               ><?=$form->$obj4->output  ?> </td>
						<td class="littletablerow" align='right' ><?=$form->$obj5->output  ?> </td>

						<?php if ($form->_status=='create' || $form->_status=='modify') {?>
							<td class="littletablerow"><a href=# onclick='del_r_mbanc(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
							<?php
						} ?>
					</tr>
					<?php
				} ?>

				<tr id='__R_MBANCPL__'>
					<td class="littletablefooterb" align='left' colspan="2"><?=$container_mb ?></td>
					<td class="littletablefooterb" colspan="2" align="right"><?=$form->totmbanc->label ?></td>
					<td class="littletablefooterb"             align="right"><?=$form->totmbanc->output ?></td>
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
			</tr>
				<?php
				for($i=0;$i<count($porcobrar);$i++) {
					?>
					<tr id='tr_porcobrar_<?=$i ?>'>
					<td class="littletablerow">              <?=form_checkbox('chrecibo_'.$i, '', FALSE);?>&nbsp;</td>
					<td class="littletablerow">              <?=$porcobrar[$i]['numero'  ] ?>&nbsp;</td>
					<td class="littletablerow">              <?=$porcobrar[$i]['fecha'   ] ?>&nbsp;</td>
					<td class="littletablerow" align="right"><?=nformat($porcobrar[$i]['monto'   ]) ?>&nbsp;</td>
					<td class="littletablerow"              ><?=$porcobrar[$i]['nombre'  ] ?>&nbsp;</td>
					</tr>
				<?php
				} ?>
				
			<tr>
				<th class="littletableheaderb" colspan="7">&nbsp;</th>
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
