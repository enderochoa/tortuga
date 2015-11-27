<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
$link=site_url('presupuesto/requisicion/getadmin');

$campos=$form->template_details('r_reciboit');
	
$scampos  ='<tr id="tr_r_reciboit_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['itid_cxcit' ]['field'].$campos['itrequiere' ]['field'].$campos['itmodo' ]['field'].$campos['itid_concit' ]['field'].$campos['itdenomi' ]['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['itano'      ]['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['itfrecuencia'    ]['field'].$campos['itfreval'    ]['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['itid_vehiculo']['field'].$campos['itv_placa']['field'].$campos['itid_inmueble']['field'].$campos['iti_catastro']['field'].$campos['itid_publicidad']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['itobserva'    ]['field'].'</td>';
$scampos .='<td class="littletablerow" align="right" >'.$campos['itbase'    ]['field'].'</td>';
$scampos .='<td class="littletablerow" align="right" >'.$campos['itmonto'    ]['field'].'</td>';
$scampos .= '<td class="littletablerow"><a href=# onclick="del_r_reciboit(<#i#>);return false;">'.image('delete.jpg','#',array("border"=>0)).'</a><span class="papelerait">_._<#i#>_._</span></td></tr>';

$campos=$form->js_escape($scampos);


if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin;
if($form->_status!='show'){
	$uri  =$this->datasis->get_uri();
	
	$idt  =$this->datasis->dameval("SELECT id FROM modbus WHERE idm='r_contribu' AND uri='$uri'");
	$modblink=site_url('/buscar/index/'.$idt);
	
	$idt  =$this->datasis->dameval("SELECT id FROM modbus WHERE idm='r_v_conc' AND uri='$uri'");
	$modblink2=site_url('/buscar/index/'.$idt);
	
	$idt  =$this->datasis->dameval("SELECT id FROM modbus WHERE idm='r_v_vehiculo' AND uri='$uri'");
	$modblinkv=site_url('/buscar/index/'.$idt);
	
	$idt  =$this->datasis->dameval("SELECT id FROM modbus WHERE idm='r_v_inmueble' AND uri='$uri'");
	$modblinki=site_url('/buscar/index/'.$idt);
?>
<style>
.ui-autocomplete {
	max-height: 200px;
	overflow-y: auto;
	max-width: 800px;
}
html.ui-autocomplete {
	height: 200px;
	width: 800px;
}
.buttonheadtable{
		font-size:12px;
}
.papelerait{
	font-size:1px;
	font-color:#ffffff;
}
</style>

<script language="javascript" type="text/javascript">
	reciboit_cont=<?=$form->max_rel_count['r_reciboit'] ?>;
	
	function creacontribu(){
		rifci = $("#rifci").val();
		id_contribu = $("#id_contribu").val();
		if(id_contribu>0)
		vent=window.open("<?=site_url('recaudacion/r_contribu/dataedit/modify/')?>"+"/"+id_contribu,"Contribuyente","height=500,width=800,scrollbars=yes");
		else
		vent=window.open("<?=site_url('recaudacion/r_contribu/dataedit/create/')?>"+"/"+rifci,"Contribuyente","height=500,width=800,scrollbars=yes");
	}
	
	function creainmueble(){
		i=null;
		creainmuebleid(i);
	}
	function creavehiculo(){
		i=null;
		creavehiculoid(i);
	}
	
	function creainmuebleid(i){
		id_contribu = $("#id_contribu").val();
		id=null;
		if(i)
		id=$("#id_inmueble_"+i).val();
		
		if(id>0)
		vent=window.open("<?=site_url('recaudacion/r_inmueble/dataedit/modify/')?>"+"/"+id,"Inmueble","height=500,width=800,scrollbars=yes");
		else
		vent=window.open("<?=site_url('recaudacion/r_inmueble/dataedit/create/')?>"+"/"+id_contribu,"Inmueble","height=500,width=800,scrollbars=yes");
	}

	function creavehiculoid(i){
		id_contribu = $("#id_contribu").val();
		id=null;
		if(i)
		id=$("#id_vehiculo_"+i).val();
		
		if(id>0)
		vent=window.open("<?=site_url('recaudacion/r_vehiculo/dataedit/modify/')?>"+"/"+id,"Vehiculo","height=500,width=800,scrollbars=yes");
		else
		vent=window.open("<?=site_url('recaudacion/r_vehiculo/dataedit/create/')?>"+"/"+id_contribu,"Vehiculo","height=500,width=800,scrollbars=yes");
	}
	
	window.onblur = function() {
		 
	}
	window.onfocus = function() {
		validarif();
	}
	
	function validarif(){
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
			
			$.ajax({
				url: "<?=site_url('recaudacion/r_contribu/damecontribu')?>",
			  type: 'POST',
			  async: false,
			  data: { rifcedula:rifci }
			}).done(function(data){
				contribu=jQuery.parseJSON(data);
				
				if(contribu.length>0){
					$("#id_contribu").val(contribu[0].id);
					$("#nombre").val(contribu[0].nombre);
				}else{
					$("#id_contribu").val("");
					$("#nombre").val("");
				}
			});
			
			$.post("<?=site_url('recaudacion/r_contribu/damecontribu')?>",{ rifcedula:rifci }
			);
		}
	}
	
	$(document).ready(function(){
		$(".inputnum").numeric(".");
		//$.post("<?=site_url('recaudacion/r_contribu/autocompleteui')?>",{ partida:"" },function(data){
		//	sprv=jQuery.parseJSON(data);
		//	jQuery.each(sprv, function(i, val) {
		//		val.label=val.nombre;
		//		
		//	});
			
			$("#nombre").autocomplete({
				//autoFocus: true,
				delay: 0,
				minLength: 3,
				source: function (request, response) {
					//$.post("<?php echo site_url('recaudacion/r_contribu/autocompleteui_nombre/') ?>", request, response);
					
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
					$( "#rifci").val( ui.item.rifci );
					//$( "#id_contribu").val( ui.item.id );
					return false;
				},
				select: function( event, ui ){
					$( "#nombre").val( ui.item.nombre );
					$( "#rifci").val( ui.item.rifci );
					$( "#id_contribu").val( ui.item.id );
					//$("#tipo").focus();
					cargacxc();
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a>" + item.nombre + "</a>" )
				.appendTo( ul );
			};
			
			//jQuery.each(sprv, function(i, val) {
			//	val.label=val.rifci;
			//});
			
			$("#rifci").autocomplete({
				//autoFocus: true,
				delay: 0,
				minLength: 3,
				source: function (request, response) {
					//$.post("<?php echo site_url('recaudacion/r_contribu/autocompleteui_rifci/') ?>", request, response);
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
					$( "#nombre").val( ui.item.nombre );
					$( "#rifci").val( ui.item.rifci );
					$( "#id_contribu").val( ui.item.id );
					//$("#tipo").focus();
					cargacxc();
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a>" + item.rifci + " "  + item.nombre + "</a>" )
				.appendTo( ul );
			};
			
		//});
		
		$.post("<?=site_url('recaudacion/r_recibo/dameconc')?>",{ partida:"" },function(data){
			datosconc=jQuery.parseJSON(data);
			
			datosconcid = datosconc;
			jQuery.each(datosconcid, function(i, val) {
				val.label=val.id;
			});
			datosconcdenomi = datosconc;
			jQuery.each(datosconcdenomi, function(i, val) {
				val.label=val.denomi;
			});
		});
		
		
		
		autoconcid();
		autoconcdenomi();
		
		$("#papelera,#papelera2").mouseover(function(){
			miRegEx=/_\._[0-9]+_\._/ig;
			linea=$(this).val();
			lista=linea.match(miRegEx);
			
			if(lista){
				jQuery.each(lista, function(i, val) {
					id=val.replace(/_\._/g,'');
					del_r_reciboit(id);
				});
			}
			cal_total();
			$(this).val('');
		});
	});
	
	function autoconcid(){
		
		$("input[name^='id_concit_']").focus(function(){
			id=this.name.substr(10,100);
			$( "#id_concit_"+id).autocomplete({
				minLength: 0,
				source: datosconcid,
				focus: function( event, ui ) {
					//$( "#id_concit_"+id).val( ui.item.id );
					//$( "#denomi_"+id).val( ui.item.denomi );
					//$( "#acronimo_"+id).val( ui.item.acronimo );
					//$( "#ano_"+id).val( ui.item.ano );
					//$( "#requiere_"+id).val( ui.item.requiere );
					return false;
				},
				select: function( event, ui ) {
					$("#id_concit_"+id).val( ui.item.id );
					$("#denomi_"+id).val( ui.item.denomi );
					$("#frecuencia_"+id).prop('selectedIndex',ui.item.frecuencia);
					$("#freval_"+id).prop('selectedIndex',ui.item.freval);
					$("#ano_"+id).val( ui.item.ano );
					$("#requiere_"+id).val( ui.item.requiere );
					$("#modo_"+id).val( ui.item.modo );
					pos=this.name.lastIndexOf('_');
					if(pos>0){
						id= this.name.substring(pos+1);
						post_conc(id);
					}
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a>" + item.id+'.'+item.ano + " - " + item.denomi+ "</a>" )
				.appendTo( ul );
			};
		});
	}
	
	function autoconcdenomi(){
		$("input[name^='denomi_']").focus(function(){
			id=this.name.substr(7,100);
			$( "#denomi_"+id).autocomplete({
				minLength: 0,
				source: datosconcdenomi,
				focus: function( event, ui ) {
					//$( "#id_concit_"+id).val( ui.item.id );
					//$( "#denomi_"+id).val( ui.item.denomi );
					//$( "#acronimo_"+id).val( ui.item.acronimo );
					//$( "#ano_"+id).val( ui.item.ano );
					//$( "#requiere_"+id).val( ui.item.requiere );
					return false;
				},
				select: function( event, ui ) {
					$("#id_concit_"+id).val( ui.item.id );
					$("#denomi_"+id).val( ui.item.denomi );
					$("#frecuencia_"+id).prop('selectedIndex',ui.item.frecuencia);
					$("#freval_"+id).prop('selectedIndex',ui.item.freval);
					$("#ano_"+id).val( ui.item.ano );
					$("#requiere_"+id).val( ui.item.requiere );
					$("#modo_"+id).val( ui.item.modo );
					pos=this.name.lastIndexOf('_');
					if(pos>0){
						id= this.name.substring(pos+1);
						post_conc(id);
					}
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a>" + item.id+'.'+item.ano + " - " + item.denomi+ "</a>" )
				.appendTo( ul );
			};
		});
	}
  
	function post_conc(i){
		requiere = $("#requiere_"+i).val();
		modo = $("#modo_"+i).val();
		
		$("#monto_"+i).numeric(".");
		$("#monto_"+i).prop('readonly', false);
		
		$("#id_inmueble_"+i).hide();
		$("#i_catastro_"+i).hide();
		$("#modbusi_"+i).hide();
		$("#modbusv_"+i).hide();
		$("#creav_"+i).hide();
		$("#creai_"+i).hide();
		
		$("#id_vehiculo_"+i).hide();
		$("#v_placa_"+i).hide();
		
		$("#observa_"+i).hide();
		$("#base_"+i).hide();
		
		$("#id_publicidad_"+i).hide();
		//$("#modbusp_"+i).hide();
		//$("#creap_"+i).hide();
		
		if(requiere=='INMUEBLE'){
			$("#id_inmueble_"+i).show();
			$("#i_catastro_"+i).show();
			$("#modbusi_"+i).show();
			$("#creai_"+i).show();
		}
		
		if(requiere=='VEHICULO'){
			$("#id_vehiculo_"+i).show();
			$("#v_placa_"+i).show();
			$("#modbusv_"+i).show();
			$("#creav_"+i).show();
		}
		
		if(requiere=='PUBLICIDAD'){
			$("#id_publicidad_"+i).show();
			//$("#modbusp_"+i).show();
			//$("#creap_"+i).show();
			$("#observa_"+i).show();
		}
		
		if(modo=='MANUAL'){
			$("#monto_"+i).numeric(".");
			$("#monto_"+i).prop('readonly', false);
			$("#observa_"+i).show();
			$("#monto_"+i).css("background-color", "#FFFFFF");
		}
		
		if(modo=='BASE'){
			$("#monto_"+i).numeric(".");
			$("#monto_"+i).prop('readonly', true);
			$("#monto_"+i).css("background-color", "#AAAAAA");
			$("#observa_"+i).show();
			$("#base_"+i).show();
		}
		
		if(modo=='CALCULADO'){
			$("#monto_"+i).numeric(".");
			$("#monto_"+i).prop('readonly', true);
			$("#monto_"+i).css("display", "block");
			$("#monto_"+i).css("background-color", "#AAAAAA");
		}
		
		//if(tm==1)
		//	traemonto(i);
		
	}
	
	function modbusdepenv(i){
		var contribu =$("#id_contribu").val();
		l=contribu.length;
		if(l>0){
			var link='<?=$modblinkv ?>'+'/'+contribu+'/'+i;
			link =link.replace(/<#id_contribu#>/g,contribu);
			link =link.replace(/<#i#>/g,i);
			vent=window.open(link,'ventbuscarv','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5');
			vent.focus();
			document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscarv\');vent.close();');
		}else{
			alert("Error. Por favor seleccione primero un contribuyente");
		}
	}
	
	function modbusdepeni(i){
		var contribu =$("#id_contribu").val();
		l=contribu.length;
		if(l>0){
			var link='<?=$modblinki ?>'+'/'+contribu+'/'+i;
			link =link.replace(/<#id_contribu#>/g,contribu);
			link =link.replace(/<#i#>/g,i);
			vent=window.open(link,'ventbuscari','width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5');
			vent.focus();
			document.body.setAttribute('onUnload','vent=window.open(\'about:blank\',\'ventbuscari\');vent.close();');
		}else{
			alert("Error. Por favor seleccione primero un contribuyente");
		}
	}
	
	$(document).ready(function(){
		
		$( "#b_cargacxc" ).button().click(function( event ) {
			cargacxc();
			return  false;
		});
		
		$( "#b_borrartodo" ).button().click(function( event ) {
			borratodo();
			return  false;
		});
		
		$( "#b_crearinmueble" ).button().click(function( event ) {
			creainmueble();
			return  false;
		});
		
		$( "#b_crearvehiculo" ).button().click(function( event ) {
			creavehiculo();
			return  false;
		});
		
		$( "#b_cargacxcnro" ).button().click(function( event ) {
			var numero = prompt("Por favor introduce el Numero de la Cuenta por Cobrar");
			
			if (numero != null) {
				borratodo();
				cargacxcback(null,numero);
				idv= $("#id_contribu").val();
				
				$.ajax({
					url: "<?=site_url('recaudacion/r_contribu/damecontribuporid')?>",
				  type: 'POST',
				  async: false,
				  data: { id:idv }
				}).done(function(data){
					//contribu=jQuery.parseJSON(data);
					//
					//if(contribu.length>0){
					//	$("#nombre").val(contribu[0].nombre);
					//	$("#rifci").val(contribu[0].rifci);
					//}else{
					//	$("#nombre").val("");
					//	$("#rifci").val("");
					//}
				});
			}
			return  false;
		});
		
		//$( "#b_cargacxcnro" ).click();
		
		arr=$('input[name^="id_concit_"]');
		
		jQuery.each(arr, function() {
			pos=this.name.lastIndexOf('_');
			if(pos>0){
				id= this.name.substring(pos+1);
				post_conc(id);
			}
		});
		
		$( "#df1" ).submit(function( event ) {
			//validarif();
			arr=$('input[name^="id_concit_"]');
			jQuery.each(arr, function() {
				nom=this.name;
				pos=this.name.lastIndexOf('_');
				if(pos>0){
					id= this.name.substring(pos+1);
					id_concit=$("#id_concit_"+id).val();
					l=id_concit.length
					if(l>0){
					}else{
						del_r_reciboit(id);
					}
				}
			});
		  //event.preventDefault();
		});
		cargacxc();
	});

	function add_r_reciboit(){
		var htm = <?=$campos ?>;
		can = reciboit_cont.toString();
		con = (reciboit_cont+1).toString();
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		$("#__UTPL__").before(htm);
		
		
		post_conc(reciboit_cont);
		reciboit_cont=reciboit_cont+1;
		cal_total();
		autoconcid();
		autoconcdenomi();
	}
	
	function del_r_reciboit(id){
		console.log("del");
		id = id.toString();
		$('#tr_r_reciboit_'+id).remove();
		cal_total();
	}
	
	function cal_total(){
		
		arr=$('input[name^="id_concit_"]');
		total=0;
		jQuery.each(arr, function() {
			nom=this.name;
			pos=this.name.lastIndexOf('_');
			if(pos>0){
				id      = this.name.substring(pos+1);
				id_concit=$("#id_concit_"+id).val();
				
				if(id_concit>0){
					id= this.name.substring(pos+1);
					monto=$("#monto_"+id).val();
					monto=Math.round(monto*100)/100;
					$("#monto_"+id).val(monto);
					
					total+=1*monto;
				}
				//del_r_reciboit(id);
			}
		});
		total=Math.round(total*100)/100
		$("#monto").val(total);
	}
	
	function cargadeuda(){
		
		id=$("#id_contribu").val();
		
		if(id.length>0){

			$.post("<?=site_url('recaudacion/r_recibo/damedeuda')?>",{ id_contribu:id },function(data){
				deuda=jQuery.parseJSON(data);
				
				var htm = <?=$campos ?>;
											
				jQuery.each(deuda, function(i, val) {
					can = reciboit_cont.toString();
					con = (reciboit_cont+1).toString();
					
					htm = <?=$campos ?>;
					
					htm = htm.replace(/<#i#>/g,can);
					htm = htm.replace(/<#o#>/g,con);
					
					$("#__UTPL__").before(htm);
					
					$("#id_concit_"+can).val(val.id   );
					$("#denomi_"+can).val(val.denomi     );
					$("#frecuencia_"+can).prop('selectedIndex',val.frecuencia);
					$("#freval_"+can).prop('selectedIndex',val.freval);
					$("#ano_"+can).val(val.ano        );
					$("#observa_"+can).val(val.observa    );
					$("#monto_"+can).val(val.monto      );
					$("#id_vehiculo_"+can).val(val.id_vehiculo );
					$("#id_inmueble_"+can).val(val.id_inmueble );
					$("#v_placa_"+can).val(val.placa     );
					$("#requiere_"+can).val(val.requiere   );
					$("#modo_"+can).val(val.modo   );
					$("#i_catastro_"+can).val(val.i_catastro  );
					
					post_conc(reciboit_cont);		
					reciboit_cont=reciboit_cont+1;
		
				});
			cal_total();
			});
		}
	}
	
	function cargacxcback(id_contribuv,id_cxcv){
		$.ajax({
			url: "<?=site_url('recaudacion/r_recibo/damecxc')?>",
			type: 'POST',
			async: false,
			data: { id_contribu:id_contribuv,id_cxc:id_cxcv }
		}).done(function(data){
			deuda=jQuery.parseJSON(data);
				
			var htm = <?=$campos ?>;
										
			jQuery.each(deuda, function(i, val) {
				can = reciboit_cont.toString();
				con = (reciboit_cont+1).toString();
				
				htm = <?=$campos ?>;
				
				htm = htm.replace(/<#i#>/g,can);
				htm = htm.replace(/<#o#>/g,con);
				
				$("#__UTPL__").before(htm);
				
				$("#id_cxcit_"+can).val(val.id_cxcit   );
				$("#id_concit_"+can).val(val.id   );
				$("#denomi_"+can).val(val.denomi     );
				$("#frecuencia_"+can).prop('selectedIndex',val.frecuencia);
				$("#freval_"+can).prop('selectedIndex',val.freval);
				$("#ano_"+can).val(val.ano        );
				$("#observa_"+can).val(val.observa    );
				$("#base_"+can).val(val.base     );
				$("#monto_"+can).val(val.monto      );
				$("#id_vehiculo_"+can).val(val.id_vehiculo );
				$("#id_inmueble_"+can).val(val.id_inmueble );
				$("#v_placa_"+can).val(val.placa     );
				$("#requiere_"+can).val(val.requiere   );
				$("#modo_"+can).val(val.modo   );
				$("#i_catastro_"+can).val(val.i_catastro  );
				$("#id_contribu").val(val.id_contribu  );
				$("#id_publicidad_"+can).val(val.id_publicidad  );
				
				post_conc(reciboit_cont);		
				reciboit_cont=reciboit_cont+1;
	
			});
			cal_total();
		});	
	}
	
	function cargacxc(){
		
		id=$("#id_contribu").val();
		
		if(id.length>0){
			cargacxcback(id,null);
		}
	}
	
	function traemonto(i){
		idr=$("#id_concit_"+i).val();
		idcontribu=$("#id_contribu").val();
		
		requiere=$("#requiere_"+i).val();
		
		idd=null;
		if(requiere=='INMUEBLE')
		idd=$("#id_inmueble_"+i).val();
		if(requiere=='VEHICULO')
		idd=$("#id_vehiculo_"+i).val();
		
		if(requiere!='MANUAL'){
			if(((requiere=='INMUEBLE' || requiere=='VEHICULO') && idd>0) || (requiere=='INMUEBLE' && requiere=='VEHICULO')){
				$.post("<?=site_url('recaudacion/r_recibo/damemonto')?>",{ id_concit:idr,id:idd,id_contribu:idcontribu },function(data){
					$("#monto_"+i).val(data);
				});
			}
		}
	}
	
	function borratodo(){
		arr=$('input[name^="id_concit_"]');
		jQuery.each(arr, function() {
			nom=this.name;
			pos=this.name.lastIndexOf('_');
			if(pos>0){
				id      = this.name.substring(pos+1);
				$('#tr_r_reciboit_'+id).remove();
			}
		});
	}
	
	function borraycargadeuda(){
		borratodo();
		cal_total();
		cargadeuda();
	}
	
	function analizar(){
		id_contribu=$("#id_contribu").val();
		if(id_contribu>0)
		vent=window.open("<?=site_url('recaudacion/r_recibo/resumen_contribu')?>"+"/"+id_contribu,"Analisis Contribuyente","height=500,width=800,scrollbars=yes");
		else
		alert("Error. Primero debe seleccionar un cliente");
	}
	
	function cal_base(i){
		//id=i;
		
		//console.log("cal_base");
		//basev = $("#base_"+i).val();
		//id_contribuv=$("#id_contribu").val();
		//
		//id_concitv=$("#id_concit_"+id).val();
		//
		//$.post("<?=site_url('recaudacion/r_recibo/damemonto')?>",{ id_concit:id_concitv,id:null,id_contribu:id_contribuv,base:basev },function(data){
		//	$("#monto_"+i).val(data);
		//});
		//cal_total();
	}
</script>
	
<?php
}else{
?>
<script language="javascript" type="text/javascript">
	$(document).ready(function(){
	});
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
					<td colspan=6 class="bigtableheader">Referencia Nro. <?php  echo $form->id->output ?></td>
				</tr>
			</table>
			<table width="100%"  style="margin:0;width:100%;">
				<tr>
					<td class="littletablerowth" ><?=$form->solvencia->label  ?>&nbsp;</td>
					<td class="littletablerow"   ><?=$form->solvencia->output ?>&nbsp; </td>
					<td class="littletablerowth" >&nbsp;</td>
					<td class="littletablerow"   >&nbsp;</td>
				</tr>
				<tr>
					<td class="littletablerowth" ><?=$form->numero->label  ?>&nbsp;</td>
					<td class="littletablerow"   ><?=$form->numero->output ?>&nbsp; </td>
					<td class="littletablerowth" ><?=$form->fecha->label  ?>*&nbsp;</td>
					<td class="littletablerow"   ><?=$form->fecha->output ?>&nbsp; </td>
				</tr>
			</table>
			
			
			<table width="100%"  style="margin:0;width:100%; background:rgb(220,220,250)" >
				<tr>
					<td class="littletablerow"  colspan="4"><strong>DATOS DEL CONTRIBUYENTE</strong></td>
				</tr>
				<tr>
					<td class="littletablerow"              ><?='<span class="littletablerowth" >'.$form->id_contribu->label.'*</span>' ?>&nbsp;</td>
					<td class="littletablerow"  colspan="3"><?=$form->id_contribu->output.'<span class="littletablerowth" >RIF/CI*</span>'.$form->rifci->output.'<span class="littletablerowth" >Nombre*</span>'.$form->nombre->output ?>&nbsp;</td>
				</tr>
			</table>
			
			
			</br>
			
			</table >
			<?php if($form->_status!='show') {?>
				<?php if($this->datasis->puede(460)){ ?>
				<button id="b_crearvehiculo"            class="buttonheadtable"  ><img src="<?=base_url()."/images/carro.png"?>" title="Crea Vehiculo" height="20" >Crear Vehiculo   </button>
				<?php } if($this->datasis->puede(461)){ ?>
				<button id="b_crearinmueble"            class="buttonheadtable"  ><img src="<?=base_url()."/images/casa.png"?>"  title="Cargar Inmueble" height="20" >Crear Inmueble</button>
				<?php } if($this->datasis->puede(462)){ ?>
				<button id="b_borrartodo"               class="buttonheadtable"  >Borrar Items   </button>
				<?php } if($this->datasis->puede(463)){ ?>
				<button id="b_cargacxc"         class="buttonheadtable"  >Cargar Cuentas por Cobrar   </button>
				<button id="b_cargacxcnro"      class="buttonheadtable"  >Carga Cuenta por Cobrar nro </button>
				<?php }?>
				<textarea id="papelera" class="textarea"  style='background-image:url(<?=base_url()?>assets/default/images/trash.png);background-repeat:no-repeat;width:48px;height:38px;border: 0px;resize:none;overflow:hidden' onchange=""  rows="1" cols="5"></textarea>
				<?php }?>
			
			<table   border="0" cellpadding="0" cellspacing="0" class="table_detalle">
				<tr>
					<th class="littletableheaderb"              >Concepto          </th>
					<th class="littletableheaderb"              >A&ntilde;o        </th>
					<th class="littletableheaderb"              >Frecuencia        </th>
					<th class="littletableheaderb"              >Ref. Vehi o Inmu  </th>
					<th class="littletableheaderb"              >Observaci&oacute;n</td> 
					<th class="littletableheaderb" align='right'>Base              </th>
					<th class="littletableheaderb" align='right'>Monto             </th>
					<?php if($form->_status!='show') {?>
					<th class="littletableheaderb">&nbsp;</td>
					<?php } ?>
				</tr>
				<?php 
				for($i=0;$i<$form->max_rel_count['r_reciboit'];$i++) {
					$obj0="itid_concit_$i";
					$obj1="itdenomi_$i";
					$obj2="itacronimo_$i";
					$obj3="itano_$i";
					$obj4="itobserva_$i";
					$obj5="itmonto_$i";
					$obj6="itid_vehiculo_$i";
					$obj7="itid_inmueble_$i";
					$obj8="itv_placa_$i";
					$obj9="itrequiere_$i";
					$obj10="iti_catastro_$i";
					$obj11="itid_cxcit_$i";
					$obj12="itfrecuencia_$i";
					$obj13="itfreval_$i";
					$obj14="itbase_$i";
					$obj15="itmodo_$i";
					$obj16="itid_publicidad_$i";
				?>
				 <tr id='tr_r_reciboit_<?=$i ?>'>
					<td class="littletablerow"              ><?=$form->$obj11->output.$form->$obj9->output.$form->$obj15->output.$form->$obj0->output.$form->$obj1->output ?></td>
					<td class="littletablerow"              ><?=$form->$obj3->output ?></td>
					<td class="littletablerow"              ><?=$form->$obj12->output.'&nbsp;'.$form->$obj13->output ?></td>
					<td class="littletablerow"              ><?=$form->$obj6->output.$form->$obj7->output.$form->$obj8->output.$form->$obj10->output.$form->$obj16->output  ?></td>
					<td class="littletablerow"              ><?=$form->$obj4->output ?></td>
					<td class="littletablerow" align='right'><?=$form->$obj14->output ?></td>
					<td class="littletablerow" align='right'><?=$form->$obj5->output ?></td>
					<?php if($form->_status!='show') {?>
					<td class="littletablerow"><a href=# onclick='del_r_reciboit(<?=$i ?>);return false;'><?=image('delete.jpg','#',array("border"=>0))?></a><span class="papelerait">_._<?=$i?>_._</span></td>
					<?php } ?>
				</tr>
				
				<?php
				} ?>
				<tr id='__UTPL__'>
				<td class="littletablefooterb" align='right' colspan="7"><?=$form->monto->output ?></td>
				<?php if($form->_status!='show') {?>
				<td class="littletablefooterb" align="center"><textarea id="papelera2" class="textarea"  style='background-image:url(<?=base_url()?>assets/default/images/trash.png);background-repeat:no-repeat;width:36px;height:36px;border: 0px;resize:none;overflow:hidden' onchange=""  rows="1" cols="5"></textarea></td>
				<?php } ?>
				</tr>
			</table>
			</br>
			
			<?php echo $form_end     ?>
			<?php echo $container_bl ?>
			<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>

