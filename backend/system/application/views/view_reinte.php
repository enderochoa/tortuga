<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
echo $form->output;
else:
$link=site_url('presupuesto/presupuesto/get_tipo');

foreach($form->detail_fields['itreinte'] AS $ind=>$data)
$campos[]=$data['field'];
$campos='<tr id="tr_itreinte_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itreinte(<#i#>);return false;">'.image('delete.jpg',"Haz Click aqui para eliminar este item",array("border"=>0,"class"=>"eliminait")).'</a></td></tr>';
$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){
	//$uri  =$this->uri->uri_string();
	$uri  =$this->datasis->get_uri();
	$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='v_presaldo' AND uri='$uri'");
	$modblink=site_url('/buscar/index/'.$idt.'/<#i#>');

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
	
	itreinte_cont=<?=$form->max_rel_count['itreinte'] ?>;
	datos    ='';
	estruadm ='';
	com=false;
  $(function() {

		$(document).keydown(function(e){
					//alert(e.which);
		if (18 == e.which) {
			com=true;
			//c = String.fromCharCode(e.which);
			return false;
		}
		if (com && e.which == 61) {
		  add_itreinte();
		  a=itreinte_cont-1;
		  $("#itcodigopres_"+a).focus();
		  
			//alert("agrega linea");
				com=false;
				return false;
			}else if (com && e.which != 16 && e.which == 17){
				com=false;
			}
			return true;
		});
  
  	$(".inputnum").numeric(".");
  	cal_total();

		$.post("<?=site_url('presupuesto/ppla/autocomplete4')?>",{ partida:"" },function(data){
			datos=jQuery.parseJSON(data);
		});
		
		$.post("<?=site_url('presupuesto/estruadm/autocompleteui')?>",{ partida:"" },function(data){
			estruadm=jQuery.parseJSON(data);
		});
		
	});
	
	function autop(){
		$("input[name^='itcodigopres_']").focus(function(){
			id=this.name.substr(13,100);
			$( "#codigopres_"+id).autocomplete({
				minLength: 0,
				source: datos,
				focus: function( event, ui ) {
				$( "#itcodigopres_"+id).val( ui.item.codigopres );
				$( "#itdenomi_"+id).val( ui.item.denominacion );
				$( "#itordinal_"+id).val( ui.item.ordinal );
					return false;
				},
				select: function( event, ui ) {
					$( "#itcodigopres_"+id).val( ui.item.codigopres );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				codigoadm=$("#itcodigoadm_"+id).val();
				fondo    =$("#itfondo_"+id).val();
				if(codigoadm==item.codigoadm && fondo==item.fondo){
					return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.codigopres+'.'+item.ordinal + " - " + item.denominacion+ "</a>" )
					.appendTo( ul );
				}
			};
		});
		
		$("input[name^='itcodigopres_']").focusout(function(){
			id=this.name.substr(13,100);
			$( "#itmonto_"+id).focus();
		});
	}
	
	function autoe(){
		$("input[name^='itcodigoadm_']").focus(function(){
			id=this.name.substr(12,100);
			$( "#itcodigoadm_"+id).autocomplete({
				minLength: 0,
				source: estruadm,
				focus: function( event, ui ) {
				$( "#itcodigoadm_"+id).val( ui.item.codigoadm );
					return false;
				},
				select: function( event, ui ) {
					$( "#itcodigoadm_"+id).val( ui.item.codigoadm );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				fondo    =$("#itfondo_"+id).val();
				if(fondo==item.fondo){
					return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>"+ item.codigoadm + " - " +item.denominacion+ "</a>" )
					.appendTo( ul );
				}
			};
			
		});
	}
  
  $(document).ready(function(){ 
    mascara();
    autop();
    autoe();
    
  });

	function mascara(){
	
		$("input[name^='itcodigoadm_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOESTRU"))?>');
		$("input[name^='itcodigopres_']").setMask('<?=str_replace("X","9",$this->datasis->traevalor("FORMATOPRES"))?>');
	}

	function cal_total(){	
		tot=can=0;
		for(i=0;i<itreinte_cont;i++){
			id=i.toString();
			valor=parseFloat($("#itmonto_"+id).val());
			if(!isNaN(valor))
				tot=tot+valor;
			$("#ittotal").val(tot);
		}
	}

	function add_itreinte(){
		var htm = <?=$campos ?>;
		can = itreinte_cont.toString();
		con = (itreinte_cont+1).toString();
		cin = (itreinte_cont-1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		$("#itmonto_"+can).numeric(".");
		
		ante=$("#itfondo_"+cin).attr('selectedIndex');
		$("#itfondo_"+can).attr('selectedIndex',ante);
		
		ante=$("#itcodigoadm_"+cin).val();
		$("#itcodigoadm_"+can).val(ante);
		
		$("#itcodigoadm_"+can).focus();
		
		itreinte_cont=itreinte_cont+1;
		mascara();
		autoe();
		autop();
	}

	function del_itreinte(id){
		id = id.toString();
		$('#tr_itreinte_'+id).remove();
	}
</script>
	<?php } ?>

<table align='center' width="80%">
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
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td colspan=6 class="bigtableheader">Reintegro Presupuestario
				Nro. <?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?=$form->comping->label  ?>&nbsp;</td>
				<td class="littletablerow"  ><?=$form->comping->output ?>&nbsp; </td>
				<td class="littletablerowth"><?=$form->status->label  ?>*&nbsp;    </td>
				<td class="littletablerow">  <?=$form->status->output ?>&nbsp;     </td>
			</tr>
			<tr>
				<td class="littletablerowth"><?=$form->uejecuta->label  ?>*&nbsp;</td>
				<td class="littletablerow"  ><?=$form->uejecuta->output ?>&nbsp; </td>
				<td class="littletablerowth"><?=$form->fecha->label  ?>*&nbsp;     </td>
				<td class="littletablerow">  <?=$form->fecha->output ?>&nbsp;      </td>
			</tr>
			<tr>
				<td class="littletablerowth">        <?=$form->concepto->label     ?>&nbsp;</td>
				<td class="littletablerow" colspan=3><?=$form->concepto->output    ?>&nbsp;</td>
			</tr>
		</table>
		<br />
		<table width='100%'>
			<tr>
				<td class="littletableheaderb">F.Financiamiento   *</td>
				<td class="littletableheaderb">Est. Admin         *</td>
				<td class="littletableheaderb">Partida            *</td>
				<td class="littletableheaderb">Ordinal             </td>
				<td class="littletableheaderb">Denominaci&oacute;n </td>
				<td class="littletableheaderb" align="right">Monto </td>
				<?php if($form->_status!='show') {?>
				<td class="littletableheaderb">&nbsp;</td>
				<?php } ?>
			</tr>
			<?php for($i=0;$i<$form->max_rel_count['itreinte'];$i++) {
				$obj0="itfondo_$i";
				$obj1="itcodigoadm_$i";
				$obj2="itcodigopres_$i";
				$obj3="itordinal_$i";
				$obj4="denomi_$i";
				$obj5="itmonto_$i";
				?>
			<tr id='tr_itreinte_<?=$i ?>'>
				<td class="littletablerow"><?=$form->$obj0->output ?></td>
				<td class="littletablerow"><?=$form->$obj1->output ?></td>
				<td class="littletablerow"><?=$form->$obj2->output ?></td>
				<td class="littletablerow"><?=$form->$obj3->output ?></td>
				<td class="littletablerow"><?=$form->$obj4->output ?></td>
				<td class="littletablerow"><?=$form->$obj5->output ?></td>
				<?php if($form->_status!='show') {?>
				<td class="littletablerow"><a href=#
					onclick='del_itreinte(<?=$i ?>);return false;'><?=image('delete.jpg','Haz Click aqui para eliminar este item.</br>De esta manera se eliminara toda la fila seleccionada',array("border"=>0,"class"=>"eliminait"))?></a></td>
					<?php } ?>
			</tr>
			<?php } ?>
			
			<?php 
			$status = $form->_dataobject->get('status');                                                                                                                 
    			?>
			<tr id='__UTPL__'>
			<td class="littletablefooterb" colspan="2">                                                                                                                   
<?php                                                                                                                                                         
                                                                                                                                             
	echo $container_bl;                                                                                                                                     
	echo $container_br;                                                                                                         
                                                                                                                                                             
?>                                                                                                                                                            
</td> 
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;<?=$form->total->label  ?></td>
				<td class="littletablefooterb"><?=$form->total->output ?>&nbsp;</td>
				<?php if($form->_status!='show') {?>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<?php } ?>
			</tr>
		</table>

		<?php echo $form_end     ?> 
		</td>
	</tr>
</table>

<?php endif; ?>
