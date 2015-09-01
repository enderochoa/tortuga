<?php
$status   = $form->get_from_dataobjetct('status');
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if (($form->_status=='modify') && ($status == 'P' ))
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

foreach($form->detail_fields['itingresos'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_itingresos_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos.=' <td class="littletablerow"><a href=# onclick="del_itingresos(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos=$form->js_escape($campos);

foreach($form->detail_fields['ingmbanc'] AS $ind=>$data)
	$campos2[]=$data['field'];
$campos2='<tr id="tr_mbanc_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos2).'</td>';
$campos2.=' <td class="littletablerow"><a href=# onclick="del_mbanc(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
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
	ingresos_cont=<?=$form->max_rel_count['itingresos'] ?>;
	mbanc_cont   =<?=$form->max_rel_count['ingmbanc'] ?>;

	$(function(){
		cal_tot();
		cal_totm();
		$(".inputnum").numeric(".");
	});

	function cal_totm(){
		tmonto =0;
		for(i=0;i<mbanc_cont;i++){
			id=i.toString();
			monto   =parseFloat($("#montom_"+id).val());
                        tipo_doc=$("#tipo_docm_"+id).val();
                        if(tipo_doc=='DP' || tipo_doc=='NC'){
                            if(isNaN(monto))monto=0;
                            tmonto+=monto;
                        }else{
                            if(isNaN(monto))monto=0;
                            tmonto-=monto;
                        }
		}
		if(!isNaN(tmonto))
			$("#totalch").val(tmonto);
	}

	function cal_tot(){
		tmonto =0;
		tdcto  =0;
		tbruto =0;
		for(i=0;i<ingresos_cont;i++){
			id=i.toString();
			monto=parseFloat($("#itmonto_"+id).val());
			dcto =parseFloat($("#itdcto_"+id).val());
			bruto=parseFloat($("#itbruto_"+id).val());
			if(isNaN(monto))monto=0;
			if(isNaN(bruto))bruto=0;
			if(isNaN(dcto)){
				dcto=0;
				$("#itdcto_"+id).val(0);
			}
			monto  =bruto-dcto;
			tmonto+=monto;
			tbruto+=bruto;
			tdcto +=dcto;

			$("#itmonto_"+id).val(Math.round((parseFloat(bruto)-parseFloat(dcto))*100)/100);
		}
		if(!isNaN(tmonto))
			$("#total").val(tmonto);
		if(!isNaN(tbruto))
			$("#tbruto").val(tbruto);
		if(!isNaN(tdcto))
			$("#tdcto").val(tdcto);
	}

	function add_itingresos(){
		var htm = <?=$campos ?>;
		can = ingresos_cont.toString();
		con = (ingresos_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#monto_"+can).numeric(".");
		ingresos_cont=ingresos_cont+1;
		cal_tot();
	}

	function del_itingresos(id){
		id = id.toString();
		$("#monto_"+id).val('0');
		$('#tr_itingresos_'+id).remove();
		cal_tot();
	}

	function add_mbanc(){
		var htm = <?=$campos2 ?>;
		can = mbanc_cont.toString();
		con = (mbanc_cont+1).toString();
		cin = (mbanc_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__MBANCUTPL__").before(htm);
		$("#montom_"+can).numeric(".");
		mbanc_cont=mbanc_cont+1;

		ante=$("#benefim_"+cin).attr('selectedIndex');
		$("#benefim_"+can).attr('selectedIndex',ante);

		ante=$("#benefim_"+cin).val();
		$("#benefim_"+can).val(ante);

		ante=$("#observam_"+cin).attr('selectedIndex');
		$("#observam_"+can).attr('selectedIndex',ante);

		ante=$("#observam_"+cin).val();
		$("#observam_"+can).val(ante);
	}

	function del_mbanc(id){
		id = id.toString();
		$("#montom_"+id).val('0');

		$('#tr_mbanc_'+id).remove();
		$('#tr_mbanc'+id).remove();
		cal_tot();
		cal_totm();
	}

	function btn_anular(i){
		if(!confirm("Esta Seguro que desea Anular el Ingreso"))
			return false;
		else
			window.location='<?=site_url('ingresos/ingmbanc2/anular')?>/'+i
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
					<td class="littletablerowth"><?=$form->tipod->label ?>&nbsp;</td>
					<td class="littletablerow"  ><?=$form->tipod->output?>&nbsp;</td>
					<td class="littletablerowth"><?=$form->recibo->label ?>&nbsp;</td>
					<td class="littletablerow"  ><?=$form->recibo->output?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth"><?=$form->fecha->label  ?>*&nbsp;</td>
					<td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp; </td>
					<td class="littletablerowth"><?=$form->fpago->label ?>&nbsp;</td>
					<td class="littletablerow"  ><?=$form->fpago->output?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth"><?=$form->tipo->label  ?>&nbsp;</td>
					<td class="littletablerow"  ><?=$form->tipo->output ?>&nbsp; </td>
					<td class="littletablerowth"><?=$form->npago->label ?>&nbsp;</td>
					<td class="littletablerow"  ><?=$form->npago->output?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth"             ><?=$form->recibido->label  ?>&nbsp;</td>
					<td class="littletablerow"   colspan="3" ><?=$form->recibido->output ?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth"            ><?=$form->concepto->label  ?>&nbsp;</td>
					<td class="littletablerow"   colspan="3"><?=$form->concepto->output ?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth"><?=$form->planillas->label ?>&nbsp;</td>
					<td class="littletablerow"  ><?=$form->planillas->output?>&nbsp; </td>
					<td class="littletablerowth">Quincena/Mes/A&ntilde;o</td>
					<td class="littletablerow"  ><?=$form->quincena->output.$form->mes->output.$form->ano->output?>&nbsp; </td>
				</tr>
								<tr>
					<td class="littletablerowth"><?=$form->articulos->label ?>&nbsp;</td>
					<td class="littletablerow"  ><?=$form->articulos->output?>&nbsp; </td>
					<td class="littletablerowth"></td>
					<td class="littletablerow"  ></td>
				</tr>

			</table><br />
			<table width='100%' bgcolor="#FFEAEB" class="table_detalle">
			<tr>
			<tr>
			<th class="littletableheaderb" colspan="<?=($form->_status=='show'?7:8)?>">INGRESOS PRESUPUESTARIOS  </th>
			</tr>
			<tr>
					<td class="littletableheaderb"              >Partida             </td>
					<td class="littletableheaderb"              >Denominaci&oacute;n </td>
					<td class="littletableheaderb"              >Recibo Inicio       </td>
					<td class="littletableheaderb"              >Recibo Fin          </td>
					<td class="littletableheaderb" align='right'>Monto Bruto         </td>
					<td class="littletableheaderb" align='right'>Descuento           </td>
					<td class="littletableheaderb" align='right'>Monto               </td>
			<?php if($form->_status!='show') {?>
			<td class="littletableheaderb">&nbsp;</td>
			<?php } ?>
			</tr>
				<?php
				for($i=0;$i<$form->max_rel_count['itingresos'];$i++) {

					$obj0="itcodigopres_$i";
					$obj1="itdenomi_$i";
					$obj2="itreferen1_$i";
					$obj3="itreferen2_$i";
					$obj4="itmonto_$i";
					$obj5="itbruto_$i";
					$obj6="itdcto_$i";
					?>
					<tr id='tr_itingresos_<?=$i ?>'>
					<td class="littletablerow">              <?=$form->$obj0->output ?></td>
					<td class="littletablerow">              <?=$form->$obj1->output ?></td>
					<td class="littletablerow">              <?=$form->$obj2->output ?></td>
					<td class="littletablerow">              <?=$form->$obj3->output ?></td>
					<td class="littletablerow" align="right"><?=$form->$obj5->output ?></td>
					<td class="littletablerow" align="right"><?=$form->$obj6->output ?></td>
					<td class="littletablerow" align="right"><?=$form->$obj4->output ?></td>
					<?php
					if (($form->_status=='create' || $form->_status=='modify') &&  $status!='C') {?>
						<td class="littletablerow"><a href=# onclick='del_itingresos(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
					<?php
					} ?>
					</tr>
					<?php
				} ?>
				<tr id='__UTPL__'>
					<td class="littletablefooterb" colspan="3"  align='left'><?=$container_pa ?>          </td>
					<td class="littletablefooterb" colspan="1"  align='right'><strong>Totales</strong>    </td>
					<td class="littletablefooterb"              align='right'><?=$form->tbruto->output  ?></td>
					<td class="littletablefooterb"              align='right'><?=$form->tdcto->output   ?></td>
					<td class="littletablefooterb"              align='right'><?=$form->total->output   ?></td>
					<?php if($form->_status!='show') {?>
					<td class="littletablefooterb">&nbsp;</td>
					<?php } ?>
				</tr>
			</table>

			<table width='100%' bgcolor="#E2E0F4">
				<tr>
					<th class="littletableheaderb" colspan="<?=(($form->_status=='create'?11:12))?>">MOVIMIENTOS BANCARIOS </th>
				<tr>
					<th class="littletableheaderb"              >Multiple                </th>
					<th class="littletableheaderb"              >Id                      </th>
					<th class="littletableheaderb"              >Estado                  </th>
					<th class="littletableheaderb"              >Banco                   </th>
					<th class="littletableheaderb"              >Tipo                    </th>
					<th class="littletableheaderb"              >Transaccion             </th>
					<th class="littletableheaderb"align='center'>Fecha                   </th>
					<th class="littletableheaderb"align='right' >Monto                   </th>
					<th class="littletableheaderb"              >A nombre de             </th>
					<th class="littletableheaderb"              >Concepto                </th>
					<? if($form->_status=='show'){
					?>
					<th class="littletableheaderb">&nbsp;</th>
					<?php
					}else{
					?>
					<th class="littletableheaderb">&nbsp;</th>
					<?php
					}?>
				</tr>
				<?php
				for($i=0;$i<$form->max_rel_count['ingmbanc'];$i++) {
					$obj0 = "itstatusm_$i";
					$obj1 = "itcodbancm_$i";
					$obj2 = "ittipo_docm_$i";
					$obj3 = "itchequem_$i";
					$obj4 = "itfecham_$i";
					$obj5 = "itmontom_$i";
					$obj7 = "itbenefim_$i";
					$obj6 = "itobservam_$i";
					$obj9 = "itidm_$i";
					$obj10= "itmultiplem_$i";
					?>
					<tr id='tr_mbanc_<?=$i ?>'>
						<td class="littletablerow"               ><?=$form->$obj10->output  ?> </td>
						<td class="littletablerow"               ><?=$form->$obj9->output  ?> </td>
						<?php
						$mid     = $form->_dataobject->get_rel('ingmbanc','id',$i);
						$mstatus = $form->_dataobject->get_rel('ingmbanc','status',$i); ?>
						<?php
						if ($status=='D2' && $form->_status=='show' && $mstatus=='E2' ) {?>
							<td class="littletablerow"><a href="<?=site_url($this->datasis->traevalor('LINKCHEQUE','forma/ver/CHEQUE/','link a usar para el formato del cheque').'/'.$mid)?>" >IMPRIMIR</a></td>
						<?php
						}elseif($status=='D2' && $form->_status=='show' ){
						?>
							<td class="littletablerow"><?=$form->$obj0->output  ?></td>
						<?php
						}else{
						?>
							<td class="littletablerow"           ><?=$form->$obj0->output  ?> </td>
						<?php
						}?>
						<td class="littletablerow"               ><?=$form->$obj1->output  ?> </td>
						<td class="littletablerow"               ><?=$form->$obj2->output  ?> </td>
						<td class="littletablerow"               ><?=$form->$obj3->output  ?> </td>
						<td class="littletablerow"               ><?=$form->$obj4->output  ?> </td>
						<td class="littletablerow" align='right' ><?=$form->$obj5->output  ?> </td>
						<td class="littletablerow"               ><?=$form->$obj7->output  ?> </td>
						<td class="littletablerow"               ><?=$form->$obj6->output  ?> </td>

						<?php if ($form->_status=='create' || $form->_status=='modify') {?>
							<td class="littletablerow"><a href=# onclick='del_mbanc(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
							<?php
						} ?>
					</tr>
					<?php
				} ?>

				<tr id='__MBANCUTPL__'>
					<td class="littletablefooterb" align='left' colspan="5"><?=$container_mb ?></td>
					<td class="littletablefooterb" align='right' colspan="<?=(($form->_status=='create'?2:2))?>"><?=$form->totalch->label?></td>
					<td class="littletablefooterb" align='right'><?=$form->totalch->output?></td>
					<td class="littletablefooterb" align='right'>&nbsp;</td>
					<td class="littletablefooterb" align='right'>&nbsp;</td>
					<td class="littletablefooterb" align='right'>&nbsp;</td>
				</tr>
			</table>

			<?php echo $form_end     ?>
			<?php echo $container_bl ?>
			<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>
