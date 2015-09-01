<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:


foreach($form->detail_fields['r_concit'] AS $ind=>$data)
	$campos[]=$data['field'];
	
$campos='<tr id="tr_r_concit_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_r_concit(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if($form->_status!='show'){
	
	?>
	
<style >
</style>

	<script language="javascript" type="text/javascript">
	r_concit_cont=<?=$form->max_rel_count['r_concit'] ?>;
	
	
	$(document).ready(function() {
		
		
	});
	
	function add_r_concit(){
		var htm = <?=$campos ?>;
		can = r_concit_cont.toString();
		con = (r_concit_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		r_concit_cont=r_concit_cont+1;
	}

	function del_r_concit(id){
		id = id.toString();
		$('#tr_r_concit_'+id).remove();
	}
	</script>
	<?php  
	} 
	?>
	<script language="javascript" type="text/javascript">
	
	</script>
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
			    <td colspan=6 class="bigtableheader">Concepto de Recaudacion <?=$form->id->output ?></td>
			  </tr>
			   <tr>
			    <td class="littletablerowth">         <?=$form->id_presup->label ?>*&nbsp;</td>
			    <td class="littletablerow" colspan=3 ><?=$form->id_presup->output ?>&nbsp;</td>
			  </tr>
			  <tr>
			    <td class="littletablerowth">         <?=$form->denomi->label ?>*&nbsp;</td>
			    <td class="littletablerow" colspan=3 ><?=$form->denomi->output ?>&nbsp;</td>
			  </tr>

	    	</table >
			<table class="table_detalle">
     		<tr>
				<th class="littletableheaderb" >Ref.        </th>
				<th class="littletableheaderb" >A&ntilde;o        </th>
				<th class="littletableheaderb" >Frecuencia        </th>
				<th class="littletableheaderb" >Valor Frecuencia   </th>
				<th class="littletableheaderb" >Acronimo          </th>
				<th class="littletableheaderb" >Denominacion      </th>
				<th class="littletableheaderb" >Requiere          </th>
				<th class="littletableheaderb" >Modo              </th>
				<th class="littletableheaderb" >Formula           </th>
				<?php if($form->_status!='show') {?>
				<th class="littletableheaderb">&nbsp;</td>
				<?php } ?>
			  </tr>
			  <?php 
			  
			  for($i=0;$i<$form->max_rel_count['r_concit'];$i++) {
					$obj0="itano_$i";
					$obj1="itacronimo_$i";
					$obj2="itdenomi_$i";				
					$obj3="itrequiere_$i";
					$obj4="itformula_$i";
					$obj5="itid_$i";
					$obj6="itfrecuencia_$i";
					$obj7="itfreval_$i";
					$obj8="itmodo_$i";
			  ?>
			  <tr id='tr_r_concit_<?=$i ?>'>
				<td class="littletablerow"><?=$form->$obj5->output ?></td>
			    <td class="littletablerow"><?=$form->$obj0->output ?></td>
			    <td class="littletablerow"><?=$form->$obj6->output ?></td>
			    <td class="littletablerow"><?=$form->$obj7->output ?></td>
			    <td class="littletablerow"><?=$form->$obj1->output ?></td>
			    <td class="littletablerow"><?=$form->$obj2->output ?></td>
			    <td class="littletablerow"><?=$form->$obj3->output ?></td>
			    <td class="littletablerow"><?=$form->$obj8->output ?></td>
			    <td class="littletablerow"><?=$form->$obj4->output ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_r_concit(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			  
			   <tr id='__UTPL__'>
			    <td class="littletablefooterb" colspan="89">&nbsp;</td>
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


