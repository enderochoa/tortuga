<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=<?=$this->config->item('charset'); ?>" />
	<?=script("scriptaculous.js");?>
	<?=script("prototype.js"); ?>
	<?=script("effects.js");?>
<style type="text/css">
#encabe{
	background-color:#7C2121;
}
body{
	background-color:#4F1010;
	margin:0;
	padding:0;
}
</style>
<script type="text/javascript" language="javascript">
	var factivo<?=$repo?>=ractivo<?=$repo?>=false;
	function afiltro() {
		if(!factivo<?=$repo?>){
			factivo<?=$repo?>=true;
			new Effect.toggle('filtro', 'appear');
		}
	}
	function dfiltro() {
		if(factivo<?=$repo?>){
			new Effect.toggle('filtro', 'appear');
			factivo<?=$repo?>=false;
		}
	}
	function arepo() {
		//ractivo<?=$repo?>=true;
		//new Effect.toggle('repor', 'appear');
	}
	
</script>
</head>
<body>
	<div id='encabe'>
	<img src="<?php echo base_url() ?>/assets/default/css/templete_01.jpg" width="120" >
	</div>
	<center>
		<div id='filtro' style="display: none;">
			<?=anchor("reportes/enlistar/$repo",image('listado.png','Volver al Listado',array('border'=>0)),array('target'=>'contenido'));?>
		</div>
		<div id='repor' style="display: none;">
			<?=anchor("reportes/ver/",image('go-previous.png','Volver al Filtro',array('border'=>0)),array('target'=>'contenido','id'=>'rgfil'));?>
		</div>
	</center>
	<!-- <a href='#' onclick='window.parent.carga();'>carga</a> <a href='#' onclick='window.parent.descarga();'>descarga</a>-->
</body>
</html>