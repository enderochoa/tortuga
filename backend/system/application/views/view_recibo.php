<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['itrecibo'] AS $ind=>$data)
	$campos[]=$data['field'];
	
$campos='<tr id="tr_itrecibo_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itrecibo(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos=$form->js_escape($campos);


if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin;
if($form->_status!='show'){
	$uri  =$this->datasis->get_uri();
	
	$idt  =$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_vehiculo_contri' AND uri='$uri'");
	$modblinkv=site_url('/buscar/index/'.$idt);
	
	$idt  =$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_inmueble_contri' AND uri='$uri'");
	$modblinki=site_url('/buscar/index/'.$idt);
	
	$idt  =$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_patente_contri' AND uri='$uri'");
	$modblinkp=site_url('/buscar/index/'.$idt);
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
	itrecibo_cont=<?=$form->max_rel_count['itrecibo'] ?>;
	var contribu='';
	
	function creacontribu(){
		rifci = $("#rifci").val();
		window.open("<?=site_url('ingresos/contribu/dataedit/create/')?>"+"/"+rifci,"Contribuyente","height=400,width=800,scrollbars=yes");
	}
	
	
	function modbusdepenv(){
		var contribu =$("#contribu").val();
		l=contribu.length;
		if(l>0)
		var link='<?=$modblinkv ?>'+'/'+contribu;
		else
		var link='<?=$modblinkv ?>'+'/.....';
		
		link =link.replace(/<#contri#>/g,contribu);
		vent=window.open(link,'ventbuscarv','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5');
		vent.focus();
		document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarv\');vent.close();');
	}
	
	function modbusdepeni(){
		var contribu =$("#contribu").val();
		l=contribu.length;
		if(l>0)
		var link='<?=$modblinki ?>'+'/'+contribu;
		else
		var link='<?=$modblinki ?>'+'/.....';
		link =link.replace(/<#contri#>/g,contribu);
		vent=window.open(link,'ventbuscari','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5');
		vent.focus();
		document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscari\');vent.close();');
	}
	
	function modbusdepenp(){
		var contribu =$("#contribu").val();
		l=contribu.length;
		if(l>0)
		var link='<?=$modblinkp ?>'+'/'+contribu;
		else
		var link='<?=$modblinkp ?>'+'/.....';
		link =link.replace(/<#contri#>/g,contribu);
		vent=window.open(link,'ventbuscarp','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5');
		vent.focus();
		document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarp\');vent.close();');
	}
	
	function limpia(){
		arr=$('input[name^="d_descrip"]');
		jQuery.each(arr, function() {
			nom=this.name
			pos=this.name.lastIndexOf('_');
			if(pos>0){
				id  = this.name.substring(pos+1);
				recibo=$("#d_descrip_"+id).val();
				if(recibo==''){
					$('#tr_itrecibo_'+id).remove();
					itrecibo_cont=itrecibo_cont-1;
				}
			}
		});
	}
	
	$(document).ready(function(){
		cal_concepto();
		var marcas='';
		$.post("<?=site_url('ingresos/contribu/autocompleteui')?>",{ partida:"" },function(data){
			sprv=jQuery.parseJSON(data);
			//sprv=data;
			jQuery.each(sprv, function(i, val) {
				val.label=val.nombre;
				
			});
			
			$("#nombre").autocomplete({
				//autoFocus: true,
				delay: 0,
				minLength: 3,
				source: sprv,
				focus: function( event, ui ){
					//$( "#nombre").val( ui.item.nombre );
					$( "#rifci").val( ui.item.rifci );
					$( "#contribu").val( ui.item.codigo );
					$( "#direccion").val( ui.item.direccion );
					$( "#telefono").val( ui.item.telefono );
					$( "#nacionalit").val( ui.item.nacionali );
					return false;
				},
				select: function( event, ui ){
					$( "#nombre").val( ui.item.nombre );
					$( "#rifci").val( ui.item.rifci );
					$( "#contribu").val( ui.item.codigo );
					$( "#direccion").val( ui.item.direccion );
					$( "#telefono").val( ui.item.telefono );
					$( "#nacionalit").val( ui.item.nacionali );
					$("#tipo").focus();
					
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
				val.label=val.rifci;
			});
			
			$("#rifci").autocomplete({
				//autoFocus: true,
				delay: 0,
				minLength: 3,
				source: sprv,
				focus: function( event, ui ){
					$( "#nombre").val( ui.item.nombre );
					//$( "#rifci").val( ui.item.rifci );
					$( "#contribu").val( ui.item.codigo );
					$( "#direccion").val( ui.item.direccion );
					$( "#telefono").val( ui.item.telefono );
					$( "#nacionalit").val( ui.item.nacionali );
					return false;
				},
				select: function( event, ui ){
					$( "#nombre").val( ui.item.nombre );
					$( "#rifci").val( ui.item.rifci );
					$( "#contribu").val( ui.item.codigo );
					$( "#direccion").val( ui.item.direccion );
					$( "#telefono").val( ui.item.telefono );
					$( "#nacionalit").val( ui.item.nacionali );
					$("#tipo").focus();
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a>" + item.rifci + "</a>" )
				.appendTo( ul );
			};
			
		});
		
	});
		
	function cal_patente(){
		$("#local").prop('selectedIndex',$("#p_localt").val());
		$("#negocio").prop('selectedIndex',$("#p_negociot").val());
		$("#claseo").prop('selectedIndex',$("#p_claseot").val());
		$("#p_tipo").prop('selectedIndex',$("#p_tipot").val());
	}
	
	function cal_inmueble(){
		$("#i_tipo_in").prop('selectedIndex',$("#i_tipo_int").val());
		$("#i_sector").prop('selectedIndex',$("#i_sectort").val());
		$("#i_clase").prop('selectedIndex',$("#i_claset").val());
		$("#i_tipo").prop('selectedIndex',$("#i_tipot").val());
	}
	
	function cal_vehiculo(){
		$("#v_clase").prop('selectedIndex',$("#v_claset").val());
	}

	function autosprv(){
	
	}
	
	function cal_concepto(){
				
		limpia();
		concepto=$("#tipo").val();
		$.post("<?=site_url('ingresos/tingresos/grupo')?>",{ conc:concepto },function(data){
			
			if(data=="1"){
				$("#detalle1"     ).hide();
				$("#tbb"          ).hide();
				$("#tabs"         ).hide();
				$("#declara"      ).hide();
				$("#declaracion"  ).hide();
				$("#oper"         ).hide();
				$("#tasam"        ).hide();
				$("#razonsocial"  ).hide();
				$("#rif"          ).hide();
				$("#nomfis"       ).hide();
				$("#efectos"      ).hide();
				$("#efectos2"     ).hide();
				$("#opert"        ).hide();
				$("#tasamt"       ).hide();
				$("#razonsocialt" ).hide();
				$("#rift"         ).hide();
				$("#nomfist"      ).hide();
				$("#efectost"     ).hide();
				$("#efectos2t"    ).hide();
			}
			if(data=="2"){
				$("#detalle1").show();
				$("#tbb").show();
				
				$("#tb").tabs("option", "disabled", [] );
				$("#tb").tabs("option", "selected", 1  );
				$("#tb").tabs("option", "disabled", [0]);
				
				$("#tabs").show();
				$( "#tabs" ).tabs( "option", "disabled", []);
				$( "#tabs" ).tabs( "option", "selected", 2 );
				$( "#tabs" ).tabs( "option", "disabled", [0, 1] );
				$("#declaracion"  ).hide();
				$("#declara"      ).hide();
				$("#tasam"        ).hide();
				$("#oper"         ).hide();
				$("#razonsocial"  ).hide();
				$("#rif"          ).hide();
				$("#nomfis"       ).hide();
				$("#efectos"      ).hide();
				$("#efectos2"     ).hide();
				$("#opert"        ).hide();
				$("#razonsocialt" ).hide();
				$("#rift"         ).hide();
				$("#nomfist"      ).hide();
				$("#efectost"     ).hide();
				$("#efectos2t"    ).hide();
				$("#tasamt"       ).hide();
				
				modbusdepenv();
				
			}
			if(data=="3"){
				$("#detalle1").hide();
				$("#tbb").hide();
				
				$("#tabs").show();
				$( "#tabs" ).tabs( "option", "disabled", []);
				$( "#tabs" ).tabs( "option", "selected", 0      );
				$( "#tabs" ).tabs( "option", "disabled", [2, 1] );
				
				$("#declara"      ).show();
				$("#declaracion"  ).show();
				$("#tasam"        ).hide();
				$("#oper"         ).hide();
				$("#razonsocial"  ).hide();
				$("#rif"          ).hide();
				$("#nomfis"       ).hide();
				$("#efectos"      ).hide();
				$("#efectos2"     ).hide();
				$("#opert"        ).hide();
				$("#razonsocialt" ).hide();
				$("#rift"         ).hide();
				$("#nomfist"      ).hide();
				$("#efectost"     ).hide();
				$("#efectos2t"    ).hide();
				$("#tasamt"       ).hide();
			}
			if(data=="4"){
				$("#detalle1").show();
				$("#tbb").show();
				$("#tb").tabs("option", "disabled", [] );
				$("#tb").tabs("option", "selected", 0  );
				$("#tb").tabs("option", "disabled", [1]);
				
				$("#tabs").show();
				$( "#tabs" ).tabs( "option", "disabled", []);
				$( "#tabs" ).tabs( "option", "selected", 0 );
				$( "#tabs" ).tabs( "option", "disabled", [2] );
				$("#declara"      ).show();
				$("#declaracion"  ).show();
				$("#tasam"        ).hide();
				$("#oper"         ).hide();
				$("#razonsocial"  ).hide();
				$("#rif"          ).hide();
				$("#nomfis"       ).hide();
				$("#efectos"      ).hide();
				$("#efectos2"     ).hide();
				$("#opert"        ).hide();
				$("#razonsocialt" ).hide();
				$("#rift"         ).hide();
				$("#nomfist"      ).hide();
				$("#efectost"     ).hide();
				$("#efectos2t"    ).hide();
				$("#tasamt"       ).hide();
			}
			if(data=="5"){
				$("#detalle1"     ).hide();
				$("#tbb"          ).hide();
				$("#tabs"         ).hide();
				$("#declara"      ).hide();
				$("#declaracion"  ).hide();
				$("#tasam"        ).show();
				$("#oper"         ).hide();
				$("#razonsocial"  ).show();
				$("#rif"          ).hide();
				$("#nomfis"       ).hide();
				$("#efectos"      ).hide();
				$("#efectos2"     ).hide();
				$("#opert"        ).hide();
				$("#razonsocialt" ).show();
				$("#rift"         ).hide();
				$("#nomfist"      ).hide();
				$("#efectost"     ).hide();
				$("#efectos2t"    ).hide();
				$("#tasamt"       ).show();
			}
			if(data=="6"){
				$("#detalle1"     ).hide();
				$("#tbb"          ).hide();
				$("#tabs"         ).hide();
				$("#declara"      ).hide();
				$("#declaracion"  ).hide();
				$("#tasam"        ).show();
				$("#oper"         ).hide();
				$("#razonsocial"  ).show();
				$("#rif"          ).hide();
				$("#nomfis"       ).hide();
				$("#efectos"      ).hide();
				$("#efectos2"     ).hide();
				$("#opert"        ).hide();
				$("#razonsocialt" ).show();
				$("#rift"         ).hide();
				$("#nomfist"      ).hide();
				$("#efectost"     ).hide();
				$("#efectos2t"    ).hide();
				$("#tasamt"       ).show();
			}
			
			if(data=="7"){
				$("#detalle1").hide();
				$("#tbb").hide();
				
				$("#tabs").show();
				$( "#tabs" ).tabs( "option", "disabled", []);
				$( "#tabs" ).tabs( "option", "selected", 0      );
				$( "#tabs" ).tabs( "option", "disabled", [2, 1] );
				
				$("#declara"      ).hide();
				$("#declaracion"  ).hide();
				$("#tasam"        ).show();
				$("#oper"         ).hide();
				$("#razonsocial"  ).hide();
				$("#rif"          ).hide();
				$("#nomfis"       ).hide();
				$("#efectos"      ).hide();
				$("#efectos2"     ).hide();
				$("#opert"        ).hide();
				$("#razonsocialt" ).hide();
				$("#rift"         ).hide();
				$("#nomfist"      ).hide();
				$("#efectost"     ).hide();
				$("#efectos2t"    ).hide();
				$("#tasamt"       ).show();
			}
			
			if(data=="8"){
				
				$("#detalle1"     ).hide();
				$("#tbb"          ).hide();
				$("#tabs"         ).hide();
				$("#declara"      ).hide();
				$("#declaracion"  ).hide();
				$("#tasam"        ).hide();
				$("#oper"         ).show();
				$("#razonsocial"  ).hide();
				$("#rif"          ).show();
				$("#nomfis"       ).show();
				$("#efectos"      ).show();
				$("#efectos2"     ).show();
				$("#opert"        ).show();
				$("#razonsocialt" ).hide();
				$("#rift"         ).show();
				$("#nomfist"      ).show();
				$("#efectost"     ).show();
				$("#efectos2t"    ).show();
				$("#tasamt"       ).hide();

			}
		});
	}
		
	$(function(){
		$("#nacionalit").hide();
		$(".inputnum").numeric(".");
		$( "#tabs" ).tabs();
		
		$( "#tb" ).tabs();
		$( "#tbb" ).dialog({
			width: 650,
			height: 200,
			 autoOpen: false,
			  position: 'top'
		});
		
		$("#deta").click(function() {
			$("#tbb").dialog("open");
		});
		
		$("#traedeuda").click(function() {
			tipo=$("#tipo").val();
			
			if(tipo==8){
				v = $("#vehiculo").val();
				
				if(v.length>0){
					$.post("<?=site_url('ingresos/recibo/damedeuda_trimestre')?>",{ vehiculo:v },function(data){
						deuda=jQuery.parseJSON(data);
						cargadeuda(deuda);
						cal_total();
					});
				}else{
					alert("Primero seleccione un Vehiculo");
				}
			}
		});
	});
	
	function cargadeuda(deuda){
		var htm = <?=$campos ?>;
										
		jQuery.each(deuda, function(i, val) {
			can = itrecibo_cont.toString();
			con = (itrecibo_cont+1).toString();
			
			htm = <?=$campos ?>;
			
			htm = htm.replace(/<#i#>/g,can);
			htm = htm.replace(/<#o#>/g,con);
			
			$("#__UTPL__").before(htm);
			$("#d_monto_"+can).numeric(".");
			
			$("#d_ano_"+can    ).val(val.ano);
			$("#d_tipo_"+can   ).val(val.tipo);
			$("#d_nro_"+can    ).val(val.nro);
			$("#d_descrip_"+can).val(val.descrip);
			$("#d_monto_"+can  ).val(val.monto);
			
			itrecibo_cont=itrecibo_cont+1;
			
		});
	}
	
	function cal_nacionali(){
		nacionalit=$("#nacionalit").val();
		if(nacionalit=="E")
		a=1;
		else
		a=0;

		$("#nacionali").prop('selectedIndex',a);
	}
	
	function cal_ch(i){
		add_itrecibo();
		
		ano=$("#ano").val();
		
		id=itrecibo_cont-1;
		$("#d_ano_"+id).val(ano);
		$("#d_tipo_"+id).val('Mes');
		$("#d_nro_"+id).val(i);
		$("#d_descrip_"+id).val('Mensualidad');
		claseo=$("#p_clase").val();
		
		$.post("<?=site_url('ingresos/claseo/montoaseo')?>",{ a:ano,codigo:claseo },function(data){
			$("#d_monto_"+id).val(data);
			cal_total();
		});
	}
	
	function cal_claseo(){
		declaracion=$("#declaracion").val();
		claseo=$("#p_clase").val();
		$.post("<?=site_url('ingresos/claseo/montodecla')?>",{ decla:declaracion,codigo:claseo },function(data){
			$("#monto").val(data);
			cal_total();
		});
	}
	
	function cal_ch2(i){
		add_itrecibo();
		ano=$("#ano").val();
		id=itrecibo_cont-1;
		$("#d_ano_"+id).val(ano);
		$("#d_tipo_"+id).val('Tri');
		$("#d_nro_"+id).val(i);
		$("#d_descrip_"+id).val('Trimestre');
		v_clase=$("#v_clase").val();
		$.post("<?=site_url('ingresos/clase/montotri')?>",{ codigo:v_clase,a:ano },function(data){
			$("#d_monto_"+id).val(data);
			cal_total();
		});
		
		
	}
	
	function btn_anular(i){
		if(!confirm("Esta Seguro que desea Anular el Ingreso"))
			return false;
		else
			window.location='<?=site_url('ingresos/recibo/anular')?>/'+i
	}
	
	function cal_total(){
		
		arr=$('input[name^="d_tipo_"]');
		t=0;
		jQuery.each(arr,function(){
			nom=this.name
			pos=this.name.lastIndexOf('_');
			if(pos>0){
			
				id= this.name.substring(pos+1);
				m=parseFloat($("#d_monto_"+id).val());
				t=t+m;
			}
			$("#monto").val(Math.round(t*100)/100);
		});
	}
	
	function add_itrecibo(){
		var htm = <?=$campos ?>;
		can = itrecibo_cont.toString();
		con = (itrecibo_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#monto_"+can).numeric(".");
		itrecibo_cont=itrecibo_cont+1;
	}
	
	function del_itrecibo(id){
		id = id.toString();
		$('#tr_itrecibo_'+id).remove();
	}
	</script>
	<?php
	}else{
	?>
	<script language="javascript" type="text/javascript">
		
		$(document).ready(function(){
			$("#detalle1").show();
			$("#tabs" ).tabs();
			$("#tabs").show();
		});
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
			</table>
			<table width="100%"  style="margin:0;width:100%; background:rgb(220,220,250)" >
				<tr>
					<td class="littletablerow"  colspan="4"><strong>DATOS DEL CONTRIBUYENTE</strong></td>
				</tr>
				<tr>
					<td class="littletablerow"              ><?='<span class="littletablerowth" >'.$form->contribu->label.'*</span>' ?>&nbsp;</td>
					<td class="littletablerow"  colspan="3"><?=$form->contribu->output.'<span class="littletablerowth" >RIF/CI*</span>'.$form->rifci->output.'<span class="littletablerowth" >Nombre*</span>'.$form->nombre->output ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletablerow"             ><?='<span class="littletablerowth" >'.$form->nacionali->label.'</span>'  ?>&nbsp;</td>
					<td class="littletablerow"  colspan="3"><?=$form->nacionali->output.$form->nacionalit->output  ?><?='<span class="littletablerowth" >Direcci&oacute;n</span>'.$form->direccion->output.'<span class="littletablerowth" >Tel&eacute;fono</span>'.$form->telefono->output ?>&nbsp;</td>
				</tr>
			</table>
			
			<table width="100%"  style="margin:0;width:100%;">
				<tr>
					<td class="littletablerowth"           ><?=$form->fecha->label  ?>*&nbsp;</td>
					<td class="littletablerow"  colspan="3"><?=$form->fecha->output ?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth"           ><?=$form->tipo->label  ?>*&nbsp;</td>
					<td class="littletablerow"  colspan="3"><?=$form->tipo->output ?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth"           ><?=$form->observa->label  ?>&nbsp;</td>
					<td class="littletablerow"  colspan="3"><?=$form->observa->output ?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth" >                   <?=$form->monto->label  ?>*&nbsp;            </td>
					<td class="littletablerow"   >                   <?=$form->monto->output ?>&nbsp;             </td>
					<td class="littletablerowth" ><span id="declara"><?=$form->declaracion->label  ?></span>&nbsp;</td>
					<td class="littletablerow"   >                   <?=$form->declaracion->output ?>&nbsp;       </td>
				</tr>
				<tr>
					<td class="littletablerowth" ><span id="opert"><?=$form->oper->label  ?></span>&nbsp;</td>
					<td class="littletablerow"   >                 <?=$form->oper->output ?>&nbsp;       </td>
				</tr>
				
				<?php if($this->datasis->traevalor("RECIBOUSATASAMYRAZON")=='S'){?>
				<tr>
					<td class="littletablerowth" ><span id="razonsocialt"><?=$form->razonsocial->label ?></span>&nbsp;</td>
					<td class="littletablerow"   >                        <?=$form->razonsocial->output?>&nbsp;       </td>
					<td class="littletablerowth" ><span id="tasamt">      <?=$form->tasam->label       ?></span>&nbsp;</td>
					<td class="littletablerow"   >                        <?=$form->tasam->output      ?>&nbsp;       </td>
				</tr>
				<?php }?>
				
				<?php if($this->datasis->traevalor("RECIBOUSARIFNOMFIS")=='S'){?>
				<tr>
					<td class="littletablerowth" ><span id="rift"><?=$form->rif->label      ?></span>&nbsp;</td>
					<td class="littletablerow"   >                <?=$form->rif->output     ?>&nbsp;       </td>
					<td class="littletablerowth" ><span id="nomfist"><?=$form->nomfis->label?></span>&nbsp;</td>
					<td class="littletablerow"   >                   <?=$form->nomfis->output  ?>&nbsp;</td>
				</tr>
				<?php }?>
				
				<?php if($this->datasis->traevalor("RECIBOUSAEFECTOS")=='S'){?>
				<tr>
					<td class="littletablerowth"             ><span id="efectos2t"><?=$form->efectos->label      ?></span>&nbsp;</td>
					<td class="littletablerow"   colspan="3" >                     <?=$form->efectos->output     ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletablerowth"             ><span id="efectos2t"><?=$form->efectos2->label      ?></span>&nbsp;</td>
					<td class="littletablerow"   colspan="3" >                     <?=$form->efectos2->output     ?>&nbsp;</td>
				</tr>
				<?php }?>
			</table>
			</br>
			
			</table >
			<table style="display:none;" width="70%" align="center" id="detalle1">
			<?php if($form->_status!='show') {?>
				<tr>
					<td>
						<div id="deta" style="font-size:10pt;color:red;cursor:pointer;alignment-adjust:central"><strong>Haz Clik Aqui Seleccionar Meses o trimestres</strong>
						</div>
						
						<div id="traedeuda" style="font-size:10pt;color:blue;cursor:pointer;alignment-adjust:central"><strong>Haz Clik Aqui para Cargar la Deuda</strong>
						</div>
					</td>
				</tr>
			<?php } ?>
			
			<tr><td>
			<table   border="0" cellpadding="0" cellspacing="0" class="table_detalle">
				<tr>
					<th class="littletableheaderb"              >A&ntilde;o        </th>
					<th class="littletableheaderb"              >                  </th>
					<th class="littletableheaderb"              >                  </th>
					<th class="littletableheaderb"              >Descripci&oacute;n</td> 
					<th class="littletableheaderb" align='right'>Monto             </th>
					<?php if($form->_status!='show') {?>
					<th class="littletableheaderb">&nbsp;</td>
					<?php } ?>
				</tr>
				<?php 
				for($i=0;$i<$form->max_rel_count['itrecibo'];$i++) {
					$obj0="d_ano_$i";
					$obj1="d_tipo_$i";
					$obj2="d_nro_$i";
					$obj3="d_descrip_$i";
					$obj4="d_monto_$i";
				?>
				 <tr id='tr_itrecibo_<?=$i ?>'>
					<td class="littletablerow"              ><?=$form->$obj0->output ?></td>
					<td class="littletablerow"              ><?=$form->$obj1->output ?></td>
					<td class="littletablerow"              ><?=$form->$obj2->output ?></td>
					<td class="littletablerow"              ><?=$form->$obj3->output ?></td>
					<td class="littletablerow" align='right'><?=$form->$obj4->output ?></td>
					<?php if($form->_status!='show') {?>
					<td class="littletablerow"><a href=# onclick='del_itrecibo(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
					<?php } ?>
				</tr>
				
				<?php
				} ?>
				<tr id='__UTPL__'>
				<td class="littletablefooterb" align='right' colspan="5">&nbsp;</td>
				<?php if($form->_status!='show') {?>
				<td class="littletablefooterb">&nbsp;</td>
				<?php } ?>
				</tr>
			</table>
			</td>
			</tr>
			</table>
			<br>
			<div style="display:none;" id="tbb">
				<? echo $form->ano->label.$form->ano->output.br(); ?>
				<div id="tb" style="background:rgb(240,200,200); ">
					<ul style="height:20px">
						<li style="height:19px"><a href="#tb1" style="size:10px; font-size:12px; font-weight:bold" >Meses</a></li>
						<li style="height:19px"><a href="#tb2" style="size:10px; font-size:12px; font-weight:bold">Trimestres</a></li>
					</ul>
					<div id="tb1">
						<table>
							<tr align="LEFT">
								
								<?
								for($i=1;$i<=6;$i++){
									$campo='m_'.str_pad($i,2,'0',STR_PAD_LEFT);
									echo '<td align="LEFT">'.$form->$campo->output.$form->$campo->label.'</td>';
								}
								?>
								
							</tr>
							<tr align="LEFT">
								
								<?
								for($i=7;$i<=12;$i++){
									$campo='m_'.str_pad($i,2,'0',STR_PAD_LEFT);
									echo '<td align="LEFT">'.$form->$campo->output.$form->$campo->label.'</td>';
								}
								?>
								
							</tr>
						</table>
					</div>
					<div id="tb2">
						<table>
							<tr align="LEFT">
								<?
								for($i=1;$i<=2;$i++){
									$campo='t_'.str_pad($i,2,'0',STR_PAD_LEFT);
									echo '<td align="LEFT">'.$form->$campo->output.$form->$campo->label.'</td>';
								}
								?>
							</tr>
							<tr align="LEFT">
								<?
								for($i=3;$i<=4;$i++){
									$campo='t_'.str_pad($i,2,'0',STR_PAD_LEFT);
									echo '<td align="LEFT">'.$form->$campo->output.$form->$campo->label.'</td>';
								}
								?>
							</tr>
						</table>
					</div>
				</div>
			</div>
			</br>
			<div id="tabs" style="display:none;">
				<ul style="height:20px">
					<li style="height:19px"><a href="#tabs1" style="size:10px; font-size:12px; font-weight:bold" >Patente</a></li>
					<li style="height:19px"><a href="#tabs2" style="size:10px; font-size:12px; font-weight:bold">Inmueble</a></li>
					<li style="height:19px"><a href="#tabs3" style="size:10px; font-size:12px; font-weight:bold">Vehiculo</a></li>
				</ul>
				<div id="tabs1">
					<table width="100%"  style="margin:0;width:100%;">
						<tr>
							<td class="littletablerowth" ><?=$form->patente->label ?>&nbsp;</td>
							<td class="littletablerow"   ><?=$form->patente->output ?>&nbsp;</td>
							<td class="littletablerowth" ><?=$form->p_tarjeta->label ?>&nbsp;</td>
							<td class="littletablerow"   ><?=$form->p_tarjeta->output ?>&nbsp;</td>
							<td class="littletablerowth" ><?=$form->p_licencia->label ?>&nbsp;</td>
							<td class="littletablerow"   ><?=$form->p_licencia->output.$form->p_nro->label.$form->p_nro->output ?>&nbsp;</td>							
						</tr>
						<tr>
							<td class="littletablerowth"            ><?=$form->p_razon->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="5"><?=$form->p_razon->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"            ><?=$form->p_dir_neg->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="5"><?=$form->p_dir_neg->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"            ><?=$form->p_local->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="3"><?=$form->p_local->output ?>&nbsp;</td>
							<td class="littletablerowth"            ><?=$form->p_fecha_es->label ?>&nbsp;</td>
							<td class="littletablerow"              ><?=$form->p_fecha_es->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth" ><?=$form->p_negocio->label ?>&nbsp;</td>
							<td class="littletablerow"   ><?=$form->p_negocio->output ?>&nbsp;</td>
							<td class="littletablerowth" ><?=$form->p_clase->label ?>&nbsp;</td>
							<td class="littletablerow"   ><?=$form->p_clase->output ?>&nbsp;</td>
							<td class="littletablerowth" ><?=$form->p_tipo->label ?>&nbsp;</td>
							<td class="littletablerow"   ><?=$form->p_tipo->output ?>&nbsp;</td>							
						</tr>
						<tr>
							<td class="littletablerowth" ><?=$form->p_oficio->label ?>&nbsp;</td>
							<td class="littletablerow"   ><?=$form->p_oficio->output ?>&nbsp;</td>
							<td class="littletablerowth" ><?=$form->p_catastro->label ?>&nbsp;</td>
							<td class="littletablerow"   ><?=$form->p_catastro->output ?>&nbsp;</td>
							<td class="littletablerowth" ><?=$form->p_publicidad->label ?>&nbsp;</td>
							<td class="littletablerow"   ><?=$form->p_publicidad->output ?>&nbsp;</td>							
						</tr>
						<tr>
							<td class="littletablerowth"            ><?=$form->p_observa->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="5"><?=$form->p_observa->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"            ><?=$form->p_capital->label ?>&nbsp;</td>
							<td class="littletablerow"              ><?=$form->p_capital->output ?>&nbsp;</td>
							<td class="littletablerowth"            ><?=$form->p_repre->label ?>&nbsp;</td>
							<td class="littletablerow"              ><?=$form->p_repre->output ?>&nbsp;</td>
							<td class="littletablerowth"            ><?=$form->p_repreced->label ?>&nbsp;</td>
							<td class="littletablerow"              ><?=$form->p_repreced->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"            ><?=$form->p_expclasi->label ?>&nbsp;</td>
							<td class="littletablerow"              ><?=$form->p_expclasi->output ?>&nbsp;</td>
							<td class="littletablerowth"            ><?=$form->p_exphor->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="3"><?=$form->p_exphor->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"            ><?=$form->p_fexpedicion->label   ?>&nbsp;</td>
							<td class="littletablerow"              ><?=$form->p_fexpedicion->output  ?>&nbsp;</td>
							<td class="littletablerowth"            ><?=$form->p_fvencimiento->label  ?>&nbsp;</td>
							<td class="littletablerow"   colspan="3"><?=$form->p_fvencimiento->output ?>&nbsp;</td>
						</tr>
						
					</table>
				</div>
				<div id="tabs2">
					<table width="100%"  style="margin:0;width:100%;">
						<tr>
							<td class="littletablerowth"           ><?=$form->inmueble->label ?>&nbsp;</td>
							<td class="littletablerow"             ><?=$form->inmueble->output ?>&nbsp;</td>
							<td class="littletablerowth"           ><?=$form->i_ctainos->label ?>&nbsp;</td>
							<td class="littletablerow" colspan="3" ><?=$form->i_ctainos->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"           ><?=$form->i_sector->label ?>&nbsp;</td>
							<td class="littletablerow"  colspan="5"><?=$form->i_sector->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"            ><?=$form->i_direccion->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="5"><?=$form->i_direccion->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"                ><?=$form->i_no_hab->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="5"    ><?=$form->i_no_hab->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"            ><?=$form->i_tipo_in->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="2"><?=$form->i_tipo_in->output ?>&nbsp;</td>
							<td class="littletablerowth"            ><?=$form->i_clase->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="2"><?=$form->i_clase->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth" ><?=$form->i_tipo->label ?>&nbsp;</td>
							<td class="littletablerow"   ><?=$form->i_tipo->output ?>&nbsp;</td>
							<td class="littletablerowth" ><?=$form->i_monto->label ?>&nbsp;</td>
							<td class="littletablerow"   ><?=$form->i_monto->output ?>&nbsp;</td>
							<td class="littletablerowth" ><?=$form->i_no_predio->label    ?>&nbsp;</td>
							<td class="littletablerow"   ><?=$form->i_no_predio->output   ?>&nbsp;</td>							
						</tr>
					</table>
				</div>
				<div id="tabs3">
					<table width="100%"  style="margin:0;width:100%;">
						<tr>
							<td class="littletablerowth"             ><?=$form->vehiculo->label ?>&nbsp;</td>
							<td class="littletablerow"               ><?=$form->vehiculo->output ?>&nbsp;</td>
							<td class="littletablerowth"             ><?=$form->v_placa_act->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="3" ><?=$form->v_placa_act->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"             ><?=$form->v_clase->label ?>&nbsp;</td>
							<td class="littletablerow"               ><?=$form->v_clase->output ?>&nbsp;</td>
							<td class="littletablerowth"             ><?=$form->v_tipo->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="3" ><?=$form->v_tipo->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"             ><?=$form->v_marca->label ?>&nbsp;</td>
							<td class="littletablerow"               ><?=$form->v_marca->output ?>&nbsp;</td>
							<td class="littletablerowth"             ><?=$form->v_modelo->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="3" ><?=$form->v_modelo->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"            ><?=$form->v_ano->label ?>&nbsp;</td>
							<td class="littletablerow"              ><?=$form->v_ano->output ?>&nbsp;</td>
							<td class="littletablerowth"            ><?=$form->v_capaci->label ?>&nbsp;</td>
							<td class="littletablerow"              ><?=$form->v_capaci->output ?>&nbsp;</td>
							<td class="littletablerowth"            ><?=$form->v_peso->label ?>&nbsp;</td>
							<td class="littletablerow"              ><?=$form->v_peso->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"            ><?=$form->v_color->label ?>&nbsp;</td>
							<td class="littletablerow"              ><?=$form->v_color->output ?>&nbsp;</td>
							<td class="littletablerowth"            ><?=$form->v_placa_ant->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="3"><?=$form->v_placa_ant->output ?>&nbsp;</td>
						</tr>
						<tr>
							<td class="littletablerowth"            ><?=$form->v_serial_m->label ?>&nbsp;</td>
							<td class="littletablerow"              ><?=$form->v_serial_m->output ?>&nbsp;</td>
							<td class="littletablerowth"            ><?=$form->v_serial_c->label ?>&nbsp;</td>
							<td class="littletablerow"   colspan="3"><?=$form->v_serial_c->output ?>&nbsp;</td>
						</tr>
					</table>
				</div>
			</div>
			
			<br />
			<?
			foreach($temp as $k=>$v)
			echo $form->$v->output;
			?>
			
			<?php echo $form_end     ?>
			<?php echo $container_bl ?>
			<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>
