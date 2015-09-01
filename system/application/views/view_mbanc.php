<?php
$status   = $form->get_from_dataobjetct('status');

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='create' || $form->_status=='modify' )
	$container_mb=join("&nbsp;", $form->_button_status[$form->_status]["MB"]);
	//$container_mb = '';
else
	$container_mb = '';

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['mbanc'] AS $ind=>$data)
	$campos2[]=$data['field'];
$campos2='<tr id="tr_mbanc_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos2).'</td>';
$campos2.=' <td class="littletablerow"><a href=# onclick="del_mbanc(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos2=$form->js_escape($campos2);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if(true){//$form->_status!='show'
$this->datasis->get_uri();
	$uri  =$this->datasis->get_uri();
	$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='odirect' AND uri='$uri'");
	$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
	?>
	<script language="javascript" type="text/javascript">
	mbanc_cont=<?=$form->max_rel_count['mbanc'] ?>;

  $.ajaxSetup({
   'beforeSend' : function(xhr) {
    xhr.overrideMimeType('text/html; charset=<?=$this->config->item('charset'); ?>');
    }
  });

	$(function() {
		$(".inputnum").numeric(".");
		
		$(document).keydown(function(e){
					//alert(e.which);
		if (18 == e.which) {
			com=true;
			//c = String.fromCharCode(e.which);
			return false;
		}
		if (com && (e.which == 61 || e.which == 107)) {
		  add_mbanc();
		  a=mbanc_cont-1;
		  $("#chequem_"+a).focus();
		  
			//alert("agrega linea");
				com=false;
				return false;
			}else if (com && e.which != 16 && e.which == 17){
				com=false;
			}
			return true;
		});
	});

	function cal_totalch(){
		tot = 0;
		for(i=0;i<mbanc_cont;i++){
			id=i.toString();
			status = $("#statusm_"+id).val();
			total = 0;
			//if(status=='E1')
				total  = parseFloat($("#montom_"+id).val());
			if(isNaN(total))total=0;
			tot+=total;
		}		
		if(!isNaN(tot))$("#totalch").val(Math.round(tot*100)/100);
	}

	function add_mbanc(){
		var htm = <?=$campos2 ?>;
		can = mbanc_cont.toString();
		con = (mbanc_cont+1).toString();
		cin = (mbanc_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__MBANCUTPL__").before(htm);
		$("#montom_"+can).numeric(".");
		mbanc_cont=mbanc_cont+1;
		
		ante=$("#benefim_"+cin).attr('selectedIndex');
		$("#benefim_"+can).attr('selectedIndex',ante);
		
		ante=$("#benefim_"+cin).val();
		$("#benefim_"+can).val(ante);
		
		ante=$("#observam_"+cin).attr('selectedIndex');
		$("#observam_"+can).attr('selectedIndex',ante);
		
		ante=$("#observam_"+cin).val();
		$("#observam_"+can).val(ante);
		
		ante=$("#codbancm_"+cin).val();
		$("#codbancm_"+can).val(ante);
		
		ante=$("#fecham_"+cin).val();
		$("#fecham_"+can).val(ante);
		
		ante=$("#fecha2m_"+cin).val();
		$("#fecha2m_"+can).val(ante);
		
		ante=$("#tipo_docm_"+cin).attr('selectedIndex');
		$("#tipo_docm_"+can).attr('selectedIndex',ante);
		
		ante=$("#destino_"+cin).attr('selectedIndex');
		$("#destino_"+can).attr('selectedIndex',ante);
	}

	function del_mbanc(id){
		id = id.toString();
		$("#montom_"+id).val('0');
		
		$('#tr_mbanc_'+id).remove();
		$('#tr_mbanc'+id).remove();		
		cal_total();
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
			<table width="100%" bgcolor="#F4F4F4"  style="margin:0;width:100%;">
			  <tr>
			    <td colspan=6 class="bigtableheader"><?=$this->tits?> Nro. <?php  echo $form->numero->output ?></td>
			  </tr>
	    	</table><br />
		<br />
			<table width='100%' bgcolor="#E2E0F4">
			<tr>
     			<th class="littletableheaderb" colspan="<?=(($form->_status=='create'?11:12))?>">MOVIMIENTOS BANCARIOS </th>
                        <tr>
			<th class="littletableheaderb">             Ref.                    </th>
     			<th class="littletableheaderb">             Estado                  </th>
     			<th class="littletableheaderb">             Banco                   </th>
     			<th class="littletableheaderb">             Destino                 </th>
     			<th class="littletableheaderb">             Tipo                    </th>
			<th class="littletableheaderb"              >Fecha                  </th>
			<th class="littletableheaderb"align='center'>A nombre de            </th>
			<th class="littletableheaderb"align='center'> Fecha Documento       </th>
			<th class="littletableheaderb">             Cheque                  </th>
			<th class="littletableheaderb"align='right'>Monto                   </th>
                        <th class="littletableheaderb"             >Concepto                </th>
                        <? if($form->_status=='show'){
                        ?>
                        <th class="littletableheaderb">&nbsp;</th>
                        <?php 
                        }else{
                        ?>
                        <th class="littletableheaderb">&nbsp;</th>
                        <?php 
                        }?>			    
			    
			  </tr>
			  <?php
			  for($i=0;$i<$form->max_rel_count['mbanc'];$i++) {
		  		$obj0 = "itstatusm_$i";
			  	$obj1 = "itcodbancm_$i"; 
				$obj2 = "ittipo_docm_$i";     			    
				$obj3 = "itchequem_$i";
				$obj4 = "itfecham_$i";
				$obj9 = "itfecha2m_$i";
				$obj5 = "itmontom_$i";
				$obj7 = "itbenefim_$i";
				$obj6 = "itobservam_$i";
				$obj8 = "itdestino_$i";
				$obj10= "itidm_$i";
			  ?>
			  <tr id='tr_mbanc_<?=$i ?>'>
			  <td class="littletablerow"               ><?=$form->$obj10->output  ?> </td>
			  <?php 
			    $mid     = $form->_dataobject->get_rel('mbanc','id',$i);?>
			    <?php if ($status=='D2' && $form->_status=='show' ) {?>
			    <td class="littletablerow"><a href="<?=site_url($this->datasis->traevalor('LINKCHEQUE','forma/ver/CHEQUE/','link a usar para el formato del cheque').'/'.$mid)?>" >IMPRIMIR</a></td>
			    <?php }elseif ( $form->_status=='show' ){
			    ?>
			    <td class="littletablerow"><?=$form->$obj0->output  ?></td>
			    <?php 
			    }else{
			    ?>
			    <td class="littletablerow"               ><?=$form->$obj0->output  ?> </td>
			    <?php 
			    } ?>
			    <td class="littletablerow"               ><?=$form->$obj1->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj8->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj2->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj4->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj7->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj9->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj3->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj5->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj6->output  ?> </td>
			    <?php
			     if ( $form->_status=='show' ) {?>
			    <td class="littletablerow"></td>
			    <?php }else{
			    ?>
			    <?php 
			    } ?>
			    
			    <?php if ($form->_status=='create' ) {?>
			    <td class="littletablerow"><a href=# onclick='del_mbanc(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			    <?php if ($form->_status=='modify' ) {?>
			    <td class="littletablerow"><a href=# onclick='del_mbanc(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			   
			  <tr id='__MBANCUTPL__'>
			    <td class="littletablefooterb" align='right' colspan="<?=(($form->_status=='create'?9:9))?>"><?=$form->totalch->label?></td>
			    <td class="littletablefooterb" align='right'><?=$form->totalch->output?></td>
			    <td class="littletablefooterb" align='right'>&nbsp;</td>
			    <td class="littletablefooterb" align='right'>&nbsp;</td>
			  </tr>
			   <tr>
			  	<td colspan="4">
			  		<?php echo $container_mb ?>
			  	</td>
			  </tr>
	    </table>
		<?php echo $form_end     ?>
		
		<td>
	<tr>
<table>
<?php endif; ?>
