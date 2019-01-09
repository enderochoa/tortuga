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
	//$campos.=' <td class="littletablerow"><a href=# onclick="del_itodirect(<#i#>);return false;">Eliminar</a></td></tr>';
	$campos=$form->js_escape($campos);
	//$campos='';


if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if($form->_status!='show'){
	$uri  =$this->datasis->get_uri();
	$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_presaldo' AND uri='$uri'");
	$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>
	<script language="javascript" type="text/javascript">
	itodirect_cont=<?=$form->max_rel_count['itodirect'] ?>;
	
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
		//cal_islr();
		//cal_timbre();
		//cal_municipal();
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
			alert('Debe Seleccionar primero una Fuente de Financiamiento y una Estructura administrativa');
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
	</script>
	<?php
	}else{
	?>
	<script language="javascript" type="text/javascript">
		function btn_anulaf(i){
			if(!confirm("Esta Seguro que desea Anular la Orden de Pago Directo De NOMINA"))
				return false;
			else
				window.location='<?=site_url('presupuesto/pagonom/reversar')?>/'+i+'/S';
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
			    <td colspan=6 class="bigtableheader"><?=$this->tits?> Nro. <?php  echo $form->numero->output ?></td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->tipo->label  ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->tipo->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->status->label ?>&nbsp;</td> 
			    <td class="littletablerow"  ><?=$form->status->output?>&nbsp; </td>
			  </tr>
			   <tr>
			  	<td class="littletablerowth"><?=$form->cod_prov->label  ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->cod_prov->output.$form->nombrep->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->fecha->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp;</td>
				</tr>
				
			  <tr>
			    <td class="littletablerowth"><?=$form->observa->label ?>&nbsp;</td>
			    <td class="littletablerow" colspan=3 ><?=$form->observa->output ?>&nbsp;</td>
			  </tr>
	    	</table><br />
			<table width='100%'>
     		<tr>
	     		<td class="littletableheaderb">Est. Administrativa                  </td>
	     		<td class="littletableheaderb">F. Financiamiento                    </td>
     			<td class="littletableheaderb">             Partida                 </td>
					<td class="littletableheaderb">             Descripci&oacute;n      </td>
			    <td class="littletableheaderb"align='right'>Monto                   </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletableheaderb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <?php
				for($i=0;$i<$form->max_rel_count['itodirect'];$i++) {
					$obj0 = "itcodigoadm_$i";
					$obj1 = "itfondo_$i";
					$obj2 = "itpartida_$i";
					$obj4 = "itdescripcion_$i"; 
					$obj5 = "itimporte_$i";    
			  ?>
			  <tr id='tr_itodirect_<?=$i ?>'>
			    <td class="littletablerow"               ><?=$form->$obj0->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj1->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj2->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj4->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj5->output  ?> </td>
			    <?php if($form->_status!='show') {?>
			    <!--  <td class="littletablerow"><a href=# onclick='del_itodirect(<?//=$i ?>);return false;'>&nbsp;</a></td>-->
			    <?php } ?>
			  </tr>
			  <?php } ?>
			   
			  <tr id='__UTPL__'>
			    <td class="littletablefooterb" align='right' colspan="4"><?=$form->subtotal->label  ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->subtotal->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			 
			  
			  <tr>
			  	<td class="littletablerow" align='right' colspan="4"><?=$form->retenomina->label   ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->retenomina->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr> 
			  <tr>
			    <td class="littletablefooterb" align='right' colspan="4"><?=$form->total2->label  ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->total2->output  ?></td>
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
