<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['itocompra'] AS $ind=>$data)
	$campos[]=$data['field'];
	$campos='<tr id="tr_itocompra_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
	$campos.=' <td class="littletablerow"><a href=# onclick="del_itocompra(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
	$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;



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
	itocompra_cont=<?=$form->max_rel_count['itocompra'] ?>;
	datos    ='';
	estruadm ='';
	sprv ='';
	com=false;

	function tipoo(){
		tipo=$("#tipo").val();
		if(tipo!="Servicio" && tipo!="Compra" && tipo!="Compromiso" && tipo!="Contrato"){
			tipo=$("#tipo2").html();
		}

		if(tipo=="Servicio" || tipo=="Contrato"){
			$("#div_reten").show();
			$("#div_creten").show();
			$("#div_creten2").show();
			$("#div_reten2").show();
			$("#div_reten4").html('M. ISLR');
			$("#div_reten3").html('ISLR');
			$("#div_reten3").show();
			$("#div_reten4").show();
			for(i=0;i<itocompra_cont;i++){
				id=i.toString();
				//$("#islr_"+id).show();
				$("#usaislr_"+id).show();
			}
		}else{
			$("#div_creten2").hide();
			$("#div_creten").hide();
			$("#div_reten2").hide();
		 	$("#div_reten").hide();
		 	$("#div_reten3").hide();
		 	$("#div_reten4").hide();
		 	for(i=0;i<itocompra_cont;i++){
				id=i.toString();
				$("#islr_"+id).hide();
				$("#usaislr_"+id).hide();
			}
		}

		if(tipo=="Compromiso"){
			for(i=0;i<itocompra_cont;i++){
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
			$("#trivaa").hide();
			$("#trivar").hide();
			$("#trivag").hide();
			$("#trtotal2").hide();
			$("#trtotal").hide();
		}else{
			for(i=0;i<itocompra_cont;i++){
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

			$("#trivaa").show();
			$("#trivar").show();
			$("#trivag").show();
			$("#trtotal2").show();
			$("#trtotal").show();
		}
		cal_total();
	}

	$(document).ready(function(){
		console.log("document ready");
		cal_islr();
		cal_total();
		tipoo();
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

	$(function(){
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
				add_itocompra();
				a=itocompra_cont-1;
				$("#partida_"+a).focus();

				com=false;
				return false;
			}else if (com && e.which != 16 && e.which == 17){
				com=false;
			}
			return true;
		});

		tipoo();
		$(".inputnum").numeric(".");
		$("#estadmin").change(function(){
			$("#fondo").val('');
			$("#fondo").html('');
			$.post("<?=site_url('presupuesto/presupuesto/get_tipo')?>",{ codigoadm:$("#estadmin").val() },function(data){$("#fondo").html(data);})
			//auto();
		});

		$("#tipo").change(function (){
				//auto();
			tipoo()
		});
		
		$("#redondear").change(function (){
			cal_total();
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
	}

	function cal_islr(){
		var tipo=$("#tipo").val();
		console.log(tipo);
		//tipo=="Servicio"
		if(true){
			
			breten=0;
			for(i=0;i<itocompra_cont;i++){
				id=i.toString();
				
				if($("#usaislr_"+id).val()=="S"){
					importe= $("#importe_"+id).val();
					breten = breten + importe;

				}else{
					$("#islr_"+id).val('0');
				}
			}
			console.log(breten);
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
			ret=(breten*(tari/100))-(utribuactual*(tari/100)*porcentsustra);
			if(ret <0)ret=0;
			ret=parseFloat(ret);
			$("#reten").val(Math.round(ret*100)/100);
		}
	}

	function cal_total(){
		cal_islr();
		cal_timbre();
		tot=stot=giva=riva=aiva=exce=reteiva=0;
		reteiva_prov=parseFloat($("#reteiva_prov").val());
		redondear =$("#redondear").val();

		if((isNaN(reteiva_prov)) || (reteiva_prov==0) || (reteiva_prov=='') || (reteiva_prov==100))
			reteiva_prov=100;
		else
			reteiva_prov=75;

		for(i=0;i<itocompra_cont;i++){
			id=i.toString();
			valor=parseFloat($("#importe_"+id).val());
			piva =parseFloat($("#iva_"+id).val());
			
			esiva=$("#itesiva_"+id).val();
			//alert(esiva);
			if(!isNaN(valor)){
				if(esiva=='N'){
					stot=stot+valor;
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
		}
		reteiva=Math.round((((giva+riva+aiva)*reteiva_prov)/100)*100)/100;
		tot=stot+giva+riva+aiva;
		if(redondear=="R0"){
			a=stot.indexOf('.');b=stot.substring(0,a+2);
			$("#subtotal").val(parseFloat(b));
			a=tot.indexOf('.');b=tot.substring(0,a+2);
			$("#total2").val(parseFloat(b));
			a=exce.indexOf('.');b=exce.substring(0,a+2);
			$("#exento").val(parseFloat(b));
		}else{
			$("#subtotal").val(Math.round(parseFloat(stot)* 100) /100);
			$("#total2").val(Math.round(parseFloat(tot) * 100) /100);
			$("#exento").val(Math.round(parseFloat(exce) * 100) /100);
		}
		
		$("#ivaa").val(aiva);
		$("#ivar").val(riva);
		$("#ivag").val(giva);
		$("#reteiva").val(reteiva);
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
				$("#importe_"+id).val(Math.round((cana*precio)*1000)/100);
			}
		}
		cal_total();
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

	function rete(){
		cal_total();
	}

	function add_itocompra(){
		var htm = <?=$campos ?>;
		can = itocompra_cont.toString();
		con = (itocompra_cont+1).toString();
		cin = (itocompra_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#cantidad_"+can).numeric(".");
		$("#precio_"+can).numeric(".");

		itocompra_cont=itocompra_cont+1;

		tipoo();

		ante=$("#itfondo_"+cin).attr('selectedIndex');
		$("#itfondo_"+can).attr('selectedIndex',ante);

		ante=$("#itcodigoadm_"+cin).val();
		$("#itcodigoadm_"+can).val(ante);

		ante=$("#partida_"+cin).val();
		$("#partida_"+can).val(ante);

		autop();
		autoe();
		mascara();
	}

	function del_itocompra(id){
		id = id.toString();
		$('#tr_itocompra_'+id).remove();

		tipoo();
	}

	function consulsprv(){
		cod_prov=$("#cod_prov").val();
		if(cod_prov.length==0){
			window.open("<?=site_url('presupuesto/sprv/dataedit/create')?>","PROVEEDORES","height=600,width=800,scrollbars=yes");
		}else{
			window.open("<?=site_url('presupuesto/sprv/dataedit/modify')?>/"+cod_prov,"PROVEEDORES","height=600,width=800,scrollbars=yes");
		}
	}
	</script>
	<?php
	}else{
	?>
	<script language="javascript" type="text/javascript">
		function btn_reverf(i){
			if(!confirm("Esta Seguro que desea Descomprometer la Orden de Compra"))
				return false;
			else
				window.location='<?=site_url('presupuesto/ocompra/reversar')?>/'+i
		}

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
	<tr>
		<td>
			<table width="100%" bgcolor="#F4F4F4"  style="margin:0;width:100%;">
			<?
			if($this->datasis->traevalor('USASIPRES')=='S' && $this->datasis->puede(306)){

			?>
				<tr>
					<td colspan=6 class="littletablerowth"><?=anchor($this->url.'sipresocompra',"Crear Compromiso basado en SIPRES")?></td>
				</tr>
			<?
			}
			?>
			  <tr>
			  <tr>
			    <td colspan=6 class="bigtableheader"><?=$title2 ?> Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>

			  </tr>
			   <?php
			   $USACERTIFICADO=$this->datasis->traevalor("USACERTIFICADO");
			   $USACOMPROMISO =$this->datasis->traevalor("USACOMPROMISO");
			    if($USACERTIFICADO=='S' || $USACOMPROMISO=='S'){?>
			   <tr>
			  	<td class="littletablerowth"><?=($USACERTIFICADO=='S'?$form->certificado->label :'') ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=($USACERTIFICADO=='S'?$form->certificado->output:'') ?>&nbsp;</td>
			    <td class="littletablerowth"><?=($USACOMPROMISO=='S'?$form->compromiso->label :'') ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=($USACOMPROMISO=='S'?$form->compromiso->output:'') ?>&nbsp;</td>
				</tr>
				 <?php }?>
			  <tr>
			  	<td class="littletablerowth">                <?=$form->tipo->label   ?>      *&nbsp;</td>
			    <td class="littletablerow"  ><div id="tipo2"><?=$form->tipo->output  ?></div> &nbsp;</td>
			    <td class="littletablerowth">                <?=$form->fecha->label  ?>       &nbsp;</td>
			    <td class="littletablerow"  >                <?=$form->fecha->output ?>       &nbsp;</td>
				</tr>
			  <tr>
			  	<td class="littletablerowth"><?=$form->uejecutora->label  ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->uejecutora->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->usolicita->label       ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->usolicita->output      ?>&nbsp; </td>
			</tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->cod_prov->label ?>*&nbsp;</td>
			    <td class="littletablerow"  colspan="3"><?=$form->cod_prov->output.'<span class="littletablerowth" >RIF</span>'.$form->rif->output.'<span class="littletablerowth" >Nombre</span>'.$form->nombrep->output ?>&nbsp;<span class="littletablerowth" ><?=$form->reteiva_prov->label  ?></span><?=$form->reteiva_prov->output  ?></td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->observa->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->observa->output?>&nbsp;</td>
			    <td class="littletablerowth"><?=$form->lentrega->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->lentrega->output?>&nbsp;</td>
			  </tr>
			  <?php if($this->datasis->traevalor("USACOMPEFP")=='S'){?>
			  <tr>
			    <td class="littletablerowth"><?=$form->pentret->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->pentret->output.$form->pentrec->output?>&nbsp;</td>
			    <td class="littletablerowth"><?=$form->fpagot->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fpagot->output?>&nbsp;</td>
			  </tr>
			  <?php }?>
			  <tr>
			    <td class="littletablerowth"><?=$form->simptimbre->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->simptimbre->output?>&nbsp;</td>
			    <td class="littletablerowth"><?=$form->status->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->status->output?>&nbsp;</td>
			  </tr>
			  <?
			  $unsolofondo=$this->datasis->traevalor('UNSOLOFONDO','S','Indica si se utiliza una sola fuente de financiamiento');
			  if($unsolofondo=='S'){
			  ?>
			  <tr>
			    <td class="littletablerowth"><?=$form->fondo->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fondo->output?>&nbsp;</td>
			    <? if($this->datasis->traevalor('USAOCOMPRAPROCED')=='S'){
				?>
			    <td class="littletablerowth"><?=$form->proced->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->proced->output?>&nbsp;</td>
			    <?
			    }
			    ?>
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
			  <?php if($this->datasis->traevalor("USAOCOMPRAMODALIDADYFORMAENTREGA","N")=='S'){?>
			  <tr>
			    <td class="littletablerowth"><?=$form->modalidad->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->modalidad->output?>&nbsp;</td>
			    <td class="littletablerowth"><?=$form->formaentrega->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->formaentrega->output?>&nbsp;</td>
			  </tr>
			  <?php }?>
			  <?php if($this->datasis->traevalor("USAOCOMPRACONDICIONES","N")=='S'){?>
			  <tr>
			    <td class="littletablerowth"><?=$form->condiciones->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->condiciones->output?>&nbsp;</td>
			    <td class="littletablerowth">&nbsp;</td>
			    <td class="littletablerow"  >&nbsp;</td>
			  </tr>
			  <?php }?>
			  
			</table>
			<table class="table_detalle" >
				<tr>
					<td class="littletableheaderb"                           >P.IVA?                                                                                </td>
					<?

					if($unsolofondo!='S'){
					?>
					<td class="littletableheaderb"                           >F.Financiamiento                                                                      </td>
					<?
					}
					?>
					<td class="littletableheaderb"                           >Est. Admin                                                                            </td>
					<td class="littletableheaderb"                           >*Partida                                                                              </td>
					<td class="littletableheaderb"                           >Descripci&oacute;n                                                                    </td>
					<?php $tipo = $form->_dataobject->get('tipo'); ?>
					<td class="littletableheaderb" id="unidad"               ><? if($form->_status=='show' && $tipo =='Compromiso')echo ""; else echo "Unidad";   ?></td>
					<td class="littletableheaderb" id="cantidad"             ><? if($form->_status=='show' && $tipo =='Compromiso')echo ""; else echo "Cant"; ?>    </td>
					<td class="littletableheaderb" id="precio"               ><? if($form->_status=='show' && $tipo =='Compromiso')echo ""; else echo "Precio";   ?></td>
					<td class="littletableheaderb"                           ><div id="div_reten3">&nbsp;                                                           </td>
					<td class="littletableheaderb"                           ><div id="div_reten4"><? if( $tipo !='Servicio')echo ""; else echo "ISLR";  ?>         </td>
					<td class="littletableheaderb" id="iva"                  ><? if($form->_status=='show' && $tipo =='Compromiso')echo ""; else echo "Iva";      ?></td>
					<td class="littletableheaderb" align='right' id="importe"><? if($form->_status=='show' && $tipo =='Compromiso')echo ""; else echo "Importe"; ?> </td>
					<?php if($form->_status!='show') {?>
					<td class="littletableheaderb">&nbsp;</td>
					<?php } ?>
				</tr>
		 <?php
		 
			  for($i=0;$i<$form->max_rel_count['itocompra'];$i++) {
			  	$obj12="itesiva_$i";
			  	$obj10="itfondo_$i";
				$obj11="itcodigoadm_$i";
		  		$obj0 ="itpartida_$i";
				$obj2 ="itdescripcion_$i";
				$obj3 ="itunidad_$i";
				$obj4 ="itcantidad_$i";
				$obj5 ="itprecio_$i";
				$obj6 ="itusaislr_$i";
				$obj7 ="itislr_$i";
				$obj8 ="itiva_$i";
				$obj9 ="itimporte_$i";

				?>
				<tr id='tr_itocompra_<?=$i ?>'>
					<td class="littletablerow"><?=$form->$obj12->output ?></td>
					<?
					if($unsolofondo!='S'){
					?>
					<td class="littletablerow"><?=$form->$obj10->output ?></td>
					<?
					}
					?>
					<td class="littletablerow"><?=$form->$obj11->output ?></td>
					<td class="littletablerow"><?=$form->$obj0->output ?></td>
					<td class="littletablerow"><?=$form->$obj2->output ?></td>
					<td class="littletablerow"><?=(($form->_status=='show' && $tipo =='Compromiso')? '':$form->$obj3->output ) ?></td>
					<td class="littletablerow"><?=(($form->_status=='show' && $tipo =='Compromiso')? '':$form->$obj4->output ) ?></td>
					<td class="littletablerow" align='right'><?=(($form->_status=='show' && $tipo =='Compromiso')? '':$form->$obj5->output) ?></td>
					<td class="littletablerow" align='right'><div id="div_reten_<?=$i ?>"                   > <?=($form->_status=='show' && $tipo =="Servicio"?$form->$obj6->output:($form->_status!='show'?$form->$obj6->output:""))  ?></div></td>
					<td class="littletablerow" align='right'><div id="div_reten1_<?=$i ?>" style="width:90px"><?=($form->_status=='show' && $tipo =="Servicio"?$form->$obj7->output:($form->_status!='show'?$form->$obj7->output:""))  ?></div></td>
					<td class="littletablerow" align='right'><?=(($form->_status=='show' && $tipo =='Compromiso')? '':$form->$obj8->output) ?></td>
					<td class="littletablerow" align='right'><?=$form->$obj9->output ?></td>
					<?php if($form->_status!='show') {?>
					<td class="littletablerow"><a href=# onclick='del_itocompra(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
					<?php } ?>
				</tr>
				<?php
				}
				?>

			   <?php $tipo = $form->_dataobject->get('tipo');
			   $status = $form->_dataobject->get('status');
			   ?>
			  <tr id='__UTPL__'>
			  <td class="littletablefooterb" colspan="2">
			  <?php
			  if($status!='P'){
			  	echo $container_bl;
					echo $container_br;
			  }
			  ?>
			  </td>
			    <td class="littletablefooterb" align='right' colspan="<?=(($form->_status=='show' && $tipo =='Compromiso')? 8:8)?>"><?=$form->subtotal->label  ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->subtotal->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>

			  <?php $tipo = $form->_dataobject->get('tipo');
			  if(!($form->_status=='show' && $tipo =='Compromiso')){
			  ?>

			  <tr id="trivag">
			    <td class="littletablerow" align='right' colspan="4"><div id="div_creten2"><?=$form->creten->label.$form->creten->output ?>&nbsp;  </div></td>
			    <td class="littletablerow" align='right' colspan="2"><div id="div_reten"  ><?=$form->reten->label   ?>&nbsp;  </div></td>
			    <td class="littletablerow" align='left'  colspan="2"><div id="div_reten2"> <?=$form->reten->output  ?>          </div></td>
			    <td class="littletablerow" align='right' colspan="2"><?=$form->ivag->label                        ?>             </td>
			    <td class="littletablerow" align='right'>            <?=$form->ivag->output                       ?>             </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>

			  <tr id="trivar">
			    <td class="littletablerow" align='right' colspan="6"><?=$form->exento->label  ?></td>
			    <td class="littletablerow" align='left'  colspan="2"><?=$form->exento->output ?></td>
			    <td class="littletablerow" align='right' colspan="2"><?=$form->ivar->label    ?></td>
			    <td class="littletablerow" align='right'>           <?=$form->ivar->output    ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>

			  <tr id="trivaa">
			  	<td class="littletablerow" align='right' colspan="4"><?=$form->imptimbre->label.$form->imptimbre->output   ?></td>
			    <td class="littletablerow" align='right' colspan="2"><?=$form->reteiva->label   ?></td>
			    <td class="littletablerow" align='left'  colspan="2"><?=$form->reteiva->output  ?></td>
			    <td class="littletablerow" align='right' colspan="2"><?=$form->ivaa->label      ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->ivaa->output     ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>

				<?php
				}
				?>
			  <tr id="trtotal">
				<td class="littletablefooterb" align='right' colspan="10"><?=$form->total2->label   ?></td>
				<td class="littletablefooterb" align='right'>            <?=$form->total2->output  ?></td>
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
