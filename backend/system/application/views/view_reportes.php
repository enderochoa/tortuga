<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=<?=$this->config->item('charset'); ?>" />
<?=style("reportes.css");?>
<?=$head ?>
<script type="text/javascript" language="javascript">
	function descarga() {
		parent.navegador.dfiltro();
		window.parent.descarga();
	}
	
</script>
</head>
<body onload='descarga()'>	
	<div id='home'>
	<p><?=$titulo ?></p>
	<p><?=$forma ?></p>
	</div>
	
</body>
</html>