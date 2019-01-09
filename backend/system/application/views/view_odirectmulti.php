<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['itodirect'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_itodirect_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos.=' <td class="littletablerow"><a href=# onclick="del_itodirect(<#i#>);return false;">Eliminar</a></td></tr>';
$campos=$form->js_escape($campos);

foreach($form->detail_fields['itfac'] AS $ind=>$data)
	$campos2[]=$data['field'];
$campos2='<tr id="tr_itfac_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos2).'</td>';
$campos2.=' <td class="littletablerow"><a href=# onclick="del_itodirect(<#i#>);return false;">Eliminar</a></td></tr>';
$campos2=$form->js_escape($campos2);



if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if($form->_status!='show'){
	$uri  =$this->uri->uri_string();
	$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_presaldo' AND uri='$uri'");
	$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>
	<script language="javascript" type="text/javascript">
	itodirect_cont=<?=$form->max_rel_count['itodirect'] ?>;
	itfac_cont=<?=$form->max_rel_count['itfac'] ?>;
	
	function tipoo(){
		tipo=$("#tipo").val();
		if(tipo=="Servicio"){
			$("#div_reten").show();
			$("#div_creten").show();
			$("#div_creten2").show();  
			$("#div_reten2").show();
		}else{
			$("#div_creten2").hide();
			$("#div_creten").hide();
			$("#div_reten2").hide();
			$("#div_reten").hide();
		}  
	}
	
	$(document).ready(function() {
		cal_total();
	});
	
	$(function() {
		tipoo();
		$("#reteiva_prov").hide();
		$(".inputnum").numeric(".");
		$("#estadmin").change(function(){
			$.post("<?=site_url('presupuesto/presupuesto/get_tipo')?>",{ codigoadm:$("#estadmin").val() },function(data){$("#fondo").html(data);})
		});

		$("#tipo").change(function (){tipoo()});
		//$(window).focus(function(){cal_total()});
		$("#multiple").change(function (){
			mul = $("#multiple").val();
			if(mul=='S'){
				$("#factura").attr("disabled","disabled");
        $("#controlfac").attr("disabled","disabled");
				$("#fechafac").attr("disabled","disabled");
			}else{
				$("#factura").attr("disabled","");
        $("#controlfac").attr("disabled","");
				$("#fechafac").attr("disabled","");

			}

			});

	});

	function cal_timbre(){
		s=$("#simptimbre").is(":checked");
		
		if(s==true){
			a=$("#subtotal").val()/<?=$imptimbre?>;
			$("#imptimbre").val(Math.round(a*100)/100);
		}else{
				$("#imptimbre").val(0);
		}
		cal_total();
	}

	function cal_municipal(){
		s=$("#simpmunicipal").is(":checked");												
		if(s==true){
			a=$("#subtotal").val() * <?=$impmunicipal?> / 100;
			$("#impmunicipal").val(Math.round(a*100)/100)
		}else{
				$("#impmunicipal").val(0);
		}
		cal_total();			
	}
	
	function cretenf(){
		$.post("<?=site_url('presupuesto/ocompra/tari1')?>",{ creten:$("#creten").val() },function(data){
			m = eval('(' + data + ')');
			tari=parseFloat(data);
			//alert(tari);
			subtot=$("#subtotal").val();
			tot=(tari*subtot)/100;
			$("#reten").val(Math.round(tot*100/100));
		})
	}
	
	function cal_islr(){
		
		var ind=$("#creten").val();
		
		var subtotal=$("#subtotal").val();
		var data = <?=$rete ?>;
		a=ind.substring(0,1);
		
		base1=eval('data._'+ind+'.base1');
		tari1=eval('data._'+ind+'.tari1');
		pama1=eval('data._'+ind+'.pama1');
		
		if(a=='1')ret=subtotal*base1*tari1/10000;
		else ret=(subtotal-pama1)*base1*tari1/10000;
		
		if(ret<0)ret=0;
		$("#reten").val(Math.round(ret*100)/100);

		//cal_total();
		//cal_timbre();
		//cal_municipal();
	}

	function modbusdepen(i){
		var id = i.toString();
		var fondo   =$("#fondo").val();
		var estadmin=$("#estadmin").val();
	
		if(fondo.length*estadmin.length == 0){
			alert('Debe Seleccionar primero un fondo y una Estructura administrativa');
			return false;
		}
		var link='<?=$modblink2 ?>'+'/'+fondo+'/'+estadmin;
		link =link.replace(/<#i#>/g,id);
		vent=window.open(link,'ventbuscarppla','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'); 
		vent.focus(); 
	
		document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarppla\');vent.close();');		
	}
	
	function cal_total(){
		tot=stot=giva=riva=aiva=exce=reteiva=iva2=0;
		reteiva_prov=parseFloat($("#reteiva_prov").val());
		
		if((isNaN(reteiva_prov)) || (reteiva_prov==0) || (reteiva_prov=='') || (reteiva_prov==100))
			reteiva_prov=100;
		else
			reteiva_prov=75;

		for(i=0;i<itodirect_cont;i++){
			id=i.toString();
			valor  =parseFloat($("#importe_"+id).val());
			piva   =parseFloat($("#iva_"+id).val());
			if(!isNaN(valor)){
				stot+=valor;		
				iva =valor*(piva/100);
				giva=giva+(iva*(piva==<?=$ivag ?>));
				riva=riva+(iva*(piva==<?=$ivar ?>));
				aiva=aiva+(iva*(piva==<?=$ivaa ?>));
				if(piva==0)exce=exce+valor;
				
			}
		}

		retenomina = parseFloat($("#retenomina").val());

		if((isNaN(retenomina)) || (reteiva_prov=='')) 
			retenomina=100;
		
		
		reteiva=reteiva+((giva+riva+aiva)*reteiva_prov)/100;
		tot=stot+giva+riva+aiva+retenomina;
		
		reten=$("#reten").val();
		
		to=tot-reteiva-reten;
		
		$("#subtotal").val(Math.round(stot*100)/100);
		$("#total2").val(Math.round(tot*100)/100);
		$("#total").val(Math.round(to*100)/100);
		$("#ivaa").val(Math.round(aiva*100)/100);
		$("#ivar").val(Math.round(riva*100)/100);
		$("#ivag").val(Math.round(giva*100)/100);
		$("#exento").val(Math.round(exce*100)/100);
		$("#reteiva").val(Math.round(reteiva*100)/100);
		
	}
	function cal(){
		creten = $("#creten").val();
		if(creten != '')
			cal_islr();
		
		cal_timbre();
		cal_municipal();
	}	

	function cal_importe(i){
		id=i.toString();
		cana  =parseFloat($("#cantidad_"+id).val());
		precio=parseFloat($("#precio_"+id).val());
		op=cana*precio;
		if(!isNaN(op)){
			$("#importe_"+id).val(cana*precio);			

		}
		cal();
	}
	
	function rete(){
		cal();
	}


	function add_itodirect(){
		var htm = <?=$campos ?>;
		can = itodirect_cont.toString();
		con = (itodirect_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#cantidad_"+can).numeric(".");
		$("#precio_"+can).numeric(".");
		itodirect_cont=itodirect_cont+1;
	}

	function del_itodirect(id){
		id = id.toString();
		$('#tr_itodirect_'+id).remove();
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
		var htm = <?=$campos2 ?>;
		can = itfac_cont.toString();
		con = (itfac_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__ITFACUTPL__").before(htm);
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
			    <td class="littletablerowth"><?=$form->fecha->label ?>*&nbsp;</td> 
			    <td class="littletablerow"  ><?=$form->fecha->output?>&nbsp; </td>
			  </tr>
			  <tr>
			    <td class="littletablerowth">               <?=$form->estadmin->label  ?>*&nbsp;</td>
			    <td class="littletablerow" id='td_estadmin'><?=$form->estadmin->output ?>&nbsp;</td>
			    <td class="littletablerowth">               <?=$form->fondo->label     ?>*&nbsp;</td>
			    <td class="littletablerow" >                <?=$form->fondo->output    ?>&nbsp;</td>
			  </tr>

			  <tr>
			  	<td class="littletablerowth"><?=$form->uejecutora->label  ?>*&nbsp;</td>			  	
			    <td class="littletablerow"  ><?=$form->uejecutora->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->codprov_sprv->label ?>&nbsp;<?=$form->reteiva_prov->output  ?></td>
			    <td class="littletablerow"  ><?=$form->codprov_sprv->output ?>&nbsp;</td>
				</tr>
				<tr>
			  	<td class="littletablerowth"><?//=$form->uejecutora->label  ?>&nbsp;</td>			  	
			    <td class="littletablerow"  ><?//=$form->uejecutora->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->nombrep->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->nombrep->output ?>&nbsp;</td>
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
			  <tr>
			  	<td class="littletablerowth">            <?=$form->multiple->label ?>    </td> 
			    <td class="littletablerow"  >            <?=$form->multiple->output?>    </td>			  	
			  	<td class="littletablerowth">            <?=$form->fechafac->label ?>    </td> 
			    <td class="littletablerow"  >            <?=$form->fechafac->output?>    </td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?//=$form->simptimbre->label  ?></td>
			    <td class="littletablerow"  ><?//=$form->simptimbre->output ?> </td>
			    <td class="littletablerowth"><?//=$form->simpmunicipal->label ?>    </td> 
			    <td class="littletablerow"  ><?//=$form->simpmunicipal->output?>    </td>
			  </tr>

	    	</table><br />
			<table width='100%'>
     		<tr>
     			<td class="littletableheaderb">             Partida                 </td>
     			<td class="littletableheaderb">             Ordinal                 </td>
					<td class="littletableheaderb">             Descripci&oacute;n      </td>
			    <td class="littletableheaderb"align='right'>Unidad                  </td>
			    <td class="littletableheaderb"align='right'>Cantidad                </td>
					<td class="littletableheaderb"align='right'>Precio                  </td>
					<td class="littletableheaderb"align='right'>Iva                     </td>
			    <td class="littletableheaderb"align='right'>Importe                 </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletableheaderb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <?php
			  for($i=0;$i<$form->max_rel_count['itodirect'];$i++) {
		  		$obj0 = "itpartida_$i"; 
					$obj1 = "itordinal_$i";     			    
					$obj2 = "itdescripcion_$i"; 
					$obj3 = "itunidad_$i";      
					$obj4 = "itcantidad_$i";    		
					$obj5 = "itprecio_$i";			 
			  	$obj6 = "itiva_$i";         
			  	$obj7 = "itimporte_$i";    
			  ?>
			  <tr id='tr_itodirect_<?=$i ?>'>
			    <td class="littletablerow"               ><?=$form->$obj0->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj1->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj2->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj3->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj4->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj5->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj6->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj7->output  ?> </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_itodirect(<?=$i ?>);return false;'>Eliminar</a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			   
			  <tr id='__UTPL__'>
			    <td class="littletablefooterb" align='right' colspan="7"><?=$form->subtotal->label  ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->subtotal->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  
			   <tr>
			    <td class="littletablerow" align='right' colspan="2"><div id="div_creten">              <?=$form->creten->label  ?> &nbsp; </div></td>
			    <td class="littletablerow"               colspan="2"><div id="div_creten2"> <?=$form->creten->output ?>&nbsp;  </div></td>
			    <td class="littletablerow" align='right' colspan="1"><div id="div_reten">   <?=$form->reten->label   ?>        </div></td>
			    <td class="littletablerow" align='right' ><div id="div_reten2">             <?=$form->reten->output  ?>        </div></td>
			    <td class="littletablerow" align='right' colspan="1">                       <?=$form->ivag->label    ?>              </td>
			    <td class="littletablerow" align='right'>                                   <?=$form->ivag->output   ?>              </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>

			  <tr>
			  	<td class="littletablerow" align='right' colspan="3"><?//=$form->imptimbre->label   ?></td>
			    <td class="littletablerow" align='right'>            <?//=$form->imptimbre->output  ?></td>
			    <td class="littletablerow" align='right'            ><?=$form->exento->label  ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->exento->output ?></td>
			    <td class="littletablerow" align='right' colspan="1"><?=$form->ivar->label    ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->ivar->output   ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  
			  <tr>
			  	<td class="littletablerow" align='right' colspan="3"><?=$form->retenomina->label   ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->retenomina->output  ?></td>
			    <td class="littletablerow" align='right'            ><?=$form->reteiva->label   ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->reteiva->output  ?></td>
			    <td class="littletablerow" align='right' colspan="1"><?=$form->ivaa->label      ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->ivaa->output     ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  		  
			  <tr>
			  	<td class="littletablefooterb" align='right' colspan="5"><?=$form->total->label  ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->total->output  ?></td>
			    <td class="littletablefooterb" align='right'            ><?=$form->total2->label  ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->total2->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  
			 
	    </table>
		<br /><br /><br />

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
			   
			  <tr id='__ITFACUTPL__'>
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