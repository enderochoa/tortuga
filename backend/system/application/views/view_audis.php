<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
echo $form->output;
else:
$link=site_url('presupuesto/presupuesto/get_tipo');

foreach($form->detail_fields['itaudis'] AS $ind=>$data)
$campos[]=$data['field'];
$campos='<tr id="tr_itaudis_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itaudis(<#i#>);return false;">'.image('delete.jpg',"Haz Click aqui para eliminar este item",array("border"=>0,"class"=>"eliminait")).'</a></td></tr>';
$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){
	//$uri  =$this->uri->uri_string();
	$uri  =$this->datasis->get_uri();
	$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_presaldo' AND uri='$uri'");
	$modblink=site_url('/buscar/index/'.$idt.'/<#i#>');

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

	//$.ajaxSetup({
	//	'beforeSend' : function(xhr) {
	//		xhr.overrideMimeType('text/html; charset=<?=$this->config->item('charset'); ?>');
	//	}
	//});
	
	function get_uadmin(){
		$.post("<?=$link ?>",{ uejecutora:$("#uejecutora").val() },function(data){
		$("#uadministra").html(data);
		})
	}

itaudis_cont=<?=$form->max_rel_count['itaudis'] ?>;
	datos    ='';
	estruadm ='';
	com=false;
  $(function() {
		$(document).keydown(function(e){
					//alert(e.which);
		if (18 == e.which) {
			com=true;
			//c = String.fromCharCode(e.which);
			return false;
		}
		if (com && (e.which == 61 || e.which == 107)) {
		  add_itaudis();
		  a=itaudis_cont-1;
		  $("#codigopres_"+a).focus();
			//alert("agrega linea");
				com=false;
				return false;
			}else if (com && e.which != 16 && e.which == 17){
				com=false;
			}
			return true;
		});
  
		$(".inputnum").numeric(".");
		cal_total();

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
					.append( "<a>" + item.codigopres + " - " + item.denominacion+ "</a>" )
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

	function mascara(){
		$("input[name^='codigoadm_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOESTRU"))?>');
		$("input[name^='codigopres_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOPRES"))?>');
	}

  function cal_total(){	
  	tot=can=0;
	arr=$('input[name^="codigoadm_"]');
		jQuery.each(arr, function() {
			nom=this.name
			pos=this.name.lastIndexOf('_');
			if(pos>0){
				id      = this.name.substring(pos+1);
				valor=parseFloat($("#monto_"+id).val());
				if(!isNaN(valor))
					tot=tot+valor;		
				$("#total").val(tot);
			}
  	});
  }
//1804  1805
  function add_itaudis(){
  	var htm = <?=$campos ?>;
  	can = itaudis_cont.toString();
  	con = (itaudis_cont+1).toString();
  	cin = (itaudis_cont-1).toString();
  	htm = htm.replace(/<#i#>/g,can);
  	htm = htm.replace(/<#o#>/g,con);
  	$("#__UTPL__").before(htm);
  	$("#monto_"+can).numeric(".");
  	
  	ante=$("#fondo_"+cin).prop('selectedIndex');
  	$("#fondo_"+can).prop('selectedIndex',ante);
  	
  	ante=$("#codigoadm_"+cin).val();
  	$("#codigoadm_"+can).val(ante);
  	
  	$("#codigoadm_"+can).focus();
  	
  	itaudis_cont=itaudis_cont+1;
  	mascara();
  	autoe();
  	autop();
	cal_total();
  }

  function del_itaudis(id){
  	id = id.toString();
  	$('#tr_itaudis_'+id).remove();
	cal_total()
  }
</script>
	<?php } ?>

<table align='center' width="80%">
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
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td colspan=6 class="bigtableheader">Aumento o Disminuci&oacute;n
				Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
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
				<td class="littletablerowth"><?=$form->tipo->label   ?>*&nbsp;</td>
				<td class="littletablerow">  <?=$form->tipo->output  ?>&nbsp; </td>
				<td class="littletablerowth"><?=$form->fecha->label  ?>*&nbsp;</td>
				<td class="littletablerow">  <?=$form->fecha->output ?>&nbsp; </td>
			</tr>
			<tr>
				<td class="littletablerowth"><?=$form->uejecutora->label  ?>*&nbsp;</td>	
				<td class="littletablerow"  ><?=$form->uejecutora->output ?>&nbsp; </td>
				<td class="littletablerowth">                  <?=$form->uadministra->label ?>&nbsp;</td>
				<td class="littletablerow" id='td_uadministra'><?=$form->uadministra->output ?>&nbsp;</td>
				</tr>
			<tr>
				<td class="littletablerowth"><?=$form->motivo->label     ?>&nbsp;</td>
				<td class="littletablerow" colspan=3><?=$form->motivo->output    ?>&nbsp;</td>
			</tr>
		</table>
		<br />
		<table width='100%'>
			<tr>
				<td class="littletableheaderb">F.Financiamiento</td>
				<td class="littletableheaderb">Est. Admin</td>
				<td class="littletableheaderb">Partida*</td>
				<td class="littletableheaderb">Denominaci&oacute;n</td>
				<td class="littletableheaderb" align="right">Monto</td>
				<?php if($form->_status!='show') {?>
				<td class="littletableheaderb">&nbsp;</td>
				<?php } ?>
			</tr>
			<?php for($i=0;$i<$form->max_rel_count['itaudis'];$i++) {
				$obj0="fondo_$i";
				$obj1="codigoadm_$i";
				$obj2="codigopres_$i";
				$obj4="denomi_$i";
				$obj5="monto_$i";
				?>
			<tr id='tr_itaudis_<?=$i ?>'>
				<td class="littletablerow"><?=$form->$obj0->output ?></td>
				<td class="littletablerow"><?=$form->$obj1->output ?></td>
				<td class="littletablerow"><?=$form->$obj2->output ?></td>
				<td class="littletablerow"><?=$form->$obj4->output ?></td>
				<td class="littletablerow"><?=$form->$obj5->output ?></td>
				<?php if($form->_status!='show') {?>
				<td class="littletablerow"><a href=#
					onclick='del_itaudis(<?=$i ?>);return false;'><?=image('delete.jpg','Haz Click aqui para eliminar este item.</br>De esta manera se eliminara toda la fila seleccionada',array("border"=>0,"class"=>"eliminait"))?></a></td>
					<?php } ?>
			</tr>
			<?php } ?>

			<tr id='__UTPL__'>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;<?=$form->total->label  ?></td>
				<td class="littletablefooterb"><?=$form->total->output ?>&nbsp;</td>
				<?php if($form->_status!='show') {?>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<?php } ?>
			</tr>
		</table>

		<?php echo $form_end     ?> <?php echo $container_bl ?> <?php echo $container_br ?>
		</td>
	</tr>
</table>

		<?php endif; ?>
