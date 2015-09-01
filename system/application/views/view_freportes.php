<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=<?=$this->config->item('charset'); ?>" />
<?=style("reportes.css");?>
<?=$head ?>
<script type="text/javascript" language="javascript">
	function prueba(){
		var opt = {
			// Use POST
			//method: 'post',
			// Send this lovely data
			//postBody: escape('thisvar=true&thatvar=Howdy&theothervar=2112'),
			// Handle successful response
			onSuccess: function(t) {
			    alert(t.responseText);
			},
			// Handle 404
			on404: function(t) {
			    alert('Error 404: location "' + t.statusText + '" was not found.');
			},
			// Handle other errors
			onFailure: function(t) {
			    alert('Error ' + t.status + ' -- ' + t.statusText);
			}
		}
		new Ajax.Request('<?php echo site_url('reportes/consulstatus') ?>', opt);
	}
	function is_loaded(){ 
		//parent.navegador.arepo();
		window.parent.carga();
		
	}
	
	
</script>
</head>
<body onload='window.parent.descarga()'>

	<div id='home'>
	<p><?=$titulo ?></p>
	<div class="alert"><?php if(isset($error)) echo $error; ?></div>
	<p><?=$filtro ?></p>
	<p><br><?php if(isset($regresar)) echo $regresar; ?></p>
	</div>
</body>
</html>