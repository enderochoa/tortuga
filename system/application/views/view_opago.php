<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['pacom'] AS $ind=>$data)
	$campos[]=$data['field'];
	$campos='<tr id="tr_pacom_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
	$campos.=' <td class="littletablerow"><a href=# onclick="del_pacom(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
	$campos=$form->js_escape($campos);
	//$campos='';


if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin;
if($form->_status!='show'){
	$uri  =$this->datasis->get_uri();
	$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='ocompra' AND uri='$uri'");
	$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>
	<script language="javascript" type="text/javascript">
	pacom_cont=<?=$form->max_rel_count['pacom'] ?>;
	
	$.ajaxSetup({
   'beforeSend' : function(xhr) {
    xhr.overrideMimeType('text/html; charset=<?=$this->config->item('charset'); ?>');
    }
  });
	
	function debe(i){
		$.post("<?=site_url('presupuesto/opago/debe')?>",{ id:$("#compra_"+i).val() },function(data){$("#monto_"+i).val(data);})
	}
	
	$(function() {
		$(".inputnum").numeric(".");
	});

	function nospaces(object) {
	  text = object.value;
	  object.value = object.value.replace(/ /,"\r\n");
	  while (object.value.search(/(\r\n\r\n)|(\n\n)/) != -1) {
	  object.value = object.value.replace(/\r\n\r\n/g, "\r\n");
	  object.value = object.value.replace(/\n\n/g, "\n");
	  }
	}
	
	function cal_concepto(i){
		var id = i.toString();
		n = $("#compra_"+id).val();		
		$.post("<?=site_url('presupuesto/opago/concepto')?>",{ numero:n },function(data){
			data = $("#observa").val()+data;
			$("#observa").val(data);
		})
	}

	function modbusdepen(i){
		var id = i.toString();
		var cod_prov =$("#cod_prov").val();

		var cod_prov =$("#cod_prov").val();
		if(cod_prov=="")cod_prov=".....";
		
		var link='<?=$modblink2 ?>'+'/'+cod_prov;
		link =link.replace(/<#i#>/g,id);
		vent=window.open(link,'ventbuscarocompra','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5');
		vent.focus();

		document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarocompra\');vent.close();');
	}

	function btn_anula(i){
		if(!confirm("Esta Seguro que desea anular la Orden de Pago"))
			return false;
		else
			window.location='<?=site_url('presupuesto/common/op_anular/')?>'+i
	}

	function cal_total(){
		
		tot =tot2=impt=r=riva=otrasrete2=0;
		for(i=0;i<pacom_cont;i++){
			id=i.toString();
			otrasrete   =parseFloat($("#otrasreteo_"+id).val());
			total       =parseFloat($("#totalo_"+id).val());
			total2      =parseFloat($("#total2o_"+id).val());
			imptimbre   =parseFloat($("#imptimbreo_"+id).val());
			reten       =parseFloat($("#reteno_"+id).val());
			reteiva     =parseFloat($("#reteivao_"+id).val());
			tot        +=total;
			tot2       +=total2;
			impt       +=imptimbre;
			r          +=reten;
			riva       += reteiva;
			riva       += re;
			otrasrete2 += otrasrete;
		}
		
		if(!isNaN(tot))$("#total").val(tot);
		if(!isNaN(tot2))$("#total2").val(tot2);
		if(!isNaN(impt))$("#imptimbre").val(impt);
		if(!isNaN(r))$("#reten").val(r);
		if(!isNaN(riva))$("#reteiva").val(riva);
		if(!isNaN(otrasrete2))$("#otrasrete").val(otrasrete2);
	}

	function cal_debe(i){
		debe(i);
		var id      = i.toString();
		var total   =$("#total_"+id).val();
		var abonado =$("#abonado_"+id).val();
		var abonar  =total - abonado;
		
		if(!isNaN(abonar))$("#monto_"+id).val(abonar);
		cal_total();

	}

	function add_pacom(){
		var htm = <?=$campos ?>;
		can = pacom_cont.toString();
		con = (pacom_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#monto_"+can).numeric(".");
		pacom_cont=pacom_cont+1;
	}

	function del_pacom(id){
		id = id.toString();
		$('#tr_pacom_'+id).remove();
	}
	</script>
	<?php
	}else{
	?>
	<script language="javascript" type="text/javascript">
		function btn_anula(i){
			if(!confirm("Esta Seguro que desea anular la Orden de Pago"))
				return false;
			else
				window.location='<?=site_url('presupuesto/common/op_anular/')?>/'+i
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
			<table width="100%"  style="margin:0;width:100%;" bgcolor="#F4F4F4" >
			  <tr>
			    <td colspan=6 class="bigtableheader"><?=$this->tits?> Nro. <?php  echo $form->numero->output ?></td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->cod_prov->label   ?>*&nbsp;</td>
			    <td class="littletablerow"  ><? echo $form->cod_prov->output.$form->nombrep->output; if($this->datasis->traevalor('USA2COD_PROVENODIREC')=='S')echo $form->cod_prov2->output.$form->nombrep2->output;  ?>&nbsp </td>
			    <td class="littletablerowth"><?=$form->fecha->label   ?>&nbsp; </td>
			    <td class="littletablerow"  ><?=$form->fecha->output  ?>&nbsp; </td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->tipoc->label   ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->tipoc->output  ?>&nbsp;</td>
			    <td class="littletablerowth"><?//=$form->fecha->label   ?>&nbsp; </td>
			    <td class="littletablerow"  ><?//=$form->fecha->output  ?>&nbsp; </td>
			  </tr>
			  <tr>
			    <td class="littletablerowth">         <?=$form->observa->label  ?>&nbsp;</td>
			    <td class="littletablerow" colspan=3 ><?=$form->observa->output ?>&nbsp;</td>
			  </tr>
	    	</table><br />
		<table width='100%' bgcolor="#F4F4F4" >
     		<tr>
			<td class="littletableheaderb">Orden de Compra                  </td>
			<td class="littletableheaderb">Certificado                      </td>
			<td class="littletableheaderb" align='right'>Total Facturado     </td>
			<td class="littletableheaderb" align='right'>Otras Retenciones   </td>
			<td class="littletableheaderb" align='right'>IMP Timbre           </td>
			<td class="littletableheaderb" align='right'>Retenci&oacute;n ISLR</td>
			<td class="littletableheaderb" align='right'>Retenci&oacute;n IVA </td>
			<td class="littletableheaderb" align='right'>Total a Pagar       </td>		
			
			<?php if($form->_status!='show') {?>
			<td class="littletableheaderb">&nbsp;</td>
			<?php } ?>
			</tr>
			  <?php
			  for($i=0;$i<$form->max_rel_count['pacom'];$i++) {
		  		$obj0="itcompra_$i";
		  		$obj5="itcertificadoo_$i";
		  		$obj1="ittotal2o_$i";
				$obj7="itotrasreteo_$i";
		  		$obj6="itimptimbreo_$i";
		  		$obj2="itreteno_$i";
		  		$obj3="itreteivao_$i";
				$obj4="ittotalo_$i";
			  ?>
			  <tr id='tr_pacom_<?=$i ?>'>
			    <td class="littletablerow">              <?=$form->$obj0->output ?></td>
			    <td class="littletablerow">              <?=$form->$obj5->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj1->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj7->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj6->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj2->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj3->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj4->output ?></td>
			  
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_pacom(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>

			  <tr id='__UTPL__'>
			    <td class="littletablefooterb" colspan="2" align='right'>Totales</td>
			    <td class="littletablefooterb"             align='right'><?=$form->total2->output  ?></td>
			    <td class="littletablefooterb"             align='right'><?=$form->otrasrete->output  ?></td>
			    <td class="littletablefooterb"             align='right'><?=$form->imptimbre->output  ?></td>
			    <td class="littletablefooterb"             align='right'><?=$form->reten->output  ?></td>
			    <td class="littletablefooterb"             align='right'><?=$form->reteiva->output  ?></td>
			    <td class="littletablefooterb"             align='right'><?=$form->total->output ?></td>
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
