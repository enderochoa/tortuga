<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin;
if(true){
	$uri  =$this->datasis->get_uri();
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
	var contribu='';
	$(document).ready(function(){
		$.post("<?=site_url('ingresos/contribu/autocompleteui')?>",{ partida:"" },function(data){
			sprv=jQuery.parseJSON(data);
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
			
			jQuery.each(sprv, function(i, val) {
				val.label=val.codigo;
			});
			
			$("#contibu").autocomplete({
				//autoFocus: true,
				delay: 0,
				minLength: 3,
				source: sprv,
				focus: function( event, ui ){
					$( "#nombre").val( ui.item.nombre );
					$( "#rifci").val( ui.item.rifci );
					//$( "#contribu").val( ui.item.codigo );
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
				.append( "<a>" + item.codigo + "</a>" )
				.appendTo( ul );
			};
		});
		
	});
		
	function autosprv(){
		
	}
		
	$(function(){
		
		$("#nacionalit").hide();
		$(".inputnum").numeric(".");
	});
	
	function cal_nacionali(){
		nacionalit=$("#nacionalit").val();
		if(nacionalit=="E")
		a=1;
		else
		a=0;

		$("#nacionali").attr('selectedIndex',a);
	}
	
	
	
	
	

	function btn_anular(i){
		if(!confirm("Esta Seguro que desea Anular el Ingreso"))
			return false;
		else
			window.location='<?=site_url('ingresos/recibo/anular')?>/'+i
	}

	</script>
	<?php
	}else{
	?>
	<script language="javascript" type="text/javascript">
	
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
			<table width="100%"  style="margin:0;width:100%;" bgcolor="#F4F4F4">
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
			<table width="100%"  style="margin:0;width:100%;" bgcolor="#F4E4C6">
				<tr>
					<td class="littletablerow"  colspan="4"><strong>DATOS DE LA PATENTE</strong></td>
				</tr>
                                <tr>
					<td class="littletablerow"              ><?='<span class="littletablerowth" >'.$form->patente->label.'*</span>' ?>&nbsp;</td>
					<td class="littletablerow"  colspan="3"><?=$form->patente->output ?>&nbsp;</td>
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
					<td class="littletablerowth"             ><?=$form->monto->label  ?>&nbsp;</td>
					<td class="littletablerow"   colspan="3" ><?=$form->monto->output ?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth"            ><?//=$form->concepto->label  ?>&nbsp;</td>
					<td class="littletablerow"   colspan="3"><?//=$form->concepto->output ?>&nbsp; </td>
				</tr>
			</table><br />
			
			
			<?php echo $form_end     ?>
			<?php echo $container_bl ?>
			<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>
