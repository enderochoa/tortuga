<?php
$status   = $form->get_from_dataobjetct('status');

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if (($form->_status=='modify') && ($status == 'D1' ))
	$container_pa=join("&nbsp;", $form->_button_status[$form->_status]["PA"]);
elseif ($form->_status=='create')
	$container_pa=join("&nbsp;", $form->_button_status[$form->_status]["PA"]);
else
	$container_pa = '';

if ($form->_status=='create' || $form->_status=='modify' )
	$container_mb=join("&nbsp;", $form->_button_status[$form->_status]["MB"]);
	//$container_mb = '';
else
	$container_mb = '';

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['pades'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_pades_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos.=' <td class="littletablerow"><a href=# onclick="del_pades(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos=$form->js_escape($campos);

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
	$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_pagos_encab' AND uri='$uri'");
	$modblink2=site_url('/buscar/index/'.$idt.'/<#i#>');
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
	pades_cont=<?=$form->max_rel_count['pades'] ?>;
	mbanc_cont=<?=$form->max_rel_count['mbanc'] ?>;
	com=false;

	$(document).keydown(function(e){
		if (18 == e.which) {
			com=true;
			return false;
		}
		if (com && (e.which == 61 || e.which == 107)) {
			add_pades();
			a=pades_cont-1;

			$("#pago_"+a).focus();
			com=false;
			return false;
		}else if (com && e.which != 16 && e.which == 17){
			com=false;
		}
		return true;
	});
	
	
	function op_desde_hasta(){
		d=false;
		h=false;
		d=prompt("Agregar DESDE Orden de Pago");
		h=prompt("Agregar HASTA Orden de Pago");
		c=$("#cod_prov").val();
		$.post("<?=site_url('tesoreria/desem/op_desde_hasta')?>",{ desde:d,hasta:h,cod_prov:c },function(data){
			datos=jQuery.parseJSON(data);
			var j;
			jQuery.each(datos, function(i, val) {
				
				add_pades();
				j=pades_cont;
				j=j-1;
				j = j.toString();
				
				$("#pago_"+j         ).val(val.numero      );
				$("#imptimbreo_"+j   ).val(val.imptimbre   );
				$("#impmunicipalo_"+j).val(val.impmunicipal);
				$("#crso_"+j         ).val(val.crs         );
				$("#totalo_"+j       ).val(val.total       );
				$("#total2o_"+j      ).val(val.total2      );
				$("#reteno_"+j       ).val(val.reten       );
				$("#reteivao_"+j     ).val(val.reteiva     );
				$("#otrasreteo_"+j   ).val(val.otrasrete   );
				
				c=val.cod_prov;
			});
			
			$("#cod_prov").val(c);
			cal_total();
		});
	 }

  $.ajaxSetup({
   'beforeSend' : function(xhr) {
    xhr.overrideMimeType('text/html; charset=<?=$this->config->item('charset'); ?>');
    }
  });

  function auto(id){
	  	$( "#pago_"+id ).autocomplete({
			source: function(request, response) {
				$.ajax({ url: "<?php echo site_url('tesoreria/desem/autocompleteopagopp'); ?>",
					data:
					{
						term: $("#pago_"+id).val(),
						cod_prov: $("#cod_prov").val(),
					},
					dataType: "json",
					type: "POST",
					success: function(data){
						response(data);
					}
				});
			},
			focus: function( event, ui ){
				return false;
			},
			select: function( event, ui ){
				$( "#pago_"+id         ).val( ui.item.numero       );
				$( "#total2o_"+id      ).val( ui.item.total2       );
				$( "#otrasreteo_"+id   ).val( ui.item.otrasrete    );
				$( "#crso_"+id         ).val( ui.item.crs          );
				$( "#imptimbreo_"+id   ).val( ui.item.imptimbre    );
				$( "#impmunicipalo_"+id).val( ui.item.impmunicipal );
				$( "#reteno_"+id       ).val( ui.item.reten        );
				$( "#reteivao_"+id     ).val( ui.item.reteiva      );
				$( "#totalo_"+id       ).val( ui.item.total        );
				$( "#cod_prov"         ).val( ui.item.cod_prov     );
				$( "#temp"             ).val( ui.item.observa      );
				cal_total();
				cal_observa();
				cal_nprov();
				return false;
			},
			minLength: 1
		}).data( "autocomplete" )._renderItem = function( ul, item ) {
			return $( "<li></li>" )
			.data( "item.autocomplete", item )
			.append( "<a>" + item.numero + "</a>" )
			.appendTo( ul );
		};
	}

	$(function() {
		$(".inputnum").numeric(".");
		$("#temp").hide();
		auto(0);


	});

	function ultimoch(i){
		id=i.toString();
		$.ajax({
			type: "POST",
			url: "<?=site_url("tesoreria/desem/ultimoch")?>",
			data:"codbanc="+$("#codbancm_"+id).val(),
			success: function(msg){
				<?php 
				IF($this->datasis->traevalor('DESEM_PREDICE_CHEQUE','N')=='S'){
				?>
				$("#chequem_"+id).val(msg);
				<?php 
				}
				?>
				
			}
		});
		tot=$("#total").val();
		monto=parseFloat($("#montom_"+id).val());
		if(!(monto>0)){
		 $("#montom_"+id).val(Math.round(tot*100)/100);
		}

	}

	function cal_nombrech(i){
		id=i.toString();
		benefi = $("#benefim_"+id).val();
		if(benefi==''){
			nombrep = $("#nombrep").val();
			$("#benefim_"+id).val(nombrep);
		}
	}

	function cal_nprov(){
		codigo = $("#cod_prov").val();
		$.ajax({
			type: "POST",
			url: "<?=site_url("presupuesto/sprv/nprov")?>",
			data:"cod_prov="+codigo,
			success: function(data){
				$("#nombrep").val(data);
			}
		});
	}

	function cal_total(){
		tot =tcrs=tmunicipal=ttimbre=tislr=triva=ttotal2=otrasrete2=0;
		for(i=0;i<pades_cont;i++){
			id=i.toString();
			total       =parseFloat($("#totalo_"+id).val());
                        crs         =parseFloat($("#crso_"+id).val());
                        municipal   =parseFloat($("#impmunipalo_"+id).val());
                        timbre      =parseFloat($("#imptimbreo_"+id).val());
                        islr        =parseFloat($("#reteno_"+id).val());
                        riva        =parseFloat($("#reteivao_"+id).val());
                        total2      =parseFloat($("#total2o_"+id).val());
                        otrasrete   =parseFloat($("#otrasreteo_"+id).val());

			if(isNaN(total))total=0;
			tot+=Math.round(total*100)/100;
                        if(isNaN(crs))crs=0;
			tcrs+=Math.round(crs*100)/100;
                        if(isNaN(municipal))municipal=0;
			tmunicipal+=Math.round(municipal*100)/100;
                        if(isNaN(timbre))timbre=0;
			ttimbre+=Math.round(timbre*100)/100;
                        if(isNaN(islr))islr=0;
			tislr+=Math.round(islr*100)/100;
                        if(isNaN(riva))riva=0;
			triva+=Math.round(riva*100)/100;
                        if(isNaN(total2))total2=0;
			ttotal2+=Math.round(total2*100)/100;
			if(isNaN(otrasrete))otrasrete=0;
			otrasrete2+=Math.round(otrasrete*100)/100;
		}
		if(!isNaN(tot))
		    $("#total").val(Math.round(tot*100)/100);
                if(!isNaN(tcrs))
		    $("#tcrs").val(Math.round(tcrs*100)/100);
                if(!isNaN(tmunicipal))
		    $("#tmunicipal").val(Math.round(tmunicipal*100)/100);
                if(!isNaN(ttimbre))
		    $("#ttimbre").val(Math.round(ttimbre*100)/100);
                if(!isNaN(tislr))
		    $("#tislr").val(Math.round(tislr*100)/100);
                if(!isNaN(triva))
		    $("#triva").val(Math.round(triva*100)/100);
                if(!isNaN(ttotal2))
		    $("#total2").val(Math.round(ttotal2*100)/100);
		if(!isNaN(otrasrete2))
		    $("#totrasrete").val(Math.round(otrasrete2*100)/100);

		cal_totalch();
	}

	function cal_observa(){
		observa = $("#observa").val()+$("#temp").val();
		$("#observam_0").val(observa);

		cal_total();
	}

	function cal_totalch(){
		tot = 0;
		for(i=0;i<mbanc_cont;i++){
			id=i.toString();
			status = $("#statusm_"+id).val();
			total = 0;
			//if(status=='E1')
			total  = parseFloat($("#montom_"+id).val());
			
			tipo_doc=$("#tipo_docm_"+id).val();
			if(isNaN(total))total=0;
			
			
			if(tipo_doc=='DP' || tipo_doc=='NC'){
				tot-=total;
			}else{
				tot+=total;
			}
		}
		if(!isNaN(tot))$("#totalch").val(Math.round(tot*100)/100);
	}

	function cal_observa(){
		observa = ''+$("#observam_0").val()+$("#temp").val();
		$("#observam_0").val(observa);
		cal_total();
	}

	function modbusdepen(i){
		var id = i.toString();
		var cod_prov =$("#cod_prov").val();
		if(cod_prov=="")cod_prov=".....";

		var link='<?=$modblink2 ?>'+'/'+cod_prov;
		link =link.replace(/<#i#>/g,id);
		vent=window.open(link,'ventbuscarocompra','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5');
		vent.focus();
		document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarocompra\');vent.close();');
	}

	function add_pades(){
		var htm = <?=$campos ?>;
		can = pades_cont.toString();
		con = (pades_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		auto(pades_cont);
		pades_cont=pades_cont+1;
		cal_total();
	}

	function del_pades(id){
		id = id.toString();
		$("#totalo_"+id).val('0');
		$('#tr_pades_'+id).remove();
		cal_total();
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
	}

	function del_mbanc(id){
		id = id.toString();
		$("#montom_"+id).val('0');

		$('#tr_mbanc_'+id).remove();
		$('#tr_mbanc'+id).remove();
		cal_total();
	}

	function btn_anulaf(i){
		if(!confirm("Esta Seguro que desea Anular el Desembolso"))
			return false;
		else
			window.location='<?=site_url($this->url.'anular')?>/'+i
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
			    <td colspan=6 class="bigtableheader"><?=$this->tits?> Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
			  </tr>
			  <tr>
			    <td class="littletablerowth"><?=$form->fdesem->label  ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fdesem->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?//=$form->fdesem->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?//=$form->fdesem->output?>&nbsp; </td>
			  </tr>
				<tr>
			    <td class="littletablerowth">            <?=$form->cod_prov->label   ?>*&nbsp;</td>
			    <td class="littletablerow" colspan="3"  ><?=$form->cod_prov->output.$form->nombrep->output  ?>&nbsp; </td>
			  </tr>
	    	</table><br />
			<table width='100%' bgcolor="#FFEAEB">
			<tr>
			<tr>
     			<th class="littletableheaderb" colspan="9">ORDENES DE PAGO    </tH>

			<?php if($form->_status!='show') {?>
			<td class="littletableheaderb">&nbsp;</td>
			<?php } ?>
			</tr>
     		<tr>
     			<th class="littletableheaderb">Orden de Pago                     </th>
			<th class="littletableheaderb" align='right'>Total Facturado     </th>
			<th class="littletableheaderb" align='right'>Otras Retenciones   </th>
			<th class="littletableheaderb" align='right'>I. C.R.S.           </th>
			<th class="littletableheaderb" align='right'>I. 1X1000           </th>
			<th class="littletableheaderb" align='right'>I. Municipal        </th>
			<th class="littletableheaderb" align='right'>Retenci&oacute;n ISLR</th>
			<th class="littletableheaderb" align='right'>Retenci&oacute;n IVA </th>
			<th class="littletableheaderb" align='right'>Total a Pagar        </th>
			<?php if($form->_status!='show') {?>
			<th class="littletableheaderb">&nbsp;</td>
			<?php } ?>
			</tr>

			  <?php
			  for($i=0;$i<$form->max_rel_count['pades'];$i++) {
		  		$obj0="itpago_$i";
		  		$obj1="ittotal2o_$i";
		  		$obj7="itcrso_$i";
                                $obj2="itimptimbreo_$i";
                                $obj3="itimpmunicipalo_$i";
                                $obj4="itreteno_$i";
                                $obj5="itreteivao_$i";
                                $obj6="ittotalo_$i";
                                $obj8="itotrasreteo_$i";
			  ?>
			<tr id='tr_pades_<?=$i ?>'>
			    <td class="littletablerow">              <?=$form->$obj0->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj1->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj8->output ?></td>
			    <td class="littletablerow" align='right'>
			    	<? $crso=$form->_dataobject->get_rel_pointer('pades','crso',$i);
			    		$pago=$form->_dataobject->get_rel('pades','pago',$i);

			    	if ($form->_status=='show' && $crso >0){
			    		?>
			    		<a href="<?=site_url("forma/ver/IMPCRS/$pago")?>" ><?=$crso?></a>
			    		<?php
			    		}else{
			    			echo $form->$obj7->output;
			    		}
			    		?>
			    </td>

			    <td class="littletablerow" align='right'>
			    	<? $imptimbreo=$form->_dataobject->get_rel_pointer('pades','imptimbreo',$i);
			    		$pago=$form->_dataobject->get_rel('pades','pago',$i);

			    	if ($form->_status=='show' && $imptimbreo >0){
			    		?>
			    		<a href="<?=site_url("forma/ver/IMPTIMBRE/$pago")?>" ><?=$imptimbreo?></a>
			    		<?php
			    		}else{
			    			echo $form->$obj2->output;
			    		}
			    		?>
			    </td>
			    <td class="littletablerow" align='right'>
			    	<? $impmunicipalo=$form->_dataobject->get_rel_pointer('pades','impmunicipalo',$i);
			    		$pago=$form->_dataobject->get_rel('pades','pago',$i);

			    	if ($form->_status=='show' && $impmunicipalo >0){
			    		?>
			    		<a href="<?=site_url("forma/ver/IMPMUNICIP/$pago")?>" ><?=$impmunicipalo?></a>
			    		<?php
			    		}else{                                $obj2="itimptimbreo_$i";
                                $obj3="itimpmunicipalo_$i";
                                $obj4="itreteno_$i";
                                $obj5="itreteivao_$i";
                                $obj6="ittotalo_$i";
			    			echo $form->$obj3->output;
			    		}
			    		?>
			    </td>
			    <td class="littletablerow" align='right'>
			    	<? $reteno=$form->_dataobject->get_rel_pointer('pades','reteno',$i);
			    		$pago=$form->_dataobject->get_rel('pades','pago',$i);

			    	if ($form->_status=='show' && $reteno >0){
			    		?>
			    		<a href="<?=site_url("forma/ver/ISLRM/$pago")?>" ><?=$reteno?></a>
			    		<?php
			    		}else{
			    			echo $form->$obj4->output;
			    		}
			    		?>
			    </td>
			    <td class="littletablerow" align='right'>
			    	<? $reteivao=$form->_dataobject->get_rel_pointer('pades','reteivao',$i);
			    		$pago=$form->_dataobject->get_rel('pades','pago',$i);

			    	if ($form->_status=='show' && $reteivao >0){
			    		?>
			    		<a href="<?=site_url("forma/ver/RIVAM/$pago")?>" ><?=$reteivao?></a>
			    		<?php
			    		}else{
			    			echo $form->$obj5->output;
			    		}
			    		?>
			    </td>
			    <td class="littletablerow" align='right'><?=$form->$obj6->output ?></td>

			  	<?php if ($form->_status=='create' ) {?>
			    <td class="littletablerow"><a href=# onclick='del_pades(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			    <?php if ($status=='D1' && $form->_status=='modify' ) {?>
			    <td class="littletablerow"><a href=# onclick='del_pades(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			  <tr id='__UTPL__'>
                            <td class="littletablefooterb"  align='right'><?=$container_pa              ?></td>
			    <td class="littletablefooterb"  align='right'><?=$form->total2->output      ?></td>
			    <td class="littletablefooterb"  align='right'><?=$form->totrasrete->output  ?></td>
                            <td class="littletablefooterb"  align='right'><?=$form->tcrs->output        ?></td>
                            <td class="littletablefooterb"  align='right'><?=$form->ttimbre->output     ?></td>
                            <td class="littletablefooterb"  align='right'><?=$form->tmunicipal->output  ?></td>
                            <td class="littletablefooterb"  align='right'><?=$form->tislr->output       ?></td>
                            <td class="littletablefooterb"  align='right'><?=$form->triva->output       ?></td>
			    <td class="littletablefooterb"  align='right'><?=$form->total->output       ?></td>
			    <?php if($form->_status!='show') {?>
                                <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>
	    </table>
		<br />
			<table width='100%' bgcolor="#E2E0F4">
			<tr>
     			<th class="littletableheaderb" colspan="<?=(($form->_status=='create'?9:10))?>">MOVIMIENTOS BANCARIOS </th>
                        <tr>
     			<th class="littletableheaderb">             Estado                  </th>
     			<th class="littletableheaderb">             Banco                   </th>
     			<th class="littletableheaderb">             Destino                 </th>
     			<th class="littletableheaderb">             Tipo                    </th>
			<th class="littletableheaderb">             Cheque                  </th>
			<th class="littletableheaderb"align='center'>Fecha                  </th>
			<th class="littletableheaderb"align='right'>Monto                   </th>
			<th class="littletableheaderb"             >A nombre de             </th>
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
				$obj5 = "itmontom_$i";
				$obj7 = "itbenefim_$i";
				$obj6 = "itobservam_$i";
				$obj8 = "itdestino_$i";
			  ?>
			  <tr id='tr_mbanc_<?=$i ?>'>
			  <?php
			    $mid     = $form->_dataobject->get_rel('mbanc','id',$i);
			    $mstatus = $form->_dataobject->get_rel('mbanc','status',$i); ?>
			    <?php if ($status=='D2' && $form->_status=='show' && $mstatus=='E2' ) {?>
			    <td class="littletablerow"><a href="<?=site_url($this->datasis->traevalor('LINKCHEQUE','forma/ver/CHEQUE/','link a usar para el formato del cheque').'/'.$mid)?>" >IMPRIMIR</a></td>
			    <?php }elseif ($status=='D2' && $form->_status=='show' ){
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
			    <td class="littletablerow"               ><?=$form->$obj3->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj4->output  ?> </td>
			    <td class="littletablerow" align='right' ><?=$form->$obj5->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj7->output  ?> </td>
			    <td class="littletablerow"               ><?=$form->$obj6->output  ?> </td>
			    <?php
			     if ($status=='D2' && $form->_status=='show' && ($mstatus=='E2' || $mstatus=='NC') ) {?>
			    <td class="littletablerow"><a href="<?=site_url("tesoreria/desem/cambcheque/modify/$mid")?>" >Cambiar</a> <? if($mstatus=='E2'){ ?> | <a href="<?=site_url("tesoreria/mbanc/modifica2/dataedit/modify/$mid")?>" >Modificar</a> <? } ?></td>
			    <?php }else{
			    ?>
			    <?php
			    } ?>

			    <?php if ($form->_status=='create' ) {?>
			    <td class="littletablerow"><a href=# onclick='del_mbanc(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			    <?php if ($status=='D1' && $form->_status=='modify' ) {?>
			    <td class="littletablerow"><a href=# onclick='del_mbanc(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>

			  <tr id='__MBANCUTPL__'>
			    <td class="littletablefooterb" align='right' colspan="<?=(($form->_status=='create'?6:6))?>"><?=$form->totalch->label?></td>
			    <td class="littletablefooterb" align='right'><?=$form->totalch->output?></td>
			    <td class="littletablefooterb" align='right'>&nbsp;</td>
			    <td class="littletablefooterb" align='right'>&nbsp;</td>
			    <td class="littletablefooterb" align='right'>&nbsp;</td>
			  </tr>
			   <tr>
			  	<td colspan="4">
			  		<?php echo $container_mb ?>
			  	</td>
			  </tr>

	    </table>
	  <?=$form->temp->output  ?>
		<?php echo $form_end     ?>

		<td>
	<tr>
<table>
<?php endif; ?>
