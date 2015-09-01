<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
	echo $form->output;
else:
 
if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_scripts;
echo $form_begin?>
<table align='center'>
	<tr>
		<td align=right>
			<?php echo $container_tr?>
		</td>
	</tr>
	<tr>
		<td>
          <table width="100%"  style="margin:0;width:100%;">
            <tr>
              <td colspan=11 class="littletableheader">Encabezado</td>
            </tr>
            <tr>
              <td width="100" class="littletablerowth"><?=$form->fecha->label ?></td>
              <td width="100" class="littletablerow"><?=$form->fecha->output ?></td>
              <td width="100" class="littletablerowth"><?=$form->orden->label ?></td>
              <td width="100" class="littletablerow"><?=$form->orden->output ?></td>
              <td width="119" class="littletablerowth"><?=$form->proveedor->label ?></td>
              <td colspan="3" class="littletablerow"><?=$form->proveedor->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?=$form->numero->label ?></td>
              <td class="littletablerow"><?=$form->numero->output ?></td>
              <td class="littletablerowth"><?=$form->cfis->label ?></td>
              <td class="littletablerow"><?=$form->cfis->output ?></td>
              <td class="littletablerowth"><?=$form->nombre->label ?></td>
              <td colspan="3" class="littletablerow"><?=$form->nombre->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?=$form->tipo->label ?></td>
              <td class="littletablerow"><?=$form->tipo->output ?></td>
              <td class="littletablerowth"><?=$form->almacen->label ?></td>
              <td class="littletablerow"><?=$form->almacen->output ?></td>
              <td class="littletablerowth"><?=$form->vence->label ?></td>
              <td width="99" class="littletablerow"><?=$form->vence->output ?></td>
              <td width="44" class="littletablerow"><span class="littletablerowth">
                <?=$form->peso->label ?>
              </span></td>
              <td width="99" class="littletablerow"><?=$form->peso->output ?></td>
                </tr>
          </table>
          <?php echo $form->detalle->output ?>
          <?php //echo $detalle ?>
<table  width="100%" style="margin:0;width:100%;" > 
	  <tr>                                                           
	  	<td colspan=10 class="littletableheader">Totales</td>      
	 </tr>                                                          
	 <tr>                                                 
	  <td width="131" class="littletablerowth"><?=$form->rislr->label ?> </td>
		<td width="122" class="littletablerow" ><?=$form->rislr->output ?> </td>
    <td width="125" class="littletablerowth"><?=$form->anticipo->label ?> </td>
		<td width="125" class="littletablerow"><?=$form->anticipo->output ?> </td>
		<td width="111" class="littletablerowth" ><?=$form->subt->label ?> </td>
		<td width="139" class="littletablerow" ><?=$form->subt->output ?> </td>
      </tr>
      <tr>
    <td class="littletablerowth"><?=$form->riva->label ?></td>
		<td class="littletablerow" ><?=$form->riva->output ?></td>
    <td class="littletablerowth"><?=$form->contado->label ?></td>
		<td class="littletablerow" ><?=$form->contado->output ?></td>
		<td class="littletablerowth"><?=$form->iva->label ?></td>
		<td class="littletablerow" ><?=$form->iva->output ?></td>
      </tr>
      <tr>
    <td class="littletablerowth"><?=$form->monto->label ?></td>
		<td class="littletablerow" ><?=$form->monto->output ?></td>
    <td class="littletablerowth"><?=$form->credito->label ?></td>
		<td class="littletablerow" ><?=$form->credito->output ?></td>
		<td class="littletablerowth"><?=$form->total->label ?></td>
		<td class="littletablerow" ><?=$form->total->output ?></td>
      </tr>
</table>

<?php echo $form_end?>
<?php echo $container_bl ?>
<?php echo $container_br ?>
	  <td>
	<tr>
<table>
<?php endif; ?>
