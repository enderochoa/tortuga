<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['ittrasla'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_ittrasla_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_ittrasla(<#i#>);return false;">'.image('delete.jpg',"Haz Click aqui para eliminar este item",array("border"=>0,"class"=>"eliminait")).'</a></td></tr>';
$campos=$form->js_escape($campos);
if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin; 
if($form->_status!='show'){
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
ittrasla_cont=<?=$form->max_rel_count['ittrasla'] ?>;
	datos    ='';
	estruadm ='';
	com=false;
	
  $(function() {
  	$(".inputnum").numeric(".");
  	
  	$(document).keydown(function(e){
					//alert(e.which);
			if (18 == e.which) {
				com=true;
				//c = String.fromCharCode(e.which);
				return false;
			}
			if (com && (e.which == 61 || e.which == 107)){
			  add_ittrasla();
			  a=ittrasla_cont-1;
			  $("#codigopres_"+a).focus();
				com=false;
				return false;
				}else if (com && e.which != 16 && e.which == 17){
					com=false;
				}
				return true;
		});
		
  	$.post("<?=site_url('presupuesto/ppla/autocomplete4')?>",{ partida:"" },function(data){
			datos=jQuery.parseJSON(data);
		});
		
		$.post("<?=site_url('presupuesto/estruadm/autocompleteui')?>",{ partida:"" },function(data){
			estruadm=jQuery.parseJSON(data);
		});
  });
  
  
  function autop(){
		$("input[name^='codigopres_']").focus(function(){
			id=this.name.substr(11,100);
			$( "#codigopres_"+id).autocomplete({
				minLength: 0,
				source: datos,
				focus: function( event, ui ) {
				$( "#codigopres_"+id).val( ui.item.codigopres );
				$( "#denomi_"+id).val( ui.item.denominacion );
				$( "#ordinal_"+id).val( ui.item.ordinal );
					return false;
				},
				select: function( event, ui ) {
					$( "#codigopres_"+id).val( ui.item.codigopres );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				codigoadm=$("#codigoadm_"+id).val();
				fondo    =$("#fondo_"+id).val();
				if(codigoadm==item.codigoadm && fondo==item.fondo){
					return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.codigopres+'.'+item.ordinal + " - " + item.denominacion+ "</a>" )
					.appendTo( ul );
				}
			};
		});
		
		$("input[name^='codigopres_']").focusout(function(){
			id=this.name.substr(11,100);
			$( "#monto_"+id).focus();
		});
	}
	
	function autoe(){
		$("input[name^='codigoadm_']").focus(function(){
			id=this.name.substr(10,100);
			$( "#codigoadm_"+id).autocomplete({
				minLength: 0,
				source: estruadm,
				focus: function( event, ui ) {
				$( "#codigoadm_"+id).val( ui.item.codigoadm );
					return false;
				},
				select: function( event, ui ) {
					$( "#codigoadm_"+id).val( ui.item.codigoadm );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				fondo    =$("#fondo_"+id).val();
				if(fondo==item.fondo){
					return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>"+ item.codigoadm + " - " +item.denominacion+ "</a>" )
					.appendTo( ul );
				}
			};
			
		});
	}
  

  $(document).ready(function(){
		mascara();
		autop();
		autoe();
  });

function cal_total(){
	taumento=tdisminucion=0;
	arr=$('input[name^="codigoadm_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			id      = this.name.substring(pos+1);
			
			if((isNaN($("#aumento_"+id).val()))||($("#aumento_"+id).val()==''))$("#aumento_"+id).val(0);
			if((isNaN($("#disminucion_"+id).val()))||($("#disminucion_"+id).val()==''))$("#disminucion_"+id).val(0);
			
			aumento=parseFloat($("#aumento_"+id).val());		
			disminucion=parseFloat($("#disminucion_"+id).val());
			
			taumento+=aumento;
			tdisminucion+=disminucion;
		}
	});
	
	$("#taumento").val(taumento);
	$("#tdisminucion").val(tdisminucion);
}

function mascara(){
	$("input[name^='codigoadm_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOESTRU"))?>');
	$("input[name^='codigopres_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOPRES"))?>');
}

function cal_totala(id){
	$("#disminucion_" + id).val(0);
	cal_total();
}

function cal_totald(id){
	$("#aumento_" + id).val(0);
	cal_total();
}

function add_ittrasla(){
	var htm = <?=$campos ?>;
	can = ittrasla_cont.toString();
	con = (ittrasla_cont+1).toString();
	cin = (ittrasla_cont-1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	$("#disminucion_"+can).numeric(".");
	$("#aumento_"+can).numeric(".");
	
	ante=$("#fondo_"+cin).prop('selectedIndex');
	$("#fondo_"+can).prop('selectedIndex',ante);
	
	ante=$("#codigoadm_"+cin).val();
	$("#codigoadm_"+can).val(ante);
	
	$("#codigopres_"+can).focus();
	
	ittrasla_cont=ittrasla_cont+1;
	
	mascara();
	autoe();
	autop();
	cal_total();
}
					
function del_ittrasla(id){
	id = id.toString();
	$('#tr_ittrasla_'+id).remove();
	cal_total();
}
</script>
<?php } ?>

<table align='center'width="80%" >
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
			    <td colspan=6 class="bigtableheader">Traslado Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
			  </tr>
			  <tr>
				<td class="littletablerowth"><?=$form->nrooficio->label   ?>*&nbsp;</td>
				<td class="littletablerow">  <?=$form->nrooficio->output  ?>&nbsp;</td>
				<td class="littletablerowth">&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
			</tr>
			<tr>
				<td class="littletablerowth"><?=$form->resolu->label   ?>&nbsp;</td>
				<td class="littletablerow">  <?=$form->resolu->output  ?>&nbsp;</td>
				<td class="littletablerowth"><?=$form->fresolu->label  ?>&nbsp;</td>
				<td class="littletablerow">  <?=$form->fresolu->output ?>&nbsp;</td>
			</tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->fecha->label  ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp; </td>
			    <td class="littletablerowth">&nbsp;</td>
			    <td class="littletablerow"  >&nbsp; </td>
			  </tr>
			  <tr>
			    <td class="littletablerowth">               <?//=$form->estadmin->label  ?>&nbsp;</td>
			    <td class="littletablerow" id='td_estadmin'><?//=$form->estadmin->output ?>&nbsp; </td>
			    <td class="littletablerowth">               <?//=$form->fondo->label     ?>&nbsp;</td>
			    <td class="littletablerow" id='td_fondo'>   <?//=$form->fondo->output    ?>&nbsp; </td>
			  </tr>
			    <td class="littletablerowth">        <?=$form->motivo->label     ?>&nbsp;</td>
			    <td class="littletablerow" colspan=3><?=$form->motivo->output    ?>&nbsp;</td>
			  </tr>
			  
	    </table><br />

			<table width='100%'>
     		<tr>
					<td class="littletableheaderb">F.Financiamiento    </td>
					<td class="littletableheaderb">Est. Administrativa </td>
					<td class="littletableheaderb">Partida             </td>
					<td class="littletableheaderb">Ordinal             </td>
					<td class="littletableheaderb">Denominaci&oacute;n </td>
					<td class="littletableheaderb">Disminuci&oacute;n  </td>
					<td class="littletableheaderb">Aumento</td>
					<?php if($form->_status!='show') {?>
					<td class="littletableheaderb">&nbsp;</td>
					<?php } ?>
			  </tr>
			  <?php for($i=0;$i<$form->max_rel_count['ittrasla'];$i++) {
				  $obj2="codigoadm_$i";
				  $obj1="fondo_$i"; 
					$obj3="codigopres_$i";
					$obj4="ordinal_$i";
					$obj5="denomi_$i";
					$obj6="disminucion_$i";
					$obj7="aumento_$i";
			  ?>
			  <tr id='tr_ittrasla_<?=$i ?>'>
			  	<td class="littletablerow"><?=$form->$obj1->output ?></td>
			    <td class="littletablerow"><?=$form->$obj2->output ?></td>
			    <td class="littletablerow"><?=$form->$obj3->output ?></td>
			    <td class="littletablerow"><?=$form->$obj4->output ?></td>
			    <td class="littletablerow"><?=$form->$obj5->output ?></td>
			    <td class="littletablerow" align="right"><?=$form->$obj6->output ?></td>
			    <td class="littletablerow" align="right"><?=$form->$obj7->output ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_ittrasla(<?=$i ?>);return false;'><?=image('delete.jpg','Haz Click aqui para eliminar este item.</br>De esta manera se eliminara toda la fila seleccionada',array("border"=>0,"class"=>"eliminait"))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>

			  <tr id='__UTPL__'>
			  	<td class="littletablefooterb" align="right">&nbsp;</td>
			    <td class="littletablefooterb" align="right">&nbsp;</td>
			    <td class="littletablefooterb" align="right">&nbsp;</td>
			    <td class="littletablefooterb" align="right">&nbsp;</td>
			    <td class="littletablefooterb" align="right">&nbsp;</td>
			    <td class="littletablefooterb" align="right"><?=$form->tdisminucion->output  ?>&nbsp;</td>
			    <td class="littletablefooterb" align="right"><?=$form->taumento->output ?>&nbsp;</td>    			    
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb" align="right">&nbsp;</td>
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
