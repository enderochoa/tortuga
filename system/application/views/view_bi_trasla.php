<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['bi_ittrasla'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_bi_ittrasla_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_bi_ittrasla(<#i#>);return false;">'.image('delete.jpg',"Haz Click aqui para eliminar este item",array("border"=>0,"class"=>"eliminait")).'</a></td></tr>';
$campos=$form->js_escape($campos);
if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin; 
if($form->_status!='show'){
	$uri      =$this->datasis->get_uri();
	$idt      =$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_bienes2' AND uri='$uri'");
	$modblink =site_url('/buscar/index/'.$idt.'/<#i#>');
	$idt2     =$this->datasis->dameval("SELECT id FROM modbus WHERE idm='bi_conc' AND uri='$uri'");
	$modblink3=site_url('/buscar/index/'.$idt2.'/<#i#>');
?>

<script language="javascript" type="text/javascript">
bi_ittrasla_cont=<?=$form->max_rel_count['bi_ittrasla'] ?>;
	com=false;

	$(function() {
	
		$("#tipo").change(function (){
			tipoo();
		});
		$(document).keydown(function(e){
					//alert(e.which);
			if (18 == e.which) {
				com=true;
				//c = String.fromCharCode(e.which);
				return false;
			}
			if(com && e.which == 61) {
				add_bi_ittrasla();
				a=bi_ittrasla_cont-1;
				$("#itbien_"+a).focus();
				com=false;
				return false;
				}else if (com && e.which != 16 && e.which == 17){
					com=false;
				}
				return true;
		});
	});
	
	function tipoo(){
		t = $("#tipo").val();
		if(t=='I'){
			//$("input[name='alma1']").hide();
			//$("input[name='alma0']").show();
			//$("#alma1").hide();
			//$("#alma0").show();
			$(".alma1").each(function(){
				$(this).hide();
			});
			$(".alma0").each(function(){
				$(this).show();
			});
			
		}else{
			$(".alma0").each(function(){
				$(this).hide();
			});
			$(".alma1").each(function(){
				$(this).show();
			});
			//$("input[name='alma0']").hide();
			//$("input[name='alma1']").show();
			//$("#alma0").hide();
			//$("#alma1").show();
			
		}
	}
	
	function modbusdepen(i){
		var id = i.toString();
		var alma   =$("#alma").val();
	
		var link='<?=$modblink ?>'+'/'+alma;
		link =link.replace(/<#i#>/g,id);
		vent=window.open(link,'ventbuscarbienes','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'); 
		vent.focus();
		document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscar\');vent.close();');
	}
	
	function modbusdepen2(i){
		var id = i.toString();
		var tipo   =$("#tipo").val();
	
		var link='<?=$modblink3 ?>'+'/'+tipo;
		link =link.replace(/<#i#>/g,id);
		vent=window.open(link,'ventbuscarconc','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'); 
		vent.focus();
		document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarconc\');vent.close();');
	}
	
	$(document).ready(function(){
		tipoo();
	});

function add_bi_ittrasla(){
	var htm = <?=$campos ?>;
	can = bi_ittrasla_cont.toString();
	con = (bi_ittrasla_cont+1).toString();
	cin = (bi_ittrasla_cont-1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);

	bi_ittrasla_cont=bi_ittrasla_cont+1;
	tipoo();
}

function del_bi_ittrasla(id){
	id = id.toString();
	$('#tr_bi_ittrasla_'+id).remove();
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
					<td colspan=6 class="bigtableheader"><?=$this->tits ?> Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
				</tr>
				<tr>
					<td class="littletablerowth"><?=$form->tipo->label   ?>*&nbsp;</td>
					<td class="littletablerow"  ><?=$form->tipo->output  ?>&nbsp; </td>
					<td class="littletablerowth"><?=$form->fecha->label  ?>*&nbsp;</td>
					<td class="littletablerow"  ><?=$form->fecha->output ?>&nbsp; </td>
				</tr>
				<tr>
					<td class="littletablerowth"  ><?=$form->alma->label  ?>&nbsp;</td>
					<td class="littletablerow"    ><?=$form->alma->output ?>&nbsp;</td>
					<td class="littletablerowth"  ><?=$form->status->label ?>&nbsp;</td>
					<td class="littletablerow"    ><?=$form->status->output?>&nbsp;</td>
				</tr>
					<td class="littletablerowth">        <?=$form->concepto->label     ?>&nbsp;</td>
					<td class="littletablerow" colspan=3><?=$form->concepto->output    ?>&nbsp;</td>
				</tr>
			</table><br />
			<table width='100%'>
			<tr>
					<td class="littletableheaderb">___ID___            </td>
					<td class="littletableheaderb">Concepto            </td>
					<td class="littletableheaderb">C&oacute;digo       </td>
					<td class="littletableheaderb">Grupo               </td>
					<td class="littletableheaderb">SubGrupo            </td>
					<td class="littletableheaderb">Secci&oacute;n      </td>
					<td class="littletableheaderb">N&uacute;mero       </td>
					<td class="littletableheaderb">Descripci&oacute;n  </td>
					<td class="littletableheaderb">Monto               </td>
					<?php if($form->_status!='show') {?>
					<td class="littletableheaderb">&nbsp;              </td>
					<?php } ?>
				</tr>
				<?php 
				for($i=0;$i<$form->max_rel_count['bi_ittrasla'];$i++) {
					$obj0="itbien_$i";
					$obj1="itcodigo_$i"; 
					$obj2="itgrupo_$i";
					$obj3="itsubgrupo_$i";
					$obj4="itseccion_$i";
					$obj5="itnumerob_$i";
					$obj6="itdescrip_$i";
					$obj7="itmonto_$i";
					$obj8="itconcepto_$i";
				?>
					<tr id='tr_bi_ittrasla_<?=$i ?>'>
					<td class="littletablerow"><?=$form->$obj0->output ?></td>
					<td class="littletablerow"><?=$form->$obj8->output ?></td>
					<td class="littletablerow"><?=$form->$obj1->output ?></td>
					<td class="littletablerow"><?=$form->$obj2->output ?></td>
					<td class="littletablerow"><?=$form->$obj3->output ?></td>
					<td class="littletablerow"><?=$form->$obj4->output ?></td>
					<td class="littletablerow"><?=$form->$obj5->output ?></td>
					<td class="littletablerow"><?=$form->$obj6->output ?></td>
					<td class="littletablerow" align="right"><?=$form->$obj7->output ?></td>
					<?php if($form->_status!='show') {?>
					<td class="littletablerow"><a href=# onclick='del_bi_ittrasla(<?=$i ?>);return false;'><?=image('delete.jpg','Haz Click aqui para eliminar este item.</br>De esta manera se eliminara toda la fila seleccionada',array("border"=>0,"class"=>"eliminait"))?></a></td>
					<?php } ?>
					</tr>
			<?php 
				} ?>
		<tr id='__UTPL__'>
		<td class="littletablefooterb" colspan="9">&nbsp;</td>
		<?php 
		if($form->_status!='show') {?>
			<td class="littletablefooterb" align="right">&nbsp;</td>
		<?php 
		} ?>
		</tr>
		</table>
		<?php echo $form_end     ?>
		<?php echo $container_bl ?>
		
		<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>
