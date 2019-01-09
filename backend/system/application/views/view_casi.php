<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin'); 

foreach($form->detail_fields['itcasi'] AS $ind=>$data)
	$campos[]=$data['field'];
	
$campos='<tr id="tr_itcasi_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itcasi(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if($form->_status!='show'){
	$uri  =$this->uri->uri_string();
	$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_presaldo' AND uri='$uri'");
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
	itcasi_cont=<?=$form->max_rel_count['itcasi'] ?>;
	cpla='';
	
	
	function mascara(){
		$("input[name^='cuenta_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOPATRI"))?>');
	}

	function cal_total(){
		arr=$('input[name^="cuenta_"]');
		tdebe=thaber=0;
		jQuery.each(arr, function() {
			nom=this.name
			pos=this.name.lastIndexOf('_');
			if(pos>0){
				id      = this.name.substring(pos+1);
				if((isNaN($("#haber_"+id).val()))||($("#haber_"+id).val()==''))$("#haber_"+id).val(0);
				if((isNaN($("#debe_"+id).val()))||($("#debe_"+id).val()==''))$("#debe_"+id).val(0);
				
				haber=parseFloat($("#haber_"+id).val());
				debe=parseFloat($("#debe_"+id).val());
				
				thaber+=haber;
				tdebe +=debe;
			}
		});
			
		$("#haber").val(thaber);
		$("#debe").val(tdebe);
		a=tdebe - thaber;
		$("#total").val(a);
		
	}

	function cal_totalh(id){
		$("#debe_" + id).val(0);
		cal_total();
	}

	function cal_totald(id){
		$("#haber_" + id).val(0);
		cal_total();
	}

	
	$(function() {
		com=false;
		$(document).keydown(function(e){
		if (18 == e.which) {
			com=true;
			return false;
		}
		if (com && (e.which == 61 || e.which == 107)) {
		  add_itcasi();
		  a=itcasi_cont-1;
		  $("#cuenta_"+a).focus();
				com=false;
				return false;
			}else if(com && e.which != 16 && e.which == 17){
				com=false;
			}
			return true;
		});

		$(".inputnum").numeric(".");
		$.post("<?=site_url('contabilidad/cpla/autocomplete')?>",{ partida:"" },function(data){
			cpla=jQuery.parseJSON(data);
		});
		
		
	});
	
	function auto(){
		$("input[name^='cuenta_']").focus(function(){
			id=this.name.substr(7,100);
			$("#cuenta_"+id).autocomplete({
				minLength: 1,
				source: cpla,
				focus: function( event, ui ) {
				$( "#cuenta_"+id).val( ui.item.label );
					return false;
				},
				select: function( event, ui ) {
					$( "#cuenta_"+id).val( ui.item.label );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a>"+ item.label + " - " +item.denominacion+ "</a>" )
				.appendTo( ul );
				
			};
		});
	}
	
	$(document).ready(function() {
		mascara();
		auto();
		
		
	});
	
	function add_itcasi(){
		var htm = <?=$campos ?>;
		can = itcasi_cont.toString();
		con = (itcasi_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#debe_"+can).numeric(".");
		$("#haber_"+can).numeric(".");
		itcasi_cont=itcasi_cont+1;
		cal_total();
		mascara();
		auto();
	}

	function del_itcasi(id){
		id = id.toString();
		$('#tr_itcasi_'+id).remove();
		cal_total();
	}
	</script>
	<?php  
	} 
	?>
	<script language="javascript" type="text/javascript">
	function ordenar(){
		orden=$("#orden").val();
		a="<?=base_url().$this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3)?>";
		a2="<?='/'.$this->uri->segment(5).'/'.$this->uri->segment(6)?>";
		window.location=a+'/'+orden+a2;
		return false;
	}
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
			    <td colspan=6 class="bigtableheader">Comprobante Nro. <?=$form->comprob->output ?></td>
			    
			  </tr>
			   <tr>
			  	<td class="littletablerowth"> <?=$form->fecha->label  ?>*&nbsp;</td>
			    <td class="littletablerow"   ><?=$form->fecha->output ?>&nbsp; </td>
			    <td class="littletablerowth"> Ordenar por:&nbsp;</td>
			    <td class="littletablerow"   ><?=$orden ?>&nbsp; </td>
				</tr>
			  <!--  <tr>
			    <td class="littletablerowth">                <?//=$form->codigoadm->label  ?>*&nbsp;</td>
			    <td class="littletablerow" id='td_codigoadm'><?//=$form->codigoadm->output ?>&nbsp;</td>
			    <td class="littletablerowth">                <?//=$form->fondo->label     ?>*&nbsp;</td>
			    <td class="littletablerow" >                 <?//=$form->fondo->output    ?>&nbsp;</td>
			  </tr>
			  -->
			  <tr>
			    <td class="littletablerowth">         <?=$form->descrip->label ?>&nbsp;</td>
			    <td class="littletablerow" colspan=3 ><?=$form->descrip->output ?>&nbsp;</td>
			  </tr>

	    	</table >
			<table class="table_detalle">
     		<tr>
				<th class="littletableheaderb"              >Cuenta            </th>
				<th class="littletableheaderb"              >Concepto          </th>
				<td class="littletableheaderb"              >Fecha             </td>
				<th class="littletableheaderb"              >Referencia        </th>
			    <th class="littletableheaderb" align='right'>Debe              </th>
			    <th class="littletableheaderb" align='right'>Haber             </th>
			    <?php if($form->_status!='show') {?>
			    <th class="littletableheaderb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  
			  <?php 
			  
			  for($i=0;$i<$form->max_rel_count['itcasi'];$i++) {
					$obj0="itcuenta_$i";
					//$obj6="itdenomi_$i";
					$obj1="itconcepto_$i";
					$obj2="itreferencia_$i";				
					$obj3="itdebe_$i";
					$obj4="ithaber_$i";
					$obj5="itorigen_$i";
					$obj6="itfecha_$i";
			  ?>
			  <tr id='tr_itcasi_<?=$i ?>'>
			    <td class="littletablerow"><?=$form->$obj0->output ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><?=$form->$obj1->output ?></td>
			    <td class="littletablerow"><?=$form->$obj6->output ?></td>
			    <td class="littletablerow"><?=$form->$obj2->output ?></td>
			    
			    <?
			    }else{
			    ?>
			    <td class="littletablerow"><?=wordwrap($form->$obj1->output,60,"\n",true) ?></td>
			    <td class="littletablerow"><?=$form->$obj6->output ?></td>
			    <td class="littletablerow"><?=wordwrap($form->$obj2->output,60,"\n",true) ?></td>			    
			    <?
			    }
			    ?>
			    <td class="littletablerow" align='right'><?=$form->$obj3->output ?></td>
			    <td class="littletablerow" align='right'><?=$form->$obj4->output.$form->$obj5->output ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_itcasi(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			   
			  <tr id='__UTPL__'>
			    <td class="littletablefooterb" align='right' colspan="5"><?=$form->debe->output  ?></td>
			    <td class="littletablefooterb" align='right'>            <?=$form->haber->output  ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablefooterb">&nbsp;</td>
			    <?php } ?>
			  </tr>
			  		
			  <tr>
			    <td class="littletablefooterb" align='right' colspan="5"><?=$form->total->label   ?></td>
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


