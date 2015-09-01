<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['itopago'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_itopago_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itopago(<#i#>);return false;">Eliminar</a></td></tr>';

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin; 
if($form->_status!='show'){
	$uri  =$this->uri->uri_string();
	$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='ppla' AND uri='$uri'");
	$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>
	<script language="javascript" type="text/javascript">
	itopago_cont=<?=$form->max_rel_count['itopago'] ?>;
	
	$(function() {
		$(".inputnum").numeric(".");
		
		$("#estadmin").change(function(){
			$.post("<?=site_url('presupuesto/presupuesto/get_tipo')?>",{ codigoadm:$("#estadmin").val() },function(data){$("#fondo").html(data);})
		});
		cal_total();
	});
				
	function modbusdepen(i){
		var id = i.toString();
		var fondo   =$("#fondo").val();
		var estadmin=$("#estadmin").val();
	
		if(fondo.length*estadmin.length == 0){
			alert('Debe Seleccionar primero un fondo y una unidad administrativa');
			return false;
		}
		var link='<?=$modblink2 ?>'+'/'+fondo+'/'+estadmin;
		link =link.replace(/<#i#>/g,id);
		vent=window.open(link,'ventbuscarppla','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'); 
		vent.focus(); 
		document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarppla\');vent.close();');
	}

	function cal_total(){
		tot=0;
		for(i=0;i<itopago_cont;i++){
			id=i.toString();
			valor=parseFloat($("#pago_"+id).val());
			
			if(!isNaN(valor)){
				tot=tot+valor;
			}
		}
		$("#total").val(tot);
	}

	function add_itopago(){
		var htm = '<?=$campos ?>';
		can = itopago_cont.toString();
		con = (itopago_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#pago_"+can).numeric(".");
		itopago_cont=itopago_cont+1;
	}

	function del_itopago(id){
		id = id.toString();
		$('#tr_itopago_'+id).remove();
	}
	</script>
	<?php  
	} ?>
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
			    <td colspan=6 class="bigtableheader">Orden de Pago Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->fecha->label     ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fecha->output    ?>&nbsp; </td>  
			    <td class="littletablerowth"><?=$form->cod_prov->label  ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->cod_prov->output ?>&nbsp;</td>
				</tr>
			  <tr>
			    <td class="littletablerowth">               <?=$form->estadmin->label  ?>*&nbsp;</td>
			    <td class="littletablerow" id='td_estadmin'><?=$form->estadmin->output ?>&nbsp;</td>
			    <td class="littletablerowth">               <?=$form->fondo->label     ?>*&nbsp;</td>
			    <td class="littletablerow" >                <?=$form->fondo->output    ?>&nbsp;</td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"           ><?=$form->observa->label ?>&nbsp;</td>
			    <td class="littletablerow" colspan="3" ><?=$form->observa->output ?>&nbsp;</td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"           ><?=$form->beneficiario->label ?>&nbsp;</td>
			    <td class="littletablerow" colspan="3" ><?=$form->beneficiario->output ?>&nbsp;</td>
			  </tr>
	    	</table><br />
			<table width='100%'>
     		<tr>
     			<td class="littletableheaderb">Partida           </td>
					<td class="littletableheaderb">Descripci&oacute;n</td>
			    <td class="littletableheaderb">Pago              </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletableheaderb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <?php 
			  for($i=0;$i<$form->max_rel_count['itopago'];$i++) {
		  		$obj0="itpartida_$i"; 
					$obj1="itdescripcion_$i";
					$obj2="itpago_$i";
			  ?>
			  <tr id='tr_itopago_<?=$i ?>'>
			    <td class="littletablerow"><?=$form->$obj0->output ?></td>
			    <td class="littletablerow"><?=$form->$obj1->output ?></td>				
			    <td class="littletablerow"><?=$form->$obj2->output ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_itopago(<?=$i ?>);return false;'>Eliminar</a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			   
			  <tr id='__UTPL__'>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->total->label  ?></td>
			    <td class="littletablefooterb" >            <?=$form->total->output  ?></td>
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
