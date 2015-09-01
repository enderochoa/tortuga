<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if (($form->_status=='modify') || $form->_status=='create')
	$container_pa=join("&nbsp;", $form->_button_status[$form->_status]["PA"]);
else
	$container_pa = '';

if ($form->_status=='create' || $form->_status=='modify' )
	$container_mb=join("&nbsp;", $form->_button_status[$form->_status]["MB"]);
else
	$container_mb = '';

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['itfac'] AS $ind=>$data)
	$campos[]=$data['field'];
	$campos='<tr id="tr_itfac_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
	$campos.=' <td class="littletablerow"><a href=# onclick="del_itfac(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
	$campos=$form->js_escape($campos);
	//$campos='';

foreach($form->detail_fields['itocompra'] AS $ind=>$data)
	$campos2[] =$data['field'];
	$campos2   ='<tr id="tr_itocompra_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos2).'</td>';
	$campos2  .=' <td class="littletablerow"><a href=# onclick="del_itocompra(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
	$campos2   =$form->js_escape($campos2);


if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin;

$tipo   = $form->_dataobject->get('tipo');
	$status = $form->_dataobject->get('status');

if($form->_status!='show'){
	$uri      =$this->datasis->get_uri();
	$idt      =$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_presaldo' AND uri='$uri'");
	$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');

	?>
	<script language="javascript" type="text/javascript">
	function oculta(){
		arr=$('input[name^="importe_"]');
		jQuery.each(arr, function() {
			nom   =this.name;
			pos   =this.name.lastIndexOf('_');
			id    = this.name.substring(pos+1);
			$("#cantidad_"+id   ).hide();
			$("#descripcion_"+id   ).hide();
			$("#unidad_"+id   ).hide();
			$("#precio_"+id   ).hide();
			$("#usaislr_"+id   ).hide();
			$("#islr_"+id   ).hide();
			$("#iva_"+id   ).hide();
		})
	}

	itfac_cont=<?=$form->max_rel_count['itfac'] ?>;
	itocompra_cont=<?=$form->max_rel_count['itocompra'] ?>;
	datos    ='';
	estruadm ='';
	$(document).ready(function() {
		oculta();
		cal_total();

		$.post("<?=site_url('presupuesto/ppla/autocomplete4')?>",{ partida:"" },function(data){
			datos=jQuery.parseJSON(data);
		});

		$.post("<?=site_url('presupuesto/estruadm/autocompleteui')?>",{ partida:"" },function(data){
			estruadm=jQuery.parseJSON(data);
		});

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

	function mascara(){
		$("input[name^='itcodigoadm_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOESTRU"))?>');
		$("input[name^='partida_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOPRES"))?>');
	}
	
	function redondear(valor){
		valor=parseFloat(valor);
		if(isNaN(valor)){
			valor=0;
		}
		valor=Math.round(valor * 100) /100
		return valor;
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

	$(function(){
		autop();
		autoe();
		mascara();

		$(".inputnum").numeric(".");
		com=false;
		$(document).keydown(function(e){
		//alert(e.which);
		if (18 == e.which) {
			com=true;
			//c = String.fromCharCode(e.which);
			return false;
		}
		if (com && (e.which == 61 || e.which == 107)) {
		  add_itfac();
		  a=itfac_cont-1;
		  $("#factura_"+a).focus();

			//alert("agrega linea");
				com=false;
				return false;
			}else if (com && e.which != 16 && e.which == 17){
				com=false;
			}
			return true;
		});
	});

	function cal_total(){
		texento=tsubtotal=trivaa=trivag=trivar=total=ttotal2=treteiva =treten= timptimbre=0;
		arr=$('input[name^="factura_"]');
		jQuery.each(arr, function() {
			nom  =this.name;
			pos  =this.name.lastIndexOf('_');
			id   = this.name.substring(pos+1);
			//id=i.toString();
			texento   += Math.round(parseFloat($("#exento_"+id   ).val()) * 100 )/ 100;
			tsubtotal += Math.round(parseFloat($("#subtotal_"+id ).val()) * 100 )/ 100;
//			trivag    += Math.round(parseFloat($("#ivag_"+id     ).val()) * 100 )/ 100;
			trivag    += parseFloat($("#ivag_"+id     ).val()) ;
//			trivaa    += Math.round(parseFloat($("#ivaa_"+id     ).val()) * 100 )/ 100;
			trivaa    += parseFloat($("#ivaa_"+id     ).val()) ;
			trivar    += Math.round(parseFloat($("#ivar_"+id     ).val()) * 100 )/ 100;
			reteiva    = Math.round(parseFloat($("#reteiva_"+id  ).val()) * 100 )/ 100;
			reten      = Math.round(parseFloat($("#rete_"+id     ).val()) * 100 )/ 100;
			imptimbre  = Math.round(parseFloat($("#imptimbre_"+id).val()) * 100 )/ 100;
			total2     = parseFloat($("#total2_"+id   ).val()) ;
			treteiva  +=reteiva;
			treten    +=reten;
			timptimbre+=imptimbre;
			ttotal2   +=total2;
			//alert("total2:"+total2);
			//alert("reteiva:"+reteiva);
			//alert("reten:"+reten);
			//alert("imptimbre:"+imptimbre);
			//$("#total_"+id ).val(Math.round(total2-reteiva-reten-imptimbre* 100 )/ 100);
			total     += Math.round(parseFloat($("#total_"+id ).val()) * 100 )/ 100;
		})

		$("#texento"         ).val(Math.round(texento        *100)/100);
		$("#tsubtotal"       ).val(Math.round(tsubtotal      *100)/100);
		$("#trivag"          ).val(Math.round(trivag         *100)/100);
		$("#trivaa"          ).val(Math.round(trivaa         *100)/100);
		$("#trivar"          ).val(Math.round(trivar         *100)/100);
		$("#ttotal"          ).val(Math.round(total          *100)/100);
		$("#ttotal2"         ).val(Math.round(ttotal2        *100)/100);
		$("#treteiva"        ).val(Math.round(treteiva       *100)/100);
		$("#treten"          ).val(Math.round(treten         *100)/100);
		$("#timptimbre"      ).val(Math.round(timptimbre     *100)/100);

		$("#exento"          ).val(Math.round(texento        *100)/100);
		$("#subtotal"        ).val(Math.round(tsubtotal      *100)/100);
		$("#ivag"            ).val(Math.round(trivag         *100)/100);
		$("#ivaa"            ).val(Math.round(trivaa         *100)/100);
		$("#ivar"            ).val(Math.round(trivar         *100)/100);
		$("#total"           ).val(Math.round(total          *100)/100);
		$("#reteiva"         ).val(Math.round(treteiva       *100)/100);
		$("#reten"           ).val(Math.round(treten         *100)/100);
		$("#imptimbre"       ).val(Math.round(timptimbre     *100)/100);
		//$("#total2"          ).val(Math.round(ttotal2        *100)/100);
		<? if(!($status=='P' || $status=='C' || $status=='T' || $status =='O' || $status=='E')){ ?>
		if(itocompra_cont==1){
			add_itocompra();
			$("#itesiva_1").attr('selectedIndex',1);
		}

		if(itocompra_cont==2){
				esiva = $("#esiva_0").val();
				if(esiva=='S'){
								$("#importe_1").val(tsubtotal);
								$("#importe_0").val(Math.round((trivaa+trivag+trivar)*100)/100);
				}else{
								$("#importe_0").val(tsubtotal);
								$("#importe_1").val(Math.round((trivaa+trivag+trivar)*100)/100);
				}

			cal_totalp();
		}
		<?
		}
		?>
	}

	function cal_totalp(){
		arr=$('input[name^="importe_"]');
		total=0;
		jQuery.each(arr, function() {
			nom   =this.name;
			pos   =this.name.lastIndexOf('_');
			id    = this.name.substring(pos+1);
			monto = parseFloat($("#importe_"+id   ).val());
			total+=monto;
		})
		$("#total22").val(Math.round(total  *100)/100);
	}

	function cal_reteiva(i){
		id=i.toString();
		subtotal     = parseFloat($("#subtotal_"+id).val());
		reteiva_prov = parseFloat($("#reteiva_prov").val());
		rete         = parseFloat($("#rete_"+id).val());
		imptimbre    = parseFloat($("#imptimbre_"+id).val());

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

		$("#reteiva_"+id ).val(reteiva );
		$("#total_"+id   ).val(Math.round((total2-reteiva-rete-imptimbre) *100)/100);
		$("#total2_"+id  ).val(Math.round(total2 * 100)/100);

		cal_total();

	}

	function cal_subtotal(i,calcula){
		id=i.toString();

		giva=riva=aiva=ret=imptimbre=0;
		subtotal    =parseFloat($("#subtotal_"+id).val());
		exento      =parseFloat($("#exento_"+id).val());
		reteiva_prov=parseFloat($("#reteiva_prov").val());

		uivag      = $("#uivag_"+id       ).val();
		uivaa      = $("#uivaa_"+id       ).val();
		uivar      = $("#uivar_"+id       ).val();
		ureten     = $("#ureten_"+id      ).val();
		uimptimbre = $("#uimptimbre_"+id  ).val();

		if((isNaN(reteiva_prov)) || (reteiva_prov==0) || (reteiva_prov=='') || (reteiva_prov==100))
			reteiva_prov=100;
		else
			reteiva_prov=75;

		if(!isNaN(subtotal)){
			if(uivag=="S")
			giva=Math.round(((subtotal-exento)*<?=$ivag ?>/100) *100)/100;
			if(uivar=="S")
			riva=Math.round(((subtotal-exento)*<?=$ivar ?>/100) *100)/100;
			if(uivaa=="S")
			aiva=Math.round(((subtotal-exento)*<?=$ivaa ?>/100) *100)/100;

			reteiva=((giva+riva+aiva)*reteiva_prov)/100;
			reteiva=Math.round(reteiva*100)/100;
		}

		if(ureten=="S"){
			breten = subtotal;
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
			
			ret=redondear((breten*(tari/100))-(utribuactual*(tari/100)*porcentsustra));
			if(ret <0)ret=0;
			
		}

		if(uimptimbre=="S"){
			imptimbre=$("#subtotal_"+id).val()*<?=$imptimbre?>/100;
			$("#imptimbre_"+id).val(Math.round(imptimbre*100)/100);
		}else{
			$("#imptimbre_"+id).val(0);
		}

		total2 = subtotal + giva+riva+aiva;
		//alert(total2+"__"+reteiva+"__"+ret+"__"+imptimbre);
		$("#total_"+id    ).val(Math.round((parseFloat(total2)-parseFloat(reteiva)-parseFloat(ret)-parseFloat(imptimbre))*100)/100 );
		//alert($("#total_"+id    ).val());
		$("#total2_"+id   ).val(Math.round(total2  *100)/100);
		$("#rete_"+id     ).val(Math.round(ret     *100)/100);
		$("#ivag_"+id     ).val(giva );
		$("#ivaa_"+id     ).val(Math.round(aiva    *100)/100);
		$("#ivar_"+id     ).val(Math.round(riva    *100)/100);
		$("#reteiva_"+id  ).val(Math.round(reteiva *100)/100);
		if(calcula)
		cal_total();
	}

	function cal_subtotal2(i,calcula){
		id=i.toString();

		giva=riva=aiva=ret=imptimbre=reteiva=0;
		subtotal    =parseFloat($("#subtotal_"+id).val());
		exento      =parseFloat($("#exento_"+id).val());
		reteiva_prov=parseFloat($("#reteiva_prov").val());

		uimptimbre = $("#uimptimbre_"+id  ).val();
		ureten     = $("#ureten_"+id  ).val();

		if((isNaN(reteiva_prov)) || (reteiva_prov==0) || (reteiva_prov=='') || (reteiva_prov==100))
			reteiva_prov=100;
		else
			reteiva_prov=75;

		if(!isNaN(subtotal)){
			giva =parseFloat($("#ivag_"+id).val());
			riva =parseFloat($("#ivar_"+id).val());
			aiva =parseFloat($("#ivaa_"+id).val());
			reteiva=((giva+riva+aiva)*reteiva_prov)/100;
			reteiva=Math.round(reteiva*100)/100;
		}

		if(ureten=="S"){
			breten = subtotal;
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
			
			ret=redondear((breten*(tari/100))-(utribuactual*(tari/100)*porcentsustra));
			if(ret <0)ret=0;
		}

		if(uimptimbre=="S"){
			imptimbre=$("#subtotal_"+id).val()*<?=$imptimbre?>/100;
			$("#imptimbre_"+id).val(Math.round(imptimbre*100)/100);
		}else{
			$("#imptimbre_"+id).val(0);
		}

		total2 = subtotal + giva+riva+aiva;
		//alert(total2+"__"+reteiva+"__"+ret+"__"+imptimbre);
		//a=parseFloat(total2)-parseFloat(reteiva);
		//alert( a);
		$("#total_"+id    ).val(Math.round((parseFloat(total2)-parseFloat(reteiva)-parseFloat(ret)-parseFloat(imptimbre))*100)/100 );
		//alert($("#total_"+id    ).val());
		$("#total2_"+id   ).val(Math.round(total2  *100)/100);
		$("#rete_"+id     ).val(Math.round(ret     *100)/100);
		$("#reteiva_"+id  ).val(Math.round(reteiva *100)/100);
		if(calcula)
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

	function cal_reten(){
		for(i=0;i<itfac_cont;i++){
			cal_subtotal(i,false);
		}
		cal_total();
	}

	function cal_simpt(){
		s=$("#simptimbre").is(":checked");

		for(i=0;i<itfac_cont;i++){
			id=i.toString();
			if(s==true){
				//alert("entro");
				$("#uimptimbre_"+id  ).prop('selectedIndex',0);
			}else{
				//alert(" no entro");
				$("#uimptimbre_"+id  ).prop('selectedIndex',1);
			}
			cal_subtotal(i,false);
		}
		cal_total();
	}

	function cal_totales(){

		tsubtotal = parseFloat($("#tsubtotal").val() );
		trivag    = parseFloat($("#trivag").val()    );
		trivaa    = parseFloat($("#trivaa").val()    );
		trivar    = parseFloat($("#trivar").val()    );
		treteiva  = parseFloat($("#treteiva").val()  );
		treten    = parseFloat($("#treten").val()    );
		timptimbre= parseFloat($("#timptimbre").val());
		tivas     = parseFloat(trivag+trivaa+trivar);
		total2    = tsubtotal+tivas;
		total     = total2-treteiva-treten-timptimbre;

		$("#ttotal").val(total);
		$("#ttotal2").val(total2);
	}

	function add_itfac(){
		var htm = <?=$campos ?>;
		can = itfac_cont.toString();
		con = (itfac_cont+1).toString();
		cin = (itfac_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#subtotal_"+can).numeric(".");
		$("#ivag_"+can).numeric(".");
		$("#ivar_"+can).numeric(".");
		$("#ivaa_"+can).numeric(".");
		$("#reteiva_"+can).numeric(".");
		$("#rete_"+can).numeric(".");
		$("#imptimbre_"+can).numeric(".");
		$("#total_"+can).numeric(".");
		$("#total2_"+can).numeric(".");
		$("#exento_"+can).numeric(".");
		$("#subtotal_"+can).val("0");
		$("#ivag_"+can).val("0");
		$("#ivar_"+can).val("0");
		$("#ivaa_"+can).val("0");
		$("#reteiva_"+can).val("0");
		$("#rete_"+can).val("0");
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

		itfac_cont=itfac_cont+1;
	}

	function del_itfac(id){
		id = id.toString();
		$('#tr_itfac_'+id).remove();
	}

	function consulsprv(){
		cod_prov=$("#cod_prov").val();
		if(cod_prov.length==0){
			window.open("<?=site_url('presupuesto/sprv/dataedit/create')?>","PROVEEDORES","height=600,width=800,scrollbars=yes");
		}else{
			window.open("<?=site_url('presupuesto/sprv/dataedit/modify')?>/"+cod_prov,"PROVEEDORES","height=600,width=800,scrollbars=yes");
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

	function add_itocompra(){
		var htm = <?=$campos2 ?>;
		can = itocompra_cont.toString();
		con = (itocompra_cont+1).toString();
		cin = (itocompra_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL2__").before(htm);
		oculta();
		$("#importe_"+can).numeric(".");

		itocompra_cont=itocompra_cont+1;
	}

	function del_itocompra(id){
		id = id.toString();
		$('#tr_itocompra_'+id).remove();
	}
	</script>
	<?php
	}else{
	?>
	<script language="javascript" type="text/javascript">
		function btn_terminada(i){
			if(!confirm("Esta Seguro que desea Marcar Como Terminada la Orden de Compra"))
				return false;
			else
				window.location='<?=site_url('presupuesto/ocompra/terminada')?>/'+i
		}

		function btn_noterminada(i){
			if(!confirm("Esta Seguro que desea Marcar Como NO Terminada la Orden de Compra"))
				return false;
			else
				window.location='<?=site_url('presupuesto/ocompra/noterminada')?>/'+i
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
	<tr >
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
			  	<td class="littletablerowth"><?=$form->uejecutora->label  ?>&nbsp;</td>
				<td class="littletablerow"  ><?=$form->uejecutora->output ?>&nbsp; </td>
				<td class="littletablerowth"><?=$form->creten->label ?>&nbsp;</td>
				<td class="littletablerow"  ><?=$form->creten->output?>&nbsp;</td>
			</tr>
			<tr>
			    <td class="littletablerowth"><?=$form->cod_prov->label ?>*&nbsp;</td>
			    <td class="littletablerow"  colspan="3"><?=$form->cod_prov->output.'<span class="littletablerowth" >RIF</span>'.$form->rif->output.'<span class="littletablerowth" >Nombre</span>'.$form->nombrep->output ?>&nbsp;<span class="littletablerowth" ><?=$form->reteiva_prov->label  ?></span><?=$form->reteiva_prov->output  ?></td>
			</tr>
			<tr>
			  	<td class="littletablerowth"><?=$form->fondo->label  ?>&nbsp; </td>
				<td class="littletablerow"  ><?=$form->fondo->output ?>&nbsp; </td>
				<td class="littletablerowth"><?=$form->simptimbre->label ?>&nbsp;</td>
				<td class="littletablerow"  ><?=$form->simptimbre->output ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletablerowth">           <?=$form->concepto->label  ?>&nbsp;   </td>
				<td class="littletablerow" colspan="3" ><?=$form->concepto->output ?>&nbsp;   </td>
			</tr>
	    	</table>
		<br />

			<table width='100%' bgcolor="#FFEAEB">
			<tr>
				<th class="littletableheaderb" colspan="<?=(($form->_status!='show'?19:18))?>">DETALLE DE FACTURAS </th>
			</tr>
			<tr>
				<td class="littletableheaderb"                         >Factura*                                </td>
				<td class="littletableheaderb"                         >Control Fiscal*                         </td>
				<td class="littletableheaderb"                         >Fecha Factura*                          </td>
				<td class="littletableheaderb"align='right'            >Subtotal                                </td>
				<td class="littletableheaderb"align='right'            >Exento                                  </td>
				<td class="littletableheaderb"align='right' colspan="2">IVA General <?=$form->tivag->output ?>% </td>
				<td class="littletableheaderb"align='right' colspan="2">IVA Reducido<?=$form->tivar->output ?>% </td>
				<td class="littletableheaderb"align='right' colspan="2">IVA Adicional<?=$form->tivaa->output ?>%</td>
				<td class="littletableheaderb"align='right'            >IVA Retenido                            </td>
				<td class="littletableheaderb"align='right' colspan="2">ISLR                                    </td>
				<td class="littletableheaderb"align='right' colspan="2">1X1000                                  </td>
				<td class="littletableheaderb"align='right'            >M.Pagar                                 </td>
				<td class="littletableheaderb"align='right'            >Total%                                  </td>
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
					$obj9  = "ittotal_$i";
					$obj10 = "ittotal2_$i";
					$obj11 = "itreten_$i";
					$obj12 = "itimptimbre_$i";
					$obj13 = "ituivag_$i";
					$obj14 = "ituivar_$i";
					$obj15 = "ituivaa_$i";
					$obj16 = "itureten_$i";
					$obj17 = "ituimptimbre_$i";


			  ?>
			  <tr id='tr_itfac_<?=$i ?>'>
			    <td class="littletablerow"               ><?=$form->$obj0->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj1->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj2->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj3->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj4->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj13->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj5->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj14->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj6->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj15->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj7->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj8->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj16->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj11->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj17->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj12->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj9->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj10->output  ?> </td>
			    <?php if($form->_status!='show' && $status!="B3") {?>
			    <td class="littletablerow"><a href=# onclick='del_itfac(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php
			    }

			    ?>
			  </tr>
			  <?php } ?>

			  <tr id='__UTPL__'>
			    <td class="littletablefooterb" align='left'  colspan="2"><?=$container_mb?>             </td>
			    <td class="littletablefooterb" align='right'            >Totales                        </td>
			    <td class="littletablefooterb" align='right'            ><?=$form->tsubtotal->output  ?></td>
			    <td class="littletablefooterb" align='right'            ><?=$form->texento->output    ?></td>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->trivag->output     ?></td>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->trivar->output     ?></td>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->trivaa->output     ?></td>
			    <td class="littletablefooterb" align='right'            ><?=$form->treteiva->output   ?></td>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->treten->output     ?></td>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->timptimbre->output ?></td>
			    <td class="littletablefooterb" align='right'            ><?=$form->ttotal->output     ?></td>
			    <td class="littletablefooterb" align='right'            ><?=$form->ttotal2->output    ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			 <tr>
			    <td class="littletablefooterb" align='right' colspan="3">Montos de Orden de Pago        </td>
			    <td class="littletablefooterb" align='right'            ><?=$form->subtotal->output   ?></td>
			    <td class="littletablefooterb" align='right'            ><?=$form->exento->output     ?></td>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->ivag->output       ?></td>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->ivar->output       ?></td>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->ivaa->output       ?></td>
			    <td class="littletablefooterb" align='right'            ><?=$form->reteiva->output    ?></td>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->reten->output      ?></td>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->imptimbre->output  ?></td>
			    <td class="littletablefooterb" align='right'            ><?=$form->total->output      ?></td>
			    <td class="littletablefooterb" align='right'            ><?=$form->total22->output     ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>

			</table>
			</br>
			<table class="table_detalle" style="width:50%;" bgcolor="#E2E0F4" align="center"  >
				<tr>
					<th class="littletableheaderb" colspan="<?=(($form->_status!='show'?5:6))?>">DETALLE PRESUPUESTARIO </th>
				</tr>
				<tr>
					<td class="littletableheaderb"                           >P.IVA?                                                                                </td>
					<td class="littletableheaderb"                           >Est. Admin                                                                            </td>
					<td class="littletableheaderb"                           >*Partida                                                                              </td>
					<td class="littletableheaderb" align='right' id="importe"><? if($form->_status=='show' && $tipo =='Compromiso')echo ""; else echo "Importe"; ?> </td>

					<?php if($form->_status!='show') {?>
					<td class="littletableheaderb">&nbsp;</td>

					<?php } ?>

				</tr>
			<?php
			  for($i=0;$i<$form->max_rel_count['itocompra'];$i++) {
			  	$obj12="itesiva_$i";
				$obj11="itcodigoadm_$i";
		  		$obj0 ="itpartida_$i";
				$obj9 ="itimporte_$i";
				$obj20  ="itdescripcion_$i";

				$obj22 ="itcantidad_$i";
				$obj23 ="itprecio_$i";

				$obj25 ="itislr_$i";
				$obj26 ="itiva_$i";
				$obj27 ="itunidad_$i";

				?>
				<tr id='tr_itocompra_<?=$i ?>'>
					<td class="littletablerow"><?=$form->$obj12->output ?></td>
					<td class="littletablerow"><?=$form->$obj11->output ?></td>
					<td class="littletablerow"><?=$form->$obj0->output ?></td>
					<td class="littletablerow" align='right'><?=$form->$obj9->output ?></td>
					
					<?php
					echo 
					$form->$obj20->output 
					.$form->$obj22->output 
					.$form->$obj23->output 
					.$form->$obj25->output 
					.$form->$obj26->output 
					.$form->$obj27->output 
					?>
					

					<?php if($form->_status!='show' && !($status=='P' || $status=='C' || $status=='T' || $status =='O' || $status=='E')) {?>
					
					<td class="littletablerow"><a href=# onclick='del_itocompra(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
					<?php } ?>
				</tr>
				<?php

				}

				?>
			  <tr id='__UTPL2__'>
			  <td class="littletablefooterb" colspan="2"><? if(!($status=='P' || $status=='C' || $status=='T' || $status =='O' || $status=='E')){ echo $container_pa; } ?> </td>

			    <td class="littletablefooterb" align='right'><?=$form->total2->label  ?></td>
			    <td class="littletablefooterb" align='right'><?=$form->total2->output  ?></td>

			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <?php $tipo = $form->_dataobject->get('tipo');
			  if(!($form->_status=='show' && $tipo =='Compromiso')){
			  ?>

			<?php
			  }
			?>
			</table>
		<?php echo $form_end     ?>
		<td>
	<tr>
<table>
<?php endif; ?>
