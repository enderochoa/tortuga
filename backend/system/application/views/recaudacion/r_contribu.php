<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:


foreach($form->detail_fields['r_contribuit'] AS $ind=>$data)
	$campos[]=$data['field'];
	
$campos='<tr id="tr_r_contribuit_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_r_contribuit(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a></td></tr>';
$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;

echo $form_begin; 
if($form->_status!='show'){
	?>
<style >
</style>

	<script language="javascript" type="text/javascript">
	r_contribuit_cont=<?=$form->max_rel_count['r_contribuit'] ?>;
	
	
	$(document).ready(function(){
		$(".inputnum").numeric(".");
		$(".inputonlynum").numeric("0");
		$("#rifci").focus();
		
		function damenombre(){
			rifci=$("#rifci").val();
			if(rifci.length>0){
			
				c1=rifci.substring(0,1);
				if(c1=="V" || c1=="E" || c1=="J" || c1=="P" || c1=="G"){
				}else{
					rifci="V"+rifci;
				}
				rifci = rifci.replace(".","");
				rifci = rifci.replace(".","");
				rifci = rifci.replace(".","");
				rifci = rifci.replace("-","");
				rifci = rifci.replace("-","");
				
				$("#rifci").val(rifci);
						
				$.post('<?php echo site_url($this->url.'damerne') ?>',{ cedula:rifci },function(data){
					rne=jQuery.parseJSON(data);
					$("#nombre").val(rne[0].nombre);
				}).always(function(){
					$.post('<?php echo site_url($this->url.'traeseniat') ?>',{ rifcedula:rifci },function(data){
						d=jQuery.parseJSON(data);
						if(d["code_result"]==1){
							nombre =d["seniat"]["nombre"];
							if(rifci.substring(0,1)=="V"){
								nombre = nombre.replace(".","");
								nombre = nombre.replace(".","");
							}else{
								nombre=nombre.substring(0,nombre.indexOf("("))
							}
							$("#nombre").val(nombre);
						}else{
							
						}
					});
				});	
			}
		}
		
		function verpatente(){
			
			patente = $("#patente").val();
			if(patente=="S"){
				$("#tr_nro"       ).show();
				$("#tr_objeto"    ).show();
				$("#tr_id_repre"  ).show();
				$("#tr_nombrep"   ).show();
				$("#tr_archivo"   ).show();
				$("#tr_reg_nro"   ).show();
				$("#tr_reg_tomo"  ).show();
				$("#tr_reg_fecha" ).show();
				$("#tr_id_sector" ).show();
				$("#tr_p_tipo"    ).show();
			}else{
				$("#tr_nro"       ).hide();
				$("#tr_objeto"    ).hide();
				$("#tr_id_repre"  ).hide();
				$("#tr_nombrep"   ).hide();
				$("#tr_archivo"   ).hide();
				$("#tr_reg_nro"   ).hide();
				$("#tr_reg_tomo"  ).hide();
				$("#tr_reg_fecha" ).hide();
				$("#tr_id_sector" ).hide();
				$("#tr_p_tipo"    ).hide();
			}
		}
		
		$("#rifci").change(function(){
				damenombre();
		});
		$("#patente").change(function(){
				verpatente();
		});
		
		$("#tipo").change(function(){
			tipocontribu();
		});
		
		tipocontribu();
		damenombre();
		verpatente();
		autorifcisocios();
		autonombresocios();
	});
	
	function tipocontribu(){
		tipo=$("#tipo").val();
		if(tipo=='M'){
			$("#rifci").val('');
			$("#tr_rifci").hide();
		}else{
			$("#tr_rifci").show();
		}	
	}
	
	function post_modbus_socios(id){
		id = id.toString();
		idcontribu = $('#itid_contribuit_'+id).val();
		$('#itid_contribuit_'+id+'_val').text(idcontribu);
	}
	
	function autorifcisocios(){
		$("input[name^='itrifcipit_']").focus(function(){
			id=this.name.substr(11,100);
			$("#itrifcipit_"+id).autocomplete({
				//autoFocus: true,
				delay: 0,
				minLength: 3,
				source: function (request, response) {
					$.ajax({
					  type: "POST",
					  url:"<?php echo site_url('recaudacion/r_contribu/autocompleteui_rifci/') ?>",
					  data: request,
					  success: response,
					  dataType: 'json'
					});
					
				},
				focus: function( event, ui ){
					//$( "#nombre").val( ui.item.nombre );
					//$( "#rifci").val( ui.item.rifci );
					//$( "#id_contribu").val( ui.item.id );
					return false;
				},
				select: function( event, ui ){
					$( "#itnombrepit_"+id).val( ui.item.nombre );
					$( "#itrifcipit_"+id).val( ui.item.rifci );
					$( "#itid_contribuit_"+id).val( ui.item.id );
					$( "#itid_contribuit_"+id+"_val").text( ui.item.id );
					
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a>" + item.rifci + " "  + item.nombre + "</a>" )
				.appendTo( ul );
			};
		});
	}
	
	function autonombresocios(){
		$("input[name^='itnombrepit_']").focus(function(){
			id=this.name.substr(12,100);
			$("#itnombrepit_"+id).autocomplete({
				//autoFocus: true,
				delay: 0,
				minLength: 3,
				source: function (request, response) {
					$.ajax({
					  type: "POST",
					  url:"<?php echo site_url('recaudacion/r_contribu/autocompleteui_nombre/') ?>",
					  data: request,
					  success: response,
					  dataType: 'json'
					});
					
				},
				focus: function( event, ui ){
					//$( "#nombre").val( ui.item.nombre );
					//$( "#rifci").val( ui.item.rifci );
					//$( "#id_contribu").val( ui.item.id );
					return false;
				},
				select: function( event, ui ){
					$( "#itnombrepit_"+id).val( ui.item.nombre );
					$( "#itrifcipit_"+id).val( ui.item.rifci );
					$( "#itid_contribuit_"+id).val( ui.item.id );
					$( "#itid_contribuit_"+id+"_val").text( ui.item.id );
					
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a>" + item.rifci + " "  + item.nombre + "</a>" )
				.appendTo( ul );
			};
		});
	}
	
	function add_r_contribuit(){
		var htm = <?=$campos ?>;
		can = r_contribuit_cont.toString();
		con = (r_contribuit_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		r_contribuit_cont=r_contribuit_cont+1;
		autorifcisocios();
		autonombresocios();
	}

	function del_r_contribuit(id){
		id = id.toString();
		$('#tr_r_contribuit_'+id).remove();
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
			    <td colspan=6 class="bigtableheader">Contribuyente <?=$form->id->output ?></td>
			  </tr>
			</table>
			<table width="100%"  style="margin:0;width:100%; background:rgb(230,230,250)" >
				<tr id="tr_id" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->id->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->id->output ?> </td>
				</tr>
				<tr id="tr_tipo" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->tipo->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->tipo->output ?> </td>
				</tr>
				<tr id="tr_rifci" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->rifci->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->rifci->output ?> </td>
				</tr>
				<tr id="tr_nombre" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->nombre->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->nombre->output ?> </td>
				</tr>
				<tr id="tr_telefono" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->telefono->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->telefono->output ?> </td>
				</tr>
				<tr id="tr_email" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->email->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->email->output ?> </td>
				</tr>
				
			</table>
			<table width="100%"  style="margin:0;width:100%; background:rgb(230,250,230)" >
				<tr id="tr_id_parroquia" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->id_parroquia->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->id_parroquia->output ?> </td>
				</tr>
				<tr id="tr_id_zona" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->id_zona->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->id_zona->output ?> </td>
				</tr>
				<tr id="tr_dir1" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->dir1->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->dir1->output ?> </td>
				</tr>
				<tr id="tr_dir2" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->dir2->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->dir2->output ?> </td>
				</tr>
				<tr id="tr_dir3" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->dir3->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->dir3->output ?> </td>
				</tr>
				<tr id="tr_dir4" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->dir4->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->dir4->output ?> </td>
				</tr>
				<tr id="tr_id_negocio" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->id_negocio->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->id_negocio->output.$form->negociop->output ?> </td>
				</tr>
				<tr id="tr_activo" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->activo->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->activo->output ?> </td>
				</tr>
				<tr id="tr_observa" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->observa->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->observa->output ?> </td>
				</tr>
			</table>
			<?php if($this->datasis->puede(398)){ ?>
			<table width="100%"  style="margin:0;width:100%; background:rgb(250,230,230)" >
				<tr id="tr_patente" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->patente->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->patente->output ?> </td>
				</tr>
				<tr id="tr_nro" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->nro->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->nro->output ?> </td>
				</tr>
				<tr id="tr_id_repre" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->id_repre->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->id_repre->output.$form->nombrep->output ?> </td>
				</tr>
				<tr id="tr_objeto" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->objeto->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->objeto->output ?> </td>
				</tr>
				<tr id="tr_archivo" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->archivo->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->archivo->output ?> </td>
				</tr>
				<tr id="tr_id_sector" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->id_sector->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->id_sector->output ?> </td>
				</tr>
				<tr id="tr_reg_nro" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->reg_nro->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->reg_nro->output ?> </td>
				</tr>
				<tr id="tr_reg_tomo" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->reg_tomo->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->reg_tomo->output ?> </td>
				</tr>
				<tr id="tr_reg_fecha" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->reg_fecha->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->reg_fecha->output ?> </td>
				</tr>
				<tr id="tr_p_tipo" >
					<td  style="width:120px;" class="littletablerowth"><?=$form->p_tipo->label  ?> </td>
					<td                       class="littletablerow"  ><?=$form->p_tipo->output ?> </td>
				</tr>
	    	</table >
	    	<?php }?>
			<table class="table_detalle" >
			<tr>
				<th  bgcolor="black" colspan="<?=($form->_status=='show'?3:4)?>"><span style="color:white"><STRONG>CONTRIBUYENTES ASOCIADOS</STRONG></span></th>
			</tr>
     		<tr>
				<th class="littletableheaderb" width="5%">Ref.            </th>
				<th class="littletableheaderb" width="30%">Rif/C.I.        </th>
				<th class="littletableheaderb" width="65%">Nombre          </th>
				<?php if($form->_status!='show') {?>
				<th class="littletableheaderb">&nbsp;</td>
				<?php } ?>
			  </tr>
			  <?php 
			  
			  for($i=0;$i<$form->max_rel_count['r_contribuit'];$i++) {
					$obj0="itid_contribuit_$i";
					$obj1="itrifcipit_$i";
					$obj2="itnombrepit_$i";
			  ?>
			  <tr id='tr_r_contribuit_<?=$i ?>'>
			    <td class="littletablerow"><?=$form->$obj0->output ?></td>
			    <td class="littletablerow"><?=$form->$obj1->output ?></td>
			    <td class="littletablerow"><?=$form->$obj2->output ?></td>
			    <?php if($form->_status!='show') {?>
			    <td class="littletablerow"><a href=# onclick='del_r_contribuit(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a></td>
			    <?php } ?>
			  </tr>
			  <?php } ?>
			  
			   <tr id='__UTPL__'>
			    <td class="littletablefooterb" colspan="3">&nbsp;</td>
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


