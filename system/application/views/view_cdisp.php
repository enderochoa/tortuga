<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['itcdisp'] AS $ind=>$data)
	$campos[]=$data['field'];
	
$campos='<tr id="tr_itcdisp_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos.=' <td class="littletablerow"><a href=# onclick="del_itcdisp(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if(true){//$form->_status!='show'
	$uri      =$this->datasis->get_uri();
	$idt      =$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_presaldo' AND uri='$uri'");
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
	cdisp_cont=<?=$form->max_rel_count['itcdisp'] ?>;
	datos    ='';
	estruadm ='';
	com      =false;
	
	function mascara(){
		$("input[name^='itcodigoadm_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOESTRU"))?>');
		$("input[name^='itcodigopres_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOPRES"))?>');
	}
	
	$(document).ready(function() {
		$.post("<?=site_url('presupuesto/estruadm/autocompleteui')?>",{ partida:"" },function(data){
			estruadm=jQuery.parseJSON(data);
		});
		
		$.post("<?=site_url('presupuesto/ppla/autocomplete4/mayor')?>",{ partida:"" },function(data){
			datos=jQuery.parseJSON(data);
		});
	});
	
	function autop(){
		$("input[name^='itcodigopres_']").focus(function(){
		
			id=this.name.substr(13,100);
			$( "#itcodigopres_"+id).autocomplete({
				minLength: 0,
				source: datos,
				focus: function( event, ui ) {
				$( "#itcodigopres_"+id).val( ui.item.codigopres );
				$( "#itordinal_"+id).val( ui.item.ordinal );
				$( "#itdenomi_"+id).val( ui.item.denominacion );
					return false;
				},
				select: function( event, ui ) {
					$( "#codigopres_"+id).val( ui.item.codigopres );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				codigoadm=$("#itcodigoadm_"+id).val();
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

	$(function() {
		$(".inputnum").numeric(".");
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
				add_itcdisp();
				a=cdisp_cont-1;
				$("#itcodigoadm_"+a).focus();
				
				com=false;
				return false;
			}else if (com && e.which != 16 && e.which == 17){
				com=false;
			}
			return true;
		});
	});

	function get_uadmin(){
		$.post("<?=$link ?>",{ uejecuta:$("#uejecuta").val() },function(data){
			$("#td_uadministra").html(data);
			})
	}

	function cal_soli(){
		tsoli =0;
		tdisp =0;
		
		for(i=0;i<cdisp_cont;i++){
			
			id=i.toString();
			soli=parseFloat($("#itsoli_"+id).val());
						
			if(isNaN(soli))soli=0;
			tsoli+=soli;

			disp=parseFloat($("#itdisp_"+id).val());
			if(isNaN(disp))disp=0;
			tdisp+=disp;
		}		
		if(!isNaN(tdisp)){
			$("#tdisp").val(tdisp);
		}
		
		if(!isNaN(tsoli)){
			$("#tsoli").val(tsoli);
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

	function add_itcdisp(){
		var htm = <?=$campos ?>;
		can = cdisp_cont.toString();
		con = (cdisp_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#soli_"+can).numeric(".");
		cdisp_cont=cdisp_cont+1;
		cal_soli();
		autop();
		autoe();
		mascara();
	}

	function del_itcdisp(id){
		id = id.toString();
		$("#soli_"+id).val('0');
		$('#tr_itcdisp_'+id).remove();		
		cal_soli();
	}

	function btn_anular(i){
		if(!confirm("Esta Seguro que desea Anular el certificado de Disponibilidad"))
			return false;
		else
			window.location='<?=site_url('presupuesto/cdisp/cd_anular')?>/'+i
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
					<td class="littletablerowth">&nbsp;</td>
					<td class="littletablerow"  >&nbsp;</td>
					<td class="littletablerowth"><?=$form->status->label ?>&nbsp;</td> 
					<td class="littletablerow"  ><?=$form->status->output?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth"><?=$form->fecha->label  ?>*&nbsp;</td>
					<td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp; </td>
					<td class="littletablerowth"><?=$form->ano->label ?>&nbsp;</td> 
					<td class="littletablerow"  ><?=$form->ano->output?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth">                    <?=$form->uejecuta->label  ?>*&nbsp;</td>
					<td class="littletablerow"  >                    <?=$form->uejecuta->output ?>&nbsp; </td>
					<td class="littletablerowth"><?=$form->uadministra->label ?>&nbsp;</td> 
					<td class="littletablerow"  id="td_uadministra"> <?=$form->uadministra->output?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth">            <?=$form->reque->label   ?>*&nbsp;</td>
					<td class="littletablerow" colspan="3"  ><?=$form->reque->output  ?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth">            <?=$form->fondo->label   ?>*&nbsp;</td>
					<td class="littletablerow" colspan="3"  ><?=$form->fondo->output  ?>&nbsp; </td>
				</tr>
			</table><br />
			<table width='100%' bgcolor="#FFEAEB" class="table_detalle">
			<tr>
			<tr>
				<th class="littletableheaderb" colspan="<?=($form->_status=='show'?7:8)?>">IMPUTACION PRESUPUESTARIA </th>
			</tr>
			<tr>
				<td class="littletableheaderb">Est. Administrativa </td>
				<td class="littletableheaderb">Partida             </td>
				<td class="littletableheaderb">Denominaci&oacute;n </td>
				<td class="littletableheaderb" align='right'>Disponible </td>
					<td class="littletableheaderb" align='right'>Solicitado </td>			
			<?php if($form->_status!='show') {?>
			<td class="littletableheaderb">&nbsp;</td>
			<?php } ?>
			</tr>
			
				<?php
				for($i=0;$i<$form->max_rel_count['itcdisp'];$i++) {
					$obj0="itcodigoadm_$i";
					$obj1="itcodigopres_$i";
					$obj4="itdenomi_$i";
					$obj5="itdisp_$i";
					$obj6="itsoli_$i";
				?>
				<tr id='tr_itcdisp_<?=$i ?>'>
					<td class="littletablerow">              <?=$form->$obj0->output ?></td>
					<td class="littletablerow">              <?=$form->$obj1->output ?></td>
					<td class="littletablerow">              <?=$form->$obj4->output ?></td>
					<td class="littletablerow" align='right'><?=$form->$obj5->output ?></td>
					<td class="littletablerow" align='right'><?=$form->$obj6->output ?></td>
					
					<?php if ($form->_status=='create' || $form->_status=='modify') {?>
					<td class="littletablerow"><a href=# onclick='del_itcdisp(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
					<?php } ?>
				</tr>
				<?php } ?>
				<tr id='__UTPL__'>
					<td class="littletablefooterb" colspan="3" align='right'><strong>Totales</strong>   </td>
					<td class="littletablefooterb"             align='right'><?=$form->tdisp->output  ?></td>
					<td class="littletablefooterb"             align='right'><?=$form->tsoli->output  ?></td>
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
