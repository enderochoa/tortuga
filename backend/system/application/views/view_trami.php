<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:

foreach($form->detail_fields['ittrami'] AS $ind=>$data)
	$campos[]=$data['field'];
	$campos='<tr id="tr_ittrami_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
	$campos.=' <td class="littletablerow"><a href=# onclick="del_ittrami(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
	$campos=$form->js_escape($campos);
	//$campos='';


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
	ittrami_cont=<?=$form->max_rel_count['ittrami'] ?>;
	datos    ='';
	estruadm ='';
	com=false;
	
	
	
	
	$(document).ready(function(){		
		$.post("<?=site_url('presupuesto/estruadm/autocompleteui')?>",{ partida:"" },function(data){
			estruadm=jQuery.parseJSON(data);
		});

		$.post("<?=site_url('presupuesto/ppla/autocomplete4/mayor')?>",{ partida:"" },function(data){
			datos=jQuery.parseJSON(data);
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
					$( "#denominacion_"+id).val( ui.item.denominacion );
					return false;
				},
				select: function( event, ui ) {
					$( "#codigopres_"+id).val( ui.item.codigopres );
					$( "#denominacion_"+id).val( ui.item.denominacion );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				codigoadm=$("#codigoadm_"+id).val();
				fondo=$("#fondo").val();
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
				fondo=$("#fondo").val();
				if(fondo==item.fondo){
					return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>"+ item.codigoadm + " - " +item.denominacion+ "</a>" )
					.appendTo( ul );
				}
			};
		});
	}
	
	function mascara(){
		$("input[name^='codigoadm_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOESTRU"))?>');
		$("input[name^='codigopres_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOPRES"))?>');
	}
	
	$(function(){
		$(".inputnum").numeric(".");
		mascara();
		autoe();
		autop();
		$(document).keydown(function(e){
					//alert(e.which);
		if (18 == e.which) {
			com=true;
			//c = String.fromCharCode(e.which);
			return false;
		}
		if (com && (e.which == 61 || e.which == 107)) {
		  add_ittrami();
		  a=ittrami_cont-1;
		  $("#codigoadm_"+a).focus();
				com=false;
				return false;
			}else if (com && e.which != 16 && e.which == 17){
				com=false;
			}
			return true;
		});
		
	});
		
	function cal_total(){
		tot=stot=tiva=0;
		arr=$('input[name^="codigoadm_"]');
		jQuery.each(arr, function() {
			nom=this.name
			pos=this.name.lastIndexOf('_');
			if(pos>0){
				id      = this.name.substring(pos+1);
				subtotal= parseFloat($("#importe_"+id).val());
				if((isNaN(subtotal)) || (subtotal=='') || (subtotal<0)){
					subtotal=0;
					$("#importe_"+id).val(0);
				}
			}
			stot += subtotal;
		});
		$("#monto").val(stot);
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
	
	function add_ittrami(){
		var htm = <?=$campos ?>;
		can = ittrami_cont.toString();
		con = (ittrami_cont+1).toString();
		cin = (ittrami_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#importe_"+can).numeric(".");
		ittrami_cont=ittrami_cont+1;
		
		ante=$("#itfondo_"+cin).attr('selectedIndex');
		$("#itfondo_"+can).attr('selectedIndex',ante);
		
		mascara();
		autoe();
		autop();
	}

	function del_ittrami(id){
		id = id.toString();
		$('#tr_ittrami_'+id).remove();
	}
	</script>
	<?php
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
			    <td class="littletablerowth"><?//=$form->concepto->label ?>&nbsp; </td>
			    <td class="littletablerow"  ><?//=$form->concepto->output ?>&nbsp;</td>
			    <td class="littletablerowth"><?=$form->fcomprome->label ?>*&nbsp;   </td> 
			    <td class="littletablerow"  ><?=$form->fcomprome->output?>&nbsp;    </td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->fecha->label ?>&nbsp; </td>
			    <td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp;</td>
			    <td class="littletablerowth"><?=$form->fpagado->label ?>&nbsp;   </td> 
			    <td class="littletablerow"  ><?=$form->fpagado->output?>&nbsp;    </td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"           ><?=$form->concepto->label ?>&nbsp; </td>
			    <td class="littletablerow"  colspan="3"><?=$form->concepto->output ?>&nbsp;</td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->fondo->label ?>&nbsp; </td>
			    <td class="littletablerow"  ><?=$form->fondo->output ?>&nbsp;</td>
			    <td class="littletablerowth"><?=$form->compromiso->label ?>&nbsp; </td> 
			    <td class="littletablerow"  ><?=$form->compromiso->output ?>&nbsp;</td>
			  </tr>
			  <tr>
				<td class="littletablerowth"><?=$form->cod_prov->label ?>&nbsp; </td> 
			    <td class="littletablerow"  ><?=$form->cod_prov->output ?>&nbsp;</td>
			    <td class="littletablerowth"><?=$form->status->label ?>&nbsp; </td> 
			    <td class="littletablerow"  ><?=$form->status->output ?>&nbsp;</td>
			  </tr>
	    	</table><br />
			<table width='100%'>
     		<tr>
     			<td class="littletableheaderb" align='right'>Codigo                               </td>
     			<td class="littletableheaderb"              >Presupuestario                       </td>
     			<td class="littletableheaderb"              >Denominaci&oacute;n                  </td>
  			<td class="littletableheaderb"align='right' >Importe                   </td>
			<?php if($form->_status!='show') {?>
			<td class="littletableheaderb">&nbsp;</td>
			<?php } ?>
		</tr>
			  <?php
			  for($i=0;$i<$form->max_rel_count['ittrami'];$i++) {
		  		$obj0="itcodigoadm_$i"; 
		  		$obj2="itcodigopres_$i";
				$obj4="itdenominacion_$i";
				$obj5="importe_$i";
			  ?>
			  <tr id='tr_ittrami_<?=$i ?>'>
			    <td class="littletablerow"><?=$form->$obj0->output ?></td>
			    <td class="littletablerow"><?=$form->$obj2->output ?></td>
			    <td class="littletablerow"><?=$form->$obj4->output ?></td>  
			    <td class="littletablerow"><?=$form->$obj5->output ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_ittrami(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			   
			  <tr id='__UTPL__'>
				<td class="littletablefooterb" align='right' colspan="3"><?=$form->monto->label  ?></td>
			    <td class="littletablefooterb" align='right'>                <?=$form->monto->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
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
