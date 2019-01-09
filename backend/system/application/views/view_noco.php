<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:


foreach($form->detail_fields['itnoco'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_itnoco_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itnoco(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos =$this->db->escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin; 
if($form->_status!='show'){
		$uri  =$this->uri->uri_string();
?>

<script language="javascript" type="text/javascript">
itnoco_cont=<?=$form->max_rel_count['itnoco'] ?>;

$(function() {
	$(".inputnum").numeric(".");
	
	$("#codigoadm").change(function(){
		$.post("<?=site_url('presupuesto/presupuesto/get_tipo')?>",{ codigoadm:$("#codigoadm").val() },function(data){$("#fondo").html(data);})
	});
	
});

function post_modbus(id){
	
	id = id.toString();
	descripp = $("#descripp_"+id).val();
	tipop=$("#tipop_"+id).val();
	formulap=$("#formulap_"+id).val();
	concepto=$("#concepto_"+id).val();
	
	$("#descripp_"+id+"_val").html(descripp);
	$("#tipop_"+id+"_val").html(tipop);
	$("#formulap_"+id+"_val").html(formulap);
	$("#concepto_"+id+"_val").html(concepto);
}

function add_itnoco(){
	var htm = <?=$campos ?>;
	can = itnoco_cont.toString();
	con = (itnoco_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	itnoco_cont=itnoco_cont+1;
}
					
function del_itnoco(id){
	id = id.toString();
	$('#tr_itnoco_'+id).remove();
}

function ordenar(){
	//arr=$('input[name^="concepto_"]');
	//var lhtml= [];
	//var lid= [];
	//var lorden= [];
	//jQuery.each(arr, function() {
	//	nom=this.name;
	//	
	//	pos=this.name.lastIndexOf('_');
	//	
	//	id      = this.name.substring(pos+1);
	//	
	//	orden=$('#orden_'+id).val();
	//	
	//	idi=parseInt(id);
	//	lhtml[idi]=$('#tr_itnoco_'+id).html();
	//	lid[idi]=orden;
	//	lorden[idi]=orden;
	//	//$('#tr_itnoco_'+id).remove();
	//});
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
			    <td colspan=6 class="bigtableheader">Contrato. <?php // echo str_pad(trim($form->codigo->output),8,0,STR_PAD_LEFT) ?></td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->codigo->label  ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->codigo->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->tipo->label  ?> &nbsp;</td>
			    <td class="littletablerow"  ><?=$form->tipo->output ?> &nbsp;</td>
			  </tr>
			  <tr>
			    <td class="littletablerowth" ><?=$form->nombre->label  ?>*&nbsp;</td>
			    <td class="littletablerow"  colspan="3"><?=$form->nombre->output ?>&nbsp; </td>
			    
			  </tr>
			  <tr>
			    <td class="littletablerowth">               <?=$form->observa1->label  ?>&nbsp;</td>
			    <td class="littletablerow" colspan="3"><?=$form->observa1->output ?>&nbsp; </td>
			  </tr>
			  <tr>
			    <td class="littletablerowth">               <?=$form->observa1->label  ?>&nbsp;</td>
			    <td class="littletablerow" colspan="3"><?=$form->observa1->output ?>&nbsp; </td>
			  </tr>
	    </table><br />

			<table width='100%'>
     		<tr>
				<td class="littletableheaderb">Orden </td>
     			<td class="littletableheaderb">Conceptos </td>
     			<td class="littletableheaderb">Descripci&oacute;n </td>
			    <td class="littletableheaderb">Tipo        </td>
			    <td class="littletableheaderb">Formula     </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletableheaderb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <?php for($i=0;$i<$form->max_rel_count['itnoco'];$i++) {
				  $obj0="itorden_$i";
				  $obj1="itconcepto_$i";
				  $obj2="itdescripp_$i"; 
					$obj3="ittipop_$i";
					$obj4="itformulap_$i";
			  ?>
			  <tr id='tr_itnoco_<?=$i ?>'>
				<td class="littletablerow"><?=$form->$obj0->output ?></td>
			  	<td class="littletablerow"><?=$form->$obj1->output ?></td>
			    <td class="littletablerow"><?=$form->$obj2->output ?></td>
			    <td class="littletablerow"><?=$form->$obj3->output ?></td>
			    <td class="littletablerow"><?=$form->$obj4->output ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_itnoco(<?=$i ?>);return false;'><?php echo image('delete.jpg','#',array("border"=>0)) ?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>

			  <tr id='__UTPL__'>
				  <td class="littletablefooterb" align="right">&nbsp;</td>
			  	<td class="littletablefooterb" align="right">&nbsp;</td>
			    <td class="littletablefooterb" align="right">&nbsp;</td>
			    <td class="littletablefooterb" align="right">&nbsp;</td>
			    <td class="littletablefooterb" align="right">&nbsp;</td>    			    
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
