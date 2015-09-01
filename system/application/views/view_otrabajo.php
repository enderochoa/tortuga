<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['itotrabajo'] AS $ind=>$data)
	$campos[]=$data['field'];
	$campos='<tr id="tr_itotrabajo_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
	$campos.=' <td class="littletablerow"><a href=# onclick="del_itotrabajo(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
	$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin;
if($form->_status!='show'){
	$uri  =$this->datasis->get_uri();
	$idt  =$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_presaldo' AND uri='$uri'");
	$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>

	<script language="javascript" type="text/javascript">
	itotrabajo_cont=<?=$form->max_rel_count['itotrabajo'] ?>;
	datos    ='';
	com=false;

	$(document).ready(function(){
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
	});

	var data;

	$(function(){
		$(document).keydown(function(e){
					//alert(e.which);
			if (18 == e.which) {
				com=true;
				//c = String.fromCharCode(e.which);
				return false;
			}
			if (com && (e.which == 61 || e.which == 107)) {
				add_itotrabajo();
				a=itotrabajo_cont-1;
				$("#partida_"+a).focus();

				com=false;
				return false;
			}else if (com && e.which != 16 && e.which == 17){
				com=false;
			}
			return true;
		});

		$(".inputnum").numeric(".");		
		$("#redondear").change(function (){
			cal_total();
		});

	});

	function cal_total(){
		tot=stot=giva=riva=aiva=exce=reteiva=0;
		redondear =$("#redondear").val();

		for(i=0;i<itotrabajo_cont;i++){
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

	function add_itotrabajo(){
		var htm = <?=$campos ?>;
		can = itotrabajo_cont.toString();
		con = (itotrabajo_cont+1).toString();
		cin = (itotrabajo_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#cantidad_"+can).numeric(".");
		$("#precio_"+can).numeric(".");

		itotrabajo_cont=itotrabajo_cont+1;
	}

	function del_itotrabajo(id){
		id = id.toString();
		$('#tr_itotrabajo_'+id).remove();
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
		function btn_noterminada(i){
			if(!confirm("Esta Seguro que desea Marcar Como NO Terminada la Orden de Trabajo"))
				return false;
			else
				window.location='<?=site_url('presupuesto/otrabajo/noterminada')?>/'+i
		}
		
		function btn_anular(i){
			if(!confirm("Esta Seguro que desea Anular la Orden de Trabajo"))
				return false;
			else
				window.location='<?=site_url('presupuesto/otrabajo/anular')?>/'+i
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
		
			  <tr>
			  <tr>
			    <td colspan=6 class="bigtableheader"><?=$title2 ?> Nro. <?php  echo $form->numero->output ?></td>
			  </tr>
			  <tr>
			  	<td class="littletablerowth">&nbsp;</td>
			    <td class="littletablerow"  >&nbsp;</td>
			    <td class="littletablerowth"><?=$form->fecha->label  ?>       &nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fecha->output ?>       &nbsp;</td>
				</tr>
			  <tr>
			  	<td class="littletablerowth"><?=$form->proced->label     ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->proced->output    ?>&nbsp;</td>
			    <td class="littletablerowth"><?=$form->usolicita->label  ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->usolicita->output ?>&nbsp;</td>
			</tr>
			  <tr>
			    <td class="littletablerowth"           ><?=$form->cod_prov->label ?>*&nbsp;</td>
			    <td class="littletablerow"  colspan="3"><?=$form->cod_prov->output.'<span class="littletablerowth" >RIF</span>'.$form->rif->output.'<span class="littletablerowth" >Nombre</span>'.$form->nombrep->output ?>&nbsp;<span class="littletablerowth" ><?=$form->reteiva_prov->label  ?></span><?=$form->reteiva_prov->output  ?></td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->lentrega->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->lentrega->output?>&nbsp;</td>
			    <td class="littletablerowth"><?=$form->condiciones->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->condiciones->output?>&nbsp;</td>
			  </tr>
			  <?php if($this->datasis->traevalor("USACOMPEFP")=='S'){?>
			  <tr>
			    <td class="littletablerowth"><?=$form->pentret->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->pentret->output.$form->pentrec->output?>&nbsp;</td>
			    <td class="littletablerowth"><?=$form->redondear->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->redondear->output?>&nbsp;</td>
			  </tr>
			  <?php }?>
			  
			  
			</table>
			<table class="table_detalle" >
				<tr>
					<td class="littletableheaderb"                           >Descripci&oacute;n   </td>
					<td class="littletableheaderb" id="unidad"               >Unidad               </td>
					<td class="littletableheaderb" id="cantidad"             >Cant                 </td>
					<td class="littletableheaderb" id="precio"               >Precio               </td>
					<td class="littletableheaderb" id="iva"                  >Iva                  </td>
					<td class="littletableheaderb" align='right' id="importe">Importe              </td>
					<?php if($form->_status!='show') {?>
					<td class="littletableheaderb">&nbsp;</td>
					<?php } ?>
				</tr>
		 <?php
			  for($i=0;$i<$form->max_rel_count['itotrabajo'];$i++) {
				$obj2 ="itdescripcion_$i";
				$obj3 ="itunidad_$i";
				$obj4 ="itcantidad_$i";
				$obj5 ="itprecio_$i";
				$obj8 ="itiva_$i";
				$obj9 ="itimporte_$i";
				?>
				<tr id='tr_itotrabajo_<?=$i ?>'>
					<td class="littletablerow"><?=$form->$obj2->output  ?></td>
					<td class="littletablerow"><?=$form->$obj3->output  ?></td>
					<td class="littletablerow"><?=$form->$obj4->output  ?></td>
					<td class="littletablerow" align='right'><?=$form->$obj5->output ?></td>
					<td class="littletablerow" align='right'><?=$form->$obj8->output ?></td>
					<td class="littletablerow" align='right'><?=$form->$obj9->output ?></td>
					<?php if($form->_status!='show') {?>
					<td class="littletablerow"><a href="#" onclick='del_itotrabajo(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
					<?php } ?>
				</tr>
				<?php
				}
				?>
			   <?php
			   $status = $form->_dataobject->get('status');
			   ?>
			  <tr id='__UTPL__'>
			  <td class="littletablefooterb" colspan="2">
			  <?php
			  
				echo $container_bl;
				echo $container_br;
			  
			  ?>
			  </td>
			    <td class="littletablefooterb" align='right' colspan="3"><?=$form->subtotal->label  ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->subtotal->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>

			  <?php $tipo = $form->_dataobject->get('tipo');
			  if(!($form->_status=='show' && $tipo =='Compromiso')){
			  ?>

			  <tr id="trivag">
			    <td class="littletablerow" align='right' colspan="3"></td>
			    <td class="littletablerow" align='right' colspan="2"><?=$form->ivag->label   ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->ivag->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>

			  <tr id="trivar">
			    <td class="littletablerow" align='right' colspan="1"><?=$form->exento->label  ?></td>
			    <td class="littletablerow" align='left'  colspan="2"><?=$form->exento->output ?></td>
			    <td class="littletablerow" align='right' colspan="2"><?=$form->ivar->label    ?></td>
			    <td class="littletablerow" align='right'>           <?=$form->ivar->output    ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>

			  <tr id="trivaa">
			  	<td class="littletablerow" align='right' colspan="1">&nbsp;</td>
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
				<td class="littletablefooterb" align='right' colspan="5"><?=$form->total2->label   ?></td>
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
