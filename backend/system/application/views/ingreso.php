<html>
<head>
<title>Entrar al Sistema</title>
<?=$estilos ?>
</head>
<body <? if($this->datasis->traevalor('USAPROPA')=='S'){ ?>background="<?=base_url()?>/images/propa.png" style="background-repeat:no-repeat;background-position:center;"<?php }?>>
<div id="header">
<table width="100%">
<tr rowspan="2" >
<td align="left"><img src="<?=base_url()?>/images/logo.jpg" height="70"></img>
</td>
<td align="center">
<h2><?=$this->datasis->traevalor('TITULO1')?></h2>
</td>
<td align="right">
<img src="<?=base_url()?>images/logo2.jpg" height="60"></img>
</td>
</table>
</div>
<center>
</br></br>
<table background="<?=base_url()?>/images/t50.png" border="0" cellspacing="0" cellpadding="0" style="color:red;font-weight:bold">
<tr>
<td align="center">

<div >Ingrese Su Usuario y Contrase&ntilde;a</div>
<?=$cuerpo ?>

</td>
</tr>
</table>

</center>

</div>
<br><br><br><br><br><br>
<table width="100%" border=0 cellspacing=0 cellpadding=0>
		    <tr>
			<td width='150px' ><div id="pie"><p><?=image("portada25.png") ?><p></p></div></td>
			<td>
			    <div id="pie"><p style="font-size:8px"><?php echo "</br>SISTEMA TORTUGA </br> Control Presupuestario, Administrativo, Tesore&iacute;a,N&oacute;mina, Contabilidad, Bienes, Suministros</br>Inversiones DREMANVA, C.A.
Telf: 58 (274) 2711922 MERIDA - VENEZUELA Correo electronico tortuga@proteoerp.org" ?></p><?php echo image('codeigniter.gif'); ?>
				<?=image('php-power-micro.png')?>
				<?=image('jquery-icon.png')?>
				<?=image('mysqlpowered.png')?>
				<?=image('buttongnugpl.png')?>
			    </div>
			</td>
			<td width='150px' ><div id="pie"><p><?=image("portada25l.png") ?>
			<a href='javascript:void(0);' onClick="window.open('/proteoerp/chat', 'wchat', 'width=580,height=600,scrollbars=yes,status=yes,resizable=yes,screenx='+((screen.availWidth/2)-290)+',screeny='+((screen.availHeight/2)-300)+'');" style="font-color:white;">Chat</a></p></div></td>
		    </tr>
		</table>
</body>
</html>
