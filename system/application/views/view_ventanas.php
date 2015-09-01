<?php
//ob_start('comprimir_pagina');
?>

<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?=$this->config->item('charset'); ?>" />
<title><?=(isset($this->titp) ? $this->titp: $this->datasis->traevalor("SISTEMA") ).' '.$this->datasis->traevalor("EJERCICIO") ?></title>
<?=style("ventanas.css");?>


<style type="text/css">
<?php if (isset($style))  echo $style;   ?>
#caja {width:98%;display: none;padding:5px;border:2px solid #D0D0D0;background-color:#FDF4E1;}
#mostrar{display:block;width:100%;padding:5px;border:2px solid #D0D0D0;background-color:#F0F0F0;}
</style>
<?php if (isset($head))   echo $head;   ?>
<?php if (isset($script)) echo $script; ?>

<script type="text/javascript">
$(function()
{
	
	$("#mostrar").click(function(event) {event.preventDefault();$("#caja").slideToggle();});
	$("#caja a").click(function(event) {event.preventDefault();$("#caja").slideUp();});
	
	<?php if (isset($filtro) && isset($help)){?>
	$("#ayuda").hide();
	$("#ayuda").dialog({
		height: 400,
		width: 600,
		modal: true,
		title:"Ayuda",
		autoOpen: false
	});
	$("#tuxhelp").click(function() {
		$("#ayuda").dialog("open");
	});
	
	<?php }?>
});
</script>
</head>
<body>
<?php if (isset($filtro) && isset($help)){?>
<div id='ayuda'>
	La Ventana que esta Observando es llamada Filtro/Lista: </br>
	     Esta sirve para Buscar y Ver una Lista de Documentos rapidamente, 
	     </br>para luego tener la oportunidad de seleccionar alguno y Realizar una serie de Acciones 
	     </br>&oacute; para crear Directamente un Nuevo Documento haciendo Click en Agregar Registro
	     </br>
	     </br>
	     Opciones Rapidas:</br>
	         Haz Click en la tortuga con la lupa para abrir el Filtro</br>
	         Haz Click en un <a href='#'>enlace azul subrayado</a>, para ver el Documento Completo<br>
	         Haz Click en el Boton Agregar Nuevo Registro  </br>
	         </br>
	Para Mayor Informaci&oacute;n visite el <a target='_blank' href='http://dremanva2.fdns.net/tortugawiki/pmwiki.php/Manual/UsoGeneral3'>http://dremanva2.fdns.net/tortugawiki/pmwiki.php/Manual/UsoGeneral3</a> o </br>
	contacte a tortuga@proteoerp.org</br></br>
</div>
<?php }?>
<div id='encabe'>	<center><?php if (isset($title)) echo $title; ?></center></div>

<?php if(isset($smenu)) echo '<div id="smenu">'.$smenu.'</div>'; ?>

<div id='contenido'>
	
	<table width="95%" border=0 align="center">
		<tr >
			<td></td>
			<td>
<?php if (isset($filtro)) { ?>  
				<table width="95%">
				<tr>
				<td width="<?=(isset($help)?'90%':'100%') ?>">
				<div>
					<a href="#" id="mostrar" ><?=image("tortulupa2.png", "#", array("border"=>"none")); ?> </a>				
				</div>
				</td>
				<td>
					<?=(isset($help)?image("tuxhelp.gif", "Haz Click Para Mostrar Una Ayuda Rapida", array("border"=>"none","height"=>"55px","id"=>"tuxhelp","class"=>"tuxhelp")):'') ?>
				</td>
				</tr>
				<tr >
				<td colspan="2">
				<div id="caja">
					<?=$filtro; ?>
				</div>
				</td>
				</tr>
				</table>
				
				
<?php } ?>
			</td>
		</tr>
		<tr>
			<td valign=top><?php if (isset($lista)) echo $lista; ?></td>
			<td><?php if (isset($content)) echo $content; ?></td>
		</tr>
	</tr>
	<div class="footer">
	<table width="100%" align="center">
	<tr>
	<td align="center"><p style='font-size:10'>tortuga@proteoerp.org | <?=$this->datasis->traevalor('SISTEMA')?>  |<a href="#" onClick="window.close()"> Cerrar</a> | tiempo {elapsed_time}</p></td>
	</tr>
	</table>
		
	</div>
</tr>
</table>
<?php if (isset($extras)) echo $extras; ?>
</div>
</body>
</html>
<?php
//ob_end_flush();
//
//// Función para eliminar todos los espacios en blanco
//function comprimir_pagina($buffer) {
//    $busca = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
//    $reemplaza = array('>','<','\\1');
//    return preg_replace($busca, $reemplaza, $buffer);
//}
?>
