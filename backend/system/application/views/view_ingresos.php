<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['itingresos'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_itingresos_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos.=' <td class="littletablerow"><a href=# onclick="del_itingresos(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if(true){//$form->_status!='show'
	$uri  =$this->datasis->get_uri();
	//$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='odirect' AND uri='$uri'");
	//$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>
	<script language="javascript" type="text/javascript">
	ingresos_cont=<?=$form->max_rel_count['itingresos'] ?>;

	$(function() {
		cal_tot();
		$(".inputnum").numeric(".");
	});



	function cal_tot(){
		t =0;
		
		for(i=0;i<ingresos_cont;i++){
			id=i.toString();
			monto=parseFloat($("#itmonto_"+id).val());
			
			if(isNaN(monto))monto=0;
			
			t+=monto;
		}
		if(!isNaN(t)){
			$("#total").val(t);
		}
	}

	function add_itingresos(){
		var htm = <?=$campos ?>;
		can = ingresos_cont.toString();
		con = (ingresos_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#monto_"+can).numeric(".");
		ingresos_cont=ingresos_cont+1;
		cal_tot();
	}

	function del_itingresos(id){
		id = id.toString();
		$("#monto_"+id).val('0');
		$('#tr_itingresos_'+id).remove();
		cal_tot();
	}

	function btn_anular(i){
		if(!confirm("Esta Seguro que desea Anular el Ingreso"))
			return false;
		else
			window.location='<?=site_url('ingresos/ingresos/anular')?>/'+i
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
					<td class="littletablerowth"><?=$form->fecha->label  ?>*&nbsp;</td>
					<td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp; </td>
					<td class="littletablerowth"><?=$form->mbanc_id->label ?>&nbsp;</td> 
					<td class="littletablerow"  ><?=$form->mbanc_id->output?>&nbsp; </td>
				</tr> 
			</table><br />
			<table width='100%' bgcolor="#FFEAEB" class="table_detalle">
			<tr>
			<tr>
			<th class="littletableheaderb" colspan="<?=($form->_status=='show'?7:8)?>">INGRESOS PRESUPUESTARIOS  </th>
			</tr>
			<tr>
					<td class="littletableheaderb"              >Partida             </td>
					<td class="littletableheaderb"              >Denominaci&oacute;n </td>
					<td class="littletableheaderb"              >Recibo Inicio       </td>
					<td class="littletableheaderb"              >Recibo Fin          </td>
					<td class="littletableheaderb" align='right'>Monto               </td>
			<?php if($form->_status!='show') {?>
			<td class="littletableheaderb">&nbsp;</td>
			<?php } ?>
			</tr>
			
				<?php
				for($i=0;$i<$form->max_rel_count['itingresos'];$i++) {
				
					$obj0="itcodigopres_$i";
					$obj1="itdenomi_$i";
					$obj2="itreferen1_$i";
					$obj3="itreferen2_$i";
					$obj4="itmonto_$i";
				?>
				<tr id='tr_itingresos_<?=$i ?>'>
				<td class="littletablerow">              <?=$form->$obj0->output ?></td>
				<td class="littletablerow">              <?=$form->$obj1->output ?></td>
				<td class="littletablerow">              <?=$form->$obj2->output ?></td>
				<td class="littletablerow">              <?=$form->$obj3->output ?></td>
				<td class="littletablerow">              <?=$form->$obj4->output ?></td>
				<?php if ($form->_status=='create' || $form->_status=='modify') {?>
				<td class="littletablerow"><a href=# onclick='del_itingresos(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
				<?php } ?>
				</tr>
				<?php } ?>
				<tr id='__UTPL__'>
					<td class="littletablefooterb" colspan="4"  align='right'><strong>Total</strong>   </td>
					<td class="littletablefooterb"              align='right'<?=$form->total->output  ?></td>
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
