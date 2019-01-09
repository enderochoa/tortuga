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
	$campos.=' <td class="littletablerow"><a href=# onclick="del_itodirect(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
	$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin; 
if($form->_status!='show'){
	$uri  =$this->datasis->get_uri();
	$idt  =$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_presaldo' AND uri='$uri'");
	$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
	
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
	itodirect_cont=<?=$form->max_rel_count['itodirect'] ?>;
	datos    ='';
	estruadm ='';
	sprv ='';
	com=false;
	
	function tipoo(){
		tipo=$("#tipo").val();
		
		if(tipo=="Servicio"){
			$("#div_reten").show();
			$("#div_creten").show();
			$("#div_creten2").show();
			$("#div_reten4").html('M. ISLR');
			$("#div_reten3").html('ISLR');
			$("#div_reten2").show();
			$("#div_reten3").show();
			$("#div_reten4").show();
			for(i=0;i<itodirect_cont;i++){
				id=i.toString();
				$("#islr_"+id).show();
				$("#usaislr_"+id).show();
			}
		}else{
			$("#div_creten2").hide();
			$("#div_creten").hide();
			$("#div_reten2").hide();
		 	$("#div_reten").hide();
		 	$("#div_reten3").hide();
		 	$("#div_reten4").hide();
		 	for(i=0;i<itodirect_cont;i++){
				id=i.toString();
				$("#islr_"+id).hide();
				$("#usaislr_"+id).hide();
			}
		}

		for(i=0;i<itodirect_cont;i++){
			id=i.toString();
			valor  =parseFloat($("#importe_"+id).val());
			piva   =parseFloat($("#iva_"+id).val());
			if(!isNaN(valor)){
				stot+=valor;		
				iva =valor*(piva/100);
				giva=giva+ (Math.round( (iva*(piva==<?=$ivag ?>))* 100) / 100 ) ;
				riva=riva+ (Math.round( (iva*(piva==<?=$ivar ?>))* 100) / 100 ) ;
				aiva=aiva+ (Math.round( (iva*(piva==<?=$ivaa ?>))* 100) / 100 ) ;

				if(piva==0)exce=exce+valor;
			}
		}
		
		if(tipo=="N" || tipo=="T"){

			for(i=0;i<itodirect_cont;i++){
				id=i.toString();
				$("#unidad_"+id).hide();
				$("#cantidad_"+id).hide();
				$("#precio_"+id).hide();
				$("#iva_"+id).hide();
			
			}
			$("#unidad").html('');
			$("#cantidad").html('');
			$("#iva").html('');
			$("#precio").html('');
			$("#importe").html('Monto');
			$("#trtotal").html('');
			
			$("#tr_factura1").hide();
			$("#tr_factura2").hide();
			if(tipo=="N")$("#trnomi").show();
			else $("#trnomi").hide();
			$("#trivaa").hide();
			$("#trivar").hide();
			$("#trivag").hide();
			$("#total").hide();
			if('<?=$this->datasis->traevalor('USANOMINA')?>'=='N' && (tipo=="N"))
				$("#tr_nomina").show();
		}else{
			for(i=0;i<itodirect_cont;i++){
				id=i.toString();
				$("#unidad_"+id).show();
				$("#cantidad_"+id).show();
				$("#precio_"+id).show();
				$("#iva_"+id).show();
			}

			$("#unidad").html('Unidad');
			$("#cantidad").html('Cantidad');
			$("#iva").html('Iva');
			$("#precio").html('Precio');
			$("#importe").html('Importe');
			$("#trtotal").html('Monto a Pagar');

			$("#total").show();
			$("#tr_factura1").show();
			$("#tr_factura2").show();
			$("#trivaa").show();
			$("#trivar").show();
			$("#trivag").show();
			$("#trnomi").hide();
		
			$("#tr_nomina").hide();
		}
	}

	function modbusdepen(i){
		var id = i.toString();
		var fondo   =$("#fondo").val();
	
		if(fondo.length == 0){
			alert('Debe Seleccionar primero una fuente de financiamiento');
			return false;
		}
		var link='<?=$modblink2 ?>'+'/'+fondo;
		link =link.replace(/<#i#>/g,id);
		vent=window.open(link,'ventbuscarppla','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'); 
		vent.focus(); 
	
		document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarppla\');vent.close();');
	}
	
	$(document).ready(function() {
		cal_total();
	
		/*
		 * INICIO CARGA DE AUTOCOMPLETE PARA LOS CAMPOS DE PROVEEDORES
		 */
		$.post("<?=site_url('presupuesto/sprv/autocompleteui')?>",{ partida:"" },function(data){
			sprv=jQuery.parseJSON(data);
			
			jQuery.each(sprv, function(i, val) {
				val.label=val.nombre;
			});
			$("#nombrep").autocomplete({
				delay: 0,
				minLength: 3,
				source: sprv,
				focus: function( event, ui ) {
					$( "#cod_prov").val( ui.item.proveed );
					$( "#nombrep").val( ui.item.nombre );
					$( "#reteiva_prov").val( ui.item.reteiva );
					$( "#rif").val( ui.item.rif );
					return false;
				},
				select: function( event, ui ) {
					$( "#nombrep").val( ui.item.nombre );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a>" + item.nombre + "</a>" )
				.appendTo( ul );
			};
			
			jQuery.each(sprv, function(i, val) {
				val.label=val.proveed;
			});
			
			$("#cod_prov").autocomplete({
				delay: 0,
				minLength: 3,
				source: sprv,
				focus: function( event, ui ) {
					$( "#cod_prov").val( ui.item.proveed );
					$( "#nombrep").val( ui.item.nombre );
					$( "#reteiva_prov").val( ui.item.reteiva );
					$( "#rif").val( ui.item.rif );
					return false;
				},
				select: function( event, ui ) {
					
					$( "#cod_prov").val( ui.item.proveed );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a>" +item.proveed+'-'+ item.nombre + "</a>" )
				.appendTo( ul );
			};
			
			jQuery.each(sprv, function(i, val) {
				val.label=val.rif;
			});
			
			$("#rif").autocomplete({
				delay: 0,
				minLength: 3,
				source: sprv,
				focus: function( event, ui ) {
					$( "#cod_prov").val( ui.item.proveed );
					$( "#nombrep").val( ui.item.nombre );
					$( "#reteiva_prov").val( ui.item.reteiva );
					$( "#rif").val( ui.item.rif );
					return false;
				},
				select: function( event, ui ) {
					$( "#rif").val( ui.item.rif );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a>" +item.rif+'-'+ item.nombre + "</a>" )
				.appendTo( ul );
			};
		});
		/*
		 * FIN CARGA DE AUTOCOMPLETE PARA LOS CAMPOS DE PROVEEDORES
		 */
		$.post("<?=site_url('presupuesto/estruadm/autocompleteui')?>",{ partida:"" },function(data){
			estruadm=jQuery.parseJSON(data);
		});
		
		$.post("<?=site_url('presupuesto/ppla/autocomplete4/mayor')?>",{ partida:"" },function(data){
			datos=jQuery.parseJSON(data);
		});
		
	});

	var data;
	
	function mascara(){
	
    $("input[name^='itcodigoadm_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOESTRU"))?>');
    $("input[name^='partida_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOPRES"))?>');    
  }
		
			<?
		$unsolofondo=$this->datasis->traevalor('UNSOLOFONDO','S','Indica si se utiliza una sola fuente de financiamiento');
		?>
	function autop(){
		$("input[name^='partida_']").focus(function(){
			id=this.name.substr(8,100);
			$( "#partida_"+id).autocomplete({
				minLength: 0,
				source: datos,
				focus: function( event, ui ) {
				$( "#partida_"+id).val( ui.item.codigopres );
				$( "#ordinal_"+id).val( ui.item.ordinal );
					return false;
				},
				select: function( event, ui ) {
					$( "#partida_"+id).val( ui.item.codigopres );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				codigoadm=$("#itcodigoadm_"+id).val();
				
				<?
				if($unsolofondo=='S'){
				?>
				fondo=$("#fondo").val()
				<?
				}else{
				?>
				fondo =$("#itfondo_"+id).val();
				<?
				}
				?>
				
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
		$("input[name^='itcodigoadm_']").focus(function(){
			id=this.name.substr(12,100);
			$( "#itcodigoadm_"+id).autocomplete({
				minLength: 0,
				source: estruadm,
				focus: function( event, ui ) {
				$( "#itcodigoadm_"+id).val( ui.item.codigoadm );
					return false;
				},
				select: function( event, ui ) {
					$( "#itcodigoadm_"+id).val( ui.item.codigoadm );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				<?
				if($unsolofondo=='S'){
				?>
				fondo=$("#fondo").val()
				<?
				}else{
				?>
				fondo =$("#itfondo_"+id).val();
				<?
				}
				?>
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
		tipoo();
		
		$(".inputnum").numeric(".");

		$("#tipo").change(function (){
			tipoo();
		});
		
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
			
		autop();
		autoe();
		mascara();
		$(document).keydown(function(e){
					//alert(e.which);
			if (18 == e.which) {
				com=true;
				//c = String.fromCharCode(e.which);
				return false;
			}
			if (com && (e.which == 61 || e.which == 107)) {
				add_itodirect();
				a=itodirect_cont-1;
				$("#partida_"+a).focus();
				
				com=false;
				return false;
			}else if (com && e.which != 16 && e.which == 17){
				com=false;
			}
			return true;
		});
	});

	function cal_timbre(){
		s=$("#simptimbre").is(":checked");
		
		if(s==true){
			a=$("#subtotal").val()*<?=$imptimbre?>/100;
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

	function cal_islr(){
		
		var tipo=$("#tipo").val();
		if(tipo=="Servicio"){
			
			breten=0;
			for(i=0;i<itodirect_cont;i++){
				id=i.toString();
				
				if($("#usaislr_"+id).val()=="S"){
					importe= $("#importe_"+id).val();
					breten = breten + importe;

				}else{
					$("#islr_"+id).val('0');
				}
			}
			
			var ind  =$("#creten").val();
			var data = <?=$rete ?>;
			var utribuactual = <?=$utribuactual ?>;
			a        =ind.substring(0,1);

			tari1=eval('data._'+ind+'.tari1');
			pama1=eval('data._'+ind+'.pama1');
			tari2=eval('data._'+ind+'.tari2');
			pama2=eval('data._'+ind+'.pama2');
			tari3=eval('data._'+ind+'.tari3');
			pama3=eval('data._'+ind+'.pama3');
			porcentsustra=eval('data._'+ind+'.porcentsustra');
			
			if(breten>=pama3*utribuactual){
					tari=tari3;
			}else{
				if(breten>=pama2*utribuactual){
					tari=tari2;
				}else{
					tari=tari1;
				}
			}
			
			$("#preten").val(tari);
			$("#preten_val").text(tari);
			
			ret=(breten*(tari/100))-(utribuactual*(tari/100)*porcentsustra);
			if(ret <0)ret=0;
			ret=parseFloat(ret);
			$("#reten").val(Math.round(ret*100)/100);
		}
	}
	
	function cal_total(){
		cal_islr();
		tot=stot=giva=riva=aiva=exce=reteiva=iva2=0;
		reteiva_prov=parseFloat($("#reteiva_prov").val());
		otrasrete=parseFloat($("#otrasrete").val());
		imptimbre=parseFloat($("#imptimbre").val());
		redondear =$("#redondear").val();
		if(isNaN(otrasrete)){
			$("#otrasrete").val('0');
			otrasrete=0;
		}
		
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
				if(redondear=="R0"){
					iva =valor*(piva/100);
					giva=giva+(iva*(piva==<?=$ivag ?>));
					riva=riva+(iva*(piva==<?=$ivar ?>));
					aiva=aiva+(iva*(piva==<?=$ivaa ?>));
				}else{
					iva =Math.round((valor*(piva/100))*100)/100;
					giva=Math.round((giva+(iva*(piva==<?=$ivag ?>)))*100)/100;
					riva=Math.round((riva+(iva*(piva==<?=$ivar ?>)))*100)/100;
					aiva=Math.round((aiva+(iva*(piva==<?=$ivaa ?>)))*100)/100;
				}
				
				if(piva==0)exce=exce+valor;
			}
		}

		retenomina = parseFloat($("#retenomina").val());

		if((isNaN(retenomina)) || (retenomina=='')) 
			retenomina=0;
		reteiva=Math.round((((giva+riva+aiva)*reteiva_prov)/100)*100)/100;
		tot=stot+giva+riva+aiva;
		
		reten=$("#reten").val();
		
		to=tot-reteiva-reten-retenomina-otrasrete-imptimbre;
		if(redondear=="R0"){
			a=stot.indexOf('.');b=stot.substring(0,a+2);
			$("#subtotal").val(parseFloat(b));
			a=tot.indexOf('.');b=tot.substring(0,a+2);
			$("#total2").val(parseFloat(b));
			a=exce.indexOf('.');b=exce.substring(0,a+2);
			$("#exento").val(parseFloat(b));
			a=to.indexOf('.');b=to.substring(0,a+2);
			$("#total").val(parseFloat(b));
			
		}else{
			$("#subtotal").val(Math.round(parseFloat(stot)* 100) /100);
			$("#total2").val(Math.round(parseFloat(tot) * 100) /100);
			$("#exento").val(Math.round(parseFloat(exce) * 100) /100);
			$("#total").val(Math.round(parseFloat(to) * 100) /100);
			
		}
		
		$("#ivaa").val(aiva);
		$("#ivar").val(riva);
		$("#ivag").val(giva);
		$("#reteiva").val(reteiva);
		
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
		cana  =Math.round(parseFloat($("#cantidad_"+id).val() )* 100) /100;
		precio=Math.round(parseFloat($("#precio_"+id).val()   )* 100) /100;
		op=cana*precio;
		
		if(!isNaN(op))
			$("#importe_"+id).val(Math.round(cana*precio* 100) / 100) ;
		
		cal_total();
	}

	function cal_importep(i){
		id=i.toString();
		cana  =Math.round(parseFloat($("#cantidad_"+id).val() )* 100) /100;
		precio=Math.round(parseFloat($("#precio_"+id).val()   )* 100) /100;
		importe=Math.round(parseFloat($("#importe_"+id).val()   )* 100) /100;
		op=cana*precio;
		
		if(!isNaN(op)){
			if( ( (cana*precio)-importe>0.05) || (importe-(cana*precio)>0.05) ){
				may = Math.round((cana*precio+0.05)*100)/100;
				men = Math.round((cana*precio-0.05)*100)/100;
				alert("ERROR. El valor maximo permitido es:"+may+" y el menor:"+men);
				$("#importe_"+id).val(Math.round((cana*precio)*100)/100);
			}
		}
		cal_total();
	}

	function consulsprv(){
		cod_prov=$("#cod_prov").val();
		if(cod_prov.length==0){
			window.open("<?=site_url('presupuesto/sprv/dataedit/create')?>","PROVEEDORES","height=600,width=800,scrollbars=yes");
		}else{
			window.open("<?=site_url('presupuesto/sprv/dataedit/modify')?>/"+cod_prov,"PROVEEDORES","height=600,width=800,scrollbars=yes");
		}
	}
	
	function rete(){
		cal();
	}


	function add_itodirect(){
		var htm = <?=$campos ?>;
		can = itodirect_cont.toString();
		con = (itodirect_cont+1).toString();
		cin = (itodirect_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#cantidad_"+can).numeric(".");
		$("#precio_"+can).numeric(".");
		
		
		itodirect_cont=itodirect_cont+1;
		tipoo();
		
		ante=$("#itfondo_"+cin).attr('selectedIndex');
  	$("#itfondo_"+can).attr('selectedIndex',ante);
  	
  	ante=$("#itcodigoadm_"+cin).val();
  	$("#itcodigoadm_"+can).val(ante);
  	
		$("#itpartida_"+can).val("4.");
		$("#itcantidad_"+can).val("1");
		autop();
		autoe();
		mascara();
	}

	function del_itodirect(id){
		id = id.toString();
		$('#tr_itodirect_'+id).remove();
		tipoo();
	}

	</script>
	<?php
	}else{
	?>
	<script language="javascript" type="text/javascript">
		function btn_anulaf(i){
			if(!confirm("Esta Seguro que desea Anular la Orden de Pago Directo"))
				return false;
			else
				window.location='<?=site_url('presupuesto/common/pd_anular')?>/'+i
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
			    <td class="littletablerowth"><?=$form->tipo->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->tipo->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->status->label ?>&nbsp;</td> 
			    <td class="littletablerow"  ><?=$form->status->output?>&nbsp; </td>
			  </tr>
			  
			  <tr id="tr_nomina">
			    <td class="littletablerowth">             <?=$form->nomina->label  ?>&nbsp;</td>
			    <td class="littletablerow"  colspan = "3"><?=$form->nomina->output.$form->denomin->output ?>&nbsp; </td>
			  </tr>
			  
     		<tr>
	     		<td class="littletablerowth"><?=$form->uejecutora->label ?>*&nbsp;</td>			  	
	     		<td class="littletablerow"  ><?=$form->uejecutora->output ?>&nbsp; </td> 
	     		<td class="littletablerowth"><?=$form->fecha->label ?></td> 
	     		<td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp;</td> 
     		</tr>
     		<tr>
			    <td class="littletablerowth"><?=$form->cod_prov->label ?>*&nbsp;</td>
			    <td class="littletablerow"  colspan="3"><?=$form->cod_prov->output.'<span class="littletablerowth" >RIF</span>'.$form->rif->output.'<span class="littletablerowth" >Nombre</span>'.$form->nombrep->output ?>&nbsp;<span class="littletablerowth" ><?=$form->reteiva_prov->label  ?></span><?=$form->reteiva_prov->output  ?></td>
			  </tr>
     		<tr> 
     			<td class="littletablerowth"><?=$form->observa->label ?>&nbsp;</td> 
     			<td class="littletablerhotow" colspan=3 ><?=$form->observa->output ?>&nbsp;</td> 
     		</tr>
     		<?php $tipo = $form->_dataobject->get('tipo');
     			if(!(($tipo =='N' || $tipo=='T'))){
				?>
			  <tr id="tr_factura1">
			  	<td class="littletablerowth">            <?=$form->multiple->label ?>    </td> 
			    <td class="littletablerow"  >            <?=$form->multiple->output?>    </td>			  	
			  	<td class="littletablerowth">            <?=$form->fechafac->label ?>    </td> 
			    <td class="littletablerow"  >            <?=$form->fechafac->output?>    </td>
			  </tr>
			  <tr id="tr_factura2">
			    <td class="littletablerowth"><?=$form->factura->label  ?></td>
			    <td class="littletablerow"  ><?=$form->factura->output ?> </td>
			    <td class="littletablerowth"><?=$form->controlfac->label ?>    </td> 
			    <td class="littletablerow"  ><?=$form->controlfac->output?>    </td>
			  </tr>
			  <?php
					}
			  ?>
			   <?
			  $unsolofondo=$this->datasis->traevalor('UNSOLOFONDO','S','Indica si se utiliza una sola fuente de financiamiento');
			  if($unsolofondo=='S'){
			  ?>
			  <tr>
			    <td class="littletablerowth"><?=$form->fondo->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fondo->output?>&nbsp;</td>
			    <td class="littletablerowth">&nbsp;</td>
			    <td class="littletablerow"  >&nbsp;</td>
			  </tr>
			  <?
				}
			  ?>
			  <?php if($this->datasis->traevalor("VERREDONDEAR","N")=='S'){?>
			  <tr>
			    <td class="littletablerowth">&nbsp;</td>
			    <td class="littletablerow"  >&nbsp;</td>
			    <td class="littletablerowth"><?=$form->redondear->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->redondear->output?>&nbsp;</td>
			  </tr>
			  <?php }?>
			<tr>
			    <td class="littletablerowth"><?=$form->simptimbre->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->simptimbre->output?>&nbsp;</td>
			    <td class="littletablerowth">&nbsp;</td>
			    <td class="littletablerow"  >&nbsp;</td>
			  </tr>

			</table><br />
			<table width='100%'>
				<tr>
					<td class="littletableheaderb"                            >P.IVA?                                                                                           </td>
					<?
					if($unsolofondo!='S'){
					?>
					<td class="littletableheaderb"                           >F.Financiamiento                                                                      </td>
					<?
					}
					?>
					<td class="littletableheaderb"                            >Est. Admin                                                                                       </td>
					<td class="littletableheaderb"                            >Partida                                                                                          </td>
					
					<td class="littletableheaderb"                            >Descripci&oacute;n                                                                               </td>
					<td class="littletableheaderb" id="unidad"   align='right'><? if($form->_status=='show' && ($tipo =='T' || $tipo =='N'))echo ""; else echo "Unidad   "; ?>  </td>
					<td class="littletableheaderb" id="cantidad" align='right'><? if($form->_status=='show' && ($tipo =='T' || $tipo =='N'))echo ""; else echo "Cantidad "; ?>  </td>
					<td class="littletableheaderb" id="precio"   align='right'><? if($form->_status=='show' && ($tipo =='T' || $tipo =='N'))echo ""; else echo "Precio   "; ?>  </td>
					<td class="littletableheaderb"                            ><div id="div_reten3">    &nbsp;                                                                  </td>
					<td class="littletableheaderb"                            ><div id="div_reten4">        <? if( $tipo !='Servicio')echo ""; else echo "ISLR";     ?>         </td>
					<td class="littletableheaderb" id="iva"      align='right'><? if($form->_status=='show' && ($tipo =='T' || $tipo =='N'))echo ""; else echo "Iva      "; ?>  </td>
					<td class="littletableheaderb" id="importe"  align='right'><? if($form->_status=='show' && ($tipo =='T' || $tipo =='N'))echo "Monto"; else echo "Importe";?></td>
					<?php if($form->_status!='show') {?>
					<td class="littletableheaderb">&nbsp;</td>
					<?php } ?>
				</tr>
				<?php
				for($i=0;$i<$form->max_rel_count['itodirect'];$i++) {
					$obj12="itesiva_$i";
					$obj10="itfondo_$i";
					$obj11="itcodigoadm_$i";
					$obj0 = "itpartida_$i"; 
					
					$obj2 = "itdescripcion_$i";
					$obj3 = "itunidad_$i";
					$obj4 = "itcantidad_$i";
					$obj5 = "itprecio_$i";
					$obj6 = "itusaislr_$i";
					$obj7 ="itislr_$i";
			  	$obj8 = "itiva_$i";
			  	$obj9 = "itimporte_$i";
			  ?>
			  <tr id='tr_itodirect_<?=$i ?>'>
			  	<td class="littletablerow"               ><?=$form->$obj12->output ?></td>
			  	<?
					if($unsolofondo!='S'){
					?>
					<td class="littletablerow"><?=$form->$obj10->output ?></td>
					<?
					}
					?>
					<td class="littletablerow"               ><?=$form->$obj11->output ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj0->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj2->output  ?> </td>
			    <td class="littletablerow"               ><?=($form->_status=='show' && ($tipo =='T' || $tipo =='N'))? '':$form->$obj3->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=($form->_status=='show' && ($tipo =='T' || $tipo =='N'))? '':$form->$obj4->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=($form->_status=='show' && ($tipo =='T' || $tipo =='N'))? '':$form->$obj5->output  ?> </td>
			    <td class="littletablerow" align='right' ><div id="div_reten_<?=$i ?>"                   > <?=($form->_status=='show' && $tipo =="Servicio"?$form->$obj6->output:($form->_status!='show'?$form->$obj6->output:""))  ?></div></td>
			    
			    <td class="littletablerow" align='right' ><div id="div_reten1_<?=$i ?>" style="width:90px"><?=($form->_status=='show' && $tipo =="Servicio"?$form->$obj7->output:($form->_status!='show'?$form->$obj7->output:""))  ?></div></td>
			    <td class="littletablerow" align='right' ><?=($form->_status=='show' && ($tipo =='T' || $tipo =='N'))? '':$form->$obj8->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj9->output  ?> </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_itodirect(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			   
			  <tr id='__UTPL__'>

				<td class="littletablefooterb" colspan="2">
				<?php
				if($form->_status!='P'){
					echo $container_bl; 
					echo $container_br;
				}
				?>
				</td>
				<td class="littletablefooterb" align='right' colspan="5">&nbsp;                      </td>
				<td class="littletablefooterb" align='right' colspan="2">&nbsp;                      </td>
			    <td class="littletablefooterb" align='right' colspan="1"><?=$form->subtotal->label  ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->subtotal->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <?php 
			  if(!($form->_status=='show' && ($tipo =='N' || $tipo=='T'))){
			  ?>
			  
			   <tr id="trivag">
			    <td class="littletablerow" align='right' colspan="2"><div id="div_creten">  <?=$form->creten->label  ?> &nbsp;<?=$form->preten->output ?>% </div></td>
			    <td class="littletablerow"               colspan="3"><div id="div_creten2"> <?=$form->creten->output ?>&nbsp;  </div></td>
			    <td class="littletablerow" align='right' colspan="1"><div id="div_reten">   <?=$form->reten->label   ?>        </div></td>
			    <td class="littletablerow" align='right' ><div id="div_reten2">             <?=$form->reten->output  ?>        </div></td>
			    <td class="littletablerow" align='right' colspan="2">                                  &nbsp;                        </td>
			    <td class="littletablerow" align='right' colspan="1">                       <?=$form->ivag->label    ?>              </td>
			    <td class="littletablerow" align='right'>                                   <?=$form->ivag->output   ?>              </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>

			  <tr id="trivar">
		  	<td class="littletablerow" align='right' colspan="4"><?=$form->imptimbre->label   ?></td>
			    <td class="littletablerow" align='right'>                <?=$form->imptimbre->output  ?></td>
			    <td class="littletablerow" align='right' colspan="1" ><?=$form->exento->label  ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->exento->output ?></td>
			    <td class="littletablerow" align='right' colspan="2">                                  &nbsp;                        </td>
			    <td class="littletablerow" align='right' colspan="1"><?=$form->ivar->label    ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->ivar->output   ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  
			  <tr id="trivaa" >
			  	<td class="littletablerow" align='right' colspan="4"><?=$form->otrasrete->label   ?></td>
			    <td class="littletablerow" align='right'>                <?=$form->otrasrete->output  ?>&nbsp;</td>
			    <td class="littletablerow" align='right' colspan="1"><?=$form->reteiva->label   ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->reteiva->output  ?></td>
			    <td class="littletablerow" align='right' colspan="2">                                  &nbsp;                        </td>
			    <td class="littletablerow" align='right' colspan="1"><?=$form->ivaa->label      ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->ivaa->output     ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <?php
				}
				$tipo = $form->_dataobject->get('tipo'); 
				if((($tipo =='N'))){
			  ?>
			  <tr id="trnomi">	
			  	<td class="littletablerow" align='right' colspan="12"><?=$form->retenomina->label   ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->retenomina->output  ?></td>
			  </tr>
			  <?php
				}
				?>
			  <tr>
			  	<td  class="littletablefooterb" align='right' colspan="7"><? if(!(($tipo =='N' || $tipo=='T')))echo $form->total->label.$form->total->output  ?></td>
			    <td class="littletablefooterb" align='right' colspan="2">                                  &nbsp;                        </td>
			    <td class="littletablefooterb" align='right'                         ><?=$form->total2->label  ?></td>
			    <td class="littletablefooterb" align='right'>                         <?=$form->total2->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  
			 
	    </table>
		<?php echo $form_end     ?>
		<td>
	<tr>
<table>
<?php endif; ?>

