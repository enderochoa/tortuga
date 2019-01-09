
<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:

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
		
		
		
		subtotal =valido_float("subtotal");
		var data = <?=$rete ?>;
		a=ind.substring(0,1);
		
		

		base1=eval('data._'+ind+'.base1');
		tari1=eval('data._'+ind+'.tari1');
		pama1=eval('data._'+ind+'.pama1');
		
		if(a=='1')ret=subtotal*base1*tari1/10000   ;
		else ret=(subtotal-pama1)*base1*tari1/10000;
		
		$("#reten").val(redondear(ret));
		
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
		cal_islr();
		tot=stot=giva=riva=aiva=exce=reteiva=iva2=total2=0;
		
		ivag        =valido_float("ivag");
		ivar        =valido_float("ivar");
		ivaa        =valido_float("ivaa");
		exento      =valido_float("exento");
		//reteiva     =valido_float("reteiva");
		reten       =valido_float("reten");
		imptimbre   =valido_float("imptimbre");
		impmunicipal=valido_float("impmunicipal");
		otrasrete   =valido_float("otrasrete");
		reteiva_prov=parseFloat($("#reteiva_prov").val());
		
		iva = ivag+ivar+ivaa;
		
		if((isNaN(reteiva_prov)) || (reteiva_prov==0) || (reteiva_prov=='') || (reteiva_prov==100)){
			$("#reteiva_prov").val('100');
			reteiva_prov=100;
		}else{
			$("#reteiva_prov").val('75');
			reteiva_prov=75;
		}
		
		reteiva=redondear(iva*reteiva_prov/100);

		arr=$('input[name^="precio_"]');
		jQuery.each(arr, function() {
			nom=this.name
			pos=this.name.lastIndexOf('_');
			if(pos>0){
				id=this.name.substring(pos+1);
				valor  =valido_float("precio_"+id);
				total2+=valor;
			}
		});

		subtotal=stot-ivaa-ivag-ivar;
		tot=stot;

		to=tot-reten-otrasrete-reteiva-imptimbre-impmunicipal;
		
		total=total2-reten-otrasrete-reteiva-imptimbre-impmunicipal;
		
		$("#reteiva").val(reteiva);
		$("#total2").val(redondear(total2));
		$("#total").val(redondear(total));
		//$("#subtotal").val(Math.round(parseFloat(subtotal)* 100) /100);
		
	}
	
	function cal(){
		creten = $("#creten").val();
		if(creten != '')
			cal_islr();

		cal_timbre();
		cal_municipal();
	}

	function cal_importe(i){
		cal_total();
	}

	function cal_importep(i){
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
				<td class="littletablerowth"><?=$form->fecha->label ?>&nbsp;</td>
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
				<?php $tipo = $form->_dataobject->get('tipo');
					if(!(($tipo =='N' || $tipo=='T'))){
					?>
				  <tr id="tr_factura1">
					<td class="littletablerowth"><?=$form->multiple->label ?>    </td>
					<td class="littletablerow"  ><?=$form->multiple->output?>    </td>
					<td class="littletablerowth"><?=$form->fechafac->label ?>    </td>
					<td class="littletablerow"  ><?=$form->fechafac->output?>    </td>
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
			  ?>
			  <tr id='tr_itodirect_<?=$i ?>'>
				<td class="littletablerow"               ><?=$form->$obj12->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj11->output   ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj0->output   ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj13->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=($form->_status=='show' && ($tipo =='T' || $tipo =='N'))? '':$form->$obj5->output  ?> </td>
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
			   <tr id="trivag">
			    <td class="littletablerow" align='right' colspan="2"><div id="div_creten">  <?=$form->creten->label  ?> &nbsp; </div></td>
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
			  	<td class="littletablerow" align='right' colspan="4" ><?//=$form->imptimbre->label   ?></td>
			    <td class="littletablerow" align='right'             ><?//=$form->imptimbre->output  ?></td>
			    <td class="littletablerow" align='right' colspan="1" ><?=$form->imptimbre->label  ?></td>
			    <td class="littletablerow" align='right'>             <?=$form->imptimbre->output ?></td>
			    <td class="littletablerow" align='right' colspan="2">                                  &nbsp;                        </td>
			    <td class="littletablerow" align='right' colspan="1"><?=$form->ivar->label    ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->ivar->output   ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>

			  <tr id="trivaa">
			  	<td class="littletablerow" align='right' colspan="4"><?=$form->impmunicipal->label   ?></td>
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
			  <tr id="trivaa">
			  	<td class="littletablerow" align='right' colspan="4">&nbsp;</td>
			    <td class="littletablerow" align='right'>            &nbsp;</td>
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
<?php endif;?>
