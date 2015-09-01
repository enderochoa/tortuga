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

if ($form->_status=='create' || $form->_status=='modify' )
	$container_ot=join("&nbsp;", $form->_button_status[$form->_status]["OT"]);
	//$container_mb = '';
else
	$container_ot = '';

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['asignomi'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_asignomi_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos.=' <td class="littletablerow"><a href=# onclick="del_asignomi(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos=$form->js_escape($campos);

foreach($form->detail_fields['retenomi'] AS $ind=>$data)
	$campos2[]=$data['field'];
$campos2='<tr id="tr_retenomi_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos2).'</td>';
$campos2.=' <td class="littletablerow"><a href=# onclick="del_retenomi(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos2=$form->js_escape($campos2);

foreach($form->detail_fields['otrosnomi'] AS $ind=>$data)
	$campos3[]=$data['field'];
$campos3='<tr id="tr_retenomi_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos3).'</td>';
$campos3.=' <td class="littletablerow"><a href=# onclick="del_retenomi(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos3=$form->js_escape($campos3);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin;
if(true){//$form->_status!='show'
	$uri  =$this->datasis->get_uri();
	//$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='odirect' AND uri='$uri'");
	//$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
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
	asignomi_cont=<?=$form->max_rel_count['asignomi'] ?>;
	retenomi_cont=<?=$form->max_rel_count['retenomi'] ?>;
	otrosnomi_cont=<?=$form->max_rel_count['otrosnomi'] ?>;
	var datos    ='';
	var estruadm ='';
	com=false;


	$(document).ready(function() {
		cal_asig();
		$.post("<?=site_url('presupuesto/estruadm/autocompleteui')?>",{ partida:"" },function(data){
			estruadm=jQuery.parseJSON(data);
		});

		$.post("<?=site_url('presupuesto/ppla/autocomplete4/mayor')?>",{ partida:"" },function(data){
			datos=jQuery.parseJSON(data);
		});
	});

	var data;

	function mascara(){
		$("input[name^='codigoadm_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOESTRU"))?>');
		$("input[name^='codigopres_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOPRES"))?>');
	}

	function autop(){
		$("input[name^='codigopres_']").focus(function(){
			id=this.name.substr(11,100);
			$( "#codigopres_"+id).autocomplete({
				minLength: 0,
				source: datos,
				focus: function( event, ui ) {
				$( "#codigopres_"+id).val( ui.item.codigopres );
				$( "#ordinal_"+id).val( ui.item.ordinal );
				$( "#denomi_"+id).val( ui.item.denominacion );
					return false;
				},
				select: function( event, ui ) {
					$( "#codigopres_"+id).val( ui.item.codigopres );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				codigoadm=$("#codigoadm_"+id).val();
				fondo    =$("#fondo_"+id).val();
				if(codigoadm==item.codigoadm && fondo==item.fondo){
					return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.codigopres+'.'+item.ordinal
					+ " - " + item.denominacion+ "</a>" )
					.appendTo( ul );
				}
			};
		});
	}

	function autoe(){
		$("input[name^='codigoadm_']").focus(function(){
			id=this.name.substr(10,100);
			$( "#codigoadm_"+id).autocomplete({
				minLength: 0,
				source: estruadm,
				focus: function( event, ui ) {
				$( "#codigoadm_"+id).val( ui.item.codigoadm );
					return false;
				},
				select: function( event, ui ) {
					$( "#codigoadm_"+id).val( ui.item.codigoadm );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				fondo    =$("#fondo_"+id).val();
				if(fondo==item.fondo){
					return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>"+ item.codigoadm + " - " +item.denominacion+ "</a>" )
					.appendTo( ul );
				}
			};

		});
	}


	$(function() {
		$(".inputnum").numeric(".");
		$("#temp").hide();
		autop();
		autoe();
		mascara();

		$(document).keydown(function(e){
					//alert(e.which)

			if (18 == e.which) {
				com=true;
				//c = String.fromCharCode(e.which);
				return false;
			}

			if (com && (e.which == 61 || e.which == 107)) {
				add_asignomi();
				a=asignomi_cont-1;
				$("#codigopres_"+a).focus();

				com=false;
				return false;
			}else if (com && e.which != 16 && e.which == 17){
				com=false;
			}
			return true;
		});
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
			$("#asig").val(Math.round(tot*100)/100);
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

	function cal_otros(){
		tot =0;

		for(i=0;i<otrosnomi_cont;i++){
			id=i.toString();
			monto  =parseFloat($("#montoro_"+id).val());
			if(isNaN(monto))monto=0;
			tot+=monto;
		}
		if(!isNaN(tot)){
			$("#otros").val(tot);
		}
	}



	function add_asignomi(){
		var htm = <?=$campos ?>;
		can = asignomi_cont.toString();
		con = (asignomi_cont+1).toString();
		cin = (retenomi_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#montoa_"+can).numeric(".");
		asignomi_cont=asignomi_cont+1;
		cal_asig();

		ante=$("#fondo_"+cin).attr('selectedIndex');
		$("#fondo_"+can).attr('selectedIndex',ante);

		ante=$("#codigoadm_"+cin).val();
		$("#codigoadm_"+can).val(ante);


		autop();
		autoe();
		mascara();
	}

	function del_asignomi(id){
		id = id.toString();
		$("#montoa_"+id).val('0');
		$('#tr_asignomi_'+id).remove();
		cal_asig();
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

	function add_otrosnomi(){
		var htm = <?=$campos3 ?>;
		can = otrosnomi_cont.toString();
		con = (otrosnomi_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__OTROS__").before(htm);
		$("#montoro_"+can).numeric(".");
		otrosnomi_cont=otrosnomi_cont+1;
		cal_otros();
	}

	function del_retenomi(id){
		id = id.toString();
		$("#montor_"+id).val('0');

		$('#tr_retenomi_'+id).remove();
		$('#tr_retenomi'+id).remove();
		cal_rete();
	}

	function del_otrosnomi(id){
		id = id.toString();
		$("#montoro_"+id).val('0');

		$('#tr_otrosnomi_'+id).remove();
		$('#tr_otrosnomi'+id).remove();
		cal_otros();
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
			<?
			if($this->datasis->traevalor('USASIPRES')=='S' && $this->datasis->puede(306)){

			?>
				<tr>
					<td colspan=6 class="littletablerowth"><?=anchor($this->url.'sipresrnomi',"Crear Compromiso basado en SIPRES")?></td>
				</tr>
			<?
			}
			?>
			  <tr>
			    <td colspan=6 class="bigtableheader"><?=$this->tits?> Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->fecha->label  ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->compromiso->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->compromiso->output?>&nbsp; </td>
			  </tr>
				<tr>
			    <td class="littletablerowth">            <?=$form->descrip->label   ?>*&nbsp;</td>
			    <td class="littletablerow" colspan="3"  ><?=$form->descrip->output  ?>&nbsp; </td>
			  </tr>
	    	</table><br />
			<table width='100%' bgcolor="#FFEAEB" class="table_detalle">
			<tr>
			<tr>
     			<th class="littletableheaderb" colspan="<?=($form->_status=='show'?6:7)?>">ASIGNACIONES DE N&oacute;MINA </th>
			</tr>
     		<tr>
     			<td class="littletableheaderb">F. Financiamiento   </td>
     			<td class="littletableheaderb">Est. Adm </td>
			    <td class="littletableheaderb">Partida             </td>
			    <td class="littletableheaderb">Denominaci&oacute;n </td>
					<td class="littletableheaderb" align='right'>Monto </td>
			<?php if($form->_status!='show') {?>
			<td class="littletableheaderb">&nbsp;</td>
			<?php } ?>
			</tr>

			  <?php
			  for($i=0;$i<$form->max_rel_count['asignomi'];$i++) {
		  		$obj0="itcodigoadm_$i";
				  $obj1="itfondo_$i";
					$obj2="itcodigopres_$i";
					$obj4="itdenomi_$i";
					$obj5="itmontoa_$i";
			  ?>
			  <tr id='tr_asignomi_<?=$i ?>'>
			    <td class="littletablerow">              <?=$form->$obj1->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj0->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj2->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj4->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj5->output ?></td>

			  	<?php if ($form->_status=='create' || $form->_status=='modify' && $status!='O') {?>
			    <td class="littletablerow">
			    	<a href=# onclick='del_asignomi(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a>
			    </td>
			    <?php } ?>
			  </tr>
			  <?php } ?>

			  <tr id='__UTPL__'>
			    <td class="littletablefooterb" colspan="4"   align='right'><?=$form->asig->label  ?></td>
			    <td class="littletablefooterb"   align='right'>            <?=$form->asig->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <tr>
			  	<td colspan="4">
			  		<?php echo $container_as ?>
			  	</td>
			  </tr>
	    </table>

		<br />

			<table width='100%' bgcolor="#E2E0F4" class="table_detalle">
				<tr>
     			<th class="littletableheaderb" colspan="<?=(($form->_status=='create'?9:10))?>">DEDUCCIONES DE N&Oacute;MINA </th>
     		<tr>
     			<td class="littletableheaderb">             Proveedor               </td>
     			<td class="littletableheaderb">             Concepto                </td>
			    <td class="littletableheaderb"align='right'>Monto                   </td>
					<? if($form->_status!='show'){
					?>
					<td class="littletableheaderb">&nbsp;</td>
					<?php
					}
					?>
			  </tr>
			  <?php
			  for($i=0;$i<$form->max_rel_count['retenomi'];$i++) {
		  		$obj0 = "itcod_prov_$i";
			  	$obj1 = "itnombre_$i";
					$obj2 = "itmontor_$i";
			  ?>
			  <tr id='tr_retenomi_<?=$i ?>'>
			  	<td class="littletablerow"               ><?=$form->$obj0->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj1->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj2->output  ?> </td>
			    <?php if ($form->_status=='create' || $form->_status=='modify') {?>
			    <td class="littletablerow"><a href=# onclick='del_retenomi(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>

			  <tr id='__RETE__'>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->rete->label?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->rete->output?></td>
			    <? if($form->_status!='show'){
					?>
					<td class="littletableheaderb">&nbsp;</td>
					<?php
					}
					?>
			  </tr>
			   <tr>
			  	<td colspan="3">
			  		<?php echo $container_re ?>
			  	</td>
			  </tr>

	    </table>


	    <br />

			<table width='100%' bgcolor="#F2F0E4" class="table_detalle">
				<tr>
     			<th class="littletableheaderb" colspan="<?=(($form->_status=='create'?9:10))?>">OTROS CONCEPTOS DE N&Oacute;MINA </th>
     		<tr>
     			<td class="littletableheaderb">             Proveedor               </td>
     			<td class="littletableheaderb">             Concepto                </td>
			    <td class="littletableheaderb"align='right'>Monto                   </td>
					<? if($form->_status!='show'){
					?>
					<td class="littletableheaderb">&nbsp;</td>
					<?php
					}
					?>
			  </tr>
			  <?php
			  for($i=0;$i<$form->max_rel_count['otrosnomi'];$i++) {
		  		$obj0 = "itcod_provo_$i";
			  	$obj1 = "itnombreo_$i";
				$obj2 = "itmontoro_$i";
			  ?>
			  <tr id='tr_otrosnomi_<?=$i ?>'>
			  	<td class="littletablerow"               ><?=$form->$obj0->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj1->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj2->output  ?> </td>
			    <?php if ($form->_status=='create' || $form->_status=='modify'){?>
			    <td class="littletablerow"><a href=# onclick='del_otrosnomi(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>

			  <tr id='__OTROS__'>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->otros->label?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->otros->output?></td>
			    <? if($form->_status!='show'){
					?>
					<td class="littletableheaderb">&nbsp;</td>
					<?php
					}
					?>
			  </tr>
			   <tr>
			  	<td colspan="3">
			  		<?php echo $container_ot ?>
			  	</td>
			  </tr>

	    </table>
		<?php echo $form_end     ?>

		<td>
	<tr>
<table>
<?php endif; ?>
