<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['itocompra'] AS $ind=>$data)
	$campos[]=$data['field'];
	
$campos='<tr id="tr_itocompra_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itocompra(<#i#>);return false;">Eliminar</a></td></tr>';

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if($form->_status!='show'){
	?>
	<script language="javascript" type="text/javascript">
	itocompra_cont=<?=$form->max_rel_count['itocompra'] ?>;

	function tipoo(){
		tipo='<?=$form->_dataobject->get('tipo')?>';
	
		if(tipo=="Compromiso"){
			for(i=0;i<itocompra_cont;i++){
				id=i.toString();
				$("#iva_"+id).hide();
			}
			$("#iva").html('');
			$("#importe").html('Monto');
			$("#trivaa").hide();
			$("#trivar").hide();
			$("#trivag").hide();
			$("#trtotal2").hide();
			$("#trtotal").hide();
		}else{
			for(i=0;i<itocompra_cont;i++){
				id=i.toString();
				$("#iva_"+id).show();
				
			}

			$("#iva").html('Iva');
			$("#importe").html('Importe');

			$("#trivaa").show();
			$("#trivar").show();
			$("#trivag").show();			
			$("#trtotal2").show();
			$("#trtotal").show();
		}
		cal_total();
	}

	$(document).ready(function() {
		//cal_total();
		tipoo();
	});

	$(function(){
				cal_reteiva();
	});

	
	
	function cal_timbre(){
		s      =$("#simptimbre").is(":checked");
		mivag  = $("#mivag").val();
		mivar  = $("#mivar").val();
		mivaa  = $("#mivaa").val();
		mexento= $("#mexento").val();
		basei  = parseFloat(mivag)+parseFloat(mivaa)+parseFloat(mivar)+parseFloat(mexento);
		if(s==true){
			a=basei*<?=$imptimbre?>/100;
			$("#imptimbre").val(Math.round(a*100)/100);
		}else{
			
			$("#imptimbre").val(0);
		}
		//cal_total();
	}
	
	function cal_mivaa(){
		mivaa=parseFloat($("#mivaa").val());
		ivaa = mivaa*<?=$ivaa?>/100;
		$("#ivaa").val(ivaa);
		cal_total();
	}
	
	function cal_mivar(){
		mivar=parseFloat($("#mivar").val());
		ivar = mivar*<?=$ivar?>/100;
		$("#ivar").val(ivar);
		cal_total();
	}
	
	function cal_mivag(){
		mivag=parseFloat($("#mivag").val());
		ivag = mivag*<?=$ivag?>/100;
		$("#ivag").val(ivag);
		cal_total();
	}
	
	function cal_islr(){
		var ind=$("#creten").val();
		var tipo='<?=$form->_dataobject->get('tipo')?>';
		var subtotal=0;
		var data = <?=$rete ?>;
		a=ind.substring(0,1);
		
		base1=eval('data._'+ind+'.base1');
		tari1=eval('data._'+ind+'.tari1');
		pama1=eval('data._'+ind+'.pama1');
		
		mivag  = $("#mivag").val();
		mivar  = $("#mivar").val();
		mivaa  = $("#mivaa").val();
		mexento = $("#mexento").val();
		basei  = parseFloat(mivag)+parseFloat(mivaa)+parseFloat(mivar)+parseFloat(mexento);
		
		if(a=='1')ret=Math.round((basei*base1*tari1/10000)*100)/100;
			else ret=Math.round(((basei-pama1)*base1*tari1/10000)*100)/100;

		if(ret <0)ret=0;
		
		$("#reten").val(Math.round(ret*100)/100);
		s_islr=0;	
		for(i=0;i<itocompra_cont;i++){
			id=i.toString();
			if($("#usaislr_"+id).val()=="S"){
				importe= $("#importe_"+id).val();

				if(a=='1')ret=Math.round((importe*base1*tari1/10000)*100)/100;
				else ret=Math.round(((importe-pama1)*base1*tari1/10000)*100)/100;

				if(ret <0)ret=0;
				
				$("#islr_"+id).val(ret);
				
				s_islr+=ret;
			}else{
				$("#islr_"+id).val('0');
			}
		}
		if(s_islr>0){
			//if(ret <0)ret=0;		
			$("#reten").val(Math.round(s_islr*100)/100);
		}
		cal_total();
	}
	
	function cal_iva(){
		ivaa   = parseFloat($("#ivaa").val());
		ivar   = parseFloat($("#ivar").val());
		ivag   = parseFloat($("#ivag").val());
		exento = parseFloat($("#exento").val());
		total2 = parseFloat($("#total2").val());
		
		$("#subtotal").val(Math.round((total2-ivag+ivaa+ivar)*100)/100);
	}

	function cal_reteiva(){
		reteiva_prov=parseFloat($("#reteiva_prov").val());
		giva        = parseFloat($("#ivaa").val()  );
		riva        = parseFloat($("#ivar").val()  );
		aiva        = parseFloat($("#ivag").val()  );
		exento      = parseFloat($("#exento").val());
		mivag       =giva/<?=$ivag?>*(100-<?=$ivag?>+giva);
		mivar       =riva/<?=$ivar?>*(100-<?=$ivar?>+riva);
		mivaa       =aiva/<?=$ivaa?>*(100-<?=$ivaa?>+aiva);

		if((isNaN(reteiva_prov)) || (reteiva_prov==0) || (reteiva_prov=='') || (reteiva_prov==100))
			reteiva_prov=100;
		else
			reteiva_prov=75;

		reteiva=Math.round((((giva+riva+aiva)*reteiva_prov)/100)*100)/100;
		$("#reteiva").val(reteiva);
		cal_total();
	}

	function cal_total(){
		cal_iva();
		
		tot=exce=reteiva=0;
		stot  =parseFloat($("#subtotal").val());
		
		reten    = parseFloat($("#reten").val()    );
		total2   = parseFloat($("#total2").val()   );
		imptimbre= parseFloat($("#imptimbre").val());
		reteiva  = parseFloat($("#reteiva").val());
		otrasrete = parseFloat($("#otrasrete").val());
		if(isNaN(otrasrete) || otrasrete<0){
			$("#otrasrete").val('0');
			otrasrete =0;	
		}
		
		//alert("timbre"+imptimbre);
		//alert("reten"+reten);
		//alert("total2"+total2);
		//alert("reteiva"+reteiva);
		a=(total2-reten-reteiva-imptimbre-otrasrete);
		//alert(a);
		$("#total").val(Math.round(a*100)/100);
	}
	
	</script>
	<?php
	}else{
	?>
	<script language="javascript" type="text/javascript">
		function btn_anulaf(i){
			if(!confirm("Esta Seguro que desea Anular la Orden de Compra Causada ?"))
				return false;
			else
				window.location='<?=site_url('presupuesto/common/ca_anular')?>/'+i
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
			<table width="100%"  style="margin:0;width:100%;">
			  <tr>
			    <td colspan=6 class="bigtableheader">Causar Orden Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
			  </tr>
			   
			  <tr>
			  	<td class="littletablerowth"><?=$form->factura->label  ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->factura->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->fechafac->label       ?>*&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fechafac->output      ?>&nbsp; </td>
				</tr>
				<tr>
			  	<td class="littletablerowth"><?=$form->controlfac->label  ?>*&nbsp;</td>			  	
			    <td class="littletablerow"  ><?=$form->controlfac->output ?>&nbsp; </td>
			    <td class="littletablerowth"><?=$form->fecha->label       ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->fecha->output      ?>&nbsp;</td>
				</tr>
				<tr>
			  	<td class="littletablerowth"><?=$form->cod_prov->label  ?>&nbsp;</td>			  	
			    <td class="littletablerow"  colspan="3"><?=$form->cod_prov->output.$form->nombrep->output ?>&nbsp; </td>
			    
				</tr>
				<tr>
			    <td class="littletablerowth"><?=$form->simptimbre->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->simptimbre->output?>&nbsp;</td>
			    <td class="littletablerowth"><?=$form->reteiva_prov->label ?>&nbsp;</td>
			    <td class="littletablerow"  ><?=$form->reteiva_prov->output?>&nbsp;</td>
			  </tr>
			</table><br />
			<?php $tipo = $form->_dataobject->get('tipo'); ?>
			<table width='100%'>
				<tr>
					<td class="littletableheaderb">P.IVA?            </td>
					<td class="littletableheaderb">F.Financiamiento  </td>
					<td class="littletableheaderb">Est.Admin         </td>
					<td class="littletableheaderb">Partida           </td>
					<td class="littletableheaderb">Descripci&oacute;n</td>
					<td  class="littletableheaderb"><div id="div_reten3">    &nbsp;       </td>   
					<td  class="littletableheaderb"><div id="div_reten4">      <?="ISLR"?></td>
					<td class="littletableheaderb">Iva               </td>
					<td class="littletableheaderb">Importe           </td>
				</tr>
			  <?php 
			  for($i=0;$i<$form->max_rel_count['itocompra'];$i++) {
			  	$obj12="itesiva_$i";
			  	$obj10="itfondo_$i";
					$obj11="itcodigoadm_$i";     
					$obj0 ="itdescripcion_$i";
					$obj1 ="itunidad_$i";     
					$obj2 ="itcantidad_$i";   
					$obj3 ="itprecio_$i";     
					$obj4 ="itiva_$i";        
					$obj5 ="itimporte_$i";    
					$obj6 ="itusaislr_$i";
					$obj7 ="itislr_$i";
					$obj8 ="itpartida_$i"; 
			  ?>
			  <tr id='tr_itocompra_<?=$i ?>'>
				<td class="littletablerow"><?=$form->$obj12->output.$form->$obj1->output.$form->$obj2->output.$form->$obj3->output ?></td>
			  	<td class="littletablerow"><?=$form->$obj10->output ?></td>
			    <td class="littletablerow"><?=$form->$obj11->output ?></td>
			  	<td class="littletablerow"><?=$form->$obj8->output  ?></td>
			    <td class="littletablerow"><?=$form->$obj0->output  ?></td>
			    <td class="littletablerow" align='right'><div id="div_reten_<?=$i ?>"                   > <?=$form->$obj6->output  ?></div></td>
			    <td class="littletablerow" align='right'><div id="div_reten1_<?=$i ?>" style="width:90px"><?=$form->$obj7->output  ?></div></td>
			    <td class="littletablerow" align='right'><?=$form->$obj4->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj5->output ?></td>
			  </tr>
			  <?php } ?>
			   
			  <tr id='__UTPL__'>
			    <td class="littletablefooterb" align='right' colspan="8"><?=$form->subtotal->label  ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->subtotal->output  ?></td>			    
			  </tr>
			  
			  <tr>
			    <td class="littletablerow" align='right' colspan="4"><div id="div_creten2"><?=$form->creten->label.$form->creten->output ?>&nbsp;  </div></td>
			    <td class="littletablerow" align='right' colspan="2"><?=$form->mivag->label.$form->mivag->output    ?>              </td>
			    <td class="littletablerow" align='right' colspan="2"><?=$form->ivag->label                          ?>              </td>
			    <td class="littletablerow" align='right'>            <?=$form->ivag->output                         ?>              </td>
			  </tr>
			  
			  <tr>
			    <td class="littletablerow" align='right' colspan="3"><?=$form->reten->label                       ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->reten->output                      ?></td>
			    <td class="littletablerow" align='right' colspan="2"><?=$form->mivar->label.$form->mivar->output  ?></td>
					<td class="littletablerow" align='right' colspan="2"><?=$form->ivar->label                        ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->ivar->output                       ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  
			  <tr>
			    <td class="littletablerow" align='right' colspan="3"><?=$form->reteiva->label                    ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->reteiva->output                   ?></td>
			    <td class="littletablerow" align='right' colspan="2"><?=$form->mivaa->label.$form->mivaa->output ?></td>
			    <td class="littletablerow" align='right' colspan="2"><?=$form->ivaa->label                       ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->ivaa->output                      ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  
			  <tr>
			    <td class="littletablerow" align='right'  colspan="3"><?=$form->imptimbre->label     ?>     </td>
			    <td class="littletablerow" align='right'><?=$form->imptimbre->output    ?>     </td>
			    <td class="littletablerow" align='right' colspan="2"><?=$form->mexento->label.$form->mexento->output ?></td>
			    <td class="littletablerow" align='right' colspan="2"><?=$form->exento->label ?></td>
			    <td class="littletablerow" align='right'>            <?=$form->exento->output?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  <tr>
			    <td class="littletablerow" align='right'  colspan="3"><?=$form->otrasrete->label     ?>     </td>
			    <td class="littletablerow" align='right'>             <?=$form->otrasrete->output    ?>     </td>
			    <td class="littletablerow" align='right' colspan="2"> &nbsp;                                </td>
			    <td class="littletablerow" align='right' colspan="2"> &nbsp;                                </td>
			    <td class="littletablerow" align='right'>             &nbsp;                                </td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow">&nbsp;</td>
			    <?php } ?>
			  </tr>
				
			  <tr>
			  <td class="littletablefooterb" align='right' colspan="7"><?=$form->total->label.$form->total->output   ?></td>
			    <td class="littletablefooterb" align='right'           ><?=$form->total2->label   ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->total2->output  ?></td>
			  </tr>
	    </table>
		<?php echo $form_end     ?>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>
