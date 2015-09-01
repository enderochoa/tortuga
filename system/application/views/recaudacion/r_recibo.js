var contribu='';

$(document).ready(function(){
	$.post("<?=site_url('ingresos/contribu/autocompleteui')?>",{ partida:"" },function(data){
		sprv=jQuery.parseJSON(data);
		jQuery.each(sprv, function(i, val) {
			val.label=val.nombre;
			
		});
		
		$("#nombre").autocomplete({
			//autoFocus: true,
			delay: 0,
			minLength: 3,
			source: sprv,
			focus: function( event, ui ){
				//$( "#nombre").val( ui.item.nombre );
				$( "#rifci").val( ui.item.rifci );
				$( "#contribu").val( ui.item.codigo );
				$( "#direccion").val( ui.item.direccion );
				$( "#telefono").val( ui.item.telefono );
				$( "#nacionalit").val( ui.item.nacionali );
				return false;
			},
			select: function( event, ui ){
				$( "#nombre").val( ui.item.nombre );
				$( "#rifci").val( ui.item.rifci );
				$( "#contribu").val( ui.item.codigo );
				$( "#direccion").val( ui.item.direccion );
				$( "#telefono").val( ui.item.telefono );
				$( "#nacionalit").val( ui.item.nacionali );
				$("#tipo").focus();
				
				return false;
			}
		})
		.data( "autocomplete" )._renderItem = function( ul, item ) {
			return $( "<li></li>" )
			.data( "item.autocomplete", item )
			.append( "<a>" + item.nombre + "</a>" )
			.appendTo( ul );
		};
		
		jQuery.each(sprv, function(i, val) {
			val.label=val.rifci;
		});
		
		$("#rifci").autocomplete({
			//autoFocus: true,
			delay: 0,
			minLength: 3,
			source: sprv,
			focus: function( event, ui ){
				$( "#nombre").val( ui.item.nombre );
				//$( "#rifci").val( ui.item.rifci );
				$( "#contribu").val( ui.item.codigo );
				$( "#direccion").val( ui.item.direccion );
				$( "#telefono").val( ui.item.telefono );
				$( "#nacionalit").val( ui.item.nacionali );
				return false;
			},
			select: function( event, ui ){
				$( "#nombre").val( ui.item.nombre );
				$( "#rifci").val( ui.item.rifci );
				$( "#contribu").val( ui.item.codigo );
				$( "#direccion").val( ui.item.direccion );
				$( "#telefono").val( ui.item.telefono );
				$( "#nacionalit").val( ui.item.nacionali );
				$("#tipo").focus();
				return false;
			}
		})
		.data( "autocomplete" )._renderItem = function( ul, item ) {
			return $( "<li></li>" )
			.data( "item.autocomplete", item )
			.append( "<a>" + item.rifci + "</a>" )
			.appendTo( ul );
		};
		
	});
	
});
		
$(function(){
	$("#nacionalit").hide();
	$(".inputnum").numeric(".");
	$( "#tabs" ).tabs();
	
	$( "#tb" ).tabs();
	$( "#tbb" ).dialog({
		width: 650,
		height: 200,
		 autoOpen: false,
		  position: 'top'
	});
	
	$("#deta").click(function() {
		$("#tbb").dialog("open");
	});
	
	$("#traedeuda").click(function() {
		tipo=$("#tipo").val();
		
		if(tipo==8){
			v = $("#vehiculo").val();
			
			if(v.length>0){
				$.post("<?=site_url('ingresos/recibo/damedeuda_trimestre')?>",{ vehiculo:v },function(data){
					deuda=jQuery.parseJSON(data);
					cargadeuda(deuda);
					cal_total();
				});
			}else{
				alert("Primero seleccione un Vehiculo");
			}
		}
	});
});

function cargadeuda(deuda){
	var htm = <?=$campos ?>;
									
	jQuery.each(deuda, function(i, val) {
		can = itrecibo_cont.toString();
		con = (itrecibo_cont+1).toString();
		
		htm = <?=$campos ?>;
		
		htm = htm.replace(/<#i#>/g,can);
		htm = htm.replace(/<#o#>/g,con);
		
		$("#__UTPL__").before(htm);
		$("#d_monto_"+can).numeric(".");
		
		$("#d_ano_"+can    ).val(val.ano);
		$("#d_tipo_"+can   ).val(val.tipo);
		$("#d_nro_"+can    ).val(val.nro);
		$("#d_descrip_"+can).val(val.descrip);
		$("#d_monto_"+can  ).val(val.monto);
		
		itrecibo_cont=itrecibo_cont+1;
		
	});
}

function cal_nacionali(){
	nacionalit=$("#nacionalit").val();
	if(nacionalit=="E")
	a=1;
	else
	a=0;

	$("#nacionali").prop('selectedIndex',a);
}

function cal_ch(i){
	add_itrecibo();
	
	ano=$("#ano").val();
	
	id=itrecibo_cont-1;
	$("#d_ano_"+id).val(ano);
	$("#d_tipo_"+id).val('Mes');
	$("#d_nro_"+id).val(i);
	$("#d_descrip_"+id).val('Mensualidad');
	claseo=$("#p_clase").val();
	
	$.post("<?=site_url('ingresos/claseo/montoaseo')?>",{ a:ano,codigo:claseo },function(data){
		$("#d_monto_"+id).val(data);
		cal_total();
	});
}

function cal_claseo(){
	declaracion=$("#declaracion").val();
	claseo=$("#p_clase").val();
	$.post("<?=site_url('ingresos/claseo/montodecla')?>",{ decla:declaracion,codigo:claseo },function(data){
		$("#monto").val(data);
		cal_total();
	});
}

function cal_ch2(i){
	add_itrecibo();
	ano=$("#ano").val();
	id=itrecibo_cont-1;
	$("#d_ano_"+id).val(ano);
	$("#d_tipo_"+id).val('Tri');
	$("#d_nro_"+id).val(i);
	$("#d_descrip_"+id).val('Trimestre');
	v_clase=$("#v_clase").val();
	$.post("<?=site_url('ingresos/clase/montotri')?>",{ codigo:v_clase,a:ano },function(data){
		$("#d_monto_"+id).val(data);
		cal_total();
	});
	
	
}

function btn_anular(i){
	if(!confirm("Esta Seguro que desea Anular el Ingreso"))
		return false;
	else
		window.location='<?=site_url('ingresos/recibo/anular')?>/'+i
}

function cal_total(){
	
	arr=$('input[name^="d_tipo_"]');
	t=0;
	jQuery.each(arr,function(){
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
		
			id= this.name.substring(pos+1);
			m=parseFloat($("#d_monto_"+id).val());
			t=t+m;
		}
		$("#monto").val(Math.round(t*100)/100);
	});
}

function add_itrecibo(){
	var htm = <?=$campos ?>;
	can = itrecibo_cont.toString();
	con = (itrecibo_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	$("#monto_"+can).numeric(".");
	itrecibo_cont=itrecibo_cont+1;
}

function del_itrecibo(id){
	id = id.toString();
	$('#tr_itrecibo_'+id).remove();
}
