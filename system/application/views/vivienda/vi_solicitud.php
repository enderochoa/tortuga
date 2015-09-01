
<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='create' || $form->_status=='modify' ){
	$container_su=join("&nbsp;", $form->_button_status[$form->_status]["SU"]);
	$container_pa=join("&nbsp;", $form->_button_status[$form->_status]["PA"]);
}else{
	$container_su = '';
	$container_pa = '';
}


if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$campos=$form->template_details('vi_solicitudit');

$scampos  ='<tr id="tr_vi_solicitudit_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['itcedulap'   ]['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['itnombre'    ]['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['itparentesco']['field'].'</td>';

$scampos .= '<td class="littletablerow"><a href=# onclick="del_vi_solicitudit(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos=$form->js_escape($scampos);

$campos2=$form->template_details('vi_solicitudm');
$scampos2  ='<tr id="tr_vi_solicitudm_<#i#>">';
$scampos2 .='<td class="littletablerow" align="left" >'.$campos2['it2codigo'  ]['field'].'</td>';
$scampos2 .='<td class="littletablerow" align="left" >'.$campos2['it2descrip' ]['field'].'</td>';
$scampos2 .='<td class="littletablerow" align="left" >'.$campos2['it2unidad'  ]['field'].'</td>';
$scampos2 .='<td class="littletablerow" align="left" >'.$campos2['it2cantidad']['field'].'</td>';
$scampos2 .= '<td class="littletablerow"><a href=# onclick="del_vi_solicitudm(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos2=$form->js_escape($scampos2);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin;
if($form->_status!='show'){
?>
<style>
  .custom-combobox {
    position: relative;
    display: inline-block;
  }
  .custom-combobox-toggle {
    position: absolute;
    top: 0;
    bottom: 0;
    margin-left: -1px;
    padding: 0;
    /* support: IE7 */
    *height: 1.7em;
    *top: 0.1em;
  }
  .custom-combobox-input {
    margin: 0;
    padding: 0.3em;
  }
   .ui-autocomplete {
    max-height: 300px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
  }
  /* IE 6 doesn't support max-height
   * we use height instead, but this forces the menu to always be this tall
   */
  * html .ui-autocomplete {
    height: 300px;
  }
  </style>
<script language="javascript" type="text/javascript">
	
	
var vi_solicitudit_cont=<?php echo $form->max_rel_count['vi_solicitudit']; ?>;
var vi_solicitudm_cont=<?php echo $form->max_rel_count['vi_solicitudm']; ?>;

$(function(){
	$(".inputnum").numeric(".");
	var cache = {};
	
	$('#cedula').change(function (){
		rcedula=$('#cedula').val();
		$.post("<?=site_url('vivienda/vi_personas/damepersona')?>",{ cedula:rcedula },function(data){
			persona=jQuery.parseJSON(data);
			if(jQuery.isEmptyObject( persona )){
				agregarpersona(rcedula);
			}else{
				nombrecompleto = persona.nombre1+" "+persona.nombre2+" "+persona.apellido1+" "+persona.apellido2;
				$("#p_nombres"     ).val( nombrecompleto);
				$("#p_nombres_val" ).html( nombrecompleto);
			}			
		});			
	});
	
	$('#cedulapropietario').change(function (){
		rcedula=$('#cedulapropietario').val();
		$.post("<?=site_url('vivienda/vi_personas/damepersona')?>",{ cedula:rcedula },function(data){
			persona=jQuery.parseJSON(data);
			if(jQuery.isEmptyObject( persona )){
				agregarpersona(rcedula);
			}else{
				nombrecompleto = persona.nombre1+" "+persona.nombre2+" "+persona.apellido1+" "+persona.apellido2;
				$("#p_nombresprop"     ).val( nombrecompleto);
				$("#p_nombresprop_val" ).html( nombrecompleto);
			}
		});			
	});
	
	function visibleterreno(){
		//terrenopropio=$('#terrenopropio').val();
		//if(terrenopropio=='Si'){
		//	$("#parroquia_terreno").show();
		//	$("#tr_dim_largo").show();
		//}else{
		//	$("#parroquia_terreno").hide();
		//	$("#tr_dim_largo").hide();
		//}
	}
	
	$('#terrenopropio').change(function (){
		visibleterreno();
	});
	visibleterreno();
	
	$('input[id^="cedulap_"]').change(function (){
		rcedula=$(this).val();
		id=this.name.substr(8,100);
		
		$.post("<?=site_url('vivienda/vi_personas/damepersona')?>",{ cedula:rcedula },function(data){
			persona=jQuery.parseJSON(data);
			if(jQuery.isEmptyObject( persona )){
				agregarpersona(rcedula);
			}else{
				nombrecompleto = persona.nombre1+" "+persona.nombre2+" "+persona.apellido1+" "+persona.apellido2;
				$("#nombre_"+id        ).val( nombrecompleto);
			}
		});
	});
	
	function cambiatipo(){
		
		tipo=$("#tipo").val();
		
		$("#situaciontecnica").hide();
		$("#recomendacionsuministros").hide();
		$("#terrenopropiotable").hide();
		$("#informacionvivienda").hide();
		$("#informacionhabitad").hide();
		$("#recomendacionestecnicas").hide();
		$("#recomendacionestecnicast").hide();
		$("#observatecnica").hide();
		
		if(tipo=="Adjudicacion de Vivienda"){
			
		}
		
		if(tipo=="Reubicacion de Vivienda"){
			$("#situaciontecnica").show();
			$("#informacionvivienda").show();
			$("#informacionhabitad").show();
			$("#recomendacionestecnicas").show();
			$("#recomendacionestecnicast").show();
			$("#observatecnica").show();
		}
		
		if(tipo=="Parcelas Aisladas"){
			$("#situaciontecnica").show();
			$("#informacionhabitad").show();	
			$("#terrenopropiotable").show();
			$("#recomendacionestecnicas").show();
			$("#recomendacionestecnicast").show();
			$("#observatecnica").show();
		}
		
		if(tipo=="Mejoramiento de Viviendas"){
			$("#situaciontecnica").show();
			$("#recomendacionsuministros").show();
			$("#informacionvivienda").show();
			$("#informacionhabitad").show();
			$("#recomendacionestecnicas").show();
			$("#recomendacionestecnicast").show();
			$("#observatecnica").show();
		}
		
	}
	$("#tipo").change(function (){
		cambiatipo();
	});
	cambiatipo();
});

function add_vi_solicitudit(){
	var htm = <?php echo $campos; ?>;
	can = vi_solicitudit_cont.toString();
	con = (vi_solicitudit_cont+1).toString();
	vi_solicitudit_cont=vi_solicitudit_cont+1;
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL__").after(htm);
	
}

function del_vi_solicitudit(id){
	id = id.toString();
	$('#tr_vi_solicitudit_'+id).remove();
	
	var arr = $('input[id^="cedulap_"]');
	if(arr.length<=0){
		add_vi_solicitudit();
	}
}

function add_vi_solicitudm(){
	var htm = <?php echo $campos2; ?>;
	can = vi_solicitudm_cont.toString();
	con = (vi_solicitudm_cont+1).toString();
	vi_solicitudm_cont=vi_solicitudm_cont+1;
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL2__").after(htm);
	
}

function del_vi_solicitudm(id){
	console.log("entro");
	id = id.toString();
	$('#tr_vi_solicitudm_'+id).remove();
	
	var arr = $('input[id^="codigo_"]');
	if(arr.length<=0){
		add_vi_solicitudm();
	}
}

function agregarpersona(cedula){
	window.open("<?=site_url('vivienda/vi_personas/dataedit/create')?>/"+cedula,"Persona","height=720,width=1024,scrollbars=yes");	
}
</script>
<?php } ?>
<table align='center' width="95%" cellpadding='0' cellspacing='0'>
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
</table>
<table align='center' width="100%" border='0' cellpadding='0' cellspacing='0'>
	<tr>
		<td>
			<table width="100%"  style="margin:0;width:100%;">
				<tr >
					<td class="littletablerowth" >&nbsp;</td>
					<td class="littletablerow"   >&nbsp;</td>
					<td class="littletablerowth" ><?php echo $form->status->label;             ?></td>
					<td class="littletablerow"   ><?php echo $form->status->output;            ?></td>
				</tr>
				<tr >
					<td class="littletablerowth" ><?php echo $form->tipo->label;              ?></td>
					<td class="littletablerow"   ><?php echo $form->tipo->output;             ?></td>
					<td class="littletablerowth" ><?php echo $form->fecha->label;             ?></td>
					<td class="littletablerow"   ><?php echo $form->fecha->output;            ?></td>
				</tr>
				<tr >
					<td class="littletablerowth"          ><?php echo $form->cedula->label;                              ?></td>
					<td class="littletablerow"            ><?php echo $form->cedula->output.$form->p_nombres->output     ?></td>
					<td class="littletablerowth" ><?php echo $form->fechainspeccion->label;    ?></td>
					<td class="littletablerow"   ><?php echo $form->fechainspeccion->output;   ?></td>
				</tr>
			</table>
		</td>
	</tr>	
	<tr>
		<th  bgcolor="black" colspan="<?=($form->_status=='show'?3:4)?>"><span style="color:white"><STRONG>SITUACION SOCIAL</STRONG></span></th>
	</tr>
	<tr>
		<td>
			<table width="100%"  style="margin:0;width:100%; background:rgb(200,200,220)" >
			<tr >
				<td class="littletablerowth" ><?php echo $form->situacion->label;         ?></td>
				<td class="littletablerow"   >
						<?php echo $form->situacion->output        ?><strong>
						<?php echo $form->cedulapropietario->label ?></strong>
						<?php echo $form->cedulapropietario->output?>
						<?php echo $form->p_nombresprop->output    ?>
					</td>
			</tr>
			<tr>
				<td class="littletablerowth" ><?php echo $form->estadovivienda->label;           ?></td>
				<td class="littletablerow"   ><?php echo $form->estadovivienda->output;          ?></td>
			</tr>
			<tr>
				<td class="littletablerowth" ><?php echo $form->observa->label;           ?></td>
				<td class="littletablerow"   ><?php echo $form->observa->output;          ?></td>
			</tr>
			</table>
		</td>
	</tr>	
	<tr>
		<th class="littletableheaderb" colspan="<?=($form->_status=='show'?3:4)?>"><STRONG>CARGA FAMILIAR</STRONG></th>
	</tr>
	<tr>
		<td>
			<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:120px'>
				<table width='100%' border='0'>
					<tr id='__INPL__'>
						<td class="littletableheaderdet" align='left'><b>C&eacute;dula         </b></td>
						<td class="littletableheaderdet" align='left'><b>Nombre                </b></td>
						<td class="littletableheaderdet" align='left'><b>Parentesco            </b></td>
						<?php if($form->_status!='show') {?>
							<td bgcolor='#7098D0'>&nbsp;</td>
						<?php } ?>
					</tr>
					<?php 
					
					for($i=0;$i<$form->max_rel_count['vi_solicitudit'];$i++) {
						$itcedulap      = "itcedulap_$i";
						$itnombre       = "itnombre_$i";
						$itparentesco   = "itparentesco_$i";
						
					?>
					<tr id='tr_vi_solicitudit_<?php echo $i; ?>'>
						<td class="littletablerow" align="left"><?php echo $form->$itcedulap->output;      ?></td>
						<td class="littletablerow" align="left"><?php echo $form->$itnombre->output;    ?></td>
						<td class="littletablerow" align="left"><?php echo $form->$itparentesco->output;   ?></td>
						<?php if($form->_status!='show') {?>
						<td class="littletablerow">
							<a href='#' onclick='del_vi_solicitudit(<?php echo $i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0)) ?></a>
						</td>
						<?php } ?>
					</tr>
					<?php } ?>
					<tr id='__UTPL__'>
						<td id='cueca'></td>
					</tr>
				</table>
			</div>
			<?php echo $container_pa ?>
		</td>
	</tr>
	<tr>
		<th id="situaciontecnica" bgcolor="black" colspan="<?=($form->_status=='show'?3:4)?>"><span style="color:white"><STRONG>SITUACION TECNICA</STRONG></span></th>
	</tr>
	<tr>
		<td>		
			<table id="informacionvivienda" width="70%"  style="margin:0;width:100%; background:rgb(220,220,250)" >
				<tr>
					<th class="littletableheaderb" colspan="4"><STRONG>INFORMACION VIVIENDA</STRONG></th>
				</tr>
				<tr >
					<td class="littletablerowth" ><?php echo $form->riesgo->label;            ?></td>
					<td class="littletablerow"   ><?php echo $form->riesgo->output;           ?></td>
					<td class="littletablerowth" >&nbsp;                                        </td>
					<td class="littletablerow"   >&nbsp;                                        </td>
				</tr>
				<tr >
					<td class="littletablerowth" ><?php echo $form->habitaciones->label;       ?></td>
					<td class="littletablerow"   ><?php echo $form->habitaciones->output;      ?></td>
					<td class="littletablerowth" >&nbsp;</td>
					<td class="littletablerow"   >&nbsp;</td>
				</tr>
				<tr >
					<td class="littletablerowth" ><?php echo $form->banos->label;              ?></td>
					<td class="littletablerow"   ><?php echo $form->banos->output;             ?></td>
					<td class="littletablerowth" ><?php echo $form->mts2const->label;          ?></td>
					<td class="littletablerow"   ><?php echo $form->mts2const->output;         ?></td>
				</tr>
				<tr >
					<td class="littletablerowth" ><?php echo $form->techo->label;            ?></td>
					<td class="littletablerow"   >
						<?php echo $form->techo->output ?><strong>
						<?php echo $form->techoc->label ?></strong>
						<?php echo $form->techoc->output?>
					</td>
				</tr>
				<tr>
					<td class="littletablerowth" ><?php echo $form->piso->label;            ?></td>
					<td class="littletablerow"   >
						<?php echo $form->piso->output ?><strong>
						<?php echo $form->pisoc->label ?></strong>
						<?php echo $form->pisoc->output?>
					</td>
				</tr>
				<tr >
					<td class="littletablerowth" ><?php echo $form->pared->label;            ?></td>
					<td class="littletablerow"   >
						<?php echo $form->pared->output ?><strong>
						<?php echo $form->paredc->label ?></strong>
						<?php echo $form->paredc->output?>
					</td>
				</tr>
			</table >
			<table id="informacionhabitad" width="100%"  style="margin:0;width:100%; background:rgb(220,220,150)" >
				<tr>
					<th class="littletableheaderb" colspan="4"><STRONG>INFORMACION HABITAT</STRONG></th>
				</tr>
				<tr >
					<td class="littletablerowth" ><?php echo $form->ablancas->label;            ?></td>
					<td class="littletablerow"   >
						<?php echo $form->ablancas->output ?><strong>
						<?php echo $form->ablancasc->label ?></strong>
						<?php echo $form->ablancasc->output?>
					</td>
					<td class="littletablerowth" ><?php echo $form->aseo->label;            ?></td>
					<td class="littletablerow"   ><?php echo $form->aseo->output;           ?></td>
				</tr>
				<tr>
					<td class="littletablerowth" ><?php echo $form->aservidas->label;            ?></td>
					<td class="littletablerow"   >
						<?php echo $form->aservidas->output ?><strong>
						<?php echo $form->aservidasc->label ?></strong>
						<?php echo $form->aservidasc->output?>
					</td>
					<td class="littletablerowth" ><?php echo $form->gas->label;           ?></td>
					<td class="littletablerow"   ><?php echo $form->gas->output;          ?></td>
				</tr>
				<tr >
					<td class="littletablerowth" ><?php echo $form->electrificacion->label;            ?></td>
					<td class="littletablerow"   >
						<?php echo $form->electrificacion->output ?><strong>
						<?php echo $form->electrificacionc->label ?></strong>
						<?php echo $form->electrificacionc->output?>
					</td>
					<td class="littletablerowth" ><?php echo $form->telefonia->label;            ?></td>
					<td class="littletablerow"   ><?php echo $form->telefonia->output;           ?></td>
				</tr>
				<tr >
					<td class="littletablerowth" ><?php echo $form->vialidad->label;            ?></td>
					<td class="littletablerow"   >
						<?php echo $form->vialidad->output ?><strong>
						<?php echo $form->vialidadc->label ?></strong>
						<?php echo $form->vialidadc->output?>
					</td>
					<td class="littletablerowth" ><?php echo $form->transporte->label;           ?></td>
					<td class="littletablerow"   ><?php echo $form->transporte->output;          ?></td>
				</tr>
			</table >
			
			<table id="terrenopropiotable" width="100%"  style="margin:0;width:100%; background:rgb(240,220,220)" >
				<tr>
					<th class="littletableheaderb" colspan="4"><STRONG>INFORMACION TERRENO</STRONG></th>
				</tr>
				<tr >
					<td class="littletablerowth" ><?php echo $form->terrenopropio->label;         ?></td>
					<td class="littletablerow"   >
							<div style="float:left;">
							<?php echo $form->terrenopropio->output        ?><strong>
							</div>
							
							<?php echo $form->id_parroquia_terreno->label ?></strong>
							<?php echo $form->id_parroquia_terreno->output?>
							
						</td>
					<td width="40%">&nbsp;</td>
					
				</tr>
				<tr >
					<td class="littletablerowth" ><?php echo $form->dimfrente->label;         ?></td>
					<td class="littletablerow"   ><?php echo $form->dimfrente->output         ?></td>
					<td class="littletablerowth" ><?php echo $form->dimfondo->label;         ?></td>
					<td class="littletablerow"   ><?php echo $form->dimfondo->output         ?></td>
				</tr>
				<tr >
					<td class="littletablerowth" ><?php echo $form->dimderecho->label;         ?></td>
					<td class="littletablerow"   ><?php echo $form->dimderecho->output         ?></td>
					<td class="littletablerowth" ><?php echo $form->dimizquierdo->label;         ?></td>
					<td class="littletablerow"   ><?php echo $form->dimizquierdo->output         ?></td>
				</tr>
				<tr>
					<td class="littletablerowth" ><?php echo $form->condterreno->label;         ?></td>
					<td class="littletablerow"   ><?php echo $form->condterreno->output         ?><td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table id="observatecnica" width="100%"  style="margin:0;width:100%; background:rgb(220,220,150)" >
	<tr >
		<td class="littletablerowth" ><?php echo $form->obsetecnica->label;        ?></td>
		<td class="littletablerow"   ><?php echo $form->obsetecnica->output;       ?></td>
	</tr>
</table>

<table id="recomendacionestecnicas" align='center' width="100%" cellpadding='0' cellspacing='0'>
	<tr>
		<th bgcolor="black" colspan="<?=($form->_status=='show'?3:4)?>"><span style="color:white"><STRONG>RECOMENDACIONES TECNICAS</STRONG></span></th>
	</tr>
</table>

<table id="recomendacionsuministros" align='center' width="100%" cellpadding='0' cellspacing='0'>
	<tr>
		<th class="littletableheaderb" colspan="<?=($form->_status=='show'?3:4)?>"><STRONG>RECOMENDACIONES DE MATERIALES</STRONG></th>
	</tr>
	<tr>
		<td>
			<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:120px'>
				<table width='100%' border='0'>
					<tr id='__INPL2__'>
						<td class="littletableheaderdet" align='left'><b>C&oacute;digo         </b></td>
						<td class="littletableheaderdet" align='left'><b>Descripci&oacute;n    </b></td>
						<td class="littletableheaderdet" align='left'><b>Unidad                </b></td>
						<td class="littletableheaderdet" align='left'><b>Cantidad              </b></td>
						<?php if($form->_status!='show') {?>
							<td bgcolor='#7098D0'>&nbsp;</td>
						<?php } ?>
					</tr>
					<?php 
					
					for($i=0;$i<$form->max_rel_count['vi_solicitudm'];$i++) {
						$it2codigo     = "it2codigo_$i";
						$it2descrip    = "it2descrip_$i";
						$it2unidad     = "it2unidad_$i";
						$it2cantidad   = "it2cantidad_$i";
						
					?>
					<tr id='tr_vi_solicitudm_<?php echo $i; ?>'>
						<td class="littletablerow" align="left"><?php echo $form->$it2codigo->output;   ?></td>
						<td class="littletablerow" align="left"><?php echo $form->$it2descrip->output;  ?></td>
						<td class="littletablerow" align="left"><?php echo $form->$it2unidad->output;   ?></td>
						<td class="littletablerow" align="left"><?php echo $form->$it2cantidad->output; ?></td>
						<?php if($form->_status!='show') {?>
						<td class="littletablerow">
							<a href='#' onclick='del_vi_solicitudm(<?php echo $i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0)) ?></a>
						</td>
						<?php } ?>
					</tr>
					<?php } ?>
					<tr id='__UTPL2__'>
						<td id='cueca2'></td>
					</tr>
				</table>
			</div>
			<?php echo $container_su ?>
		</td>
	</tr>
</table>

<table id="recomendacionestecnicast" align='center' width="100%" cellpadding='0' cellspacing='0'>
	<tr>
		<td class="littletablerowth" ><?php echo $form->rectecnicas->label;         ?></td>
		<td class="littletablerow"   ><?php echo $form->rectecnicas->output;        ?></td>
	</tr>
</table>

<?php echo $form_end; ?>
<?php endif; ?>
