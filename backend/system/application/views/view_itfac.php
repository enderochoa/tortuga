<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['itfac'] AS $ind=>$data)
	$campos[]=$data['field'];
	$campos='<tr id="tr_itfac_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
	$campos.=' <td class="littletablerow"><a href=# onclick="del_itfac(<#i#>);return false;">Eliminar</a></td></tr>';
	$campos=$form->js_escape($campos);
	//$campos='';


if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if($form->_status!='show'){
	$uri  =$this->uri->uri_string();
	//$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_presaldo' AND uri='$uri'");
	//$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>
	<script language="javascript" type="text/javascript">
	itfac_cont=<?=$form->max_rel_count['itfac'] ?>;
		
	$(document).ready(function() {
		cal_total();
	});
	
	$(function() {		
		$("#reteiva_prov").hide();
		$(".inputnum").numeric(".");
	});

	function cal_total(){
		texento=tsubtotal=trivaa=trivag=trivar=total=total2=treteiva = 0;
		for(i=0;i<itfac_cont;i++){
			id=i.toString();
			texento   +=  Math.round(parseFloat($("#exento_"+id   ).val()) * 100 )/ 100;
			tsubtotal +=  Math.round(parseFloat($("#subtotal_"+id ).val()) * 100 )/ 100;
			trivag    +=  Math.round(parseFloat($("#ivag_"+id     ).val()) * 100 )/ 100;
			trivaa    +=  Math.round(parseFloat($("#ivaa_"+id     ).val()) * 100 )/ 100;
			trivar    +=  Math.round(parseFloat($("#ivar_"+id     ).val()) * 100 )/ 100;
			total     +=  Math.round(parseFloat($("#total_"+id    ).val()) * 100 )/ 100;
			total2    +=  Math.round(parseFloat($("#total2_"+id   ).val()) * 100 )/ 100;
			treteiva  +=  Math.round(parseFloat($("#reteiva_"+id  ).val()) * 100 )/ 100;
		}
		
		$("#texento"          ).val(Math.round(texento        *100)/100);
		$("#tsubtotal"        ).val(Math.round(tsubtotal      *100)/100);
		$("#trivag"           ).val(Math.round(trivag         *100)/100);
		$("#trivaa"           ).val(Math.round(trivaa         *100)/100);
		$("#trivar"           ).val(Math.round(trivar         *100)/100);
		$("#ttotal"           ).val(Math.round(total          *100)/100);
		$("#ttotal2"          ).val(Math.round(total2         *100)/100);
		$("#treteiva"         ).val(Math.round(treteiva       *100)/100);
	}

	function cal_reteiva(i){

		id=i.toString();
		subtotal     = parseFloat($("#subtotal_"+id).val());
		reteiva_prov = parseFloat($("#reteiva_prov").val());

		ivaa = parseFloat(1 * $("#ivaa_"+id).val());
		ivar = parseFloat(1 * $("#ivar_"+id).val());
		ivag = parseFloat(1 * $("#ivag_"+id).val());
		
		if((isNaN(reteiva_prov)) || (reteiva_prov==0) || (reteiva_prov=='') || (reteiva_prov==100))
			reteiva_prov=100;
		else
			reteiva_prov=75;
		
		if(!isNaN(subtotal)){
			reteiva=((ivaa+ivar+ivag) * reteiva_prov)/100;
		}

		total2 = subtotal + ivaa + ivar +ivag;
		
		$("#reteiva_"+id  ).val(Math.round(reteiva *100)/100);
		$("#total_"+id  ).val(Math.round((total2-reteiva) *100)/100);
		$("#total2_"+id  ).val(Math.round(total2 * 100)/100);

		cal_total();
		
	}

	function cal_subtotal(i){
		id=i.toString();
		subtotal    =parseFloat($("#subtotal_"+id).val());
		reteiva_prov=parseFloat($("#reteiva_prov").val());

		if((isNaN(reteiva_prov)) || (reteiva_prov==0) || (reteiva_prov=='') || (reteiva_prov==100))
			reteiva_prov=100;
		else
			reteiva_prov=75;
		
		if(!isNaN(subtotal)){

			giva=subtotal*<?=$ivag ?>/100;
			riva=subtotal*<?=$ivar ?>/100;
			aiva=subtotal*<?=$ivaa ?>/100;

			reteiva=((giva+riva+aiva)*reteiva_prov)/100;
		}
		
		$("#ivag_"+id         ).val(Math.round(giva    *100)/100);
		$("#ivaa_"+id         ).val(Math.round(riva    *100)/100);
		$("#ivar_"+id         ).val(Math.round(aiva    *100)/100);
		$("#reteiva_"+id       ).val(Math.round(reteiva *100)/100);

		cal_total();

	}

	function cal_itivag(i){
		cal_reteiva(i);
	}

	function cal_itivar(i){
		
		cal_reteiva(i);
	}

	function cal_itivaa(i){
		cal_reteiva(i);
	}
	
	function add_itfac(){
		var htm = <?=$campos ?>;
		can = itfac_cont.toString();
		con = (itfac_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#cantidad_"+can).numeric(".");
		$("#precio_"+can).numeric(".");
		itfac_cont=itfac_cont+1;
	}

	function del_itfac(id){
		id = id.toString();
		$('#tr_itfac_'+id).remove();
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
			    <td class="littletablerowth"><?=$form->tipo->label  ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->tipo->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->fecha->label ?>&nbsp;</td> 
			    <td class="littletablerow"  ><?=$form->fecha->output?>&nbsp; </td>
			  </tr>
			  <tr>
			    <td class="littletablerowth">               <?=$form->estadmin->label  ?>&nbsp;</td>
			    <td class="littletablerow" id='td_estadmin'><?=$form->estadmin->output ?>&nbsp;</td>
			    <td class="littletablerowth">               <?=$form->fondo->label     ?>&nbsp;</td>
			    <td class="littletablerow" >                <?=$form->fondo->output    ?>&nbsp;</td>
			  </tr>
			  <tr>
			  	<td class="littletablerowth"><?=$form->uejecutora->label  ?>&nbsp;</td>			  	
			    <td class="littletablerow"  ><?=$form->uejecutora->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->codprov_sprv->label ?>&nbsp;<?=$form->reteiva_prov->output  ?></td>
			    <td class="littletablerow"  ><?=$form->codprov_sprv->output.$form->nombre->output ?>&nbsp;</td>
				</tr>
				<tr>
			  	<td class="littletablerowth"><?//=$form->uejecutora->label  ?>&nbsp;</td>			  	
			    <td class="littletablerow"  ><?//=$form->uejecutora->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?//=$form->beneficiario->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?//=$form->beneficiario->output ?>&nbsp;</td>
				</tr>
				<tr>
			    <td class="littletablerowth"><?//=$form->codprov_sprv->label  ?></td>
			    <td class="littletablerow"  ><?//=$form->codprov_sprv->output ?> </td>
			    <td class="littletablerowth"><?//=$form->nombre->label ?>    </td> 
			    <td class="littletablerow"  ><?//=$form->nombre->output?>    </td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->observa->label ?>&nbsp;</td>
			    <td class="littletablerow" colspan=3 ><?=$form->observa->output ?>&nbsp;</td>
			  </tr>		  

	    	</table><br />
			<table width='100%'>
     		<tr>
     			<td class="littletableheaderb">             Factura*                 </td>
     			<td class="littletableheaderb">             Control Fiscal*                 </td>
					<td class="littletableheaderb">             Fecha Factura*      </td>
					<td class="littletableheaderb"align='right'>Subtotal                       </td>
					<td class="littletableheaderb"align='right'>Exento                         </td>			    
			    <td class="littletableheaderb"align='right'>IVA General <?=$form->tivag->output ?>%              </td>					
					<td class="littletableheaderb"align='right'>IVA Reducido<?=$form->tivar->output ?>%              </td>			    
			    <td class="littletableheaderb"align='right'>IVA Adicional<?=$form->tivaa->output ?>%             </td>
			    <td class="littletableheaderb"align='right'>IVA Retenido               </td>					
					<td class="littletableheaderb"align='right'>M.Pagar              </td>			    
			    <td class="littletableheaderb"align='right'>Total%             </td>
			    
			    <?php if($form->_status!='show') {?>
			    <td class="littletableheaderb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <?php
			  for($i=0;$i<$form->max_rel_count['itfac'];$i++) {
		  		$obj0 = "itfactura_$i"; 
					$obj1 = "itcontrolfac_$i";     			    
					$obj2 = "itfechafac_$i";
					$obj3 = "itsubtotal_$i";
					$obj4 = "itexento_$i"; 
					$obj5 = "itivag_$i";
					$obj6 = "itivar_$i";
			  	$obj7 = "itivaa_$i";
			  	$obj8 = "itreteiva_$i";
					$obj9 = "ittotal_$i";
			  	$obj10 = "ittotal2_$i";
			  	    
			  ?>
			  <tr id='tr_itfac_<?=$i ?>'>
			    <td class="littletablerow"               ><?=$form->$obj0->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj1->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj2->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj3->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj4->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj5->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj6->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj7->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj8->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj9->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj10->output  ?> </td>
			    <?php if($form->_status!='show' && $status!="B3") {?>
			    <td class="littletablerow"><a href=# onclick='del_itfac(<?=$i ?>);return false;'>Eliminar</a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			   
			  <tr id='__UTPL__'>
			 		<td class="littletablefooterb" align='right' colspan = "3">Totales</td>
			    <td class="littletablefooterb" align='right'><?=$form->tsubtotal->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->texento->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->trivag->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->trivar->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->trivaa->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->treteiva->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->ttotal->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->ttotal2->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?> 
			   
			 <tr>
			 		<td class="littletablefooterb" align='right' colspan = "3">Montos de Orden de Pago</td>
			    <td class="littletablefooterb" align='right'><?=$form->subtotal->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->exento->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->ivag->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->ivar->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->ivaa->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->reteiva->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->total->output  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->total2->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
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


