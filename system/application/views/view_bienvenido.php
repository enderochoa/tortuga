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
<title><?php echo property('app_title')?></title>
<?php echo style("estilos.css");?>
<?php //echo style("classic.css");?>
<?php echo style("menutab.css");?>
<?php echo style("acordeon.css");?>

<?php echo script("prototype.js");?>
<?php echo script("scriptaculous.js");?>
<?php echo script("menutab.js");?>
<?php echo script("accordion.js");?>
<?php echo script("effects.js"); ?>
<?php //echo script("jsquery.js"); ?>

</head>
<body>
	<div id="container">
		<div id="header">
			identificacion de usuario
		</div>
		
		<div id="ddimagetabs" class="halfmoon">
			<?php echo $menu ?>
		</div>
		<div id="tabcontentcontainer">
			<?php echo $amenu ?>
		</div>
		
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
				<td  valign='top' id='tablemenu'>
					<div id='micelanias'>
						<?php echo $smenu ?>
					</div>
				</td>
				<td  valign='top'>
				<script> var acc = new Accordion("accordion","h3","acc") </script>
					<?php echo $contenido ?>
				
				</td>
			</tr>
		</table>
		<div id="pie"><p><?php echo $copyleft ?></p><?php echo image('codeigniter.gif'); ?></div>
	</div>
</body>
</html>