<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

$container_fa='';
if ($form->_status=='create' OR $form->_status=='modify')
$container_fa=join("&nbsp;", $form->_button_status[$form->_status]["FA"]);


if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:

foreach($form->detail_fields['itodirect'] AS $ind=>$data)
	$campos[]=$data['field'];
	$campos='<tr id="tr_itodirect_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
	$campos.=' <td class="littletablerow"><a href=# onclick="del_itodirect(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
	$campos=$form->js_escape($campos);
	
foreach($form->detail_fields['itfac'] AS $ind=>$data)
	$campos2[]=$data['field'];
	$campos2='<tr id="tr_itfac_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos2).'</td>';
	$campos2.=' <td class="littletablerow"><a href=# onclick="del_itfac(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
	$campos2=$form->js_escape($campos2);

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
	itfac_cont=<?=$form->max_rel_count['itfac'] ?>;
	datos    ='';
	estruadm ='';
	sprv ='';
	com=false;

	$(document).ready(function() {
		cal_totalfac();
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
				delay: 0.00,
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

			//$("#rif").autocomplete({
			//	delay: 0,
			//	minLength: 3,
			//	source: sprv,
			//	focus: function( event, ui ) {
			//		$( "#cod_prov").val( ui.item.proveed );
			//		$( "#nombrep").val( ui.item.nombre );
			//		$( "#reteiva_prov").val( ui.item.reteiva );
			//		$( "#rif").val( ui.item.rif );
			//		return false;
			//	},
			//	select: function( event, ui ) {
			//		$( "#rif").val( ui.item.rif );
			//		return false;
			//	}
			//})
			//.data( "autocomplete" )._renderItem = function( ul, item ) {
			//	return $( "<li></li>" )
			//	.data( "item.autocomplete", item )
			//	.append( "<a>" +item.rif+'-'+ item.nombre + "</a>" )
			//	.appendTo( ul );
			//};
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


				fondo=$("#fondo").val()


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

	$(function() {
		$(".inputnum").numeric(".");

	});

	function cal_timbre(){
		console.log("cal_timbre");
		s=$("#simptimbre").is(":checked");

		if(s==true){
			a=$("#subtotal").val()/<?=$imptimbre?>;
			$("#imptimbre").val(Math.round(a*100)/100);
		}else{
				$("#imptimbre").val(0);
		}
		cal_total();
	}

	function cretenf(){
		
		breten = $("#breten").val();
		var ind  =$("#creten").val();
		var data = <?=$rete ?>;
		var utribuactual = <?=$utribuactual ?>;
		a=ind.substring(0,1);

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
		
		ret=redondear((breten*(tari/100))-redondear(utribuactual*(tari/100)*porcentsustra));
		if(ret <0)ret=0;
		$("#reten").val(ret);
		
		arr=$('input[name^="factura_"]');
		jQuery.each(arr, function(){
			nom  =this.name;
			pos  =this.name.lastIndexOf('_');
			id   = this.name.substring(pos+1);
			
			bretend  = parseFloat($("#breten_"+id      ).val());
			
			ret=redondear((bretend*(tari/100))-(utribuactual*(tari/100)*porcentsustra));
			//if(ret <0)ret=0;
			//$("#rete_"+id).val(ret);
			//console.log(ret);
		});
	}

	function cal_reten(){
		console.log("cal_reten");
		arr=$('input[name^="factura_"]');
		jQuery.each(arr, function() {
			nom  =this.name;
			pos  =this.name.lastIndexOf('_');
			id   = this.name.substring(pos+1);
			
			cal_subtotal(id);
		})
	}
	
	function valido_float(id){
		valor=parseFloat($("#"+id+"").val());
		if(isNaN(valor)){
			$("#"+id+"").val('0');
			valor=0;
		}
		return valor;
	}
	
	function redondear(valor){
		valor=parseFloat(valor);
		if(isNaN(valor)){
			valor=0;
		}
		valor=Math.round(valor * 100) /100
		return valor;
	}
	

	function cal_total(){
		console.log("cal_total");
		tot=stot=giva=riva=aiva=exce=reteiva=iva2=total2=amortiza=totalbase=0;
		
		ivag        =valido_float("ivag");
		ivar        =valido_float("ivar");
		ivaa        =valido_float("ivaa");
		exento      =valido_float("exento");
		reteiva     =valido_float("reteiva");
		reten       =valido_float("reten");
		imptimbre   =valido_float("imptimbre");
		impmunicipal=valido_float("impmunicipal");
		otrasrete   =valido_float("otrasrete");
		porcent     =valido_float("porcent");
		//reteiva_prov=parseFloat($("#reteiva_prov").val());
		
		iva = ivag+ivar+ivaa;
		
		//if((isNaN(reteiva_prov)) || (reteiva_prov==0) || (reteiva_prov=='') || (reteiva_prov==100)){
		//	$("#reteiva_prov").val('100');
		//	reteiva_prov=100;
		//}else{
		//	$("#reteiva_prov").val('75');
		//	reteiva_prov=75;
		//}
		//
		//reteiva=redondear(iva*reteiva_prov/100);

		arr=$('input[name^="precio_"]');
		jQuery.each(arr, function() {
			nom=this.name
			pos=this.name.lastIndexOf('_');
			if(pos>0){
				id=this.name.substring(pos+1);
				valor  =valido_float("precio_"+id);
				total2+=valor;
				
				esiva=$("#esiva_"+id).val();
				if(esiva=='N')
				totalbase+=valido_float("precio_"+id);
			}
		});
		
		tsubtotal=valido_float("tsubtotal");
		texento=valido_float("texento");
		if(tsubtotal+texento>0){
			
			subtotal=redondear(tsubtotal);
			$("#subtotal"        ).val(subtotal);
		}else{
			console.log("entro");
			subtotal=redondear(total2   );
			$("#subtotal"        ).val(subtotal);
		}

		tot=stot;
		
		if(porcent>0){
			//amortiza=redondear(totalbase*porcent/100);
		}
		
		amortiza=$("#amortiza").val();

		to=tot-reten-otrasrete-reteiva-imptimbre-impmunicipal;
		
		total=(subtotal+ivag+ivaa+ivar+exento)-reten-otrasrete-reteiva-imptimbre-impmunicipal-amortiza;
		
		//$("#subtotal").val(subtotal);
		//$("#amortiza").val(amortiza);
		$("#total2").val(redondear(total2));
		$("#total").val(redondear(total));
	}
	
	function cal(){
		console.log("cal");
		cal_timbre();
		cal_municipal();
	}

	function cal_importe(i){
		console.log("cal_importe("+i+")");
		cal_total();
	}

	function cal_importep(i){
		console.log("cal_importep("+i+")");
		id=i.toString();
		
		cal_total();
	}
	
	function rete(){
		cal();
	}
	
	

	function consulsprv(){
		cod_prov=$("#cod_prov").val();
		if(cod_prov.length==0){
			window.open("<?=site_url('presupuesto/sprv/dataedit/create')?>","PROVEEDORES","height=600,width=800,scrollbars=yes");
		}else{
			window.open("<?=site_url('presupuesto/sprv/dataedit/modify')?>/"+cod_prov,"PROVEEDORES","height=600,width=800,scrollbars=yes");
		}
	}
	
	function cal_subtotal(i){
		console.log("cal_subtotal("+i+")");
		id=i.toString();
		subtotal    =valido_float("subtotal_"+id);
		exento      =valido_float("exento_"+id  );
		giva        =valido_float("ivag_"+id);
		riva        =valido_float("ivaa_"+id);
		aiva        =valido_float("ivar_"+id);
		reteiva_prov=$("#reteiva_prov").val();
		
		if((isNaN(reteiva_prov)) || (reteiva_prov==0) || (reteiva_prov=='') || (reteiva_prov==100))
			reteiva_prov=100;
		else
			reteiva_prov=75;
		
		reteiva=redondear(((giva+riva+aiva)*reteiva_prov)/100);
		
		uimptimbre = $("#uimptimbre_"+id  ).val();
		imptimbre=0;
		if(uimptimbre=="S"){
			$("#imptimbre_"+id ).show();
			imptimbre=subtotal*<?=$imptimbre?>/100;
		}
			
		uimpmunicipal = $("#uimpmunicipal_"+id  ).val();
		impmunicipal=0;
		if(uimpmunicipal=="S"){
			$("#impmunicipal_"+id  ).show();
			impmunicipal=subtotal*<?=$impmunicipal?>/100;
		}
			
		total2 = subtotal + giva+riva+aiva+exento;	
		
		$("#imptimbre_"+id).val(redondear(imptimbre));
		$("#impmunicipal_"+id).val(redondear(impmunicipal));
		$("#reteiva_"+id  ).val(reteiva );
		$("#total2_"+id   ).val(redondear(total2));
		cal_totalfac();
		
	}
	
	function cal_subtotal_subtotal(i){
		console.log("cal_subtotal_subtotal("+i+")");
		id=i.toString();

		subtotal    =valido_float("subtotal_"+id);
		
		$("#breten_"+id).val(subtotal);

		cal_subtotal_ivag(i,"CALC");
		cal_subtotal_ivar(i,"CALC");
		cal_subtotal_ivaa(i,"CALC");
	}
	
	function cal_subtotal_exento(i){
	console.log("cal_subtotal_exento("+i+")");
		cal_subtotal(i,true);
	}
	
	function cal_subtotal_ivag(i,calcula){
		console.log("cal_subtotal_ivag("+i+")");
		
		if(calcula=='CALC'){
			id=i.toString();
			uivag   = $("#uivag_"+id       ).val();
			if(uivag=="S"){
				subtotal    =valido_float("subtotal_"+id);
				giva=(subtotal)*<?=$ivag ?>/100
			}else{
				giva=0;
			}
			$("#ivag_"+id     ).val(redondear(giva));
		}
		
		
		cal_subtotal(i,true);
	}
	
	function cal_subtotal_ivar(i,calcula){
		console.log("cal_subtotal_ivar("+i+")");
		if(calcula=='CALC'){
			id=i.toString();
			
			uivar      = $("#uivar_"+id       ).val();
			if(uivar=="S"){
				subtotal    =valido_float("subtotal_"+id);
				riva=redondear((subtotal)*<?=$ivar ?>/100)
			}else{
				riva=0;
			}
			$("#ivar_"+id     ).val(redondear(riva    ));
		}
		cal_subtotal(i,true);
	}
	
	function cal_subtotal_ivaa(i,calcula){
		console.log("cal_subtotal_ivaa("+i+")");
		if(calcula=='CALC'){
			id=i.toString();
			uivaa      = $("#uivaa_"+id       ).val();
			if(uivaa=="S"){
				subtotal    =valido_float("subtotal_"+id);
				aiva=redondear((subtotal)*<?=$ivaa ?>/100)
			}else{
				aiva=0;
			}		
			$("#ivaa_"+id     ).val(redondear(aiva    ));
		}
		
		cal_subtotal(i,true);
	}
	
	
	
	function cal_showhide_fields_fac(id){
		
		uimptimbre = $("#uimptimbre_"+id  ).val();
		if(uimptimbre=="S"){
			$("#imptimbre_"+id ).show();
			bimptimbre=bimptimbre+1;
		}else{
			$("#imptimbre_"+id ).hide();
		}
			
		uimpmunicipal = $("#uimpmunicipal_"+id  ).val();
		if(uimpmunicipal=="S"){
			$("#impmunicipal_"+id  ).show();
			bimpmunicipal=bimpmunicipal+1;
		}else{
			$("#impmunicipal_"+id  ).hide();
		}
		
		uivar = $("#uivar_"+id  ).val();
		if(uivar=="S"){
			$("#ivar_"+id  ).show();
			bivar=bivar+1;
		}else{
			$("#ivar_"+id  ).hide();
		}
		
		uivaa = $("#uivaa_"+id  ).val();
		if(uivaa=="S"){
			$("#ivaa_"+id  ).show();
			bivaa=bivaa+1;
		}else{
			$("#ivaa_"+id  ).hide();
		}
		
	}
	
	function cal_totalfac(){
		console.log("cal_totalfac");
		breten=texento=tsubtotal=trivaa=trivag=trivar=ttotal2=treteiva = timptimbre=timpmunicipal=0;
		bimptimbre=bimpmunicipal=bivaa=bivar=0;
		
		arr=$('input[name^="factura_"]');
		jQuery.each(arr, function() {
			nom  =this.name;
			pos  =this.name.lastIndexOf('_');
			id   = this.name.substring(pos+1);
			
			texento     += parseFloat($("#exento_"+id      ).val());
			tsubtotal   += parseFloat($("#subtotal_"+id    ).val());
			trivag      += parseFloat($("#ivag_"+id        ).val());
			trivaa      += parseFloat($("#ivaa_"+id        ).val());
			trivar      += parseFloat($("#ivar_"+id        ).val());
			breten      += parseFloat($("#breten_"+id      ).val());
			reteiva      = parseFloat($("#reteiva_"+id     ).val());
			imptimbre    = parseFloat($("#imptimbre_"+id   ).val());
			impmunicipal = parseFloat($("#impmunicipal_"+id).val());
			total2       = parseFloat($("#total2_"+id      ).val());
			treteiva       +=reteiva;
			timptimbre     +=imptimbre;
			timpmunicipal  +=impmunicipal;
			ttotal2        +=total2;
			
			cal_showhide_fields_fac(id);
			
		});
		
		
		if(bimptimbre>0){
			$("#timptimbre" ).show();
		}else{
			$("#timptimbre" ).hide();
		}
		
		if(bimpmunicipal>0){
			$("#timpmunicipal" ).show();
		}else{
			$("#timpmunicipal" ).hide();
		}
		
		if(bivar>0){
			$("#trivar" ).show();
		}else{
			$("#trivar" ).hide();
		}
		
		if(bivaa>0){
			$("#trivaa" ).show();
		}else{
			$("#trivaa" ).hide();
		}

		$("#texento"         ).val(redondear(texento      ));
		$("#tsubtotal"       ).val(redondear(tsubtotal    ));
		$("#trivag"          ).val(redondear(trivag       ));
		$("#trivaa"          ).val(redondear(trivaa       ));
		$("#trivar"          ).val(redondear(trivar       ));
		$("#ttotal2"         ).val(redondear(ttotal2      ));
		$("#treteiva"        ).val(redondear(treteiva     ));
		$("#timptimbre"      ).val(redondear(timptimbre   ));
		$("#timpmunicipal"   ).val(redondear(timpmunicipal));
		$("#breten"          ).val(redondear(breten       ));
		
		cretenf();
		
		$("#exento"          ).val(redondear(texento      ));
		$("#ivag"            ).val(redondear(trivag       ));
		$("#ivaa"            ).val(redondear(trivaa       ));
		$("#ivar"            ).val(redondear(trivar       ));
		$("#reteiva"         ).val(redondear(treteiva     ));
		$("#imptimbre"       ).val(redondear(timptimbre   ));
		$("#impmunicipal"    ).val(redondear(timpmunicipal));
		cal_total();
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
		cal_total();
	}
	
	function add_itfac(){
		var htm = <?=$campos2 ?>;
		can = itfac_cont.toString();
		con = (itfac_cont+1).toString();
		cin = (itfac_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPITFAC__").before(htm);
		$("#subtotal_"+can).numeric(".");
		$("#ivag_"+can).numeric(".");
		$("#ivar_"+can).numeric(".");
		$("#ivaa_"+can).numeric(".");
		$("#reteiva_"+can).numeric(".");
		
		$("#imptimbre_"+can).numeric(".");
		$("#total_"+can).numeric(".");
		$("#total2_"+can).numeric(".");
		$("#exento_"+can).numeric(".");
		$("#subtotal_"+can).val("0");
		$("#ivag_"+can).val("0");
		$("#ivar_"+can).val("0");
		$("#ivaa_"+can).val("0");
		$("#reteiva_"+can).val("0");
		
		$("#imptimbre_"+can).val("0");
		$("#total_"+can).val("0");
		$("#total2_"+can).val("0");
		$("#exento_"+can).val("0");

		uivag      = $("#uivag_"+cin       ).attr('selectedIndex');
		uivaa      = $("#uivaa_"+cin       ).attr('selectedIndex');
		uivar      = $("#uivar_"+cin       ).attr('selectedIndex');
		ureten     = $("#ureten_"+cin      ).attr('selectedIndex');
		uimptimbre = $("#uimptimbre_"+cin  ).attr('selectedIndex');

		$("#uivag_"+can       ).attr('selectedIndex',uivag     );
		$("#uivaa_"+can       ).attr('selectedIndex',uivaa     );
		$("#uivar_"+can       ).attr('selectedIndex',uivar     );
		$("#ureten_"+can      ).attr('selectedIndex',ureten    );
		$("#uimptimbre_"+can  ).attr('selectedIndex',uimptimbre);
		
		cal_subtotal(cin);

		itfac_cont=itfac_cont+1;
	}

	function del_itfac(id){
		id = id.toString();
		$('#tr_itfac_'+id).remove();
		cal_totalfac();
	}

	</script>
	<?php
	}else{
	?>
	<script language="javascript" type="text/javascript">
		function btn_anulaf(i){
			if(!confirm("Esta Seguro que desea Anular la Orden de Pago "))
				return false;
			else
				window.location='<?=site_url('presupuesto/common/pc_anular')?>/'+i
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
				<td class="littletablerowth"><?=$form->fecha->label ?>&nbsp; </td>
				<td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp; </td>
				<td class="littletablerowth"><?=$form->status->label ?>&nbsp;</td>
				<td class="littletablerow"  ><?=$form->status->output?>&nbsp; </td>
				
			</tr>

				<tr>
					<td class="littletablerowth"><?=$form->cod_prov->label ?>*&nbsp;</td>
					<td class="littletablerow"  colspan="3"><?=$form->cod_prov->output.'<span class="littletablerowth" >RIF</span>'.$form->rifp->output.'<span class="littletablerowth" >Nombre</span>'.$form->nombrep->output ?>&nbsp;<span class="littletablerowth" ><?=$form->reteiva_prov->label  ?></span><?=$form->reteiva_prov->output  ?></td>
				  </tr>
				<tr>
					<td class="littletablerowth"><?=$form->observa->label ?>&nbsp;</td>
					<td class="littletablerhotow" colspan=3 ><?=$form->observa->output ?>&nbsp;</td>
				</tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->fondo->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fondo->output?>&nbsp;</td>
			    <td class="littletablerowth"><?=$form->tipoc->label   ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->tipoc->output  ?>&nbsp;</td>
			  </tr>
			</table>
			<br />
			<table width='100%'>
				<tr>
					<td class="littletableheaderb"               >Orden                </td>
					<td class="littletableheaderb"               >Est. Admin           </td>
					<td class="littletableheaderb"               >Partida              </td>
					<td class="littletableheaderb"               >Denominaci&oacute;n  </td>
					<td class="littletableheaderb"  align='right'>Monto                </td>
					<?php if($form->_status!='show') {?>
					<td class="littletableheaderb">&nbsp;                              </td>
					<?php } ?>
				</tr>
				<?php
				for($i=0;$i<$form->max_rel_count['itodirect'];$i++){
					$obj11="itcodigoadm_$i";
					$obj0 = "itpartida_$i";
					$obj5 = "itprecio_$i";
					$obj12= "itocompra_$i";
					$obj13= "itdenominacion_$i";
					$obj1 = "itesiva_$i";
			  ?>
			  <tr id='tr_itodirect_<?=$i ?>'>
				<td class="littletablerow"               ><?=$form->$obj1->output.$form->$obj12->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj11->output   ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj0->output   ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj13->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj5->output  ?> </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"                ><a href=# onclick='del_itodirect(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php
				}
			   ?>
			  <tr id='__UTPL__'>

			    <td class="littletablefooterb" align='right' colspan="4"><?=$form->subtotal->label  ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->subtotal->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			 </table>
			 <table width='100%'>
			   <tr>
			    <td class="littletablerow" align='right' colspan="2"><div id="div_creten">  <?=$form->creten->label  ?> &nbsp; </div></td>
			    <td class="littletablerow"               colspan="3"><div id="div_creten2"> <?=$form->creten->output ?>&nbsp;<?=$form->preten->output ?>% </div></td>
			    <td class="littletablerow" align='right' colspan="1"><div id="div_reten">   <?=$form->reten->label   ?>        </div></td>
			    <td class="littletablerow" align='right' ><div id="div_reten2">             <?=$form->reten->output  ?>        </div></td>
			    <td class="littletablerow" align='right' colspan="2">                                  &nbsp;                        </td>
			    <td class="littletablerow" align='right' colspan="1">                       <?=$form->ivag->label    ?>              </td>
			    <td class="littletablerow" align='right'>                                   <?=$form->ivag->output   ?>              </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>

			  <tr >
			  	<td class="littletablerow" align='right' colspan="2" ><?=$form->montocontrato->label   ?></td>
			    <td class="littletablerow" align='right'             ><?=$form->montocontrato->output  ?></td>
			    <td class="littletablerow" align='right' colspan="3" ><?=$form->imptimbre->label  ?></td>
			    <td class="littletablerow" align='right'             ><?=$form->imptimbre->output ?></td>
			    <td class="littletablerow" align='right' colspan="2" >                                  &nbsp;                        </td>
			    <td class="littletablerow" align='right' colspan="1" ><?=$form->ivar->label    ?></td>
			    <td class="littletablerow" align='right'             ><?=$form->ivar->output   ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>

			  <tr >
				<td class="littletablerow" align='right' colspan="2"><?=$form->porcent->label   ?></td>
			    <td class="littletablerow" align='right'            ><?=$form->porcent->output  ?>&nbsp;</td>
			  	<td class="littletablerow" align='right' colspan="1"><?=$form->impmunicipal->label   ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->impmunicipal->output  ?>&nbsp;</td>
			    <td class="littletablerow" align='right' colspan="1"><?=$form->reteiva->label   ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->reteiva->output  ?></td>
			    <td class="littletablerow" align='right' colspan="2">                                  &nbsp;                        </td>
			    <td class="littletablerow" align='right' colspan="1"><?=$form->ivaa->label      ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->ivaa->output     ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <tr >
			  	<td class="littletablerow" align='right' colspan="4"><?=$form->amortiza->label   ?></td>
			    <td class="littletablerow" align='right'            ><?=$form->amortiza->output  ?></td>
			    <td class="littletablerow" align='right' colspan="1"><?=$form->otrasrete->label   ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->otrasrete->output  ?></td>
			    <td class="littletablerow" align='right' colspan="2">        &nbsp;                        </td>
			    <td class="littletablerow" align='right' colspan="1"><?=$form->exento->label      ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->exento->output     ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <tr>
			  	<td  class="littletablefooterb" align='right' colspan="7"><? echo $form->total->label.$form->total->output  ?></td>
			    <td class="littletablefooterb" align='right' colspan="2">                                  &nbsp;                        </td>
			    <td class="littletablefooterb" align='right'                         ><?=$form->total2->label  ?></td>
			    <td class="littletablefooterb" align='right'>                         <?=$form->total2->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>
	    </table>
	    </br>
	    
		<table width='100%' bgcolor="#FFEAEB">
			<tr>
				<th class="littletableheaderb" colspan="<?=(($form->_status!='show'?22:21))?>">DETALLE DE FACTURAS </th>
			</tr>
			<tr>
				<td class="littletableheaderb"                         >Factura*                                </td>
				<td class="littletableheaderb"                         >Control Fiscal*                         </td>
				<td class="littletableheaderb"                         >Fecha Factura*                          </td>
				<td class="littletableheaderb"align='right'            >Base Imponible                          </td>
				<td class="littletableheaderb"align='right'            >Exento                                  </td>
				<td class="littletableheaderb"align='right' colspan="2">IVA General <?=$form->tivag->output ?>% </td>
				<td class="littletableheaderb"align='right' colspan="2">IVA Reducido<?=$form->tivar->output ?>% </td>
				<td class="littletableheaderb"align='right' colspan="2">IVA Adicional<?=$form->tivaa->output ?>%</td>
				<td class="littletableheaderb"align='right'            >IVA Retenido                            </td>
				<td class="littletableheaderb"align='right'            >Base ISLR                               </td>
				<td class="littletableheaderb"align='right' colspan="2">1X1000                                  </td>
				<td class="littletableheaderb"align='right' colspan="2">I.Muni                                  </td>
				<td class="littletableheaderb"align='right'            >Total                                   </td>
				<?php if($form->_status!='show') {?>
				<td class="littletableheaderb">&nbsp;</td>
				<?php } ?>
			</tr>
			<?php
			for($i=0;$i<$form->max_rel_count['itfac'];$i++) {
				$obj0 = "itfactura_$i";
				$obj1  = "itcontrolfac_$i";
				$obj2  = "itfechafac_$i";
				$obj3  = "itsubtotal_$i";
				$obj4  = "itexento_$i";
				$obj5  = "itivag_$i";
				$obj6  = "itivar_$i";
				$obj7  = "itivaa_$i";
				$obj8  = "itreteiva_$i";
				$obj10 = "ittotal2_$i";
				$obj12 = "itimptimbre_$i";
				$obj13 = "ituivag_$i";
				$obj14 = "ituivar_$i";
				$obj15 = "ituivaa_$i";
				$obj17 = "ituimptimbre_$i";
				$obj18 = "itbreten_$i";
				$obj19 = "itimpmunicipal_$i";
				$obj20 = "ituimpmunicipal_$i";
				?>
			<tr id='tr_itfac_<?=$i ?>'>
				<td class="littletablerow"               ><?=$form->$obj0->output  ?> </td>
				<td class="littletablerow"               ><?=$form->$obj1->output  ?> </td>
				<td class="littletablerow"               ><?=$form->$obj2->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj3->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj4->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj13->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj5->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj14->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj6->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj15->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj7->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj8->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj18->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj17->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj12->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj20->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj19->output  ?> </td>
				<td class="littletablerow" align='right' ><?=$form->$obj10->output  ?> </td>
				<?php if($form->_status!='show') {?>
				<td class="littletablerow"><a href=# onclick='del_itfac(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
				<?php
				}
				?>
			</tr>
			<?php } ?>

			<tr id='__UTPITFAC__'>
				<td class="littletablefooterb" align='left'  colspan="2"><?=$container_fa?>                </td>
				<td class="littletablefooterb" align='right'            >Totales                           </td>
				<td class="littletablefooterb" align='right'            ><?=$form->tsubtotal->output     ?></td>
				<td class="littletablefooterb" align='right'            ><?=$form->texento->output       ?></td>
				<td class="littletablefooterb" align='right' colspan="2"><?=$form->trivag->output        ?></td>
				<td class="littletablefooterb" align='right' colspan="2"><?=$form->trivar->output        ?></td>
				<td class="littletablefooterb" align='right' colspan="2"><?=$form->trivaa->output        ?></td>
				<td class="littletablefooterb" align='right'            ><?=$form->treteiva->output      ?></td>
				<td class="littletablefooterb" align='right'            ><?=$form->breten->output        ?></td>
				<td class="littletablefooterb" align='right' colspan="2"><?=$form->timptimbre->output    ?></td>
				<td class="littletablefooterb" align='right' colspan="2"><?=$form->timpmunicipal->output ?></td>
				<td class="littletablefooterb" align='right'            ><?=$form->ttotal2->output       ?></td>
				<?php if($form->_status!='show') {?>
				<td class="littletablefooterb">&nbsp;</td>
				<?php } ?>
		</table>
		<?php echo $form_end     ?>
		<td>
	<tr>
<table>
<?php endif;?>
