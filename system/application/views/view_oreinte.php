<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['itoreinte'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_itoreinte_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itoreinte(<#i#>);return false;">Eliminar</a></td></tr>';

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if($form->_status!='show'){
	$uri  =$this->uri->uri_string();
	$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='itodirect' AND uri='$uri'");
	$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>
	<script language="javascript" type="text/javascript">
	itoreinte_cont=<?=$form->max_rel_count['itoreinte'] ?>;
	
	$(function() {
		$(".inputnum").numeric(".");
	});
	
	function modbusdepen(i){
		var id = i.toString();
		var odirect   =$("#odirect").val();
		
	
		if(odirect.length == 0){
			alert('Debe Seleccionar primero un fondo y una unidad administrativa');
			return false;
		}
		var link='<?=$modblink2 ?>'+'/'+odirect;
		link =link.replace(/<#i#>/g,id);
		vent=window.open(link,'ventbuscaritodirect','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5'); 
		vent.focus(); 
	
		document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscaritodirect\');vent.close();');
		
	}
	
	
	function add_itoreinte(){
		var htm = '<?=$campos ?>';
		can = itoreinte_cont.toString();
		con = (itoreinte_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#reinte_"+can).numeric(".");
		itoreinte_cont=itoreinte_cont+1;
	}

	function del_itoreinte(id){
		id = id.toString();
		$('#tr_itoreinte_'+id).remove();
	}
	</script>
	<?php  
	} 
	?>
	
<table align='center'width="98%" >
	<tr>
		<td align=right>
			<?php echo $container_tr?>
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
			    <td class="littletablerow"  >&nbsp; </td>
			    <td class="littletablerowth"><?=$form->fecha->label       ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fecha->output      ?>&nbsp; </td>
				</tr>
			  <tr>
			    <td class="littletablerowth">               <?=$form->odirect->label  ?>*&nbsp;</td>
			    <td class="littletablerow" id='td_estadmin'><?=$form->odirect->output ?>&nbsp;</td>
			    <td class="littletablerowth">                                            &nbsp;</td>
			    <td class="littletablerow" >                                             &nbsp;</td>
			  </tr>

			  <tr>
			    <td class="littletablerowth"><?=$form->observa->label ?>&nbsp;</td>
			    <td class="littletablerow" colspan=3 ><?=$form->observa->output ?>&nbsp;</td>
			  </tr>

	    	</table><br />
			<table width='100%'>
     		<tr>
     			<td class="littletableheaderb">Partida           </td>
					<td class="littletableheaderb">Descripci&oacute;n</td>
     			<td class="littletableheaderb">Monto            </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletableheaderb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <?php 
			  for($i=0;$i<$form->max_rel_count['itoreinte'];$i++) {
		  		$obj0="itpartida_$i"; 
					$obj1="itdescripcion_$i";
					$obj2="itreinte_$i";
			  ?>
			  <tr id='tr_itoreinte_<?=$i ?>'>
			    <td class="littletablerow">             <?=$form->$obj0->output ?></td>
			    <td class="littletablerow">             <?=$form->$obj1->output ?></td>
			    <td class="littletablerow"align='right'><?=$form->$obj2->output ?></td>
			   
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_itoreinte(<?=$i ?>);return false;'>Eliminar</a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
				
			  <tr id='__UTPL__'>
			    <td class="littletablefooterb" align='right' colspan="2"><?=$form->total->label   ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->total->output  ?></td>
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
