<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=<?=$this->config->item('charset'); ?>" />
	<title><?=$titu; ?></title>
	<?=script("scriptaculous.js");?>
	<?=script("prototype.js"); ?>
	<?=script("effects.js");?>
	<script type="text/javascript" language="javascript">
		var <?='bool'.$repo ?>=false;
		function carga() {
			if(!<?='bool'.$repo ?>){
				<?='bool'.$repo ?>=true;
				//new Effect.Opacity('contenido', {duration:0.5, from:1.0, to:0.3}); 
				//new Effect.toggle('preloader', 'appear');
				/*new Ajax.Request('<?php echo site_url('reportes/consulstatus') ?>', {
				onComplete: function(transport) {
				  alert(transport.responseText);
				}
				});*/
			}
		}
		function descarga() {
			//if(<?='bool'.$repo ?>){
			//	<?='bool'.$repo ?>=false;
			//	new Effect.Opacity('contenido', {duration:0.5, from:0.3, to:1.0}); 
			//	new Effect.toggle('preloader', 'appear');
			//}
		}
		</script>
	</head>
	<body marginheight="0" marginheight="0" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" onload='descarga()'>
		<IFRAME id="navegador" name="navegador" src="<?php echo site_url("reportes/cabeza/$repo") ?>" width="100%" height="65" scrolling="no" frameborder="0"></IFRAME>
		<IFRAME id="contenido" name="contenido" src="<?php echo site_url("reportes/enlistar/$pre") ?>" width="100%" height="100%" scrolling="auto" frameborder="0">
			El navegador no soporta iFrames o esta desactivado <A href="<?php echo site_url('reportes/enlistar/sfac') ?>">Alternativa</A>]
		</IFRAME>
		
		<div id="preloader" style="display: none;	position:absolute; left:40%; top:150px; font-family:Verdana, Arial, Helvetica, sans-serif;">
			<center>
			<?=image("loading4.gif");?><br>
			<?=image("loadingBarra.gif");?><br>
			<b>Cargando . . . </b>
			</center>
		</div>
	</body>
</html> 