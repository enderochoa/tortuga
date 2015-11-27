<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php // <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> ?>
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?=$this->config->item('charset'); ?>" />
<meta http-equiv="Content-Language" content="es-es" />
<meta name="ROBOTS" content="NONE" />
<meta name="MSSmartTagsPreventParsing" content="true" />
<meta name="Keywords" content="<?=property('app_keywords')?>" />
<meta name="Description" content="<?=property('app_description')?>" />
<meta name="Copyright" content="<?=property('app_copyright')?>" />
<title>Men&uacute; <?=$this->datasis->traevalor('SISTEMA').' '.$this->datasis->traevalor('EJERCICIO') ?></title>

<?php echo style("estilos.css");  ?>
<?php //echo style("classic.css");?>
<?php echo style("menutab.css");  ?>
<?php echo style("acordeon.css"); ?>
<?php echo style("masonry.css"); ?>


<?php echo script("jquery.js"); ?>
<?php echo script("plugins/myAccordion.js"); ?>
<?php echo script("plugins/interface.js"); ?>
<?php echo script("plugins/jquery.masonry.min.js"); ?>


<script type="text/javascript" charset=<?=$this->config->item('charset'); ?>">
$(document).ready(function() {
	$("#accordion").myAccordion({
		speed: "fast", // @param : low, medium, fast
		defautContent: 0 // @param : number
	});
	
	$("a[name='_mp']").click(function () {
		$("a[name='_mp']").removeClass("current");
		url=this.href;
		pos=url.lastIndexOf('/');
		carga=url.substring(pos);
		$('#accordion').load('<?php echo site_url('bienvenido/accordion') ?>'+carga,'',function() { $('#accordion').myAccordion({ speed: 'fast', defautContent: 0 });
		});
		$(this).addClass('current');

		$('#tumblelog').load('<?php echo site_url('bienvenido/cargapanel') ?>'+carga,function(){
			$('#maso').masonry({ singleMode: true,itemSelector: '.box' });
		});
		return false;
	});
});

</script>

</head>

<body>
	
	<div id="container">
		
	<?php  $this->load->view($data['settings']['default'].'/'.$data['settings']['commons']."header", $data); ?>
		<table border='0' cellpadding='0' cellspacing='0' width='99%'>
			<tr>
				<td   valign='top' id='tablemenu'>
					<div id='micelanias'>
					<?php echo $smenu ?>
					</div>
				</td>
				
				<?
				$CI =& get_instance();
				
				if($CI->session->userdata('logged_in')){
					$usuario = $CI->session->userdata['usuario'];
					$usuarioe =$this->db->escape($usuario);
					$usachat  =$this->datasis->traevalor('USACHAT','S');
					$usachatu = $this->datasis->dameval("SELECT usachat FROM usuario WHERE us_codigo=$usuarioe");
					?>
				<td width="<?php echo ($usachat=='S' && $usachatu=='S'?'50':'80') ?>%" valign='top'>
					
					<?php $this->load->view($data['settings']['default'].'/'.$data['settings']['content'].$view,  $data); ?>
				</td>
				<td width="<?php echo ($usachat=='S' && $usachatu=='S'?'30':'0') ?>%" valign='top'>
					<?php 
					if($usachat=='S' && $usachatu=='S'){
					 ?>
					<iframe src="<?=site_url("phpfreechat")."?user=$usuario" ?>" width="500px" height="600px">
					</iframe>
					<?PHP 
					}
					?>
				</td>
				<?}else{?>
				<td  valign='top'>
					
					<?php $this->load->view($data['settings']['default'].'/'.$data['settings']['content'].$view,  $data); ?>
				</td>
				<?}?>
			</tr>
		</table>
		<b class='mininegro'><?php
		//echo "Conectado a: ".$this->db->database;
		//if(isset($_SERVER['REMOTE_ADDR'])){
		//echo br().'Tu ip: '.$_SERVER['REMOTE_ADDR'];
		//}
		?>
		</b>
		<table width="100%" border=0 cellspacing=0 cellpadding=0>
		    <tr>
			<td width='150px' ><div id="pie"><p><?=image("portada25.png") ?><p></p></div></td>
			<td>
			    <div id="pie"><p style="font-size:8px"><?php echo $copyright ?></p><?php echo image('codeigniter.gif'); ?>
				<?=image('php-power-micro.png')?>
				<?=image('jquery-icon.png')?>
				<?=image('mysqlpowered.png')?>
				<?=image('buttongnugpl.png')?>
			    </div>
			</td>
			<td width='150px' ><div id="pie"><p><?=image("portada25l.png") ?>
			<a href='javascript:void(0);' onclick="window.open('/proteoerp/chat', 'wchat', 'width=580,height=600,scrollbars=yes,status=yes,resizable=yes,screenx='+((screen.availWidth/2)-290)+',screeny='+((screen.availHeight/2)-300)+'');" style="font-color:white;">Chat</a></p></div></td>
		    </tr>
		</table>
	</div>
</body>
</html>
