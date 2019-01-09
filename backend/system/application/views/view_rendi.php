<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['itrendi'] AS $ind=>$data)
	$campos[]=$data['field'];
	$campos='<tr id="tr_itrendi_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
	$campos.=' <td class="littletablerow"><a href=# onclick="del_itrendi(<#i#>);return false;">Eliminar</a></td></tr>';
	$campos=$form->js_escape($campos);
	//$campos='';


if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if($form->_status!='show'){
	$uri  =$this->uri->uri_string();
	$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_presaldo' AND uri='$uri'");
	$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>
	<script language="javascript" type="text/javascript">
	itrendi_cont=<?=$form->max_rel_count['itrendi'] ?>;
	
	
	$(function() {	
		$(".inputnum").numeric(".");
	});
		
	function cal_total(){
		tot=stot=tiva=0;
		reteiva_prov=parseFloat($("#reteiva_prov").val());
			
		for(i=0;i<itrendi_cont;i++){
			id=i.toString();
			subtotal= parseFloat($("#subtotal_"+id).val());
			iva     = parseFloat($("#iva_"+id).val());
			if((isNaN(iva)) || (iva=='') || (iva<0)){
				iva=0;
				$("#iva_"+id).val(0)
			}
			if((isNaN(subtotal)) || (subtotal=='') || (subtotal<0)){
				subtotal=0;
				$("#subtotal_"+id).val(0)
			}
			
			importe = subtotal +(subtotal*iva/100);			
			$("#total_"+id).val(importe);

			stot += subtotal;
			tot  += importe;
			tiva += iva;
		}
		
		$("#subtotal").val(stot);
		$("#total").val(tot);
		$("#iva").val(tiva);
		
	}
	
	function add_itrendi(){
		var htm = <?=$campos ?>;
		can = itrendi_cont.toString();
		con = (itrendi_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#subtotal_"+can).numeric(".");
		$("#iva_"+can).numeric(".");
		itrendi_cont=itrendi_cont+1;
	}

	function del_itrendi(id){
		id = id.toString();
		$('#tr_itrendi_'+id).remove();
	}
	</script>
	<?php
	}else{
	?>
	<script language="javascript" type="text/javascript">
		function btn_anulaf(i){
			if(!confirm("Esta Seguro que desea Anular la Rendicion de Cuentas"))
				return false;
			else
				window.location='<?=site_url($this->url.'anular')?>/'+i
		}
	</script>
	<?
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
			    <td class="littletablerowth"><?=$form->cod_prov->label  ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->cod_prov->output.$form->nombrep->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->fecha->label ?>*&nbsp;</td> 
			    <td class="littletablerow"  ><?=$form->fecha->output?>&nbsp; </td>
			  </tr>
			
			  <tr>
			    <td class="littletablerowth"><?=$form->observa->label ?>&nbsp;</td>
			    <td class="littletablerow" colspan=3 ><?=$form->observa->output ?>&nbsp;</td>
			  </tr>

	    	</table><br />
			<table width='100%'>
     		<tr>
			<td class="littletableheaderb"                           >P.IVA?    </td>
     			<td class="littletableheaderb">Est. Admin                           </td>
     			<td class="littletableheaderb">F. Financiamiento                    </td>
     			<td class="littletableheaderb">Partida                              </td>
     			<td class="littletableheaderb">Ordinal                              </td>
     			<td class="littletableheaderb">Descripci&oacute;n                   </td>
     			<td class="littletableheaderb">Factura                              </td>
     			<td class="littletableheaderb">Nro. Control                         </td>
			    <td class="littletableheaderb">Fecha                                </td>
			    <td class="littletableheaderb"align='right'>Sub Total               </td>
			    <td class="littletableheaderb"align='right'>Monto IVA               </td>
			    <td class="littletableheaderb"align='right'>Total                   </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletableheaderb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <?php
			  for($i=0;$i<$form->max_rel_count['itrendi'];$i++) {
		  		$obj0="itcodigoadm_$i"; 
		  		$obj1="itfondo_$i";
		  		$obj2="itcodigopres_$i";
		  		$obj3="itordinal_$i";
				$obj4="itdescripcion_$i";
				$obj5="itfactura_$i";
				$obj6="itcontrolfac_$i"; 
				$obj7="itfechafac_$i";				  
				$obj8="itsubtotal_$i";
				$obj9="itiva_$i";
				$obj10="ittotal_$i";
				$obj12="itesiva_$i";
			  ?>
			  <tr id='tr_itrendi_<?=$i ?>'>
			   <td class="littletablerow"><?=$form->$obj12->output ?></td>
			    <td class="littletablerow"><?=$form->$obj0->output ?></td>
			    <td class="littletablerow"><?=$form->$obj1->output ?></td>
			    <td class="littletablerow"><?=$form->$obj2->output ?></td>
			    <td class="littletablerow"><?=$form->$obj3->output ?></td>
			    <td class="littletablerow"><?=$form->$obj4->output ?></td>
			    <td class="littletablerow"><?=$form->$obj5->output ?></td>
			    <td class="littletablerow"><?=$form->$obj6->output ?></td>
			    <td class="littletablerow"><?=$form->$obj7->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj8->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj9->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj10->output ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_itrendi(<?=$i ?>);return false;'>Eliminar</a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			   
			  <tr id='__UTPL__'>
			  	<td class="littletablefooterb" align='right' colspan="9"><?=$form->total->label  ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->subtotal->output  ?></td>
			    <td class="littletablefooterb" align='right'             ><?=$form->iva->output  ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->total->output  ?></td>
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
