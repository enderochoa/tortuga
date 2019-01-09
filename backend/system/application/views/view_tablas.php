<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['doc_campos'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_doc_campos_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos.=' <td class="littletablerow"><a href=# onclick="del_doc_campos(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin; 
if(true){
	$uri  =$this->datasis->get_uri();
	?>
	<script language="javascript" type="text/javascript">
	campos_cont=<?=$form->max_rel_count['doc_campos'] ?>;

	function add_doc_campos(){
		var htm = <?=$campos ?>;
		can = campos_cont.toString();
		con = (campos_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		campos_cont=campos_cont+1;
	}

	function del_doc_campos(id){
		id = id.toString();
		$('#tr_doc_campos_'+id).remove();		
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
			    <td colspan=6 class="bigtableheader"><?=$title ?> Nro. <?=$form->nombre->output ?></td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->referen->label ?>&nbsp;</td> 
			    <td class="littletablerow"  ><?=$form->referen->output?>&nbsp; </td>
			  </tr>
	    	</table><br />
			<table width='100%' bgcolor="#FFEAEB" class="table_detalle">
			<tr>
			<tr>
     			<th class="littletableheaderb" colspan="<?=($form->_status=='show'?7:8)?>">Descripcion de campos</th>
			</tr>
     		<tr>
     			<td class="littletableheaderb">Campo  </td>
			    <td class="littletableheaderb">Type   </td>
			    <td class="littletableheaderb">Null   </td>
			    <td class="littletableheaderb">Key    </td>
			    <td class="littletableheaderb">Default</td>
				<td class="littletableheaderb">Extra  </td>
				<td class="littletableheaderb">Comentario</td>
			<?php if($form->_status!='show') {?>
			<td class="littletableheaderb">&nbsp;</td>
			<?php } ?>
			</tr>
			
			  <?php
			for($i=0;$i<$form->max_rel_count['doc_campos'];$i++) {
				$obj0="itcampo_$i";
				$obj1="ittype_$i";
				$obj2="itnull_$i";
				$obj3="itkey_$i";
				$obj4="itdefault_$i";
				$obj5="itextra_$i";
				$obj6="itdcomment_$i";
			  ?>
			  <tr id='tr_doc_campos_<?=$i ?>'>
			    <td class="littletablerow"><?=$form->$obj0->output ?></td>
			    <td class="littletablerow"><?=$form->$obj1->output ?></td>
			    <td class="littletablerow"><?=$form->$obj2->output ?></td>
			    <td class="littletablerow"><?=$form->$obj3->output ?></td>
			    <td class="littletablerow"><?=$form->$obj4->output ?></td>
			    <td class="littletablerow"><?=$form->$obj5->output ?></td>
			    <td class="littletablerow"><?=$form->$obj6->output ?></td>
	
			  	<?php if ($form->_status=='create' || $form->_status=='modify') {?>
			    <td class="littletablerow"><a href=# onclick='del_doc_campos(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			  <tr id='__UTPL__'>
			    
			  </tr>
	    </table>
	 		<?php echo $form_end     ?>
	 		<?php echo $container_bl ?>
			<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>
