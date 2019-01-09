<html>
<head>
<title>Presupueso</title>
<style type="text/css">
.Estilo1 {font-family: Arial, Helvetica, sans-serif}
.Estilo8 {font-family: Arial, Helvetica, sans-serif; font-size: 14px; }
.Estilo10 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 36px; }
</style>
</head>																																														
<body>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>																																															
<div id="Layer1" style="position:absolute; left:186px; top:4px; width:86px; height:83px; z-index:1"><img src="<?php echo $_direccion ?>/images/logo.jpg" width="127" height="77"></div>
<div id="Layer2" style="position:absolute; left:326px; top:22px; width:310px; height:46px; z-index:2">
<table width="484" border="0">
    <tr>
    	<th width="478" scope="row"><div align="left"><span class="Estilo10">Papeleria Moderna C.A</span></div></th>
    </tr>
</table>
</div>
	<div id="Layer3" style="position:absolute; left:206px; top:120px; width:159px; height:22px; z-index:3; font-family: Arial, Helvetica, sans-serif; font-size: 24px;">
	<div align="left" class="Estilo1">Presupuesto</div>
</div>
	<p align="center">&nbsp;</p>
	<div align="center">
<table width="800" border="0">
    <tr>
			<th width="67"><div align="left" class="Estilo1">Fecha:</div></th>
			<td width="64" class="Estilo4"><div align="left"><?=$fecha?></div></td>
			<th width="67"><div align="left" class="Estilo1">Cliente:</div></th>
			<td colspan="4" class="Estilo4"><div align="left"><?=$cod_cli?></div></td>
    </tr>
    <tr>
      <th><div align="left" class="Estilo1">Numero:</div></th>
      <td width="64" class="Estilo4"><div align="left"><?=$numero?></div></td>
      <th><div align="left" class="Estilo1">Nombre:</div></th>
      <td colspan="4" class="Estilo8"><div align="left"><?=$nombre?></div></td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr>
    	<th colspan="6" scope="row">
			<table width="590" border="1">
		<tr>
      <th><span align="left" class="Estilo1">Codigo</span></th>
      <th><span align="center" class="Estilo1">Descripci&oacute;n</span></th>
      <th><span align="center" class="Estilo1">Cant</span></th>
      <th><span align="center" class="Estilo1">Fr</span></th>
      <th><span align="right" class="Estilo1">Precio</span></th>
      <th><span align="right" class="Estilo1">Importe</span></th>
    </tr>
		<?php foreach ($detalle AS $items){?>
    <tr>
      <td><span class="Estilo1"><?=$items->codigo?></span></td>
      <td><div align="lef"><span class="Estilo8"><?=$items->descrip?></span></td>
      <td><div align="center"><span class="Estilo1"><?=$items->cantidad?></span></div></td>
      <td><span class="Estilo1"><?=$items->fraccion?></span></td>
      <td><span class="Estilo1"><?=$items->precio?></span></td>
      <td><span class="Estilo1"><?=$items->importe?></span></td>
    </tr>
		<?php } ?>
</table>
    </th>
    </tr>
    <tr>
      <th colspan="4" rowspan="3" scope="row"><div align="right"></div>        <div align="right"></div>        <div align="right"></div></th>
      <th width="99" scope="row"><div align="right"><span align="right" class="Estilo1">Sub-Total:</span></div></th>
      <th width="39" scope="row"><div align="right"><span class="Estilo1">
          <?=$stotal ?>
      </span></div></th>
    </tr>
    <tr>
      <th scope="row"><div align="right"><span align="right" class="Estilo1">Impuesto:</span></div></th>
      <th scope="row"><div align="right"><span class="Estilo1">
          <?=$impuesto ?>
      </span></div></th>
    </tr>
    <tr>
      <th height="23" scope="row"><div align="right"><span align="right" class="Estilo1">Total:</span></div></th>
      <th scope="row"><div align="right"><span class="Estilo1">
          <?=$gtotal ?>
      </span></div></th>
    </tr>
</table>
</body>
</html>
